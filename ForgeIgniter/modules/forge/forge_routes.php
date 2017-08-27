<?php

$route['forge'] = 'forge/admin/dashboard';
$route['forge/summary'] = 'forge/admin/dashboard';
$route['forge/summary/permissions'] = 'forge/admin/dashboard/permissions';
$route['admin'] = 'forge/admin/dashboard';
$route['admin/site'] = 'forge/admin/site';
$route['admin/dashboard'] = 'forge/admin/dashboard';
$route['admin/dashboard/(.*)'] = 'forge/admin/dashboard/$1';
$route['admin/login'] = 'forge/admin/login';
$route['admin/login/(.*)'] = 'forge/admin/login/$1';
$route['admin/logout'] = 'forge/admin/logout';
$route['admin/logout/(.*)'] = 'forge/admin/logout/$1';
$route['admin/stats'] = 'forge/admin/stats';
$route['admin/stats/(:num)'] = 'forge/admin/stats/$1';
$route['admin/tracking'] = 'forge/admin/tracking';
$route['admin/tracking_ajax'] = 'forge/admin/tracking_ajax';
$route['admin/activity'] = 'forge/admin/activity';
$route['admin/activity_ajax'] = 'forge/admin/activity_ajax';
$route['admin/backup'] = 'forge/admin/backup';

?>
