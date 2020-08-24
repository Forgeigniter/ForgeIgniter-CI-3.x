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
    public $redirect = '/admin/events/viewall';				// default redirect
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

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        //  load models and libs
        $this->load->library('tags');
        $this->load->model('events_model', 'events');
    }

    public function index()
    {
        redirect($this->redirect);
    }

    public function viewall()
    {
        // default where
        $where = array('siteID' => $this->siteID, 'deleted' => 0);

        // where event has not passed
        //$where['eventDate <'] = date("Y-m-d H:i:s", strtotime('-2 days', time()));

        // grab data and display
        $output = $this->core->viewall('events', $where, array('dateCreated', 'desc'));

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/viewall', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_event()
    {
        // check permissions for this page
        if (!in_array('events_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required
        $this->core->required = array(
            'eventTitle' => array('label' => 'Event title', 'rules' => 'required|trim'),
            'description' => 'Description'
        );

        // get values
        $output['data'] = $this->core->get_values('events');

        if (count($_POST)) {
            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['tags'] = trim(strtolower($this->input->post('tags')));
            $this->core->set['userID'] = $this->session->userdata('userID');
            $this->core->set['eventDate'] = date("Y-m-d H:i:s", strtotime($this->input->post('eventDate').' 12AM'));
            $this->core->set['eventEnd'] = ($this->input->post('eventEnd')) ? date("Y-m-d H:i:s", strtotime($this->input->post('eventEnd').' 11.59PM')) : '';

            // update
            if ($this->core->update('events')) {
                $eventID = $this->db->insert_id();

                // update tags
                $this->events->update_tags($eventID, $this->input->post('tags'));

                // where to redirect to
                redirect($this->redirect);
            }
        }

        // set default date
        $output['data']['eventDate'] = ($this->input->post('eventDate')) ? $this->input->post('eventDate') : dateFmt(date("Y-m-d H:i:s"), 'd M Y');

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/add_event', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_event($eventID)
    {
        // check permissions for this page
        if (!in_array('events_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // set object ID
        $objectID = array('eventID' => $eventID);

        // required
        $this->core->required = array(
            'eventTitle' => array('label' => 'Event title', 'rules' => 'required|trim'),
            'description' => 'Description'
        );

        // get values
        $output['data'] = $this->core->get_values('events', $objectID);

        if (count($_POST)) {
            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['tags'] = trim(strtolower($this->input->post('tags')));
            $this->core->set['eventDate'] = date("Y-m-d H:i:s", strtotime($this->input->post('eventDate').' 12AM'));
            $this->core->set['eventEnd'] = ($this->input->post('eventEnd')) ? date("Y-m-d H:i:s", strtotime($this->input->post('eventEnd').' 11.59PM')) : '';

            // update
            if ($this->core->update('events', $objectID)) {
                // update tags
                $this->events->update_tags($eventID, $this->input->post('tags'));

                // set success message
                $this->session->set_flashdata('success', true);

                // where to redirect to
                redirect($this->uri->uri_string());
            }
        }

        // set message
        if ($this->session->flashdata('success')) {
            $output['message'] = '<p>Your changes were saved.</p>';
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/edit_event', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function delete_event($objectID)
    {
        // check permissions for this page
        if (!in_array('events_delete', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->core->soft_delete('events', array('eventID' => $objectID))) {
            // where to redirect to
            redirect($this->redirect);
        }
    }

    public function preview()
    {
        // get parsed body
        $html = $this->template->parse_body($this->input->post('body'));

        // filter for scripts
        $html = preg_replace('/<script(.*)<\/script>/is', '<em>This block contained scripts, please refresh page.</em>', $html);

        // output
        $this->output->set_output($html);
    }
}
