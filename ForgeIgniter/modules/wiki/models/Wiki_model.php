<?php
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2010 - 2016, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @version		1.1
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Wiki_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();

		$this->table = 'wiki';

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	public function get_page($pageID = '', $uri = FALSE)
	{
		// check stuff
		if (intval($pageID))
		{
			$this->db->where('t1.pageID', $pageID, FALSE);
		}
		elseif ($uri !== FALSE)
		{
			$this->db->where('uri', $uri);
		}
		else
		{
			return FALSE;
		}

		// select
		$this->db->select('t1.*, t2.body, t2.dateCreated, t2.userID');

		$this->db->from('wiki t1');
		$this->db->limit(1);

		// Join versions
		$this->db->join('wiki_versions t2', 't2.versionID = t1.versionID', 'left');

		//	Where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('active', 1);
		$this->db->where('deleted', 0);
		$this->db->where('uri', $uri);

		// order
		$this->db->order_by('t2.dateCreated', 'desc');

		// get wiki page
		$query = $this->db->get();

		if ($query->num_rows() == 1)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_pages($catID = '', $searchIDs = FALSE)
	{
		// wheres
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		$this->db->where('active', 1);
		if ($catID)	$this->db->where('catID', $catID);
		if ($catID === FALSE) $this->db->where('catID', 0);
		if ($searchIDs !== FALSE) $this->db->where_in('pageID', $searchIDs);

		// grab total
		$query_total = $this->db->get('wiki');
		$totalRows = $query_total->num_rows();

		// set paging
		$this->core->set_paging($totalRows, $this->site->config['paging']);

		// wheres
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		$this->db->where('active', 1);
		if ($catID)	$this->db->where('catID', $catID);
		if ($catID === FALSE) $this->db->where('catID', 0);
		if ($searchIDs !== FALSE) $this->db->where_in('pageID', $searchIDs);

		// order
		$this->db->order_by('pageName', 'asc');

		$query = $this->db->get('wiki');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_versions($pageID)
	{
		$this->db->where('pageID', $pageID);

		$this->db->order_by('dateCreated', 'desc');

		$query = $this->db->get('wiki_versions', 30);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_recent_changes()
	{
		$this->db->where('deleted', 0);
		$this->db->where('t1.siteID', $this->siteID, FALSE);

		// where
		$this->db->select('t1.*, t2.pageName, t2.pageID, t2.uri');
		$this->db->from('wiki_versions t1');
		$this->db->limit(50);

		// Join
		$this->db->join('wiki t2', 't2.pageID = t1.pageID');

		$this->db->order_by('dateCreated', 'desc');

		// Get
		$query = $this->db->get();

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_categories($catID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get based on category ID
		if ($catID)
		{
			// select
			$this->db->select('wiki_cats.*, parentID as tempParentID, (SELECT catName from '.$this->db->dbprefix.'wiki_cats where '.$this->db->dbprefix.'wiki_cats.catID = tempParentID) AS parentName', FALSE);

			// wheres
			$this->db->where('catID', $catID);

			$this->db->order_by('catOrder');

			$query = $this->db->get('wiki_cats', 1);

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
			$this->db->select('wiki_cats.*, if(parentID>0, parentID+1, catID) as parentOrder', FALSE);
			$this->db->order_by('parentOrder');
			$this->db->order_by('catOrder');

			// template type
			$query = $this->db->get('wiki_cats');

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

	public function get_category($catID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get category by ID
		$this->db->where('catID', $catID);

		$query = $this->db->get('wiki_cats', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_category_parents()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->db->where('parentID', 0);

		$this->db->order_by('catOrder', 'asc');

		$query = $this->db->get('wiki_cats');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function get_category_children($catID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get category by ID
		$this->db->where('parentID', $catID);

		$this->db->order_by('catOrder', 'asc');

		$query = $this->db->get('wiki_cats');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	public function search_wiki($query)
	{
		// make sure query is greater than 2 (otherwise load will be too high)
		if (strlen($query) > 2)
		{
			// default wheres
			$where = array(
				'wiki.deleted' => 0,
				'wiki.siteID' => $this->siteID,
			);

			// grab total
			$this->db->where($where);

			// search
			$this->db->like('wiki.pageName', $query);
			$this->db->or_like('wiki_versions.body', $query);

			// join topics
			$this->db->join('wiki_versions', 'wiki.pageID = wiki_versions.pageID');

			// stuff
			$this->db->order_by('wiki.dateCreated', 'desc');
			$this->db->group_by('wiki.pageID');

			// grab
			$query = $this->db->get('wiki');

			if ($query->num_rows())
			{
				$result = $query->result_array();

				foreach($result as $row)
				{
					$pageIDs[] = $row['pageID'];
				}

				return $pageIDs;
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

	public function lookup_user($userID, $display = FALSE)
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

	public function update_page($uri = '')
	{
		// find out if it already exists
		if ($pagedata = $this->get_page(FALSE, $uri))
		{
			// edit the page
			$this->db->set('pageName', $this->input->post('pageName'));
			$this->db->set('catID', $this->input->post('catID'));
			$this->db->where('pageID', $pagedata['pageID']);
			$this->db->update('wiki');

			// add a version
			if ($versionID = $this->add_version($pagedata['pageID']))
			{
				// update the page with version
				$this->db->set('versionID', $versionID);
				$this->db->where('pageID', $pagedata['pageID']);
				$this->db->update('wiki');
			}

			return TRUE;
		}
		elseif ($uri)
		{
			// add the page
			$this->db->set('pageName', $this->input->post('pageName'));
			$this->db->set('catID', $this->input->post('catID'));
			$this->db->set('dateCreated', date("Y-m-d H:i:s"));
			$this->db->set('userID', $this->session->userdata('userID'));
			$this->db->set('uri', $uri);
			$this->db->set('siteID', $this->siteID);
			$this->db->insert('wiki');

			// get pageID
			$pageID = $this->db->insert_id();

			// add a version
			if ($versionID = $this->add_version($pageID))
			{
				// update the page with version
				$this->db->set('versionID', $versionID);
				$this->db->where('pageID', $pageID);
				$this->db->update('wiki');
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function add_version($pageID)
	{
		// check page
		if (!$pagedata = $this->get_page($pageID))
		{
			return FALSE;
		}

		// check version is not the same as latest one
		if ($pagedata['body'] == $this->input->post('body'))
		{
			return FALSE;
		}

		// check post has body
		if (!$this->input->post('body'))
		{
			return FALSE;
		}

		// add version
		$this->db->set('pageID', $pageID);
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('userID', $this->session->userdata('userID'));
		$this->db->set('body', $this->input->post('body'));
		$this->db->set('notes', $this->input->post('notes'));
		$this->db->set('siteID', $this->siteID);

		$this->db->insert('wiki_versions');

		// get version ID
		$versionID = $this->db->insert_id();

		return $versionID;
	}

	public function revert_page($pageID, $versionID)
	{
		// update the page with version
		$this->db->set('versionID', $versionID);
		$this->db->where('pageID', $pageID);
		$this->db->update('wiki');

		return TRUE;
	}

}
