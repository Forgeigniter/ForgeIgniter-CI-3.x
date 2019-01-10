<?php defined('BASEPATH') OR exit('No direct script access allowed');

	$route['events/page'] = 'events/index';
	$route['events/page/:num'] = 'events/index/$1';
	$route['events/:num/:num/:num'] = 'events/day/$1/$2/$3';
	$route['events/:num/:num'] = 'events/month/$1/$2';
	$route['events/:num'] = 'events/year/$1';

?>
