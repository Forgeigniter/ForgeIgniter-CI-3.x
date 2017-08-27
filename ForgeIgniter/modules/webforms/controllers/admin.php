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

// ------------------------------------------------------------------------

class Admin extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';			// path to includes for header and footer
	var $redirect = '/admin/webforms/tickets';		// default redirect
	var $objectID = 'ticketID';						// default unique ID	
	var $permissions = array();
	
	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		
		// get permissions and redirect if they don't have access to this module
		if (!$this->permission->permissions)
		{
			redirect('/admin/dashboard/permissions');
		}
		if (!in_array($this->uri->segment(2), $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load libs
		$this->load->model('tickets_model', 'tickets');
	}
	
	function index()
	{
		redirect($this->redirect);
	}

	function viewall($status = '')
	{
		// check permissions for this page
		if (!in_array('webforms_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
	
		// grab data and display
		$output = $this->core->viewall('web_forms');

		$this->load->view($this->includes_path.'/header');
		$this->load->view('viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_form()
	{
		// check permissions for this page
		if (!in_array('webforms_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}	

		// required
		$this->core->required = array(
			'formName' => array('label' => 'Form Name', 'rules' => 'required|unique[web_forms.formName]'),
		);

		// get values
		$output['data'] = $this->core->get_values('web_forms');
		$output['groups'] = $this->permission->get_groups('normal');

		// deal with post
		if (count($_POST))
		{
			// set stuff
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['formRef'] = trim(strtolower(url_title($this->input->post('formName'))));

			// set action
			if (!$this->input->post('account'))
			{
				$this->core->set['groupID'] = '';
			}
					
			// update
			if ($this->core->update('web_forms'))
			{								
				// where to redirect to
				redirect('/admin/webforms/viewall');
			}			
		}		
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('add_form',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_form($formID)
	{
		// check permissions for this page
		if (!in_array('webforms_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// set object ID
		$objectID = array('formID' => $formID);		

		// required
		$this->core->required = array(
			'formName' => array('label' => 'Form Name', 'rules' => 'required|unique[web_forms.formName]'),
		);

				
		// get values
		$output['data'] = $this->core->get_values('web_forms', $objectID);	
		$output['groups'] = $this->permission->get_groups('normal');		

		// deal with post
		if (count($_POST))
		{
			// set form ref
			$this->core->set['formRef'] = trim(strtolower(url_title($this->input->post('formName'))));
			
			// set action
			if (!$this->input->post('account'))
			{
				$this->core->set['groupID'] = '';
			}
			
			// update
			if ($this->core->update('web_forms', $objectID))
			{
				// set message
				$output['message'] = '<p>Your changes have been saved.</p>';
			}
		}
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('edit_form',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_form($objectID)
	{
		// check permissions for this page
		if (!in_array('webforms_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}	
				
		if ($this->core->soft_delete('web_forms', array('formID' => $objectID)))
		{		
			// where to redirect to
			redirect('/admin/webforms/viewall');
		}
	}	
	
	function tickets($status = '')
	{	
		// status	
		if ($status == 'open')
		{
			$where['closed'] = 0;
			$status = 'Open';
			
		}
		elseif ($status == 'closed')
		{
			$where['closed'] = 1;
			$status = 'Closed';
		}
		elseif ($webform = $this->tickets->get_web_form($status))
		{
			$where['formName'] = $webform['formName'];
			$status = $webform['formName'];
		}
		else
		{
			$where = FALSE;
			$status = '';
		}
	
		// grab data and display
		$output = $this->core->viewall('tickets', $where, array('dateCreated', 'desc'));

		// get web forms
		$output['webforms'] = $this->tickets->get_all_web_forms();

		// output status
		$output['status'] = $status;

		$this->load->view($this->includes_path.'/header');
		$this->load->view('tickets',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function view_ticket($ticketID)
	{
		// set object ID
		$objectID = array($this->objectID => $ticketID);		

		// get values
		$output['data'] = $this->core->get_values('tickets', $objectID);

		// set date
		$this->core->set['dateModified'] = date("Y-m-d H:i:s");

		// update
		if ($this->core->update('tickets', $objectID) && count($_POST))
		{
			// where to redirect to
			redirect($this->redirect);
		}

		// set view flag
		if (!$output['data']['viewed'])
		{
			$this->tickets->view_ticket($ticketID);
		}
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('view_ticket',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_ticket($objectID)
	{
		// check permissions for this page
		if (!in_array('webforms_tickets', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		if ($this->core->soft_delete('tickets', array($this->objectID => $objectID)))
		{
			// where to redirect to
			redirect($this->redirect);
		}
	}
	
}