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

class Images_model extends CI_Model {

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function search_images($query, $limit = '')
	{
		if (!$query)
		{
			return FALSE;
		}

		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		$this->db->where('(imageRef LIKE "%'.$this->db->escape_like_str($query).'%" OR imageName LIKE "%'.$this->db->escape_like_str($query).'%")');

		$this->db->order_by('imageRef', 'asc');

		$query = $this->db->get('images', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function get_images_by_folder_ref($ref, $limit = '')
	{
		$this->db->where(array('images.siteID' => $this->siteID, 'images.deleted' => 0));

		$this->db->where('folderSafe', $ref);

		$this->db->select('images.*, folderName, folderSafe');
		$this->db->join('image_folders', 'image_folders.folderID = images.folderID');

		$this->db->order_by('imageRef', 'asc');

		$query = $this->db->get('images', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function get_folders($folderID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		$this->db->order_by('folderOrder');

		// get based on folder ID
		if ($folderID)
		{
			$query = $this->db->get_where('image_folders', array('folderID' => $folderID), 1);

			if ($query->num_rows())
			{
				return $query->row_array();
			}
			else
			{
				return FALSE;
			}
		}
		// or just get all of em
		else
		{
			$query = $this->db->get('image_folders');

			if ($query->num_rows())
			{
				return $query->result_array();
			}
			else
			{
				return FALSE;
			}
		}
	}

	function lookup_user($userID, $display = FALSE)
	{
		// default wheres
		$this->db->where('userID', $userID);

		// grab
		$query = $this->db->get('users', 1);

		if ($query->num_rows())
		{
			$row = $query->row_array();

			if ($display !== FALSE)
			{
				return ($row['displayName']) ? $row['displayName'] : $row['firstName'].' '.$row['lastName'];
			}
			else
			{
				return $row;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function update_children($folderID)
	{
		// update page draft
		$this->db->set('folderID', 0);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('folderID', $folderID);
		$this->db->update('images');

		return TRUE;
	}
}
