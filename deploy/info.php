<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'date';
$app['version'] = '5.9.9.3';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('date_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('date_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_settings');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['date']['tooltip'] = lang('date_app_tooltip');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_requires'] = array(
    'app-network-core', 
    'app-tasks-core', 
    'ntpdate >= 4.2.4p8'
);

$app['core_file_manifest'] = array( 
   'app-date.cron' => array(
        'target' => '/etc/cron.d/app-date',
        'mode' => '0644',
        'owner' => 'root',
        'group' => 'root',
    ),

   'timesync' => array(
        'target' => '/usr/sbin/timesync',
        'mode' => '0755',
        'owner' => 'root',
        'group' => 'root',
    ),
);
