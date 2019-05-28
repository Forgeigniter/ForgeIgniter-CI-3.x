<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|  Setting: URL Paths
*/

if (! defined('BASE_URL')) {
    $_base_path = $_SERVER['HTTP_HOST'].str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

    if (! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' or $_SERVER['SERVER_PORT'] == 443) {
        define('BASE_URL', "https://".$_base_path);
    } else {
        define('BASE_URL', "http://".$_base_path);
    }
}

define('FULL_URL', "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

/*
|  Setting: Common Paths
*/

const PATH = [
    'adminLogin' => BASE_URL . 'admin/login',
    'static' => BASE_URL . 'static/',
    'theme' => BASE_URL . 'static/themes/'
];
