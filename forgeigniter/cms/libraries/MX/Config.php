<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Config class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Config.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.6.4
 *
 **/
class MX_Config extends CI_Config
{
    /**
     * Load Module Config
     * @param string $file 
     * @param bool   $use_sections
     * @param bool   $fail_gracefully
     * @param string $_module
     * @return array|null
     */
    public function load($file = '', $use_sections = false, $fail_gracefully = false, $_module = '')
    {
        if (in_array($file, $this->is_loaded, true)) {
            return $this->item($file);
        }
        
        $_module or $_module = CI::$APP->router->fetch_module();
        [$path, $file] = Modules::find($file, $_module, 'config/');

        if ($path === false) {
            parent::load($file, $use_sections, $fail_gracefully);
            return $this->item($file);
        }

        if ($config = Modules::load_file($file, $path, 'config')) {
            /* reference to the config array */
            $current_config =& $this->config;

            if ($use_sections === true) {
                if (isset($current_config[$file])) {
                    $current_config[$file] = array_merge($current_config[$file], $config);
                } else {
                    $current_config[$file] = $config;
                }
            } else {
                $current_config = array_merge($current_config, $config);
            }

            $this->is_loaded[] = $file;
            unset($config);
            return $this->item($file);
        }
    }
}
