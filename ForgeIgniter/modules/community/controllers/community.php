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

class Community extends MX_Controller {

	// set defaults
	var $redirect = '/community';			// default redirect
	var $permissions = array();
	var $partials = array();

	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// get site permissions and redirect if it don't have access to this module
		if (!$this->permission->sitePermissions)
		{
			show_error('You do not have permission to view this page');
		}
		if (!in_array('community', $this->permission->sitePermissions))
		{
			show_error('You do not have permission to view this page');
		}

		// get permissions for the logged in admin
		if ($this->session->userdata('session_admin'))
		{
			$this->permission->permissions = $this->permission->get_group_permissions($this->session->userdata('groupID'));
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load libs etc
		$this->load->library('tags');
		$this->load->model('community_model', 'community');

		// load modules
		$this->load->module('pages');
	}

	function index()
	{
		$this->load->model('users_model', 'users');

		// get members
		if ($users = $this->users->get_public_users())
		{
			foreach($users as $user)
			{
				$output['members'][] = array(
					'member:id' => $user['userID'],
					'member:avatar' => anchor('/users/profile/'.$user['userID'], display_image($this->users->get_avatar($user['avatar']), 'User Avatar', 80, 'class="avatar"', site_url().$this->config->item('staticPath').'/images/noavatar.gif')),
					'member:name' => ($user['displayName']) ? $user['displayName'] : $user['firstName'].' '.$user['lastName'],
					'member:email' => $user['email'],
					'member:group' => ($user['groupName']) ? $user['groupName'] : '',
					'member:link' => site_url('/users/profile/'.$user['userID'])
				);
			}
		}

		// set page heading
		$output['page:title'] = $this->site->config['siteName'].' | Members';
		$output['page:heading'] = anchor('/community/members', 'Members');

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		//User Count
		$output['user:count'] = ($count = $this->community->count_users()) ? $count : 0;


		// display with cms layer
		$this->pages->view('community_members', $output, TRUE);
	}

}
