<?php

///////////////////////////////////////////////////////////////////////////////
//
// Copyright 2003-2010 ClearFoundation
//
///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//  
///////////////////////////////////////////////////////////////////////////////
    
/** 
 * System time manager.
 *  
 * @package ClearOS
 * @subpackage API
 * @author {@link http://www.foundation.com/ ClearFoundation}
 * @license http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @copyright Copyright 2003-2010 ClearFoundation
 */

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = isset($_ENV['CLEAROS_BOOTSTRAP']) ? $_ENV['CLEAROS_BOOTSTRAP'] : '/usr/clearos/framework/shared';
require_once($bootstrap . '/bootstrap.php');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('date/date');
clearos_load_language('base/base');
clearos_load_library('base/Folder');
clearos_load_library('base/File');
clearos_load_library('base/ConfigurationFile');

///////////////////////////////////////////////////////////////////////////////
// E X C E P T I O N  C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Timezone not set exception.
 *
 * @package ClearOS
 * @subpackage Exception
 * @author {@link http://www.foundation.com/ ClearFoundation}
 * @license http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @copyright Copyright 2003-2010 ClearFoundation
 */

class TimezoneNotSetException extends EngineException
{
	/**
	 * TimezoneNotSetException constructor.
	 *
	 * @param string $message error message
	 */

	public function __construct($message)
	{
		parent::__construct($message, ClearOsError::CODE_ERROR);
	}
}

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/** 
 * System time manager.
 *  
 * @package ClearOS
 * @subpackage API
 * @author {@link http://www.foundation.com/ ClearFoundation}
 * @license http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @copyright Copyright 2003-2010 ClearFoundation
 */

class Time extends Engine
{
	///////////////////////////////////////////////////////////////////////////////
	// M E M B E R S
	///////////////////////////////////////////////////////////////////////////////

	const CMD_HWCLOCK = "/sbin/hwclock";
	const FILE_CONFIG = "/etc/sysconfig/clock";
	const FILE_TIMEZONE = "/etc/localtime";
	const FILE_TIMEZONE_INFO = "/etc/localtime.info";
	const PATH_ZONEINFO = "/usr/share/zoneinfo/posix";

	/**
	 * Time constructor.
	 */

	function __construct()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		parent::__construct();

//		require_once(GlobalGetLanguageTemplate(__FILE__));
	}

	/**
	 * Returns the system time (in seconds since Jan 1, 1970).
	 * 
	 * @return integer system time in seconds since Jan 1, 1970
	 */

	public function GetTime()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		return time();
	}

	/**
	 * Returns the current timzeone.
	 * 
	 * @return string current time zone
	 * @throws EngineException, TimezoneNotSetException
	 */

	public function GetTimeZone()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		// Sanity check existence of real time zone file
		//----------------------------------------------
		
		$file = new File(self::FILE_TIMEZONE);
		$fileok = false;

		try {
			$fileok = $file->Exists();
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

		if (! $fileok)
			throw new TimezoneNotSetException(TIME_LANG_ERRMSG_TIMEZONE_NOT_SET);

		// Check the /etc/sysconfig/clock file for time zone info
		//-------------------------------------------------------

		try {
			$metafile = new ConfigurationFile(self::FILE_CONFIG);
			$timezone = $metafile->Load();
			if (isset($timezone['ZONE']))
				return preg_replace("/\"/", "", $timezone['ZONE']);
		} catch (FileNotFoundException $e) {
			// Not fatal, use methodology below
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

		// If time zone is not defined in /etc/sysconfig/clock, try to
		// determine it by comparing /etc/localtime with time zone data
		//--------------------------------------------------------------

		$currentmd5 = md5_file(self::FILE_TIMEZONE);

		try {
			$folder = new Folder(self::PATH_ZONEINFO);
			$zones = $folder->GetRecursiveListing();

			foreach ($zones as $zone) {
				if ($currentmd5 == md5_file(self::PATH_ZONEINFO . "/$zone"))
					return "$zone";
			}
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

		// Ugh -- sometimes the time zone files change.
		try {
			$currenttz = date_default_timezone_get();
			$this->SetTimezone($currenttz);
			return $currenttz;
		} catch (Exception $e) {
			throw new EngineException(TIME_LANG_ERRMSG_TIMEZONE_INVALID, ClearOsError::CODE_ERROR);
		}
	}

	/**
	 * Returns a list of available time zones on the system.
	 * 
	 * @return array a list of available time zones
	 * @throws EngineException
	 */

	public function GetTimeZoneList()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		try {
			$folder = new Folder(self::PATH_ZONEINFO);
			$zones = $folder->GetRecursiveListing();
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

		$zonelist = array();

		foreach ($zones as $zone)
			$zonelist[] = $zone;

		return $zonelist;
	}

	/**
	 * Sets the Hardware Clock to the current system time.
	 * 
	 * @return void
	 * @throws EngineException
	 */

	public function SendSystemToHardware()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		try {
			$shell = new ShellExec();
			if ($shell->Execute(self::CMD_HWCLOCK, "--systohc", true) != 0)
				throw new EngineException($shell->GetFirstOutputLine(), ClearOsError::CODE_ERROR);
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}
	}

	/**
	 * Sets the current timzeone.
	 *
	 * The /etc/localtime file is just a copy of the appropriate file in
	 * the time zones directory.  This ends up giving us a one to many
	 * relationship (the localtime file could correspond to many time zone
	 * files).  We keep time zone information in /etc/localtime.info just to
	 * make it a one-to-one relationship.
	 * 
	 * @param string $timezone time zone
	 * @return void
	 * @throws EngineException, ValidationException
	 */

	public function SetTimeZone($timezone)
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		if (!$this->IsValidTimeZone($timezone))
			throw new ValidationException(TIME_LANG_ERRMSG_TIMEZONE_INVALID);

		// Set /etc/localtime
		//-------------------

		try {
      		$file = new File(self::PATH_ZONEINFO . "/" . $timezone);
			$file->CopyTo(self::FILE_TIMEZONE);
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

		// Set meta information in /etc/sysconfig/clock
		//---------------------------------------------

		try {
			$info = new File(self::FILE_CONFIG);

			if ($info->Exists()) {
				$info->ReplaceLines("/^ZONE=/", "ZONE=\"$timezone\"\n");
			} else {
				$info->Create("root", "root", "0644");
				$info->AddLines("ZONE=\"$timezone\"\n");
			}
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}
	}

	/**
	 * @access private
	 */

	public function __destruct()
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		parent::__destruct();
	}

	///////////////////////////////////////////////////////////////////////////////
	// V A L I D A T I O N   R O U T I N E S
	///////////////////////////////////////////////////////////////////////////////

	/**
	 * Validates time zone.
	 *
	 * @param string $timezone time zone
	 * @return boolean true if time zone is valid
	 * @throws EngineException
	 */

	public function IsValidTimeZone($timezone)
	{
		ClearOsLogger::Profile(__METHOD__, __LINE__);

		if (!$timezone) {
			$this->AddValidationError(TIME_LANG_ERRMSG_TIMEZONE_INVALID, __METHOD__, __LINE__);
			return false;
		}

		try {
			$file = new File(self::PATH_ZONEINFO . "/" . $timezone);

			if ($file->Exists()) {
				return true;
			} else {
				$this->AddValidationError(TIME_LANG_ERRMSG_TIMEZONE_INVALID, __METHOD__, __LINE__);
				return false;
			}
		} catch (Exception $e) {
			throw new EngineException($e->GetMessage(), ClearOsError::CODE_ERROR);
		}

	}
}

// vim: syntax=php ts=4
?>
