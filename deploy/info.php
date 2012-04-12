<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'date';
$app['version'] = '1.0.10';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('date_app_description');
$app['tooltip'] = lang('date_app_tooltip');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('date_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_settings');

// Wizard extras
$app['controllers']['date']['wizard_name'] = lang('date_app_name');
$app['controllers']['date']['wizard_description'] = lang('date_time_zone_wizard_help');
$app['controllers']['date']['inline_help'] = array(
    lang('date_synchronize') => lang('date_synchronize_help'),
);

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_requires'] = array(
    'app-network-core', 
    'app-tasks-core', 
    'ntpdate >= 4.2.4p8'
);

$app['core_file_manifest'] = array( 
    'filewatch-date-network.conf'=> array('target' => '/etc/clearsync.d/filewatch-date-network.conf'),
    'app-date.cron' => array(
        'target' => '/etc/cron.d/app-date',
    ),
    'date.conf' => array(
        'target' => '/etc/clearos/date.conf',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
    'timesync' => array(
        'target' => '/usr/sbin/timesync',
        'mode' => '0755',
    ),
);
