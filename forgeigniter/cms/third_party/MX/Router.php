<?php
defined('BASEPATH') or exit('No direct script access allowed');

// load the MX core module class
require_once __DIR__ .'/Modules.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter router class.
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @license     MIT - See included LICENSE.txt 
 * @version 	5.6.4
 *
 **/
class MX_Router extends CI_Router
{
    public $module;
    private $located = 0;

    /**
     * Get current module name
     * @return string
     */
    public function fetch_module(): string
    {
        return $this->module ?? ''; // or empty if not set
    }

    protected function _set_request($segments = array())
    {
        if ($this->translate_uri_dashes === true) {
            foreach (range(0, 2) as $v) {
                isset($segments[$v]) && $segments[$v] = str_replace('-', '_', $segments[$v]);
            }
        }

        $segments = $this->locate($segments);

        if ($this->located === -1) {
            $this->_set_404override_controller();
            return;
        }

        if (empty($segments)) {
            $this->_set_default_controller();
            return;
        }

        $this->set_class($segments[0]);

        if (isset($segments[1])) {
            $this->set_method($segments[1]);
        } else {
            $segments[1] = 'index';
        }

        array_unshift($segments, null);
        unset($segments[0]);
        $this->uri->rsegments = $segments;
    }

    protected function _set_404override_controller()
    {
        $this->_set_module_path($this->routes['404_override']);
    }

    protected function _set_default_controller()
    {
        if (empty($this->directory)) {
            /* set the default controller module path */
            $this->_set_module_path($this->default_controller);
        }

        parent::_set_default_controller();

        if (empty($this->class)) {
            $this->_set_404override_controller();
        }
    }

    /**
     *  Locate the controller
     *  @param array $segments of URI
     *  @return ?array
     */
    public function locate($segments)
    {
        $this->directory = null;
        $this->located = 0;
        $ext = $this->config->item('controller_suffix') . '.php';

        /* use module route if available */
        if (isset($segments[0]) && $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
            $segments = $routes;
        }

        /* get the segments array elements */
        [$module, $directory, $controller] = array_pad($segments, 3, null);

        /* check modules */
        foreach (Modules::$locations as $location => $offset) {
            /* module exists? */
            if (is_dir($source = $location.$module.'/controllers/')) {
                $this->module = $module;
                $this->directory = $offset.$module.'/controllers/';

                /* module sub-controller exists? */
                if ($directory) {
                    /* module sub-directory exists? */
                    if (is_dir($source.$directory.'/')) {
                        $source .= $directory.'/';
                        $this->directory .= $directory.'/';

                        /* module sub-directory controller exists? */
                        if ($controller) {
                            if (is_file($source.ucfirst($controller).$ext)) {
                                $this->located = 3;
                                return array_slice($segments, 2);
                            }
                            $this->located = -1;
                        }
                        $this->located = -1;
                    } elseif (is_file($source.ucfirst($directory).$ext)) {
                        $this->located = 2;
                        return array_slice($segments, 1);
                    } else {
                        $this->located = -1;
                    }
                }

                /* module controller exists? */
                if (is_file($source.ucfirst($module).$ext)) {
                    $this->located = 1;
                    return $segments;
                }
            }
        }

        if (! empty($this->directory)) {
            return array();
        }

        /* application sub-directory controller exists? */
        if ($directory) {
            if (is_file(APPPATH.'controllers/'.$module.'/'.ucfirst($directory).$ext)) {
                $this->directory = $module.'/';
                return array_slice($segments, 1);
            }

            /* application sub-sub-directory controller exists? */
            if ($controller && is_file(APPPATH . 'controllers/' . $module . '/' . $directory . '/' . ucfirst($controller) . $ext)) {
                $this->directory = $module.'/'.$directory.'/';
                return array_slice($segments, 2);
            }
        }

        /* application controllers sub-directory exists? */
        if (is_dir(APPPATH.'controllers/'.$module.'/')) {
            $this->directory = $module.'/';
            return array_slice($segments, 1);
        }

        /* application controller exists? */
        if (is_file(APPPATH.'controllers/'.ucfirst($module).$ext)) {
            return $segments;
        }

        $this->located = -1;
    }

    /**
     * Set Module / Controller Path
     * @param string $_route
     * @return void
     */
    protected function _set_module_path(&$_route)
    {
        if (! empty($_route)) {
            // Are module/directory/controller/method segments being specified?
            $sgs = sscanf($_route, '%[^/]/%[^/]/%[^/]/%s', $module, $directory, $class, $method);

            // set the module/controller directory location if found
            if ($this->locate(array($module, $directory, $class))) {
                //reset to class/method
                switch ($sgs) {
                    case 1:	$_route = $module.'/index';
                        break;
                    case 2: $_route = ($this->located < 2) ? $module.'/'.$directory : $directory.'/index';
                        break;
                    case 3: $_route = ($this->located === 2) ? $directory.'/'.$class : $class.'/index';
                        break;
                    case 4: $_route = ($this->located === 3) ? $class.'/'.$method : $method.'/index';
                        break;
                }
            }
        }
    }


    /**
     * Set the controller class
     * @param $class
     * @return void
     */
    public function set_class($class)
    {
        $suffix = $this->config->item('controller_suffix');
        if ($suffix && strpos($class, $suffix) === false) {
            $class .= $suffix;
        }
        parent::set_class($class);
    }
}
