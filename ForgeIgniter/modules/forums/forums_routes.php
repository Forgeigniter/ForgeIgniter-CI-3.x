<?php defined('BASEPATH') OR exit('No direct script access allowed');

	$route['forums/viewforum/(:num)/feed'] = 'forums/topics_feed/$1';
	$route['forums/viewtopic/(:num)/feed'] = 'forums/posts_feed/$1';

?>
