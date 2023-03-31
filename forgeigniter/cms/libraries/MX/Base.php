<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* load MX core classes */
require_once __DIR__ . '/Lang.php';
require_once __DIR__ . '/Config.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Controller class and creates an application
 * object allowing use of the HMVC design pattern.
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.6.4
 *
 **/

class CI extends CI_Controller
{
    public static $APP;

    public function __construct()
    {

        // assign the application instance
        self::$APP = $this;

        // re-assign language and config for modules
        global $LANG, $CFG;

        if (! $LANG instanceof MX_Lang) {
            $LANG = new MX_Lang;
        }
        if (! $CFG instanceof MX_Config) {
            $CFG = new MX_Config;
        }

        parent::__construct();

    }
}

// create the application object
new CI;
