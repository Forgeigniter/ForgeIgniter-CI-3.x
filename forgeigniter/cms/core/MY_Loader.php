<?php
defined('BASEPATH') or exit('No direct script access allowed');

// load the MX_Loader class
require_once APPPATH."libraries/MX/Loader.php";

class MY_Loader extends MX_Loader
{
    private $ci_objects = array();

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Magic method to handle dynamic getter and setter for CI objects
     * @param string $method Method name
     * @param array $args Method arguments
     * @return mixed
     */
    public function __call($method, $args)
    {
        $property = strtolower(substr($method, 3));
        $prefix = substr($method, 0, 3);

        if (isset($this->ci_objects[$property]))
        {
            if ($prefix == 'get')
            {
                return $this->ci_objects[$property];
            }
            else if ($prefix == 'set')
            {
                $this->ci_objects[$property] = $args[0];
            }
        }
        else
        {
            parent::__call($method, $args);
        }
    }


    /**
     * Load a module view
     * @param string $view
     * @param array $vars
     * @param bool $return
     * @return mixed
    */
    public function view($view, $vars = [], $return = false)
    {
        list($path, $_view) = Modules::find($view, $this->_module, 'views/');

        if ($path != false) {
            $this->_ci_view_paths = array($path => true) + $this->_ci_view_paths;
            $view = $_view;
        }

        // Prepare view variables
        $prepared_vars = method_exists($this, '_ci_object_to_array') ?
                         $this->_ci_object_to_array($vars) :
                         $this->_ci_prepare_view_vars($vars);

        // Prepare load arguments
        $load_args = [
            '_ci_view' => $view,
            '_ci_vars' => $prepared_vars,
            '_ci_return' => $return
        ];

        // Load the view
        return $this->_ci_load($load_args);

    }

}
