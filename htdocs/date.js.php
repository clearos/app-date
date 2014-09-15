<?php

/**
 * System time javascript helper.
 *
 * @category   apps
 * @package    date
 * @subpackage javascript
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
clearos_load_language('base');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type:application/x-javascript');
?>

$(document).ready(function() {

    // Translations
    //-------------

    lang_synchronizing = '<?php echo lang("date_synchronizing"); ?>';
    lang_synchronization_success = '<?php echo lang("date_synchronization_success"); ?>';
    lang_synchronized = '<?php echo lang("date_synchronized"); ?>';
    lang_status = '<?php echo lang("base_status"); ?>';

    // Main
    //-----

	$("#sync").click(function(){
        var options = new Object();
        options.text = lang_synchronizing;
        $('#auto_synchronize_text').html(clearos_loading(options));
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
        var options = new Object();
        options.type = 'warning';
		clearos_dialog_box('sync_stat', lang_status, payload.error_message, options);
    } else {
        var options = new Object();
        options.type = 'success';
        options.reload_on_close = true;
		clearos_dialog_box('sync_stat', lang_status, lang_synchronization_success, options);
        $('#auto_synchronize_text').html('');
    }
}

// vim: ts=4 syntax=javascript
