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

class Messages_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function get_messages()
	{
		// start cache
		$this->db->start_cache();
		
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('deleted', 0, FALSE);
		$this->db->where('t2.toUserID != ', 't2.userID', FALSE);
		$this->db->where('t2.toUserID =', $this->session->userdata['userID'], FALSE);

		// grab total
		$this->db->select('t1.*, max(unread) as unread, max(t2.messageID) AS lastMessageID, parentID, deleted, users.userID, email, firstName, lastName, displayName, avatar', FALSE);

		// joins
		$this->db->join('community_messagemap t2', 't2.messageID = t1 . messageID ');
		$this->db->join('users', 'users.userID = t1 . userID');	

		// group
		$this->db->group_by('t1 . messageID');

		// stop cache
		$this->db->stop_cache();

		// get totals
		$query_total = $this->db->get('community_messages t1');
		$totalRows = $query_total->num_rows();

		// order
		$this->db->order_by('dateCreated', 'desc');
		
		// grab
		$query = $this->db->get('community_messages t1', $this->site->config['paging'], $this->pagination->offset);

		// flush cache
		$this->db->flush_cache();
		
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

	function get_message($messageID)
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t1.messageID', $this->db->escape($messageID), FALSE);
		$this->db->where('deleted', 0, FALSE);
		$this->db->where('(t2.toUserID = '.$this->session->userdata['userID'].' OR t2.userID = '.$this->session->userdata['userID'].')', NULL, FALSE);

		// joins
		$this->db->join('community_messagemap t2', 't2.messageID = t1 . messageID ');
		$this->db->join('users', 'users.userID = t1 . userID');	
		
		// get user join
		$this->db->select('t1.*, t2.toUserID, max(unread) as unread, parentID, deleted, users.userID, email, firstName, lastName, displayName, avatar', FALSE);

		// groups
		$this->db->group_by('messageID');
		
		// get
		$query = $this->db->get('community_messages t1', 1);
		
		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_replies($messageID)
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('deleted', 0, FALSE);
		$this->db->where('t2.parentID', $this->db->escape($messageID), FALSE);
		$this->db->where('(t2.toUserID = '.$this->session->userdata['userID'].' OR t2.userID = '.$this->session->userdata['userID'].')', NULL, FALSE);

		// joins
		$this->db->join('community_messagemap t2', 't2.messageID = t1 . messageID ');
		$this->db->join('users', 'users.userID = t1 . userID');	
		
		// get user join
		$this->db->select('t1.*, max(unread) as unread, parentID, deleted, users.userID, email, firstName, lastName, displayName, avatar', FALSE);

		// groups
		$this->db->group_by('messageID');
		
		// get
		$query = $this->db->get('community_messages t1');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_unread_message_count()
	{
		if ($this->session->userdata('userID'))
		{
			// default where
			$this->db->where('t1.siteID', $this->siteID, FALSE);
			$this->db->where('deleted', 0, FALSE);
			$this->db->where('t2.toUserID != ', 't2.userID', FALSE);
			$this->db->where('t2.toUserID =', $this->session->userdata['userID'], FALSE);
			$this->db->where('t2.unread', 1, FALSE);
	
			// grab total
			$this->db->select('t1.*', FALSE);
		
			// joins
			$this->db->join('community_messagemap t2', 't2.messageID = t1 . messageID ');
	
			// group
			$this->db->group_by('t1 . messageID');

			// get
			$query = $this->db->get('community_messages t1');			

			if ($totalRows = $query->num_rows())
			{
				return $totalRows;
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

	function search_messages($query)
	{
		// start cache
		$this->db->start_cache();
		
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('deleted', 0, FALSE);
		$this->db->where('t2.toUserID != ', 't2.userID', FALSE);
		$this->db->where('t2.toUserID =', $this->session->userdata['userID'], FALSE);

		// tidy query
		$q = $this->db->escape_like_str($query);

		$this->db->where('(subject LIKE "%'.$q.'%" OR message LIKE "%'.$q.'%")');

		// grab total
		$this->db->select('t1.*, max(unread) as unread, max(t2.messageID) AS lastMessageID, parentID, deleted, users.userID, email, firstName, lastName, displayName, avatar', FALSE);

		// joins
		$this->db->join('community_messagemap t2', 't2.messageID = t1 . messageID ');
		$this->db->join('users', 'users.userID = t1 . userID');	

		// group
		$this->db->group_by('t1 . messageID');

		// stop cache
		$this->db->stop_cache();

		// get totals
		$query_total = $this->db->get('community_messages t1');
		$totalRows = $query_total->num_rows();

		// order
		$this->db->order_by('dateCreated', 'desc');
		
		// grab
		$query = $this->db->get('community_messages t1', $this->site->config['paging'], $this->pagination->offset);

		// flush cache
		$this->db->flush_cache();
		
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

	function get_recipient($messageID)
	{
		// default where
		$this->db->where('siteID', $this->siteID);
		$this->db->where('messageID', $messageID);
		$this->db->where('toUserID !=', $this->session->userdata['userID']);
		
		// groups
		$this->db->group_by('userID');
		
		// get
		$query = $this->db->get('community_messagemap');

		if ($query->num_rows())
		{
			$row = $query->row_array();
			return $row['toUserID'];
		}
		else
		{
			return FALSE;
		}
	}

	function add_messagemap($toUserID, $messageID, $parentID = 0)
	{	
		// undelete parent
		if ($parentID)
		{
			$this->db->set('deleted', 0);
			$this->db->where('messageID', $parentID);
			$this->db->where('siteID', $this->siteID);
			$this->db->update('community_messagemap');
		}
		
		// add recipient's copy
		$this->db->set('messageID', $messageID);
		$this->db->set('toUserID', $toUserID);
		$this->db->set('userID', $this->session->userdata('userID'));
		$this->db->set('parentID', $parentID);		
		$this->db->set('siteID', $this->siteID);
		
		$this->db->insert('community_messagemap');

		// add this users copy
		$this->db->set('messageID', $messageID);
		$this->db->set('toUserID', $this->session->userdata('userID'));
		$this->db->set('userID', $this->session->userdata('userID'));
		$this->db->set('parentID', $parentID);		
		$this->db->set('siteID', $this->siteID);
		
		$this->db->insert('community_messagemap');

		return TRUE;
	}

	function read_message($messageID)
	{
		// get message details
		if ($message = $this->get_message($messageID))
		{
			// set parent to read
			$this->db->set('unread', '0');
			$this->db->where('messageID', $messageID);
			$this->db->where('userID !=', $this->session->userdata('userID'));
			$this->db->where('siteID', $this->siteID);
			$this->db->update('community_messagemap');

			// set parent to read
			$this->db->set('unread', '0');
			$this->db->where('parentID', $messageID);
			$this->db->where('userID !=', $this->session->userdata('userID'));
			$this->db->where('siteID', $this->siteID);
			$this->db->update('community_messagemap');

			return TRUE;		
		}
		else
		{
			return FALSE;
		}
	}

	function unread_message($messageID)
	{
		// get message details
		if ($message = $this->get_message($messageID))
		{
			// set parent to read
			$this->db->set('unread', '1');
			$this->db->where('messageID', $messageID);
			$this->db->where('siteID', $this->siteID);
			$this->db->update('community_messages');
						
			return TRUE;		
		}
		else
		{
			return FALSE;
		}
	}

	function delete_message($messageID)
	{
		// get message details
		if ($message = $this->get_message($messageID))
		{
			// delete message
			$this->db->set('deleted', 1);
			$this->db->where('toUserID', $this->session->userdata('userID'));
			$this->db->where('siteID', $this->siteID);

			if ($message['parentID'] > 0)
			{				
				$this->db->where('(messageID = "'.$messageID.'" OR parentID = "'.$messageID.'" OR messageID = "'.$message['parentID'].'" OR parentID = "'.$message['parentID'].'")');
			}
			else
			{
				$this->db->where('(messageID = "'.$messageID.'" OR parentID = "'.$messageID.'")');
			}

			// delete
			$this->db->update('community_messagemap');
							
			return TRUE;		
		}
		else
		{
			return FALSE;
		}
	}

}