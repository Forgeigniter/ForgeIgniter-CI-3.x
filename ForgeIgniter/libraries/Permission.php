<?php defined('BASEPATH') OR exit('No direct script access allowed');
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

class Permission {

	// init vars
	var $CI;						// CI instance
	var $table = '';				// default table
	var $where = array();
	var $set = array();
	var $required = array();
	var $permissions = array();
	var $groupID = '';
	var $sitePermissions = array();
	var $siteGroupID = '';

	function __construct()
	{
		// init vars
		$this->CI =& get_instance();

		// always use siteID in an insert, if the siteID is available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// define
		if (defined('SITEGROUPID'))
		{
			$this->siteGroupID = SITEGROUPID;
		}

		// set groupID from session (if set)
		if ($this->CI->session->userdata('session_user') || $this->CI->session->userdata('session_admin'))
		{
			$this->groupID = $this->CI->session->userdata('groupID');

			// set user permissions if logged in
			$this->permissions = $this->get_group_permissions($this->CI->session->userdata('groupID'));
		}

		// get site permissions
		$this->sitePermissions = $this->get_group_permissions($this->siteGroupID);

	}

	function get_group_permissions($groupID)
	{
		// grab keys
		$this->CI->db->select('`key`');
		$this->CI->db->join('permissions', 'permissions.permissionID = permission_map.permissionID');
		$this->CI->db->where('groupID', $groupID);
		$this->CI->db->order_by('category');

		// return
		$query = $this->CI->db->get('permission_map');

		if ($query->num_rows())
		{
			foreach ($query->result_array() as $row)
			{
				// get module from permission key
				$module = @preg_split('/_/', $row['key']);

				// check module actually exists
				if (@is_dir(APPPATH.'/modules/'.$module[0]))
				{
					$permissions[] = $row['key'];
				}
			}

			return $permissions;
		}
		else
		{
			return FALSE;
		}
	}

	function get_group_permissions_ids($groupID)
	{
		// grab keys
		$this->CI->db->select('permission_map.permissionID');
		$this->CI->db->join('permissions', 'permissions.permissionID = permission_map.permissionID');
		$this->CI->db->where('groupID', $groupID);
		$this->CI->db->order_by('category');

		// only show the non-special perms
		if ($this->CI->session->userdata('groupID') >= 0)
		{
			$this->CI->db->where('special', 0);
		}

		// return
		$query = $this->CI->db->get('permission_map');

		if ($query->num_rows())
		{
			foreach ($query->result_array() as $row)
			{
				$permissions[] = $row['permissionID'];
			}

			return $permissions;
		}
		else
		{
			return FALSE;
		}
	}

	function get_permissions($groupID = '')
	{
		// select
		$this->CI->db->select('DISTINCT(category)');

		// if groupID is set get on that groupID
		if ($groupID)
		{
			$this->CI->db->where_in('key', $this->get_group_permissions($groupID));
		}

		// only show the non-special perms
		if ($this->CI->session->userdata('groupID') >= 0)
		{
			$this->CI->db->where('special', 0);
		}

		$this->CI->db->order_by('category');

		// return
		$query = $this->CI->db->get('permissions');

		if ($query->num_rows())
		{
			$result = $query->result_array();

			foreach($result as $row)
			{
				if ($cat_perms = $this->get_perms_from_cat($row['category']))
				{
					$permissions[$row['category']] = $cat_perms;
				}
				else
				{
					$permissions[$row['category']] = 'N/A';
				}
			}
			return $permissions;
		}
		else
		{
			return FALSE;
		}
	}

	function get_perms_from_cat($category = '')
	{
		// where
		if ($category)
		{
			$this->CI->db->where('category', $category);
		}

		// return
		$query = $this->CI->db->get('permissions');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_permission_map($groupID)
	{
		// grab keys
		$this->CI->db->select('permissionID');

		// where
		$this->CI->db->where('groupID', $groupID);

		// return
		$query = $this->CI->db->get('permission_map');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_groups($type = '')
	{
		// where
		$this->CI->db->where('permission_groups.siteID', $this->siteID);

		// where not the default administrator
		$this->CI->db->where('permission_groups.groupID !=', $this->CI->site->config['groupID']);

		// only select admin groups
		if ($type == 'admin')
		{
			$this->CI->db->select('COUNT(*), permission_groups.*', FALSE);
			$this->CI->db->join('permission_map', 'permission_map.groupID = permission_groups.groupID');
			$this->CI->db->group_by('permission_groups.groupID');
		}

		// only select non admin groups
		elseif ($type == 'normal')
		{
			$this->CI->db->select('permission_groups.*, permissionID', FALSE);
			$this->CI->db->join('permission_map', 'permission_map.groupID = permission_groups.groupID', 'left');
			$this->CI->db->where('permissionID IS NULL');
			$this->CI->db->group_by('permission_groups.groupID');
		}

		// return
		$query = $this->CI->db->get('permission_groups');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_group($groupID)
	{
		// where
		$this->CI->db->where('siteID', $this->siteID);

		// where not the default administrator
		$this->CI->db->where('groupID', $groupID);

		// return
		$query = $this->CI->db->get('permission_groups', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function add_permissions($groupID, $siteID = '')
	{
		// get permissions (not special)
		$permissions = $this->get_group_permissions_ids($groupID);

		// delete all permissions on this groupID first
		if ($siteID)
		{
			$this->CI->db->where('siteID', $siteID);
		}
		$this->CI->db->where('groupID', $groupID);
		$this->CI->db->where_in('permissionID', $permissions);
		$this->CI->db->delete('permission_map');

		// get post
		$post = $this->CI->core->get_post();
		foreach ($post as $key => $value)
		{
			if (preg_match('/^perm([0-9]+)/i', $key, $matches))
			{
				if ($siteID)
				{
					$this->CI->db->set('siteID', $siteID);
				}
				$this->CI->db->set('groupID', $groupID);
				$this->CI->db->set('permissionID', $matches[1]);
				$this->CI->db->insert('permission_map');
			}
		}

		return true;
	}

	function add_default_permissions($groupID, $siteID = '')
	{
		// delete all permissions on this groupID first
		if ($siteID)
		{
			$this->CI->db->where('siteID', $siteID);
		}
		$this->CI->db->where('groupID', $groupID);
		$this->CI->db->delete('permission_map');

		// get permissions
		$query = $this->CI->db->get('permissions');

		if ($query->num_rows())
		{
			foreach ($query->result_array() as $row)
			{
				if ($siteID)
				{
					$this->CI->db->set('siteID', $siteID);
				}
				$this->CI->db->set('groupID', $groupID);
				$this->CI->db->set('permissionID', $row['permissionID']);
				$this->CI->db->insert('permission_map');
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function add_group($groupName = '', $siteID = '')
	{
		if ($groupName)
		{
			if ($siteID)
			{
				$this->CI->db->set('siteID', $siteID);
			}
			else
			{
				$this->CI->db->set('siteID', $this->siteID);
			}
			$this->CI->db->set('groupName', $groupName);
			$this->CI->db->insert('permission_groups');

			return $this->CI->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}

}
