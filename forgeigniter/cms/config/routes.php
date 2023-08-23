<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| URI ROUTING
|
| Please see the user guide for complete details:
| https://codeigniter.com/userguide3/general/routing.html
|
*/

// Core Admin and Modules
function load_module_routes($path, &$route) {
    $handle = opendir($path);
    if (!$handle) return;

    while (false !== ($module = readdir($handle))) {
        if (substr($module, 0, 1) == '.') continue;

        $module_path = $path . $module;
        $config_file = $module_path . '/config/' . $module . '_routes.php';
        $admin_file = $module_path . '/controllers/Admin.php';
        $controller_file = $module_path . '/controllers/' . $module . '.php';

        if (file_exists($config_file)) {
            include($config_file);
        }

        if (file_exists($admin_file)) {
            $route['admin/' . $module] = $module . '/admin';
            $route['admin/' . $module . '/(.*)'] = $module . '/admin/$1';
        }

        if (file_exists($controller_file)) {
            $route[$module] = $module;
            $route[$module . '/(.*)'] = $module . '/$1';
        }
    }
}

$module_paths = [
    APPPATH . 'modules/' => '../modules/',
    FCPATH . 'forgeigniter/modules/' => '../../modules/',
];

foreach ($module_paths as $path => $relative_path) {
    load_module_routes($path, $route);
}

$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
