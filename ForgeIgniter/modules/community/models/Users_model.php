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
defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Users_model extends CI_Model {

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function get_user($userID)
	{
		// default wheres
		$this->db->where('siteID', $this->siteID);

		$this->db->where('userID', $userID);

		// grab
		$query = $this->db->get('users', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_user_by_email($email)
	{
		// default wheres
		$this->db->where('siteID', $this->siteID);

		$this->db->where('email', $email);

		// grab
		$query = $this->db->get('users', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_avatar($filename)
	{
		$site_base_path = realpath('');
		$pathToAvatars = $this->uploads->uploadsPath.'/avatars/';
		if (is_file('.'.$pathToAvatars.$filename))
		{
			$avatar = $pathToAvatars.$filename;
		}
		else
		{

			$avatar = $site_base_path.$pathToAvatars.'noavatar.gif';
		}
		return $avatar;
	}

	function get_public_users()
	{
		// default where
		$where = array('users.siteID' => $this->siteID, 'privacy !=' => 'H');

		$this->db->where($where);
		$query_total = $this->db->get('users');
		$totalRows = $query_total->num_rows();

		// order
		$this->db->order_by('lastLogin', 'desc');

		// grab
		$this->db->where($where);

		// select
		$this->db->select('users.*, groupName', FALSE);

		// join groups table
		$this->db->join('permission_groups', 'permission_groups.groupID = users.groupID', 'left');

		$query = $this->db->get('users', $this->site->config['paging'], $this->pagination->offset);

		if ($query->num_rows())
		{
			// set paging
			$this->core->set_paging($totalRows, $this->site->config['paging']);

			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_users($userIDs, $friendIDs = FALSE)
	{
		// default where
		$where = array('siteID' => $this->siteID, 'privacy !=' => 'H');

		// grab total
		$this->db->where_in('userID', $userIDs);

		// just search friends
		if ($friendIDs !== FALSE)
		{
			$this->db->where_in('userID', $friendIDs);
		}

		$this->db->where($where);
		$query_total = $this->db->get('users');
		$totalRows = $query_total->num_rows();

		// order
		$this->db->order_by('lastLogin', 'desc');

		// just search friends
		if ($friendIDs !== FALSE)
		{
			$this->db->where_in('userID', $friendIDs);
		}

		// grab
		$this->db->where_in('userID', $userIDs);
		$this->db->where($where);
		$query = $this->db->get('users', $this->site->config['paging'], $this->pagination->offset);

		if ($query->num_rows())
		{
			// set paging
			$this->core->set_paging($totalRows, $this->site->config['paging']);

			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_friends($userID, $limit = '')
	{
		// default where
		$where = array('t1.siteID' => $this->siteID, 't1.userID' => $userID);

		// grab total
		$this->db->where($where, '', FALSE);
		$query_total = $this->db->get('ce_friendmap as t1');
		$totalRows = $query_total->num_rows();

		// get user join
		$this->db->select('t1.friendID, groupName, users.*', FALSE);
		$this->db->join('ce_friendmap as t2', 't1 . friendID = t2 . userID AND t2.friendID = t1 . userID');
		$this->db->join('users', 'users.userID = t1 . friendID');
		$this->db->join('permission_groups', 'permission_groups.groupID = users.groupID', 'left');

		// grab
		$this->db->where($where, '', FALSE);

		if ($limit)
		{
			$query = $this->db->get('ce_friendmap as t1', $this->site->config['paging'], $this->pagination->offset);
		}
		else
		{
			$query = $this->db->get('ce_friendmap as t1');
		}

		if ($query->num_rows())
		{
			// set paging
			$this->core->set_paging($totalRows, $this->site->config['paging']);

			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_friend_requests($userID, $friendIDs, $myRequests = TRUE)
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);

		// if myRequests is FALSE it means that we want to find out who is requesting this user
		if ($myRequests === FALSE)
		{
			$this->db->where('t1.friendID', $userID, FALSE);

			// get user join
			$this->db->select('t2.*, t1.friendID');
			$this->db->join('users t2', 't2.userID = t1 . userID');
		}

		// otherwise just find out what friend requests this user has
		else
		{
			$this->db->where('t1.userID', $userID, FALSE);
		}

		// make sure this users friends in there
		$this->db->where_not_in('t1 . userID', $friendIDs);

		$query = $this->db->get('ce_friendmap as t1');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function search_users($query)
	{
		// make sure query is greater than 1 (otherwise load will be too high)
		if (strlen($query) > 1)
		{
			// grab total
			$this->db->where('siteID', $this->siteID);

			// tidy query
			$query = $this->db->escape_like_str($query);

			$name = @preg_split('/ /', $query);
			if (count($name) > 1)
			{
				$firstName = $name[0];
				$lastName = $name[1];

				$this->db->where('(displayName LIKE "%'.$query.'%" OR firstName LIKE "%'.$firstName.'%" AND lastName LIKE "%'.$lastName.'%")');
			}
			else
			{
				$this->db->where('(displayName  LIKE "%'.$query.'%" OR firstName LIKE "%'.$query.'%" OR lastName LIKE "%'.$query.'%")');
			}

			// grab
			$query = $this->db->get('users');

			if ($query->num_rows())
			{
				$result = $query->result_array();

				foreach($result as $row)
				{
					$userIDs[] = $row['userID'];
				}

				return $userIDs;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function add_friendmap($userID)
	{
		$this->db->insert('ce_friendmap', array('friendID' => $userID, 'userID' => $this->session->userdata('userID'), 'siteID' => $this->siteID));

		return true;
	}

	function remove_friendmap($userID)
	{
		$this->db->delete('ce_friendmap', array('friendID' => $userID, 'userID' => $this->session->userdata('userID'), 'siteID' => $this->siteID));
		$this->db->delete('ce_friendmap', array('userID' => $userID, 'friendID' => $this->session->userdata('userID'), 'siteID' => $this->siteID));

		return true;
	}

	function get_user_posts($userID, $limit = 10)
	{
		$this->db->select('post, dateCreated');
		$this->db->where(array('ce_posts.siteID' => $this->siteID, 'ce_posts.deleted' => 0));
		$this->db->where('ce_posts.type', 'P');
		$this->db->where('ce_posts.objectID', $userID);
		$this->db->order_by('ce_posts.dateCreated', 'desc');

		$query = $this->db->get('ce_posts', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_feed($userIDs = '', $start = 0, $limit = 20)
	{
		// where stuff
		$this->db->where(array('t1.siteID' => $this->siteID, 't1.deleted' => 0, 't1.type' => '"P"'), NULL, FALSE);
		$this->db->where_in('t1 .objectID', $userIDs);
		$this->db->order_by('t1 .dateModified', 'desc');

		// get file and user join
		$this->db->select('t1.*, t2.filename, t2.image, t2.fileTitle, t2.description, t3.userID, t3.email, t3.firstName, t3.lastName, t3.displayName, t3.avatar, t3.privacy', FALSE);
		$this->db->join('ce_files t2', 't2.fileID = t1 . fileID', 'left');
		$this->db->join('users t3', 't3.userID = t1 . userID');

		$query = $this->db->get('ce_posts t1', $limit, $start);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_comments($postID)
	{
		// default where
		$this->db->where('ce_comments.siteID', $this->siteID);
		$this->db->where('deleted', 0);
		$this->db->where('postID', $postID);

		// join
		$this->db->select('ce_comments.*, users.userID, email, firstName, lastName, displayName, avatar');
		$this->db->join('users', 'users.userID = ce_comments.userID');

		// order
		$this->db->order_by('dateCreated', 'asc');

		$query = $this->db->get('ce_comments');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_files($userID)
	{
		// where stuff for total
		$this->db->where(array('ce_files.siteID' => $this->siteID, 'ce_files.deleted' => 0));
		$this->db->where('ce_files.filename IS NOT NULL', NULL, FALSE);
		$this->db->where('ce_files.userID', $userID);
		$this->db->order_by('dateCreated', 'desc');

		// grab total
		$query_total = $this->db->get('ce_files');
		$totalRows = $query_total->num_rows();

		// where stuff
		$this->db->where(array('ce_files.siteID' => $this->siteID, 'ce_files.deleted' => 0));
		$this->db->where('ce_files.filename IS NOT NULL', NULL, FALSE);
		$this->db->where('ce_files.userID', $userID);
		$this->db->order_by('dateCreated', 'desc');

		// get user join
		$this->db->select('ce_files.*, users.userID, email, firstName, lastName, displayName, avatar', FALSE);
		$this->db->join('users', 'users.userID = ce_files.userID');

		$query = $this->db->get('ce_files', $this->site->config['paging'], $this->pagination->offset);

		if ($query->num_rows())
		{
			// set paging
			$this->core->set_paging($totalRows, $this->site->config['paging']);

			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function delete_avatar()
	{
		// delete avatar reference
		$this->db->set('avatar', '');
		$this->db->where(array('siteID' => $this->siteID, 'userID' => $this->session->userdata('userID')));
		$this->db->limit(1);

		$this->db->update('users');

		return true;
	}

	function delete_logo()
	{
		// delete avatar reference
		$this->db->set('companyLogo', '');
		$this->db->where(array('siteID' => $this->siteID, 'userID' => $this->session->userdata('userID')));
		$this->db->limit(1);

		$this->db->update('users');

		return true;
	}

	function set_reset_key($userID = '', $key = '')
	{
		if ($key && $userID)
		{
			// set password reset key
			$this->db->set('resetkey', $key);
			$this->db->where(array('siteID' => $this->siteID, 'userID' => $userID));
			$this->db->limit(1);

			$this->db->update('users');

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function check_key($key = '')
	{
		if ($key)
		{
			// check reset key
			$this->db->where(array('siteID' => $this->siteID, 'resetkey' => $key));
			$query = $this->db->get('users', 1);

			if ($query->num_rows())
			{
				return $query->row_array();
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

}
