<?php
defined('BASEPATH') or exit('No direct script access allowed');

    $route['forums'] = 'forums';

    $route['forums/viewforum/(:num)'] = 'forums/viewforum/$1';
    $route['forums/addtopic/(:num)'] = 'forums/addtopic/$1';
    $route['forums/addreply/(:num)'] = 'forums/addreply/$1';
    $route['forums/viewtopic/(:num)'] = 'forums/viewtopic/$1';
    // RSS
    $route['forums/viewforum/(:num)/feed'] = 'forums/topics_feed/$1';
    $route['forums/viewtopic/(:num)/feed'] = 'forums/posts_feed/$1';
