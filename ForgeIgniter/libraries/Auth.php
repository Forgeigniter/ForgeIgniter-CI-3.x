<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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

// MARKED FOR UPDATE IN FI V1.0

// ------------------------------------------------------------------------

class Auth {

	// set defaults
	var $CI;								// CI instance
	var $table = 'users';					// default table
	var $base_path = '';					// default base path
	var $redirect = '';						// default redirect
	var $sessionName = 'logged_in';			// name of session
	var $error = '';						// error message

	function __construct()
	{
		$this->CI =& get_instance();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function login($username = '', $password = '', $sessionName = '', $redirect = FALSE, $remember = FALSE)
	{
		// set default session
		if (!$sessionName)
		{
			$sessionName = $this->sessionName;
		}

		// set default redirect
		if (!$redirect && $this->redirect)
		{
			$redirect = $this->redirect;
		}

		// check if already logged in
		if ($this->CI->session->userdata($sessionName) == $sessionName)
		{
			return ($redirect) ? redirect($redirect) : TRUE;
		}

		// create account
		if ($this->do_login($username, $password, $sessionName))
		{
			// check if remember is set
			if ($remember)
			{
				// set cookie
				$cookie = array(
					'name'   => 'forgeigniter',
					'value'  => $this->CI->core->encode(serialize(array($username, $password, $sessionName))),
					'expire' => '604800',
				);
				set_cookie($cookie);
			}

			return ($redirect) ? redirect($redirect) : TRUE;
		}
	}

	function do_login($username, $password, $sessionName, $cookie = FALSE)
	{
		// login with something other than username, and check against siteID
		if (is_array($username))
		{
			// based on siteID
			$this->CI->db->where('siteID', $this->siteID);

			$this->CI->db->where($username['field'], $username['value']);
		}

		// login with username
		else
		{
			$this->CI->db->where('username', $username);
		}

		// grab from db
		$query = $this->CI->db->get_where($this->table);

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();

			// check against password
			if (md5($password) != $row['password'])
			{
				$this->error = 'The login details used did not match our records. Please try again.';

				return FALSE;
			}

			// check they have permission to access this site
			if ($row['groupID'] > 0 && $row['siteID'] != $this->siteID)
			{
				$this->error = 'You do not have permission to edit this site.';

				return FALSE;
			}

			// remove the password field
			unset($row['password']);

			// check if they are active or not
			if (!$row['active'])
			{
				$this->error = 'Your account is not yet active. Please bear with us until your account is activated.';

				return FALSE;
			}

			// set session data
			$this->CI->session->set_userdata($row);

			// set logged_in to true
			$this->CI->session->set_userdata(array($sessionName => true));

			// update last login
			$this->CI->db->where('userID', $row['userID']);
			$this->CI->db->set('lastLogin', date("Y-m-d H:i:s"));
			$this->CI->db->update('users');

			// login was successful
			return TRUE;
		}
		else
		{
			$this->error = 'The login details used did not match our records. Please try again.';

			// no database result found
			return FALSE;
		}
	}

	function logout($redirect = '')
	{
		// set default redirect
		if (!$redirect)
		{
			$redirect = $this->base_path;
		}

		// destroy any cookies
		delete_cookie('forgeigniter');

		// destroy session
		$this->CI->session->sess_destroy();

		// redirect
		redirect($redirect);
	}

}
