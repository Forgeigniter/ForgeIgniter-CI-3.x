<?php

	// users
	$route['users'] = 'community/users';
	$route['users/(.*)'] = 'community/users/$1';
	$route['users/(.*)/(.*)'] = 'community/users/$1/$2';

	// specific message redirects
	$route['messages/page'] = 'community/messages/index/page';
	$route['messages/page/(:num)'] = 'community/messages/index/page/$1';

	// messages
	$route['messages'] = 'community/messages';
	$route['messages/(.*)'] = 'community/messages/$1';
	$route['messages/(.*)/(.*)'] = 'community/messages/$1/$2';
	$route['messages/(.*)/(.*)/(.*)'] = 'community/messages/$1/$2/$3';

?>
