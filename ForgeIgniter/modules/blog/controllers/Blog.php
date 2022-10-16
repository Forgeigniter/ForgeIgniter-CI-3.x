<?php
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2020, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link      http://forgeigniter.com/
 */
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Blog extends MX_Controller
{
    //declare properties
    protected $CI;
    protected $siteID;
    protected $partials = array();
    protected $sitePermissions = array();
    protected $num = 10;

    public function __construct()
    {
        parent::__construct();

        $this->CI = get_instance();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // get site permissions and redirect if it don't have access to this module
        if (!$this->permission->sitePermissions) {
            show_error('You do not have permission to view this page');
        }
        if (!in_array($this->uri->segment(1), $this->permission->sitePermissions)) {
            show_error('You do not have permission to view this page');
        }

        // load models and modules
        $this->load->library('tags');
        $this->load->model('blog_model', 'blog');
        $this->load->module('pages');

        // load config
        $this->CI->load->config('blog/blog_config');

        // load partials - categories
        if ($cats = $this->blog->get_cats()) {
            foreach ($cats as $cat) {
                $this->partials['blog:categories'][] = array(
                    'category:link' => site_url('/blog/'.$cat['catSafe']),
                    'category:title' => $cat['catName'],
                    'category:count' => $cat['numPosts']
                );
            }
        }

        // load partials - archive
        if ($archive = $this->blog->get_archive()) {
            foreach ($archive as $date) {
                $this->partials['blog:archive'][] = array(
                    'archive:link' => site_url('/blog/'.$date['year'].'/'.$date['month'].'/'),
                    'archive:title' => $date['dateStr'],
                    'archive:count' => $date['numPosts']
                );
            }
        }

        // load partials - latest
        if ($latest = $this->blog->get_headlines()) {
            foreach ($latest as $post) {
                $this->partials['blog:latest'][] = array(
                    'latest:link' => site_url('blog/'.dateFmt($post['dateCreated'], 'Y/m').'/'.$post['uri']),
                    'latest:title' => $post['postTitle'],
                    'latest:date' => dateFmt($post['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                );
            }
        }

        // get tags
        if ($popularTags = $this->tags->get_popular_tags('blog_posts')) {
            foreach ($popularTags as $tag) {
                $this->partials['blog:tags'][] = array(
                    'tag' => $tag['tag'],
                    'tag:link' => site_url('/blog/tag/'.$tag['safe_tag']),
                    'tag:count' => $tag['count']
                );
            }
        }
    }

    public function index()
    {
        // get partials
        $output = $this->partials;

        // get latest posts
        $posts = $this->blog->get_posts($this->num);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // set title
        $output['page:title'] = 'Blog'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/more/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog', '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ?
                anchor('/blog/more/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with class
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/more/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog', '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/more/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function more()
    {
        // get partials
        $output = $this->partials;

        // get latest posts
        $posts = $this->blog->get_posts($this->num);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // set title
        $output['page:title'] = 'Blog'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/more/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog', '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/more/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with class
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/more/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog', '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/more/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function read()
    {
        // get partials
        $output = $this->partials;

        // get post based on uri
        $year = $this->uri->segment(2);
        $month = $this->uri->segment(3);
        $uri = $this->uri->segment(4);

        if ($post = $this->blog->get_post($year, $month, $uri)) {
            // add comment
            if (count($this->input->post())) {
                // required
                $this->core->required = array(
                    'fullName' => 'Full name',
                    'comment' => 'Comment',
                );

                // check for spam
                preg_match_all('/http:\/\//i', $this->input->post('comment'), $urlMatches);
                preg_match_all('/viagra|levitra|cialis/i', $this->input->post('comment'), $spamMatches);
                if (count($urlMatches[0]) > 2 || (count($urlMatches[0]) > 0 && count($spamMatches[0]) > 0)) {
                    $this->form_validation->set_error('Sorry but your comment looks like spam. Please remove links and try again.');
                } elseif (isset($this->input->post['captcha']) && $this->_captcha_check() !== false) {
                    $this->form_validation->set_error('Sorry you didn\'t pass the spam check. Please contact us to post a comment.');
                } else {
                    // set date
                    $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
                    $this->core->set['postID'] = $post['postID'];

                    // awaiting moderation
                    if ($this->session->userdata('session_admin')) {
                        $this->core->set['active'] = 1;
                    } else {
                        $this->core->set['active'] = 0;
                    }

                    // update
                    if ($this->core->update('blog_comments')) {
                        // get insertID
                        $commentID = $this->db->insert_id();

                        // get details on post poster
                        $user = $this->blog->get_user($post['userID']);

                        // construct URL
                        $url = '/blog/'.$year.'/'.$month.'/'.$uri.'/';

                        if ($user['notifications'] && !$this->session->userdata('session_admin')) {
                            // set header and footer
                            $emailHeader = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailHeader']);
                            $emailHeader = str_replace('{email}', $user['email'], $emailHeader);
                            $emailFooter = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailFooter']);
                            $emailFooter = str_replace('{email}', $user['email'], $emailFooter);

                            // send email
                            $this->load->library('email');
                            $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                            $this->email->to($user['email']);
                            $this->email->subject('New Blog Comment on '.$this->site->config['siteName']);
                            $this->email->message($emailHeader."\n\nSomeone has just commented on your blog post titled \"".$post['postTitle']."\".\n\nYou can either approve or delete this comment by clicking on the following URL:\n\n".site_url('/admin/blog/comments')."\n\nThey said:\n\"".$this->input->post('comment')."\"\n\n".$emailFooter);
                            $this->email->send();
                        }

                        // output message
                        $output['message'] = 'Thank you, your comment has been posted and is awaiting moderation.';

                        // disable form
                        $post['allowComments'] = 0;
                    }
                }
            }

            // set page title
            $output['page:title'] = $post['postTitle'].(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

            // Set Meta Description
            if ( isset($post['seo_description']) ) {
              $output['page:description'] = $post['seo_description'];
            } else {
              $output['page:description'] = $post['excerpt'];
            }

            // Set Meta Keywords
            $output['page:keywords'] = $post['seo_keywords'];

            // get author details
            $author = $this->blog->lookup_user($post['userID']);

            // populate template
            $output['post:title'] = $post['postTitle'];
            $output['post:link'] = site_url('blog/'.dateFmt($post['dateCreated'], 'Y/m').'/'.$post['uri']);
            $output['post:date'] = dateFmt($post['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
            $output['post:day'] = dateFmt($post['dateCreated'], 'd');
            $output['post:month'] = dateFmt($post['dateCreated'], 'M');
            $output['post:year'] = dateFmt($post['dateCreated'], 'y');
            $output['post:body'] = $this->template->parse_body($post['body']);
            $output['post:excerpt'] = $this->template->parse_body($post['excerpt']);
            $output['post:comments-count'] = $post['numComments'];
            $output['post:author'] = (($author['displayName']) ? $author['displayName'] : $author['firstName'].' '.$author['lastName']);
            $output['post:author-id'] = $author['userID'];
            $output['post:author-email'] = $author['email'];
            $output['post:author-avatar'] = anchor('/users/profile/'.$author['userID'], display_image($this->blog->get_user_avatar($author['avatar']), 'User Avatar', 100, 'class="bordered"', site_url().$this->config->item('staticPath').'/images/noavatar.gif'));
            $output['post:author-gravatar'] = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5(trim($author['email'])).'&default='.urlencode(site_url('/static/uploads/avatars/noavatar.gif'));
            $output['post:author-bio'] = empty($author['bio']) ? 'This Author has no bio set yet.' : $author['bio'];
            $output['post:allow-comments'] = ($post['allowComments']) ? true : false;
            $output['form:name'] = set_value('fullName', $this->session->userdata('firstName').' '.$this->session->userdata('lastName'));
            $output['form:email'] = set_value('email', $this->session->userdata('email'));
            $output['form:website'] = $this->input->post('website');
            $output['form:comment'] = $this->input->post('comment');

            // get cats
            if ($cats = $this->blog->get_cats_for_post($post['postID'])) {
                $i = 0;
                foreach ($cats as $cat) {
                    $output['post:categories'][$i]['category:link'] = site_url('blog/'.url_title(strtolower(trim($cat))));
                    $output['post:categories'][$i]['category'] = $cat;

                    $i++;
                }
            }

            // get tags
            if ($post['tags']) {
                $tags = explode(',', $post['tags']);

                $i = 0;
                foreach ($tags as $tag) {
                    $output['post:tags'][$i]['tag:link'] = site_url('blog/tag/'.$this->tags->make_safe_tag($tag));
                    $output['post:tags'][$i]['tag'] = $tag;

                    $i++;
                }
            }

            // get comments
            if ($comments = $this->blog->get_comments($post['postID'])) {
                $i = 0;
                foreach ($comments as $comment) {
                    $output['post:comments'][$i]['comment:class'] = ($i % 2) ? ' alt ' : '';
                    $output['post:comments'][$i]['comment:id'] = $comment['commentID'];
                    $output['post:comments'][$i]['comment:gravatar'] = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5(trim($comment['email'])).'&default='.urlencode(site_url('/static/uploads/avatars/noavatar.gif'));
                    $output['post:comments'][$i]['comment:author'] = (!empty($comment['website'])) ? anchor(prep_url($comment['website']), $comment['fullName']) : $comment['fullName'];
                    $output['post:comments'][$i]['comment:date'] = dateFmt($comment['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
                    $output['post:comments'][$i]['comment:body'] = nl2br(auto_link(strip_tags($comment['comment'])));

                    $i++;
                }
            }

            // load errors
            $output['errors'] = (validation_errors()) ? validation_errors() : false;

            // add view
            $this->blog->add_view($post['postID']);

            // output post ID for CMS
            $output['postID'] = $post['postID'];

            // display with cms layer
            $this->pages->view('blog_single', $output, true);
        } else {
            show_404();
        }
    }

    public function tag($tag = [])
    {
        // get partials
        $output = $this->partials;

        // get posts
        $posts = $this->blog->get_posts_by_tag($tag);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // output tag tags
        $output['tag:title'] = ucwords(str_replace('-', ' ', $tag));
        $output['tag:link'] = '/blog/tag/'.$tag;

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/tag/'.$tag.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog/tag/'.$tag, '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/tag/'.$tag.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with class
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/tag/'.$tag.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog/tag/'.$tag, '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/tag/'.$tag.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // set title
        $output['page:title'] = ucwords(str_replace('-', ' ', $tag)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = '<small>Tag:</small> '.ucfirst(str_replace('-', ' ', $tag));

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function category($cat = '')
    {
        // get partials
        $output = $this->partials;

        // get posts
        $posts = $this->blog->get_posts_by_category($cat);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // output category tags
        $output['category:title'] = ucwords(str_replace('-', ' ', $cat));
        $output['category:link'] = '/blog/'.$cat;

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$cat.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog/'.$cat, '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$cat.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with class
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$cat.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog/'.$cat, '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$cat.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // set title
        $output['page:title'] = ucwords(str_replace('-', ' ', $cat)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = '<small>Category:</small> '.ucwords(str_replace('-', ' ', $cat));

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function month()
    {
        // get partials
        $output = $this->partials;

        // get post based on uri
        $year = $this->uri->segment(2);
        $month = $this->uri->segment(3);

        // get posts
        $posts = $this->blog->get_posts_by_date($year, $month);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // set title
        $output['page:title'] = 'Posts For '.date('F Y', mktime(0, 0, 0, $month, 1, $year)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = '<small>Archive:</small> '.date('F Y', mktime(0, 0, 0, $month, 1, $year));

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$year.'/'.$month.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog/'.$year.'/'.$month, '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$year.'/'.$month.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with class
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$year.'/'.$month.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog/'.$year.'/'.$month, '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$year.'/'.$month.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function year()
    {
        // get partials
        $output = $this->partials;

        // get post based on uri
        $year = $this->uri->segment(2);

        // get tags
        $posts = $this->blog->get_posts_by_date($year);
        $output['blog:posts'] = $this->_populate_posts($posts);

        // set title
        $output['page:title'] = 'Posts For '.date('Y', mktime(0, 0, 0, 1, 1, $year)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = '<small>Archive:</small> '.date('Y', mktime(0, 0, 0, 1, 1, $year));

        // set pagination and breadcrumb
        $output['blog:newer'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$year.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer') :
                anchor('/blog/'.$year, '&laquo; Newer'))
        : '';
        $output['blog:older'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$year.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;') : '';

        // set pagination and breadcrumb with Button
        $output['blog:newer:btn'] = ($this->pagination->offset) ?
            (($this->pagination->offset - $this->num > 0) ?
                anchor('/blog/'.$year.'/page/'.($this->pagination->offset - $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) :
                anchor('/blog/'.$year, '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))))
        : '';
        $output['blog:older:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/'.$year.'/page/'.($this->pagination->offset + $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';

        // display with cms layer
        $this->pages->view('blog', $output, true);
    }

    public function search($query = '')
    {
        // get partials
        $output = $this->partials;

        // set tags
        $query = ($query) ? $query : strip_tags($this->input->post('query', true));

        // get result from tags
        $objectIDs = $this->tags->search('blog_posts', $query);

        $posts = $this->blog->search_posts($query, $objectIDs);
        $output['blog:posts'] = $this->_populate_posts($posts);
        $output['query'] = $query;

        // set title
        $output['page:title'] = 'Search the Blog'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = 'Search for "'.$output['query'].'"';

        // set pagination and breadcrumb
        $output['blog:older'] = ($this->pagination->offset) ? anchor('/blog/more/page/'.($this->pagination->offset - $this->num), 'Older &raquo;') : '';
        $output['blog:newer'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/more/page/'.($this->pagination->offset + $this->num), '&laquo; Newer') : '';

        // set pagination and breadcrumb with class
        $output['blog:older:btn'] = ($this->pagination->offset) ? anchor('/blog/more/page/'.($this->pagination->offset - $this->num), 'Older &raquo;', array('class' => $this->config->item('blogBtnClass'))) : '';
        $output['blog:newer:btn'] = ($this->pagination->total_rows > ($this->pagination->offset + $this->num)) ? anchor('/blog/more/page/'.($this->pagination->offset + $this->num), '&laquo; Newer', array('class' => $this->config->item('blogBtnClass'))) : '';

        // display with cms layer
        $this->pages->view('blog_search', $output, true);
    }

    public function feed($cat = '')
    {
        // rss feed
        $this->load->helper('xml');

        $data['encoding'] = 'utf-8';
        $data['page_language'] = 'en';
        $data['creator_email'] = $this->site->config['siteEmail'];

        // get posts by category
        if ($cat) {
            $category = ucwords(str_replace('-', ' ', $cat));

            $data['feed_name'] = $this->site->config['siteName'].' - '.$category;
            $data['feed_url'] = site_url('/blog/'.$cat);
            $data['page_description'] = 'Blog Category RSS Feed for '.$this->site->config['siteName'].' - '.$category.'.';

            $data['posts'] = $this->blog->get_posts_by_category($cat);
        }

        // get latest posts
        else {
            $data['feed_name'] = $this->site->config['siteName'];
            $data['feed_url'] = site_url('/blog');
            $data['page_description'] = 'Blog RSS Feed for '.$this->site->config['siteName'].'.';

            $data['posts'] = $this->blog->get_posts(10);
        }

        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('blog/rss', $data);
    }

    public function ac_search()
    {
        //Define Vars
        $items = null;

        $tags = strtolower($this->input->post["q"]);
        if (!$tags) {
            return false;
        }

        if ($objectIDs = $this->tags->search('blog_posts', $tags)) {
            // form dropdown and myql get countries
            if ($searches = $this->blog->search_posts($objectIDs)) {
                // go foreach
                foreach ($searches as $search) {
                    $items[$search['tags']] = array('id' => dateFmt($search['dateCreated'], 'Y/m').'/'.$search['uri'], 'name' => $search['postTitle']);
                }
                foreach ($items as $key=>$value) {
                    $id = $value['id'];
                    $name = $value['name'];
                    /* If you want to force the results to the query
                    if (strpos(strtolower($key), $tags) !== false)
                    {
                        echo "$key|$id|$name\n";
                    }*/
                    $this->output->set_output("$key|$id|$name\n");
                }
            }
        }
    }

    private function _captcha_check()
    {
        // if captcha is posted, check its not a bot (requires js)
        if ($this->input->post('captcha') == 'notabot') {
            return true;
        } elseif ($this->input->post('captcha') != 'notabot') {
            $this->form_validation->set_message('captcha_check', 'You didn\'t pass the spam check, please contact us to post a comment.');
            return false;
        }
    }

    private function _populate_posts($posts = '')
    {
        //Define Vars
        $data = null;

        if ($posts && is_array($posts)) {
            $x = 0;
            foreach ($posts as $post) {
                // get author details
                $author = $this->blog->lookup_user($post['userID']);

                // populate template array
                $data[$x] = array(
                'post:link' => site_url('blog/'.dateFmt($post['dateCreated'], 'Y/m').'/'.$post['uri']),
                'post:title' => $post['postTitle'],
                'post:date' => dateFmt($post['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
                'post:day' => dateFmt($post['dateCreated'], 'd'),
                'post:month' => dateFmt($post['dateCreated'], 'M'),
                'post:year' => dateFmt($post['dateCreated'], 'y'),
                'post:body' => $this->template->parse_body($post['body'], true, site_url('blog/'.dateFmt($post['dateCreated'], 'Y/m').'/'.$post['uri'])),
                'post:excerpt' => $this->template->parse_body($post['excerpt'], true, site_url('blog/'.dateFmt($post['dateCreated'], 'Y/m').'/'.$post['uri'])),
                'post:author' => (($author['displayName']) ? $author['displayName'] : $author['firstName'].' '.$author['lastName']),
                'post:author-id' => $author['userID'],
                'post:author-email' => $author['email'],
                // Replace with proper avatar
                'post:author-gravatar' => 'http://www.gravatar.com/avatar.php?gravatar_id='.md5(trim($author['email'])).'&default='.urlencode(site_url('/static/uploads/avatars/noavatar.gif')),
                'post:author-bio' => $author['bio'],
                'post:comments-count' => $post['numComments']
            );

                // get cats
                if ($cats = $this->blog->get_cats_for_post($post['postID'])) {
                    $i = 0;
                    foreach ($cats as $cat) {
                        $data[$x]['post:categories'][$i]['category:link'] = site_url('blog/'.url_title(strtolower(trim($cat))));
                        $data[$x]['post:categories'][$i]['category'] = $cat;

                        $i++;
                    }
                }

                // get tags
                if ($post['tags']) {
                    $tags = explode(',', $post['tags']);

                    $i = 0;
                    foreach ($tags as $tag) {
                        $data[$x]['post:tags'][$i]['tag:link'] = site_url('blog/tag/'.$this->tags->make_safe_tag($tag));
                        $data[$x]['post:tags'][$i]['tag'] = $tag;

                        $i++;
                    }
                }

                $x++;
            }

            return $data;
        } else {
            return false;
        }
    }
}
