<?php
	$route['blog/more'] = 'blog/more';
	$route['blog/more/page/(:num)'] = 'blog/more/page/$1';
	$route['blog/category/(:any)'] = 'blog/category/$1';
	$route['blog/category/(:any)/page/(:num)'] = 'blog/category/$1/page/$2';
	$route['blog/tag/(:any)'] = 'blog/tag/$1';
	$route['blog/search'] = 'blog/search';
	$route['blog/search/(:any)'] = 'blog/search/$1';
	$route['blog/feed'] = 'blog/feed';
	$route['blog/ac_search'] = 'blog/ac_search';
	$route['blog/(:any)/feed'] = 'blog/feed/$1';
	$route['blog/(:num)/(:num)'] = 'blog/month/$1/$2';
	$route['blog/(:num)/(:num)/page/(:num)'] = 'blog/month/$1/$2/page/$3';
	$route['blog/(:num)'] = 'blog/year/$1';
	$route['blog/(:num)/page/(:num)'] = 'blog/year/$1/page/$2';
	$route['blog/(:num)/(:num)/(:any)'] = 'blog/read/$1/$2/$3';
	$route['blog/(:any)'] = 'blog/category/$1';
	$route['blog/(:any)/page/(:num)'] = 'blog/category/$1/page/$2';
?>