<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_Controller extends MX_Controller {
    public function __construct() {
        parent::__construct();

        // Set the default view path for admin controllers
        $module = $this->router->fetch_module();  // Get current module
        $this->load->set_view_path(APPPATH.'modules/'.$module.'/views/admin/');
    }

}