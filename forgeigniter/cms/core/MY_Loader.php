<?php
defined('BASEPATH') or exit('No direct script access allowed');

// load the MX_Loader class
require_once APPPATH."libraries/MX/Loader.php";

class MY_Loader extends MX_Loader
{

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
