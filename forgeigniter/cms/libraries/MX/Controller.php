<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require_once __DIR__ .'/Base.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library replaces the CodeIgniter Controller class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Controller.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.6.4
 *
 **/
class MX_Controller
{
    public $autoload = array();


    public function __construct()
    {
        $class = str_replace(CI::$APP->config->item('controller_suffix') ?? '', '', get_class($this));
        log_message('debug', $class. ' MX_Controller Initialized');
        Modules::$registry[strtolower($class)] = $this;

        /* copy a loader instance and initialize */
        $this->load = clone load_class('Loader');
        $this->load->initialize($this);

        /* autoload module items */
        $this->load->_autoloader($this->autoload);
    }

    public function __get($class)
    {
        return CI::$APP->$class;
    }
}
