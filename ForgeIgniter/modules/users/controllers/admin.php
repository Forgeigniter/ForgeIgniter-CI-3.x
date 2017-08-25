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
	var $table = 'users';						// table to update
	var $includes_path = '/includes/admin';		// path to includes for header and footer
	var $redirect = '/admin/users/viewall';		// default redirect
	var $objectID = 'userID';					// default unique ID	
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

		// get site permissions
		$this->permission->sitePermissions = $this->permission->get_group_permissions($this->site->config['groupID']);
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		//  load models and libs
		$this->load->model('users_model', 'users');
	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
	function viewall()
	{
		// check permissions for this page
		if (!in_array('users', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// if search
		$where = '';
		if (count($_POST) && ($query = $this->input->post('searchbox')))
		{
			$name = @preg_split('/ /', $query);
			if (count($name) > 1)
			{
				$firstName = $name[0];
				$lastName = $name[1];
				
				$where = '(email LIKE "%'.$this->db->escape_like_str($query).'%" OR firstName LIKE "%'.$this->db->escape_like_str($firstName).'%" AND lastName LIKE "%'.$this->db->escape_like_str($lastName).'%")';
			}
			else
			{
				$where = '(email LIKE "%'.$this->db->escape_like_str($query).'%" OR firstName LIKE "%'.$this->db->escape_like_str($query).'%" OR lastName LIKE "%'.$this->db->escape_like_str($query).'%")';
			}
		}

		// output users
		$output = $this->core->viewall($this->table, $where);

		// get admin groups
		if ($adminGroups = $this->permission->get_groups('admin'))
		{
			foreach($adminGroups as $group)
			{
				$output['adminGroups'][] = $group['groupID'];
			}
		}

		// get normal groups
		if ($normalGroups = $this->permission->get_groups('normal'))
		{
			foreach($normalGroups as $group)
			{
				$output['normalGroups'][] = $group['groupID'];
			}
		}
		
		// get all groups
		if ($userGroups = $this->permission->get_groups())
		{
			foreach($userGroups as $group)
			{
				$output['userGroups'][$group['groupID']] = $group['groupName'];
			}
		}

		$this->load->view($this->includes_path.'/header');
		$this->load->view('viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add()
	{
		// check permissions for this page
		if (!in_array('users_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'username' => array('label' => 'Username', 'rules' => 'really_unique[users.username]|trim'),
			'email' => array('label' => 'Email', 'rules' => 'valid_email|unique[users.email]|trim'),
			'firstName' => array('label' => 'First name', 'rules' => 'trim|ucfirst'),
			'lastName' => array('label' => 'Last name', 'rules' => 'trim|ucfirst')
		);

		// get values
		$output['data'] = $this->core->get_values($this->table);
		$output['groups'] = $this->permission->get_groups();		

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");

		// check groupID is not being overridden
		if (($this->input->post('groupID') && @!in_array('users_groups', $this->permission->permissions)) || ($this->input->post('groupID') < 0 && $this->session->userdata('groupID') >= 0))
		{
			redirect('/admin/dashboard/permissions');
			die();
		}

		// check resellerID is not being overridden
		if ($this->input->post('resellerID') && !$this->session->userdata('resellerID'))
		{
			redirect('/admin/dashboard/permissions');
			die();
		}

		// set siteID
		if ($this->input->post('siteID') && ($this->session->userdata('groupID') == 1 || $this->session->userdata('groupID') == 2))
		{
			$this->core->set['siteID'] = $this->input->post('siteID');
		}
		else
		{
			$this->core->set['siteID'] = $this->siteID;
		}

		// update
		if ($this->core->update($this->table) && count($_POST))
		{
			// where to redirect to
			redirect($this->redirect);
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('add', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit($userID)
	{
		// check permissions for this page
		if (!in_array('users_edit', $this->permission->permissions) && $userID != $this->session->userdata('userID'))
		{
			redirect('/admin/dashboard/permissions');
		}

		// check this is a valid user
		if (!$user = $this->users->get_user($userID))
		{
			show_error('Not a valid user.');
		}

		// check they are not trying to edit a superuser
		if ($user['groupID'] < 0 && $this->session->userdata('groupID') >= 0)
		{
			show_error('You do have permission to edit this user.');
		}
		
		// set object ID
		$objectID = array($this->objectID => $userID);		

		// required
		$this->core->required = array(
			'username' => array('label' => 'Username', 'rules' => 'really_unique[users.username]|trim'),
			'email' => array('label' => 'Email', 'rules' => 'valid_email|unique[users.email]|trim'),
			'firstName' => array('label' => 'First name', 'rules' => 'trim|ucfirst'),
			'lastName' => array('label' => 'Last name', 'rules' => 'trim|ucfirst')
		);

		// get values
		$output['data'] = $this->core->get_values($this->table, $objectID);
		$output['groups'] = $this->permission->get_groups();			

		// deal with post
		if (count($_POST))
		{
			// set date
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
	
			// check groupID is not being overridden
			if (($this->input->post('groupID') && @!in_array('users_groups', $this->permission->permissions)) || ($this->input->post('groupID') < 0 && $this->session->userdata('groupID') >= 0))
			{
				redirect('/admin/dashboard/permissions');
				die();
			}
	
			// set siteID
			if ($this->input->post('siteID') && $this->session->userdata('groupID') < 0)
			{
				$this->core->set['siteID'] = $this->input->post('siteID');
			}
			else
			{
				$this->core->set['siteID'] = $this->siteID;
			}

			// update
			if ($this->core->update($this->table, $objectID))
			{
				$output['message'] = '<p>Your details have been updated.</p>';
			}
		}	
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('edit',$output);
		$this->load->view($this->includes_path.'/footer');
	}


	function delete($objectID)
	{
		// check permissions for this page
		if (!in_array('users_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->delete($this->table, array($this->objectID => $objectID)))
		{
			// where to redirect to
			redirect($this->redirect);
		}
	}

	function import()
	{
		// check permissions for this page
		if (!in_array('users_import', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		$output = '';
		if (isset($_FILES['csv']))
		{
			if ($numImported = $this->users->import_csv($_FILES['csv']))
			{
				$output['message'] = '<strong>'.$numImported.'</strong> rows have been imported or updated successfully.';
			}
		}
				
		$this->load->view($this->includes_path.'/header');
		$this->load->view('import', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function export()
	{
		// check permissions for this page
		if (!in_array('users_import', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// export orders as CSV
		$this->load->dbutil();

		$query = $this->users->export();
		
		$csv = $this->dbutil->csv_from_result($query); 
		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Length: " .(string)(strlen($csv)));
		header("Content-Disposition: attachment; filename=users-".date('U').".csv");
		header("Content-Description: File Transfer");
		
		$this->output->set_output($csv);
	}

	function ac_users()
	{	
		$q = strtolower($_POST["q"]);
        if (!$q) return;

        // form dropdown
        $results = $this->users->get_users($q);

        // go foreach
        foreach((array)$results as $row)
        {
            $items[$row['email']] = $row['firstName'].' '.$row['lastName'];
        }

        foreach ($items as $key=>$value) {
			/* If you want to force the results to the query
			if (strpos(strtolower($key), $tags) !== false)
			{
				echo "$key|$id|$name\n";
			}*/
			$this->output->set_output("$key|$value\n");
        }
	}

	function groups()
	{
		// check permissions for this page
		if (!in_array('users_groups', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// wheres
		$where['groupID !='] = $this->site->config['groupID'];
				
		// if return
		$output = $this->core->viewall('permission_groups', $where);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('groups',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_group()
	{
		// check permissions for this page
		if (!in_array('users_groups', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}	

		// required
		$this->core->required = array(
			'groupName' => array('label' => 'Group name', 'rules' => 'required'),
		);

		// deal with post
		if (count($_POST))
		{			
			// check groupID is not being overridden
			if ($this->input->post('groupID') < 0 && $this->session->userdata('groupID') >= 0)
			{
				redirect('/admin/dashboard/permissions');
				die();
			}

			// update
			if ($this->core->update('permission_groups'))
			{
				// get new groupID
				$groupID = $this->db->insert_id();
				
				// add permissions on new groupID
				$this->permission->add_permissions($groupID, $this->siteID);
								
				// where to redirect to
				redirect('/admin/users/groups');
			}			
		}
				
		// get values
		$output['data'] = $this->core->get_values('permission_groups');

		// get permissions
		$output['permissions'] = $this->permission->get_permissions($this->session->userdata('groupID'));
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('add_group',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_group($groupID)
	{
		// check permissions for this page
		if (!in_array('users_groups', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}	
				
		// set object ID
		$objectID = array('groupID' => $groupID);		

		// required
		$this->core->required = array(
			'groupName' => array('label' => 'Group name', 'rules' => 'required'),
		);

		// deal with post
		if (count($_POST))
		{			
			// check groupID is not being overridden
			if ($this->input->post('groupID') < 0 && $this->session->userdata('groupID') >= 0)
			{
				redirect('/admin/dashboard/permissions');
				die();
			}

			// update
			if ($this->core->update('permission_groups', $objectID))
			{
				// add permissions
				$this->permission->add_permissions($groupID, $this->siteID);
								
				// where to redirect to
				redirect('/admin/users/groups');
			}			
		}
				
		// get values
		$output['data'] = $this->core->get_values('permission_groups', $objectID);

		// get permissions
		$output['permissions'] = $this->permission->get_permissions($this->session->userdata('groupID'));

		// populate permissions
		$perms = $this->permission->get_permission_map($groupID);
		foreach ((array)$perms as $perm)
		{
			$output['data']['perm'.$perm['permissionID']] = 1;
		}
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('edit_group',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_group($objectID)
	{
		// check permissions for this page
		if (!in_array('users_groups', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->delete('permission_groups', array('groupID' => $objectID)))
		{		
			// where to redirect to
			redirect('/admin/users/groups');
		}
	}	
	
}