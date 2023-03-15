<?php

class Menus_model extends CI_Model 
{

    public function __construct() {
        parent::__construct();
    }

    public function get_menus() {
        $query = $this->db->get('admin_menus');
        return $query->result_array();
    }

    public function get_menu($menu_id) {
        $this->db->where('menu_id', $menu_id);
        $query = $this->db->get('admin_menus');
        return $query->row_array();
    }

    public function get_menu_items($menu_id) {
        $this->db->where('menu_id', $menu_id);
        $query = $this->db->get('admin_menu_links');
        return $query->result_array();
    }

    public function add_menu($menu_data) {
        return $this->db->insert('admin_menus', $menu_data);
    }

    public function update_menu($menu_id, $menu_data) {
        $this->db->where('menu_id', $menu_id);
        return $this->db->update('admin_menus', $menu_data);
    }

    public function delete_menu($menu_id) {
        $this->db->where('menu_id', $menu_id);
        return $this->db->delete('admin_menus');
    }
    
    public function add_menu_item($menu_item_data) {
        $this->db->insert('admin_menu_links', $menu_item_data);
        return $this->db->insert_id();
    }
    

}

