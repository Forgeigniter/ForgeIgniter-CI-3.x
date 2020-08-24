<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// wiki
$route['wiki'] = 'wiki';
$route['wiki/pages'] = 'wiki/pages/';
$route['wiki/pages/(:any)'] = 'wiki/pages/$1';
$route['wiki/pages/(:any)/(.*)'] = 'wiki/pages/$1/$2';
$route['wiki/pages/(:any)/(.*)/(.*)'] = 'wiki/pages/$1/$2/$3';
$route['wiki/search'] = 'wiki/search';
$route['wiki/search/(:any)'] = 'wiki/search/$1';
$route['wiki/edit/(:any)'] = 'wiki/edit/$1';
$route['wiki/revert/(:any)/(:any)'] = 'wiki/revert/$1/$2';
$route['wiki/(:any)'] = 'wiki/index/$1';
