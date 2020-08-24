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
 defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Blog_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }
    }

    public function get_all_posts()
    {
        $this->db->where('published', 1);
        $this->db->where('deleted', 0);
        $this->db->where('siteID', $this->siteID);

        $query = $this->db->get('blog_posts');

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result;
        } else {
            return false;
        }
    }

    public function get_posts($num = 10)
    {
        // start cache
        $this->db->start_cache();

        // default where
        $this->db->where(array(
            'published' => 1,
            'deleted' => 0,
            'siteID' => $this->siteID
        ));

        // order
        $this->db->order_by('dateCreated', 'desc');

        // stop cache
        $this->db->stop_cache();

        // get total rows
        $query = $this->db->get('blog_posts');
        $totalRows = $query->num_rows();

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);

        // init paging
        $this->core->set_paging($totalRows, $num);
        $query = $this->db->get('blog_posts', $num, $this->pagination->offset);

        // flush cache
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_posts_by_tag($tag, $limit = 10)
    {
        // get rows based on this tag
        $tags = $this->tags->fetch_rows(array(
            'table' => 'blog_posts',
            'tags' => array(1, $tag),
            'siteID' => $this->siteID
        ));
        if (!$tags) {
            return false;
        }

        // build tags array
        foreach ($tags as $tag) {
            $tagsArray[] = $tag['row_id'];
        }

        // default where
        $this->db->start_cache();
        $this->db->where(array(
            'published' => 1,
            'deleted' => 0,
            'siteID' => $this->siteID
        ));

        // where tags
        $this->db->where_in('postID', $tagsArray);
        $this->db->order_by('dateCreated', 'desc');

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);
        $this->db->stop_cache();

        $query = $this->db->get('blog_posts');
        $totalRows = $query->num_rows();

        // init paging
        $this->core->set_paging($totalRows, $limit);
        $query = $this->db->get('blog_posts', $limit, $this->pagination->offset);
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_posts_by_category($cat, $limit = 10)
    {
        // get cat IDs
        if (!$postsArray = $this->get_catmap_post_ids($cat)) {
            return false;
        }

        // stop cache
        $this->db->start_cache();

        // default where
        $this->db->where(array(
            'published' => 1,
            'deleted' => 0,
            'siteID' => $this->siteID
        ));

        // where category
        $this->db->where_in('postID', $postsArray);

        // order
        $this->db->order_by('dateCreated', 'desc');

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);

        // stop cache
        $this->db->stop_cache();

        // get total rows
        $query = $this->db->get('blog_posts');
        $totalRows = $query->num_rows();

        // init paging
        $this->core->set_paging($totalRows, $limit);
        $query = $this->db->get('blog_posts', $limit, $this->pagination->offset);

        // flush cache
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_catmap_post_ids($cat)
    {
        // get rows based on this category
        $this->db->join('blog_cats', 'blog_cats.catID = blog_catmap.catID');
        $this->db->where('blog_cats.catSafe', $cat);

        // get result
        $result = $this->db->get('blog_catmap');

        if ($result->num_rows()) {
            $cats = $result->result_array();

            foreach ($cats as $cat) {
                $postsArray[] = $cat['postID'];
            }

            return $postsArray;
        } else {
            return false;
        }
    }

    public function get_posts_by_date($year, $month = '', $limit = 10)
    {
        if ($month) {
            $next_month = $month + 1;
            $from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 0, $year));
            $to =  date("Y-m-d H:i:s", mktime(23, 59, 59, $next_month, 0, $year));
        } else {
            $from =  date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 0, $year));
            $to =  date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 0, ($year+1)));
        }

        $this->db->start_cache();
        $this->db->where('dateCreated >', $from);
        $this->db->where('dateCreated <', $to);
        $this->db->where('published', 1);
        $this->db->where('deleted', 0);
        $this->db->where('siteID', $this->siteID);

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);
        $this->db->stop_cache();

        $query = $this->db->get('blog_posts');
        $totalRows = $query->num_rows();

        // init paging
        $this->core->set_paging($totalRows, $limit);
        $query = $this->db->get('blog_posts', $limit, $this->pagination->offset);
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_post_by_title($title = '', $limit = 10)
    {
        $this->db->start_cache();
        $this->db->where('postTitle', $title);
        $this->db->where('published', 1);
        $this->db->where('deleted', 0);
        $this->db->where('siteID', $this->siteID);

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);
        $this->db->stop_cache();

        $query = $this->db->get('blog_posts');

        // init paging
        $this->core->set_paging($totalRows, $limit);
        $query = $this->db->get('blog_posts', $limit, $this->pagination->offset);
        $this->db->flush_cache();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function get_post($year, $month, $uri)
    {
        $next_month = $month + 1;

        $from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 0, $year));
        $to =  date("Y-m-d H:i:s", mktime(23, 59, 59, $next_month, 0, $year));

        $this->db->where('dateCreated >', $from);
        $this->db->where('dateCreated <', $to);
        $this->db->where('uri', $uri);

        if (!$this->session->userdata('session_admin')) {
            $this->db->where('published', 1);
        }

        $this->db->where('deleted', 0);
        $this->db->where('siteID', $this->siteID);

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);

        $query = $this->db->get('blog_posts', 1);

        if ($query->num_rows() == 1) {
            $post = $query->row_array();

            return $post;
        } else {
            return false;
        }
    }

    public function get_post_by_id($postID)
    {
        $this->db->where('postID', $postID);

        $query = $this->db->get('blog_post', 1);

        if ($query->num_rows()) {
            $post = $query->row_array();

            return $post;
        } else {
            return false;
        }
    }

    public function get_tags()
    {
        $this->db->join('tags_ref', 'tags_ref.tag_id = tags.id');
        $this->db->where('tags_ref.siteID', $this->siteID);

        $query = $this->db->get('tags');

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function update_cats($postID, $catsArray = '')
    {
        $this->db->delete('blog_catmap', array('postID' => $postID, 'siteID' => $this->siteID));

        if ($catsArray) {
            foreach ($catsArray as $cat) {
                if ($cat) {
                    $cat = trim(htmlentities($cat));

                    $query = $this->db->get_where('blog_cats', array('catName' => $cat, 'siteID' => $this->siteID));

                    if (!$query->num_rows()) {
                        $this->db->insert('blog_cats', array('catName' => $cat, 'catSafe' => url_title(strtolower(trim($cat))), 'siteID' => $this->siteID));
                        $catID = $this->db->insert_id();
                    } else {
                        $row = $query->row_array();
                        $catID = $row['catID'];
                    }

                    $query = $this->db->get_where('blog_catmap', array('postID' => $postID, 'catID' => $catID, 'siteID' => $this->siteID));

                    if (!$query->num_rows()) {
                        $this->db->insert('blog_catmap', array('postID' => $postID, 'catID' => $catID, 'siteID' => $this->siteID));
                    }
                }
            }
        }

        return true;
    }

    public function get_categories($catID = '')
    {
        // default where
        $this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));
        $this->db->order_by('catOrder');

        // get based on category ID
        if ($catID) {
            $query = $this->db->get_where('blog_cats', array('catID' => $catID), 1);

            if ($query->num_rows()) {
                return $query->row_array();
            } else {
                return false;
            }
        }
        // or just get all of em
        else {
            // template type
            $query = $this->db->get('blog_cats');

            if ($query->num_rows()) {
                return $query->result_array();
            } else {
                return false;
            }
        }
    }

    public function get_cats()
    {
        $this->db->select('(SELECT COUNT(*) FROM '.$this->db->dbprefix.'blog_posts JOIN '.$this->db->dbprefix.'blog_catmap USING(postID) WHERE '.$this->db->dbprefix.'blog_catmap.catID = '.$this->db->dbprefix.'blog_cats.catID AND '.$this->db->dbprefix.'blog_posts.deleted = 0 AND published = 1) AS numPosts, catName, catSafe');
        $this->db->join('blog_catmap', 'blog_cats.catID = blog_catmap.catID', 'left');
        $this->db->where('blog_cats.deleted', 0);
        $this->db->group_by('catSafe');
        $this->db->order_by('catName');
        $this->db->where('blog_cats.siteID', $this->siteID);

        $query = $this->db->get('blog_cats');

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_cats_for_post($postID)
    {
        // get cats for this post
        $this->db->join('blog_cats', 'blog_catmap.catID = blog_cats.catID', 'left');
        $this->db->order_by('catOrder');
        $query = $this->db->get_where('blog_catmap', array('postID' => $postID));

        $catsArray = $query->result_array();
        $cats = array();

        foreach ($catsArray as $cat) {
            $cats[$cat['catID']] = $cat['catName'];
        }

        return $cats;
    }

    public function parse_post($body, $condense = false, $uri = '')
    {
        if ($condense) {
            if ($endchr = strpos($body, '{more}')) {
                $body = substr($body, 0, ($endchr + 6));
                $body = str_replace('{more}', '<p><strong><a href="'.$uri.'" class="button">Read more</a></strong></p>', $body);
            }
        } else {
            $body = str_replace('{more}', '', $body);
        }

        $body = $this->parse_images($body);

        $body = mkdn($body);

        return $body;
    }

    public function get_archive($limit = 20)
    {
        $this->db->select('COUNT(postID) as numPosts, DATE_FORMAT(dateCreated, "%M %Y") as dateStr, DATE_FORMAT(dateCreated, "%m") as month, DATE_FORMAT(dateCreated, "%Y") as year, (SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments', false);
        $this->db->where('published', 1);
        $this->db->where('deleted', 0);
        $this->db->where('siteID', $this->siteID);

        $this->db->order_by('dateCreated', 'desc');
        $this->db->group_by('dateStr');

        $query = $this->db->get('blog_posts');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_headlines($num = 10)
    {
        // default where
        $this->db->where(array(
            'published' => 1,
            'deleted' => 0,
            'siteID' => $this->siteID
        ));
        $this->db->order_by('dateCreated', 'desc');

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*', false);

        $query = $this->db->get('blog_posts', $num);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_latest_comments($postID = '')
    {
        // get comments based on a post
        if ($postID) {
            $this->db->where('t1.postID', $postID, false);
            $this->db->where('t1.active', 1, false);
        }

        $this->db->select('t1.*, t2.postTitle, t2.dateCreated as uriDate, t2.uri');
        $this->db->from('blog_comments t1');
        $this->db->limit(30);

        $this->db->where('t1.deleted', 0, false);
        $this->db->where('t1.siteID', $this->siteID, false);

        $this->db->join('blog_posts t2', 't2.postID = t1.postID');

        $this->db->order_by('t1.dateCreated', 'desc');

        $query = $this->db->get();

        $comments = array();

        if ($query->num_rows() > 0) {
            $comments = $query->result_array();
        }

        return $comments;
    }

    public function get_comments($postID = '')
    {
        // get comments based on a post
        if ($postID) {
            $this->db->where('t1.postID', $postID, false);
            $this->db->where('t1.active', 1, false);
        }

        // select
        $this->db->select('t1.*, t2.postTitle, t2.dateCreated as uriDate, t2.uri');
        $this->db->from('blog_comments t1');

        $this->db->where('t1.deleted', 0, false);
        $this->db->where('t1.siteID', $this->siteID, false);

        // join revisions
        $this->db->join('blog_posts t2', 't2.postID = t1.postID');

        $this->db->order_by('t1.dateCreated', 'asc');


        $query = $this->db->get();

        $comments = array();

        if ($query->num_rows() > 0) {
            $comments = $query->result_array();
        }

        return $comments;
    }

    public function approve_comment($commentID)
    {
        $this->db->set('active', 1);
        $this->db->where('siteID', $this->siteID);
        $this->db->where('commentID', $commentID);

        $this->db->update('blog_comments');

        return true;
    }

    public function get_user($userID)
    {
        $query = $this->db->get_where('users', array('userID' => $userID), 1);

        if ($query->num_rows()) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    public function lookup_user($userID, $display = false)
    {
        // default wheres
        $this->db->where('userID', $userID);

        // grab
        $query = $this->db->get('users', 1);

        if ($query->num_rows()) {
            $row = $query->row_array();

            if ($display !== false) {
                return ($row['displayName']) ? $row['displayName'] : $row['firstName'].' '.$row['lastName'];
            } else {
                return $row;
            }
        } else {
            return false;
        }
    }

    //Grab Users Avatar
    public function get_user_avatar($filename)
    {
        $site_base_path = realpath('');
        $pathToAvatars = $this->uploads->uploadsPath.'/avatars/';
        if (is_file('.'.$pathToAvatars.$filename)) {
            $avatar = $pathToAvatars.$filename;
        } else {
            $avatar = $site_base_path.$pathToAvatars.'noavatar.gif';
        }
        return $avatar;
    }

    public function search_posts($query = '', $ids = '')
    {
        if (!$query && !$ids) {
            return false;
        }

        // default wheres
        $this->db->where('deleted', 0);
        $this->db->where('published', 1);
        $this->db->where('siteID', $this->siteID);

        // search
        if ($query) {
            // tidy query
            $q = $this->db->escape_like_str($query);

            $sql = '(postTitle LIKE "%'.$q.'%" OR body LIKE "%'.$q.'%")';
        }
        if ($ids) {
            $sql .= ' OR postID IN ('.implode(',', $ids).')';
        }
        $this->db->where($sql);

        $this->db->order_by('dateCreated', 'desc');

        // get comment count and post data
        $this->db->select('(SELECT COUNT(*) from '.$this->db->dbprefix.'blog_comments where '.$this->db->dbprefix.'blog_comments.postID = '.$this->db->dbprefix.'blog_posts.postID and deleted = 0 and active = 1) AS numComments, blog_posts.*');

        $query = $this->db->get('blog_posts');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function add_view($pageID)
    {
        $this->db->set('views', 'views+1', false);
        $this->db->where('postID', $pageID);
        $this->db->where('siteID', $this->siteID);
        $this->db->update('blog_posts');
    }
}
