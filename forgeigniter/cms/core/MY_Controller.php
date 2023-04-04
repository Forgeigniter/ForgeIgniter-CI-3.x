<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MX_Controller extends CI_Controller {

    protected $load_instance;

    public function __construct()
    {
        parent::__construct();
        $this->load_instance =& load_class('Loader', 'core');
    }

    public function get_load_instance() {
        return $this->load_instance;
    }

    public function set_load_instance($load_instance) {
        $this->load_instance = $load_instance;
    }
}
