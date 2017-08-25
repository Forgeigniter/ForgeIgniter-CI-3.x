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
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/wiki/viewall';			// default redirect
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
		
		//  load models and libs
		$this->load->model('wiki_model', 'wiki');
	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
	function viewall()
	{
		// grab data and display
		$output = $this->core->viewall('wiki', NULL, array('dateCreated', 'desc'));

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function changes()
	{
		// grab data and display
		$output['changes'] = $this->wiki->get_recent_changes();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/changes',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_page()
	{
		// required
		$this->core->required = array(
			'pageName' => array('label' => 'Title', 'rules' => 'required|trim'),
			'uri' => array('label' => 'URI', 'rules' => 'unique[wiki.uri]|trim'),
		);	

		// get values
		$output['data'] = $this->core->get_values('wiki');

		// get categories
		$output['categories'] = $this->wiki->get_categories();

		// handle post
		if (count($_POST))
		{	
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			// update
			if ($this->core->update('wiki'))
			{							
				// where to redirect to
				redirect($this->redirect);
			}
		}
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_page',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_page($pageID)
	{
		// required
		$this->core->required = array(
			'pageName' => array('label' => 'Title', 'rules' => 'required|trim'),
			'uri' => array('label' => 'URI', 'rules' => 'unique[wiki.uri]|trim'),
		);	

		// set object ID
		$objectID = array('pageID' => $pageID);

		// get values
		$output['data'] = $this->core->get_values('wiki', $objectID);

		// get categories
		$output['categories'] = $this->wiki->get_categories();

		// handle post
		if (count($_POST))
		{	
			// set date
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
			
			// update
			if ($this->core->update('wiki', $objectID))
			{							
				// where to redirect to
				redirect($this->redirect);
			}
		}
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_page',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_page($objectID)
	{
		// check permissions for this page
		if (!in_array('wiki_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->soft_delete('wiki', array('pageID' => $objectID)));
		{	
			// where to redirect to
			redirect($this->redirect);
		}
	}

	function categories()
	{
		// check permissions for this page
		if (!in_array('wiki_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
			
		// get parents
		if ($parents = $this->wiki->get_category_parents())
		{
			// get children
			foreach($parents as $parent)
			{
				$children[$parent['catID']] = $this->wiki->get_category_children($parent['catID']);
			}
		}

		// send data to view
		$output['parents'] = @$parents;
		$output['children'] = @$children;

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/categories',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_cat()
	{
		// check permissions for this page
		if (!in_array('wiki_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'catName' => 'Title',
		);

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// set stuff
				$this->core->set['dateModified'] = date("Y-m-d H:i:s");
				
				// update
				if ($this->core->update('wiki_cats'))
				{
					redirect('/admin/wiki/categories');
				}
			}
		}

		// get parents
		$output['parents'] = $this->wiki->get_category_parents();		

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/category_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_cat($catID)
	{
		// check permissions for this page
		if (!in_array('wiki_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'catName' => 'Title',
		);

		// where
		$objectID = array('catID' => $catID);

		// get values from version
		$row = $this->wiki->get_category($catID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// set stuff
				$this->core->set['dateModified'] = date("Y-m-d H:i:s");
				
				// update
				if ($this->core->update('wiki_cats', $objectID))
				{
					redirect('/admin/wiki/categories');
				}
			}
		}

		// get parents
		$output['parents'] = $this->wiki->get_category_parents();		

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/category_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_cat($catID)
	{
		// check permissions for this page
		if (!in_array('wiki_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('catID' => $catID);	
		
		if ($this->core->soft_delete('wiki_cats', $objectID))
		{
			// delete sub categories
			$objectID = array('parentID' => $catID);
			
			$this->core->soft_delete('wiki_cats', $objectID);
			
			// where to redirect to
			redirect('/admin/wiki/categories');
		}		
	}

	function order($field = '')
	{
		$this->core->order(key($_POST), $field);
	}
		
}