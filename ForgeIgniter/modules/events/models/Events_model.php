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

class Events_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function get_all_events()
	{
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		$query = $this->db->get('events');

		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function get_event($eventID)
	{
		$this->db->where('eventID', $eventID);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		$query = $this->db->get('events', 1);

		if ( $query->num_rows() == 1 )
		{
			$event = $query->row_array();

			return $event;
		}
		else
		{
			return FALSE;
		}
	}

	function get_post_by_id($eventID)
	{
		$this->db->where('eventID', $eventID);

		$query = $this->db->get('event_post', 1);

		if ($query->num_rows())
		{
			$post = $query->row_array();

			return $post;
		}
		else
		{
			return FALSE;
		}
	}

	function get_tags()
	{
		$this->db->join('tags_ref', 'tags_ref.tag_id = tags.id');
		$this->db->where('tags_ref.siteID', $this->siteID);

		$query = $this->db->get('tags');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function update_tags($eventID = '', $tags = '')
	{
		// add tags
		if ($tags)
		{
			$this->tags->delete_tag_ref(
			array(
				'table' => 'events',
				'row_id' => $eventID,
				'siteID' => $this->siteID)
			);

			$tags = str_replace(',', ' ', trim($tags));
			$tagsArray = explode(' ', $tags);
			foreach($tagsArray as $key => $tag)
			{
				$tag = trim($tag);
				if (isset($tag) && $tags != '' && strlen($tag) > 0)
				{
					$tidyTagsArray[] = $tag;
				}
			}
			$tags = array(
		 		'table' => 'events',
		 		'tags' => $tidyTagsArray,
				'row_id' => $eventID,
				'siteID' => $this->siteID
			);
			$this->tags->add_tags($tags);

			return true;
		}
		else
		{
			return FALSE;
		}
	}

	function get_events($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID
		);

		// wheres
		$this->db->where($where);

		// check event isn't passed
		$this->db->where('IF(eventEnd > 0, eventEnd, eventDate) >=', date("Y-m-d H:i:s", time()));

		// order by event date
		$this->db->order_by('eventDate', 'asc');

		// get rows with paging
		$query = $this->db->get('events', $num);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_featured_events($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'featured' => 1,
			'siteID' => $this->siteID
		);

		// wheres
		$this->db->where($where);

		// order by event date
		$this->db->order_by('eventDate', 'desc');

		// get rows with paging
		$query = $this->db->get('events');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_month($month = '', $year = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID
		);

		// where event is not old and is in this month
		$month = ($month) ? $month : date("m", time());
		$next_month = $month + 1;
		$year = ($year) ? $year : date("Y", time());

		$from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year));
		$to =  date("Y-m-d H:i:s", mktime(23, 59, 59, $next_month, 0, $year));

		$where['eventDate >='] = $from;
		$where['eventDate <='] = $to;

		// wheres
		$this->db->where($where);

		// order by event date
		$this->db->order_by('eventDate', 'asc');

		// get rows with paging
		$query = $this->db->get('events');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_events_by_tag($tag, $limit = 10)
	{
		// get rows based on this tag
		$result = $this->tags->fetch_rows(array(
			'table' => 'events',
			'tags' => array(1, $tag),
			'siteID' => $this->siteID
		));
		$tags = $result->result_array();
		foreach ($tags as $tag)
		{
			$tagsArray[] = $tag['row_id'];
		}

		// default where
		$this->db->where(array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID
		));

		// check event isn't passed
		$this->db->where('IF(eventEnd > 0, eventEnd, eventDate) >=', date("Y-m-d H:i:s", time()));

		// where tags
		$this->db->where_in('eventID', $tagsArray);
		$this->db->order_by('eventDate', 'asc');

		$query = $this->db->get('events', $limit);

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_events_by_date($year, $month = '', $day = 0)
	{
		if ($month)
		{
			$from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, ((!$day) ? 1 : $day), $year));
			$to = ($day < 1) ? date("Y-m-d H:i:s", mktime(23, 59, 59, ($month+1), $day, $year)) : date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));
		}
		else
		{
			$from = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, ((!$day) ? 1 : $day), $year));
			$to = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, ($year+1)));
		}

		$this->db->where('eventDate >=', $from);
		$this->db->where('eventDate <=', $to);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		$this->db->order_by('eventDate');

		$query = $this->db->get('events');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_post_by_title($title = '')
	{
		$this->db->where('eventTitle', $title);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		$query = $this->db->get('events');

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_archive()
	{
		// selects
		$this->db->select('COUNT(eventID) as numEvents, DATE_FORMAT(eventDate, "%M %Y") as dateStr, DATE_FORMAT(eventDate, "%m") as month, DATE_FORMAT(eventDate, "%Y") as year', FALSE);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		// check event isn't passed
		$this->db->where('IF(eventEnd > 0, eventEnd, eventDate) >=', date("Y-m-d H:i:s", time()));

		// group by month
		$this->db->group_by('dateStr');

		$query = $this->db->get('events');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_headlines($num = 10)
	{
		$this->db->select('eventID, eventTitle, eventDate, description');
		return $this->get_events($num);
	}

	function search_events($query = '', $ids = '')
	{
		if (!$query && !$ids)
		{
			return FALSE;
		}

		// default wheres
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);

		// search
		if ($query)
		{
			// tidy query
			$q = $this->db->escape_like_str($query);

			$sql = '(eventTitle LIKE "%'.$q.'%" OR description LIKE "%'.$q.'%")';
		}
		if ($ids)
		{
			$sql .= ' OR eventID IN ('.implode(',', $ids).')';
		}
		$this->db->where($sql);

		// check event isn't passed
		$this->db->where('IF(eventEnd > 0, eventEnd, eventDate) >=', date("Y-m-d H:i:s", time()));

		$this->db->order_by('eventDate', 'asc');

		$query = $this->db->get('events');

		if ($query->num_rows() > 0)
		{

			return $query->result_array();
		}
		else
		{
			return FALSE;
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




	/// OLD!!! ///


	function tag_cloud($num)
	{
		$this->db->select('t.tag, COUNT(pt.tagID) as qty', FALSE);
		$this->db->join('tags pt', 'pt.tag_id = t.id', 'inner');
		$this->db->groupby('t.id');

		$query = $this->db->get('tags t');

		$built = array();

		if ($query->num_rows > 0)
		{
			$result = $query->result_array();

			foreach ($result as $row)
			{
				$built[$row['tag']] = $row['qty'];
			}

			return $built;
		}
		else
		{
			return array();
		}
	}

}
