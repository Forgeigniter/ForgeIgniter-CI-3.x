<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Menu_manager {

    public $CI;    // CI instance
    public $siteID;
    public $table = 'admin_menu';

    function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('forge/Menus_model');

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }
    }

    public function get_menus() {
        return $this->CI->Menus_model->get_menus();
    }

    public function get_menu($menu_id) {
        return $this->CI->Menus_model->get_menu($menu_id);
    }

    public function get_menu_items($menu_id) {
        return $this->CI->Menus_model->get_menu_items($menu_id);
    }

    public function add_menu($menu_data) {
        return $this->CI->Menus_model->add_menu($menu_data);
    }

    public function update_menu($menu_id, $menu_data) {
        $this->CI->Menus_model->update_menu($menu_id, $menu_data);
    }

    public function delete_menu($menu_id) {
        return $this->CI->Menus_model->delete_menu($menu_id);
    }

    // Menu Items
    public function add_menu_item($menu_item_data) {
        return $this->CI->Menus_model->add_menu_item($menu_item_data);
    }

}
