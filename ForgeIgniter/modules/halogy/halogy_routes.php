<?php

$route['halogy'] = 'halogy/admin/dashboard';
$route['halogy/summary'] = 'halogy/admin/dashboard';
$route['halogy/summary/permissions'] = 'halogy/admin/dashboard/permissions';
$route['admin'] = 'halogy/admin/dashboard';
$route['admin/site'] = 'halogy/admin/site';
$route['admin/dashboard'] = 'halogy/admin/dashboard';
$route['admin/dashboard/(.*)'] = 'halogy/admin/dashboard/$1';
$route['admin/login'] = 'halogy/admin/login';
$route['admin/login/(.*)'] = 'halogy/admin/login/$1';
$route['admin/logout'] = 'halogy/admin/logout';
$route['admin/logout/(.*)'] = 'halogy/admin/logout/$1';
$route['admin/stats'] = 'halogy/admin/stats';
$route['admin/stats/(:num)'] = 'halogy/admin/stats/$1';
$route['admin/tracking'] = 'halogy/admin/tracking';
$route['admin/tracking_ajax'] = 'halogy/admin/tracking_ajax';
$route['admin/activity'] = 'halogy/admin/activity';
$route['admin/activity_ajax'] = 'halogy/admin/activity_ajax';
$route['admin/backup'] = 'halogy/admin/backup';

?>