<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* load MX core classes */
require_once __DIR__ .'/Lang.php';
require_once __DIR__ .'/Config.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library creates a CI class which allows the use of modules in an application.
 *
 * Install this file as application/third_party/MX/Ci.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.6.4
 *
 **/
class CI
{
    public static $APP;

    public function __construct()
    {

        /* assign the application instance */
        self::$APP = CI_Controller::get_instance();

        global $LANG, $CFG;

        /* re-assign language and config for modules */
        if (! $LANG instanceof MX_Lang) {
            $LANG = new MX_Lang;
        }
        if (! $CFG instanceof MX_Config) {
            $CFG = new MX_Config;
        }
    }
}

// create the application object
new CI;
