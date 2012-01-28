<?php

/**
 * System time javascript helper.
 *
 * @category   ClearOS
 * @package    Date
 * @subpackage Javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/date/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('date');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type:application/x-javascript');
?>

$(document).ready(function() {

    // Translations
    //-------------

    lang_synchronizing = '<?php echo lang("date_synchronizing"); ?>';
    lang_synchronized = '<?php echo lang("date_synchronized"); ?>';

    // Main
    //-----

	$("#sync").click(function(){
		$("#status_synchronize").html('<div class="theme-form-header-working">' + lang_synchronizing + '</div>');

		$.ajax({
			url: '/app/date/sync',
			method: 'GET',
			dataType: 'json',
			success : function(payload) {
				showData(payload);
            },
			error: function (XMLHttpRequest, textStatus, errorThrown) {
			}

		});
	});
});

function showData(payload) {
    if (payload.error_message) {
        // $("#result").html(payload.error_message);
    } else {
        $("#status_synchronize").html(lang_synchronized);
    }
}

// vim: ts=4 syntax=javascript
