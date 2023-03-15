<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Dashboard
$route['admin'] = 'forge/admin/dashboard';
$route['admin/dashboard'] = 'forge/admin/dashboard';
$route['admin/dashboard/(.*)'] = 'forge/admin/dashboard/$1';
$route['forge/summary'] = 'forge/admin/dashboard';
$route['forge/summary/permissions'] = 'forge/admin/dashboard/permissions';
// Sites
$route['admin/site'] = 'forge/admin/site';
$route['admin/sites'] = 'forge/sites';
// Admin Menu
$route['admin/menus'] = 'forge/menus';
$route['admin/menus/add-menu'] = 'forge/menus/add_menu';
$route['admin/menus/edit-menu'] = 'forge/menus/edit_menu';
$route['admin/menus/edit-menu/(:num)'] = 'forge/menus/edit_menu/$1';
$route['admin/menus/delete-menu/(:num)'] = 'forge/menus/delete_menu/$1';
$route['admin/menus/add-menu-item'] = 'forge/menus/add_menu_item';
// Login & Logout
$route['admin/login'] = 'forge/admin/login';
$route['admin/login/(.*)'] = 'forge/admin/login/$1';
$route['admin/logout'] = 'forge/admin/logout';
$route['admin/logout/(.*)'] = 'forge/admin/logout/$1';
// Tracking & Statistics
$route['admin/stats'] = 'forge/admin/stats';
$route['admin/stats/(:num)'] = 'forge/admin/stats/$1';
$route['admin/tracking'] = 'forge/admin/tracking';
$route['admin/tracking_ajax'] = 'forge/admin/tracking_ajax';
$route['admin/activity'] = 'forge/admin/activity';
$route['admin/activity_ajax'] = 'forge/admin/activity_ajax';
// Backups
$route['admin/backup'] = 'forge/admin/backup';
