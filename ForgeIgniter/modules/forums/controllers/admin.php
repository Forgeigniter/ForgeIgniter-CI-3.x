<?php
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2015, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Admin extends MX_Controller
{

    // set defaults
    public $includes_path = '/includes/admin';				// path to includes for header and footer
    public $redirect = '/admin/forums/forums';				// default redirect
    public $permissions = array();

    public function __construct()
    {
        parent::__construct();

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_admin')) {
            redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // get permissions and redirect if they don't have access to this module
        if (!$this->permission->permissions) {
            redirect('/admin/dashboard/permissions');
        }
        if (!in_array($this->uri->segment(2), $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        //  load models and libs
        $this->load->model('forums_model', 'forums');
        $this->load->library('tags');

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }
    }

    public function index()
    {
        redirect($this->redirect);
    }

    public function forums()
    {
        // grab data and display
        $output = $this->core->viewall('forums');

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/forums', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_forum()
    {
        // check permissions for this page
        if (!in_array('forums_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // get values
        $output['data'] = $this->core->get_values('forums');
        $output['groups'] = $this->permission->get_groups();

        // get categories
        $output['categories'] = $this->forums->get_categories();

        if (count($_POST)) {
            // required
            $this->core->required = array(
                'forumName' => array('label' => 'Forum Name', 'rules' => 'required|trim'),
            );

            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

            // update
            if ($this->core->update('forums')) {
                $forumID = $this->db->insert_id();

                // where to redirect to
                redirect($this->redirect);
            }
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/add_forum', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_forum($forumID)
    {
        // check permissions for this page
        if (!in_array('forums_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // set object ID
        $objectID = array('forumID' => $forumID);

        // get values
        $output['data'] = $this->core->get_values('forums', $objectID);
        $output['groups'] = $this->permission->get_groups();

        // get categories
        $output['categories'] = $this->forums->get_categories();

        if (count($_POST)) {
            // required
            $this->core->required = array(
                'forumName' => array('label' => 'forum Name', 'rules' => 'required|trim'),
            );

            // set date
            $this->core->set['dateModified'] = date("Y-m-d H:i:s");

            // update
            if ($this->core->update('forums', $objectID)) {
                // where to redirect to
                redirect($this->redirect);
            }
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/edit_forum', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function delete_forum($objectID)
    {
        // check permissions for this page
        if (!in_array('forums_delete', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->core->soft_delete('forums', array('forumID' => $objectID))) {
            // where to redirect to
            redirect($this->redirect);
        }
    }

    public function categories()
    {
        // check permissions for this page
        if (!in_array('forums_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array('catName' => 'Category name');

        // set date
        $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

        // get values
        $output = $this->core->get_values('forums_cats');

        // update
        if ($this->core->update('forums_cats') && count($_POST)) {
            // where to redirect to
            redirect('/admin/forums/categories');
        }

        $output['categories'] = $this->forums->get_categories();

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/categories', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_cat()
    {
        // check permissions for this page
        if (!in_array('forums_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // go through post and edit each list item
        $listArray = $this->core->get_post();
        if (count($listArray)) {
            foreach ($listArray as $ID => $value) {
                if ($ID != '' && sizeof($value) > 0) {
                    // set object ID
                    $objectID = array('catID' => $ID);
                    $this->core->set['catName'] = $value['catName'];
                    $this->core->update('forums_cats', $objectID);
                }
            }
        }

        // where to redirect to
        redirect('/admin/forums/categories');
    }

    public function delete_cat($catID)
    {
        // check permissions for this page
        if (!in_array('forums_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('catID' => $catID);

        if ($this->core->soft_delete('forums_cats', $objectID)) {
            // where to redirect to
            redirect('/admin/forums/categories');
        }
    }

    public function order($field = '')
    {
        $this->core->order(key($_POST), $field);
    }
}
