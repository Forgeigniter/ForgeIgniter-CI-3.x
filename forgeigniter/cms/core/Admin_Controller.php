<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends MX_Controller {
    public function __construct() {
        parent::__construct();

        // Set the default view path for admin controllers

        $module = $this->router->fetch_module();  // Get current module

        // Since we have multiple module locations
        $module_path = NULL;
        $module_locations = $this->config->item('modules_locations');
        foreach ($module_locations as $location) {
            if (is_dir($location . $module)) {
                $module_path = $location . $module;
                break;
            }
        }

        if ($module_path) {
            $this->load->set_view_path($module_path . '/views/admin/');
        } else {
            log_message('error', 'Admin Module view path not found for: ' . $module);
        }

    }

}