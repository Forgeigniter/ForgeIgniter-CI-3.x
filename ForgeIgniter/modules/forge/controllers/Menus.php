<?php

class Menus extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check Persmissions

        // Load Libraries
        $this->load->library('forge/menu_manager');
    }

    public $includes_path = '/includes/admin';

    public function index()
    {
        
        $output['menus'] = $this->menu_manager->get_menus();
        $output['menu_items'] = array();

        foreach ($output['menus'] as $menu) {
            $output['menu_items'][$menu['menu_id']] = $this->menu_manager->get_menu_items($menu['menu_id']);
        }        

        $this->load->view($this->includes_path.'/header');
        $this->load->view('menu/admin-menus', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    // Add Menu
    public function add_menu() {
        if ($this->input->post()) {
            $menu_data = array(
                'menu_name' => $this->input->post('menu_name')
            );
    
            $result = $this->menu_manager->add_menu($menu_data);
    
            if ($result) {
                $this->session->set_flashdata('message', 'Menu added successfully');
            } else {
                $this->session->set_flashdata('error', 'Failed create a menu');
            }
    
            redirect('admin/menus');
        }
    
        $this->load->view($this->includes_path.'/header');
        $this->load->view('menu/admin-add-menu');
        $this->load->view($this->includes_path.'/footer');
    }

    // Edit Menu
    public function edit_menu($menu_id = null) {

        if ($menu_id == null) {
            redirect('admin/menus');
        }

        if ($this->input->post()) {
            $menu_data = array(
                'menu_name' => $this->input->post('menu_name')
            );
    
            $this->menu_manager->update_menu($menu_id, $menu_data);
            redirect('admin/menus');
        }
    
        $output['menu'] = $this->menu_manager->get_menu($menu_id);
    
        $this->load->view($this->includes_path.'/header');
        $this->load->view('menu/admin-edit-menu', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    // Delete Menu
    public function delete_menu($menu_id = null) {
        if ($menu_id != null && $this->menu_manager->delete_menu($menu_id)) {
            // Menu was deleted successfully
            $this->session->set_flashdata('message', 'Menu deleted successfully');
        } else {
            // Menu deletion failed or menu_id is null
            $this->session->set_flashdata('error', 'Menu deletion failed');
        }
        redirect('admin/menus');
    }

    // Add Menu Item
    public function add_menu_item() {
        if ($this->input->post()) {
            $menu_item_data = array(
                'link_name' => $this->input->post('link_name'),
                'link_uri' => $this->input->post('link_uri'),
                'menu_id' => $this->input->post('menu_id')
            );
    
            $inserted_data = $this->menu_manager->add_menu_item($menu_item_data);

            if ($inserted_data) {
                $this->session->set_flashdata('message', 'Menu item added successfully');
            } else {
                $this->session->set_flashdata('error', 'Menu item addition failed');
            }
            
            redirect('admin/menus');
        }
    
        $this->load->view($this->includes_path.'/header');
        $this->load->view('menu/admin-add-menu-item');
        $this->load->view($this->includes_path.'/footer');
    }
    
}