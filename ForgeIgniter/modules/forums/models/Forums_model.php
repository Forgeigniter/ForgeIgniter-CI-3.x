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

class Forums_Model extends CI_Model {

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load libs
		$this->load->library('tags');
	}

	function get_user($userID)
	{
		// default wheres
		$this->db->where('users.siteID', $this->siteID);

		// select
		$this->db->select('users.*, groupName', FALSE);

		// where user ID
		$this->db->where('userID', $userID);

		// join groups table
		$this->db->join('permission_groups', 'permission_groups.groupID = users.groupID', 'left');

		// grab
		$query = $this->db->get('users', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return false;
		}
	}

	function get_categories($catID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
		$this->db->order_by('catOrder');

		// get based on category ID
		if ($catID)
		{
			$query = $this->db->get_where('forums_cats', array('catID' => $catID), 1);

			if ($query->num_rows())
			{
				return $query->row_array();
			}
			else
			{
				return false;
			}
		}
		// or just get all of em
		else
		{
			// template type
			$query = $this->db->get('forums_cats');

			if ($query->num_rows())
			{
				return $query->result_array();
			}
			else
			{
				return false;
			}
		}
	}

	function get_forums($catID = '')
	{
		// catID if set
		if ($catID)
		{
			$this->db->where('forums.catID', $catID);
			$this->db->join('forums_cats', 'forums.catID = forums_cats.catID');
		}

		// default wheres
		$this->db->where('forums.deleted', 0);
		$this->db->where('forums.active', 1);
		$this->db->where('forums.private', 0);
		$this->db->where('forums.siteID', $this->siteID);

		// grab
		$query = $this->db->get('forums');

		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return false;
		}
	}

	function get_forum($forumID)
	{
		// default wheres
		$this->db->where('forums.deleted', 0);
		$this->db->where('forums.siteID', $this->siteID);
		$this->db->where('forums.forumID', $forumID);

		// join cats
		$this->db->join('forums_cats', 'forums.catID = forums_cats.catID', 'left');

		// grab
		$query = $this->db->get('forums');

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return false;
		}
	}

	function get_topics($forumID = '', $limit = '', $searchIDs = FALSE)
	{
		if (!$forumID)
		{
			return FALSE;
		}

		// get based on forum ID
		if (!$limit)
		{
			// default wheres
			$where = array(
				'deleted' => 0,
				'siteID' => $this->siteID,
				'forumID' => $forumID
			);

			// search
			if ($searchIDs !== FALSE)
			{
				$this->db->where_in('forums_topics.topicID', $searchIDs);
			}

			// grab total
			$this->db->where($where);
			$query_total = $this->db->get('forums_topics');
			$totalRows = $query_total->num_rows();

			// set paging
			$this->core->set_paging($totalRows, 10);

			// order
			$this->db->order_by('sticky', 'desc');
			$this->db->order_by('dateModified', 'desc');

			// search
			if ($searchIDs !== FALSE)
			{
				$this->db->where_in('forums_topics.topicID', $searchIDs);
			}

			// grab
			$this->db->where($where);
			$query = $this->db->get('forums_topics', 10, $this->pagination->offset);

			if ($query->num_rows())
			{
				return $query->result_array();
			}
			else
			{
				return FALSE;
			}
		}

		// or just get all of em, well get latest 10
		else
		{
			// default wheres
			$where = array(
				'forums_topics.deleted' => 0,
				'forums_topics.siteID' => $this->siteID,
				'forums_topics.forumID' => $forumID
			);

			// grab total
			$this->db->where($where);

			// search
			if ($searchIDs !== FALSE)
			{
				$this->db->where_in('forums_topics.topicID', $searchIDs);
			}

			// select
			$this->db->select('forums_topics.*, forums_posts.dateCreated, forums_posts.body, users.userID, displayName, firstName, lastName, signature, avatar, posts', FALSE);

			// join users
			$this->db->join('users', 'forums_topics.userID = users.userID');

			// join last post
			$this->db->join('forums_posts', 'forums_posts.postID = forums_topics.lastPostID AND lastPostID > 0', 'left');

			// order
			$this->db->order_by('forums_posts.dateCreated', 'desc');

			// template type
			$query = $this->db->get('forums_topics', $limit);

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

	function get_topic($topicID)
	{
		// default wheres
		$this->db->where('deleted', 0);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('topicID', $topicID);

		// grab
		$query = $this->db->get('forums_topics', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_posts($topicID = '', $limit = '')
	{
		if (!$topicID)
		{
			return FALSE;
		}

		if (!$limit)
		{
			// default wheres
			$where = array(
				'deleted' => 0,
				'forums_posts.siteID' => $this->siteID,
				'forums_posts.topicID' => $topicID
			);

			// grab total
			$this->db->where($where);
			$query_total = $this->db->get('forums_posts');
			$totalRows = $query_total->num_rows();

			// set paging
			$this->core->set_paging($totalRows, 10);

			// select
			$this->db->select('forums_posts.*, groupName, users.userID, displayName, firstName, lastName, signature, avatar, posts, kudos', FALSE);

			// join users
			$this->db->join('users', 'forums_posts.userID = users.userID');

			// join groups table
			$this->db->join('permission_groups', 'permission_groups.groupID = users.groupID', 'left');

			// order
			$this->db->order_by('dateCreated', 'asc');

			// grab rows
			$this->db->where($where);
			$query = $this->db->get('forums_posts', 10, $this->pagination->offset);

			if ($query->num_rows())
			{
				return $query->result_array();
			}
			else
			{
				return false;
			}
		}

		// or just get all of em, well latest 10
		else
		{
			// default wheres
			$where = array(
				'forums_posts.deleted' => 0,
				'forums_posts.siteID' => $this->siteID,
				'forums_posts.topicID' => $topicID
			);

			// grab total
			$this->db->where($where);

			// select
			$this->db->select('forums_posts.*, groupName, replies, lastPostID, topicTitle, users.userID, displayName, firstName, lastName, signature, avatar, posts, kudos', FALSE);

			// join users
			$this->db->join('users', 'forums_posts.userID = users.userID');
			$this->db->join('forums_topics', 'forums_posts.topicID = forums_topics.topicID');

			// join groups table
			$this->db->join('permission_groups', 'permission_groups.groupID = users.groupID', 'left');

			// order
			$this->db->order_by('dateCreated', 'desc');

			// template type
			$query = $this->db->get('forums_posts', $limit);

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

	function get_all_posts($topicID)
	{
		// default wheres
		$where = array(
			'forums_posts.deleted' => 0,
			'forums_posts.siteID' => $this->siteID,
			'forums_posts.topicID' => $topicID
		);

		// grab total
		$this->db->where($where);

		// select
		$this->db->select('forums_posts.*, replies, lastPostID, topicTitle, users.userID, displayName, firstName, lastName, signature, avatar, posts', FALSE);

		// join users
		$this->db->join('users', 'forums_posts.userID = users.userID');
		$this->db->join('forums_topics', 'forums_posts.topicID = forums_topics.topicID');

		// order
		$this->db->order_by('dateCreated', 'desc');

		// template type
		$query = $this->db->get('forums_posts');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_topic_size($topicID)
	{
		// default wheres
		$where = array(
			'deleted' => 0,
			'forums_posts.siteID' => $this->siteID,
			'forums_posts.topicID' => $topicID
		);

		// grab total
		$this->db->where($where);
		$query_total = $this->db->get('forums_posts');

		if ($totalRows = $query_total->num_rows())
		{
			return $totalRows;
		}
		else
		{
			return FALSE;
		}
	}

	function get_post($postID)
	{
		// select
		$this->db->select('forums_posts.*, displayName, firstName, lastName, topicTitle, forums_topics.dateCreated as topicDate', FALSE);

		// default wheres
		$this->db->where('forums_posts.deleted', 0);
		$this->db->where('forums_posts.siteID', $this->siteID);

		$this->db->where('forums_posts.postID', $postID);

		// join topic
		$this->db->join('forums_topics', 'forums_posts.topicID = forums_topics.topicID');
		$this->db->join('users', 'forums_posts.userID = users.userID');
		$this->db->group_by('postID');

		// grab
		$query = $this->db->get('forums_posts', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return false;
		}
	}

	function search_forums($query)
	{
		// make sure query is greater than 2 (otherwise load will be too high)
		if (strlen($query) > 2)
		{
			// default wheres
			$where = array(
				'forums_posts.deleted' => 0,
				'forums_posts.siteID' => $this->siteID,
			);

			// grab total
			$this->db->where($where);

			// search
			$this->db->like('forums_topics.topicTitle', $query);
			$this->db->or_like('forums_posts.body', $query);

			// select
			$this->db->select('forums_posts.topicID, topicTitle', FALSE);

			// join topics
			$this->db->join('forums_topics', 'forums_posts.topicID = forums_topics.topicID');

			// stuff
			$this->db->order_by('forums_posts.dateCreated', 'desc');
			$this->db->group_by('forums_posts.topicID');

			// grab
			$query = $this->db->get('forums_posts');

			if ($query->num_rows())
			{
				$result = $query->result_array();

				foreach($result as $row)
				{
					$topicIDs[] = $row['topicID'];
				}

				return $topicIDs;
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

	function add_topic($forumID, $topicID, $postID)
	{
		// add topic count
		$this->db->set('topics', 'topics+1', false);
		$this->db->set('lastPostID', $postID);
		$this->db->where('forumID', $forumID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums');

		// add last post to topic
		$this->db->set('lastPostID', $postID);
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		// add to post count of user
		$this->db->set('posts', 'posts+1', false);
		$this->db->where('userID', $this->session->userdata('userID'));
		$this->db->update('users');

		return true;
	}

	function add_reply($topicID, $forumID, $postID)
	{
		// add reply count
		$this->db->set('replies', 'replies+1', false);
		$this->db->set('lastPostID', $postID);
		$this->db->set('dateModified', date("Y-m-d H:i:s"));
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		// add reply count to forum
		$this->db->set('replies', 'replies+1', false);
		$this->db->set('lastPostID', $postID);
		$this->db->where('forumID', $forumID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums');

		// add to post count of user
		$this->db->set('posts', 'posts+1', false);
		$this->db->where('userID', $this->session->userdata('userID'));
		$this->db->update('users');

		return true;
	}

	function add_view($topicID)
	{
		// add topic count
		$this->db->set('views', 'views+1', false);
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		return true;
	}

	function minus_reply($topicID, $forumID, $deleteTopic = FALSE)
	{
		// get posts data
		$posts = $this->get_all_posts($topicID);

		// minus reply count
		if ($deleteTopic === TRUE)
		{
			$replies = sizeof($posts)-1;
		}
		else
		{
			$replies = 1;
		}
		$this->db->set('replies', 'replies-'.$replies, false);
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		// minus reply count to forum
		$this->db->set('replies', 'replies-'.$replies, false);
		if ($deleteTopic === TRUE)
		{
			$this->db->set('topics', 'topics-1', false);
		}
		$this->db->where('forumID', $forumID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums');

		return TRUE;
	}

	function move_topic($topicID, $forumID)
	{
		// check it's a valid topic
		$query = $this->db->get_where('forums_topics', array('topicID' => $topicID), 1);
		if ($query->num_rows())
		{
			$topicRow = $query->row_array();

			// get post count
			$this->db->where(array('topicID' => $topicID));
			$query = $this->db->get('forums_posts');
			$posts = $query->num_rows();
			$replies = $posts - 1;

			// check it's a valid forum
			$query = $this->db->get_where('forums', array('forumID' => $forumID), 1);
			if ($query->num_rows())
			{
				// update new forum topic count
				$this->db->set('topics', 'topics+1', FALSE);
				$this->db->set('replies', 'replies+'.$replies, FALSE);
				$this->db->where('forumID', $forumID);
				$this->db->where('siteID', $this->siteID);
				$this->db->update('forums');

				// update old forum topic count
				$this->db->set('topics', 'topics-1', FALSE);
				$this->db->set('replies', 'replies-'.$replies, FALSE);
				$this->db->where('forumID', $topicRow['forumID']);
				$this->db->where('siteID', $this->siteID);
				$this->db->update('forums');

				// move
				$this->db->set('forumID', $forumID);
				$this->db->where('topicID', $topicID);
				$this->db->where('siteID', $this->siteID);
				$this->db->update('forums_topics');

				return TRUE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function lock_topic($topicID)
	{
		// add topic count
		$this->db->set('locked', '1');
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		return true;
	}

	function unlock_topic($topicID)
	{
		// add topic count
		$this->db->set('locked', '0');
		$this->db->where('topicID', $topicID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('forums_topics');

		return true;
	}

	function update_tags($postID = '', $tags = '')
	{
		// add tags
		if ($tags)
		{
			$this->tags->delete_tag_ref(
			array(
				'table' => 'forums_posts',
				'row_id' => $postID,
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
		 		'table' => 'forums_posts',
		 		'tags' => $tidyTagsArray,
				'row_id' => $postID,
				'siteID' => $this->siteID
			);
			$this->tags->add_tags($tags);

			return true;
		}
		else
		{
			return false;
		}
	}

	function get_headlines($num = 10)
	{
		$this->db->select('postTitle, uri, dateCreated');
		return $this->get_posts($num);
	}

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

	function get_subscriptions($topicID)
	{
		// where
		$this->db->where('topicID', $topicID);

		// grab
		$query = $this->db->get('forums_subs');

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

	function get_emails($userIDs)
	{
		// default wheres
		$where = array(
			'siteID' => $this->siteID,
			'notifications' => 1
		);

		// select
		$this->db->select('email, firstName, lastName');

		// lookup userIDs
		$this->db->where_in('users.userID', $userIDs);
		$this->db->where('userID !=', $this->session->userdata('userID'));

		// grab
		$query = $this->db->get('users');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function add_subscription($topicID, $userID)
	{
		$this->db->set('topicID', $topicID);
		$this->db->set('userID', $userID);
		$this->db->set('siteID', $this->siteID);
		$this->db->insert('forums_subs');

		return TRUE;
	}

	function remove_subscription($topicID, $userID)
	{
		$this->db->where('topicID', $topicID);
		$this->db->where('userID', $userID);
		$this->db->delete('forums_subs');

		return TRUE;
	}
}
