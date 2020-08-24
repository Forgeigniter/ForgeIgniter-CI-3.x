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

class Forums extends MX_Controller
{
    public $partials = array();
    public $permissions = array();
    public $sitePermissions = array();

    public function __construct()
    {
        parent::__construct();

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

        // get permissions for the logged in admin
        if ($this->session->userdata('session_admin')) {
            $this->permission->permissions = $this->permission->get_group_permissions($this->session->userdata('groupID'));
        }

        // load models and modules
        $this->load->model('forums_model', 'forums');
        $this->load->module('pages');
        $this->load->library('mkdn');
        $this->load->helper('bbcode');

        // Set Pagination Stuff
        $this->config->load('forum_config', true);
        $config['total_rows'] = $this->config->item('total_rows', 'forum_config');
        $config['per_page'] = $this->config->item('per_page', 'forum_config');
        // Display Page Number
        $config['display_pages'] = $this->config->item('display_pages', 'forum_config');
        // Previous Page
        $config['prev_tag_open'] = $this->config->item('prev_tag_open', 'forum_config');
        $config['prev_tagl_close'] = $this->config->item('prev_tagl_close', 'forum_config');
        $config['prev_link'] = $this->config->item('prev_link', 'forum_config');
        // Current Page
        $config['cur_tag_open'] = $this->config->item('cur_tag_open', 'forum_config');
        $config['cur_tag_close'] = $this->config->item('cur_tag_close', 'forum_config');
        // Digit
        $config['num_tag_open'] = $this->config->item('num_tag_open', 'forum_config');
        $config['num_tag_close'] = $this->config->item('num_tag_close', 'forum_config');
        // Next Page
        $config['next_tag_open'] = $this->config->item('next_tag_open', 'forum_config');
        $config['next_tag_close'] = $this->config->item('next_tag_close', 'forum_config');
        $config['next_link'] = $this->config->item('next_link', 'forum_config');
        // Last Link
        $config['last_link'] = $this->config->item('last_link', 'forum_config');
        $config['last_tag_open'] = $this->config->item('last_tag_open', 'forum_config');
        $config['last_tag_close'] = $this->config->item('last_tag_close', 'forum_config');

        $this->pagination->initialize($config);
    }

    public function index()
    {
        // get partials
        $output = $this->partials;

        // see what categories there are
        if ($categories = $this->forums->get_categories()) {
            foreach ($categories as $category) {
                // get forums
                if ($forums = $this->forums->get_forums($category['catID'])) {
                    // get category name
                    $output['categories'][$category['catID']]['category:title'] = $category['catName'];

                    foreach ($forums as $forum) {
                        if ($forum['groupID'] > 0 && @!in_array('forums', $this->permission->permissions) && $forum['groupID'] != $this->session->userdata('groupID')) {
                            $output['categories'][$category['catID']]['category:forums'] = array();
                        } else {
                            $output['categories'][$category['catID']]['category:forums'][] = array(
                                'forum:title' => $forum['forumName'],
                                'forum:link' => site_url('/forums/viewforum/'.$forum['forumID']),
                                'forum:description' => $forum['description'],
                                'forum:topics' => $forum['topics'],
                                'forum:replies' => $forum['replies'],
                                'forum:latest-post' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? anchor('/forums/viewpost/'.$latestPost['postID'], $latestPost['topicTitle']) : "",
                                'forum:latest-date' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? dateFmt($latestPost['dateCreated']) : "",
                                'forum:latest-time' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? timeFmt($latestPost['dateCreated']) : "",
                                'forum:latest-user' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? anchor('/users/profile/'.$latestPost['userID'], (($latestPost['displayName']) ? $latestPost['displayName'] : $latestPost['firstName'].' '.$latestPost['lastName'])) : ''
                            );
                        }
                    }
                }
            }
        } else {
            // set the name of the array
            $forumArray = 'forums';

            if ($forums = $this->forums->get_forums()) {
                foreach ($forums as $forum) {
                    if ($forum['groupID'] > 0 && @!in_array('forums', $this->permission->permissions) && $forum['groupID'] != $this->session->userdata('groupID')) {
                        $output['forums'][] = array();
                    } else {
                        $output['forums'][] = array(
                            'forum:title' => $forum['forumName'],
                            'forum:link' => site_url('/forums/viewforum/'.$forum['forumID']),
                            'forum:description' => $forum['description'],
                            'forum:topics' => $forum['topics'],
                            'forum:replies' => $forum['replies'],
                            'forum:latest-post' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? anchor('/forums/viewpost/'.$latestPost['postID'], $latestPost['topicTitle']) : "",
                            'forum:latest-date' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? dateFmt($latestPost['dateCreated']) : "",
                            'forum:latest-time' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? timeFmt($latestPost['dateCreated']) : "",
                            'forum:latest-user' => ($latestPost = $this->forums->get_post($forum['lastPostID'])) ? anchor('/users/profile/'.$latestPost['userID'], (($latestPost['displayName']) ? $latestPost['displayName'] : $latestPost['firstName'].' '.$latestPost['lastName'])) : ''
                        );
                    }
                }
            } else {
                show_error('There are no forums set up yet.');
            }
        }

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Community Forums';

        // display with cms layer
        $this->pages->view('forums', $output, true);
    }

    public function viewforum($forumID = '')
    {
        // get forum info and redirect if forum isn't set
        if (!$forumID || !$forum = $this->forums->get_forum($forumID)) {
            redirect('/forums');
        }

        // check its not a private forum
        if ($forum['groupID'] > 0 && (@!in_array('forums', $this->permission->permissions) && $forum['groupID'] != $this->session->userdata('groupID'))) {
            redirect('/forums');
        }

        // get topics
        if ($topics = $this->forums->get_topics($forumID)) {
            foreach ($topics as $topic) {
                $output['topics'][] = array(
                    'topic:title' => (($topic['sticky']) ? '<small>Sticky:</small> ' : '').$topic['topicTitle'],
                    'topic:link' => site_url('/forums/viewtopic/'.$topic['topicID']),
                    'topic:user' => anchor('/users/profile/'.$topic['userID'], ($this->forums->lookup_user($topic['userID'], true))),
                    'topic:class' => ($topic['sticky']) ? 'sticky' : '',
                    'topic:replies' => $topic['replies'],
                    'topic:views' => $topic['views'],
                    'topic:latest-post' => ($latestPost = $this->forums->get_post($topic['lastPostID'])) ? '<small>Posted: '.dateFmt($latestPost['dateCreated']).' by '.anchor('/users/profile/'.$latestPost['userID'], ($latestPost['displayName']) ? $latestPost['displayName'] : $latestPost['firstName'].' '.$latestPost['lastName']).'</small>' : '',
                    'topic:latest-date' => ($latestPost = $this->forums->get_post($topic['lastPostID'])) ? dateFmt($latestPost['dateCreated']) : '',
                    'topic:latest-time' => ($latestPost = $this->forums->get_post($topic['lastPostID'])) ? timeFmt($latestPost['dateCreated']) : '',
                    'topic:latest-user' => ($latestPost = $this->forums->get_post($topic['lastPostID'])) ? anchor('/users/profile/'.$latestPost['userID'], ($latestPost['displayName']) ? $latestPost['displayName'] : $latestPost['firstName'].' '.$latestPost['lastName']) : ''
                );
            }
        }

        // populate template
        $output['forum:title'] = $forum['forumName'];
        $output['forum:id'] = $forum['forumID'];

        // set title
        $output['page:title'] = $forum['forumName'].' | Community Forums';

        // set feed
        $output['page:feed'] = '<link rel="alternate" type="application/rss+xml" title="RSS" href="/forums/viewforum/'.$this->uri->segment(3).'/feed" />';

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
        $output['breadcrumb'] = ((isset($forum['catName'])) ? anchor('/forums', $forum['catName']) : anchor('/forums', 'Forums')).' &gt; '.anchor('/forums/viewforum/'.$forum['forumID'], $forum['forumName']);

        // display with cms layer
        $this->pages->view('forums_forum', $output, true);
    }

    public function viewtopic($topicID = '')
    {
        // get forum info and redirect if forum isn't set
        if (!$topicID || !$topic = $this->forums->get_topic($topicID)) {
            redirect('/forums');
        }

        // get forum info
        $forum = $this->forums->get_forum($topic['forumID']);

        // check its not a private forum
        if ($forum['groupID'] > 0 && (@!in_array('forums', $this->permission->permissions) && $forum['groupID'] != $this->session->userdata('groupID'))) {
            redirect('/forums');
        }

        // get posts
        if ($posts = $this->forums->get_posts($topicID)) {
            foreach ($posts as $post) {
                $output['posts'][] = array(
                    'post:id' => $post['postID'],
                    'post:date' => dateFmt($post['dateCreated']),
                    'post:links' => anchor('/forums/addreply/'.$post['topicID'].'/'.$post['postID'], 'Quote').' | '.
                    (
                        ($this->session->userdata('userID') == $post['userID'] || @in_array('forums', $this->permission->permissions)) ?
                        anchor('/forums/editpost/'.$post['postID'], 'Edit').' | '.anchor('/forums/deletepost/'.$post['postID'], 'Delete') : ''
                    ),
                    'post:body' => bbcode($post['body']),
                    'user:name' => anchor('/users/profile/'.$post['userID'], (($post['displayName']) ? $post['displayName'] : $post['firstName'].' '.$post['lastName'])),
                    'user:group' => ($post['groupName']) ? $post['groupName'] : '',
                    'user:avatar' => anchor('/users/profile/'.$post['userID'], display_image($this->forums->get_avatar($post['avatar']), 'post Avatar', 200, 'class="avatar"', base_url().$this->config->item('staticPath').'/images/noavatar.gif')),
                    'user:joined' => dateFmt($post['joined']),
                    'user:posts' => $post['posts'],
                    'user:kudos' => $post['kudos'],
                    'user:signature' => ($post['signature']) ? '<hr /><small>'.bbcode($post['signature']).'</small>' : ''
                );
            }
        };

        // get subscriptions
        $subscriptions = $this->forums->get_subscriptions($topicID);

        // load add topic form
        if ($this->session->userdata('session_user') && @in_array('forums', $this->permission->permissions)) {
            // move topic
            if (count($_POST) && $this->input->post('moveTopic') && intval($this->input->post('forumID'))) {
                // update
                if ($this->forums->move_topic($topicID, $this->input->post('forumID'))) {
                    redirect('/forums/viewforum/'.$this->input->post('forumID'));
                }
            }

            // lock topic
            if (count($_POST) && $this->input->post('lockTopic')) {
                // update
                if ($this->forums->lock_topic($topicID)) {
                    redirect('/forums/viewtopic/'.$topicID);
                }
            }

            // unlock topic
            if (count($_POST) && $this->input->post('unlockTopic')) {
                // update
                if ($this->forums->unlock_topic($topicID)) {
                    redirect('/forums/viewtopic/'.$topicID);
                }
            }
        }

        // add view
        $this->forums->add_view($topicID);

        // populate template
        $output['forum:title'] = $forum['forumName'];
        $output['forum:id'] = $forum['forumID'];
        $output['topic:title'] = $topic['topicTitle'] . (($topic['userID'] == $this->session->userdata('userID') || @in_array('forums', @$this->permission->permissions)) ?
            anchor('/forums/edittopic/'.$topic['topicID'], ' <small>(edit)</small>') : '');
        $output['topic:id'] = $topic['topicID'];
        $output['topic:subscribed'] = (@in_array($this->session->userdata('userID'), $subscriptions)) ? true : false;
        $output['topic:locked'] = ($topic['locked']) ? true : false;

        // get categories
        if ($categories = $this->forums->get_categories()) {
            foreach ($categories as $category) {
                $options['C'.$category['catID']] = '**'.$category['catName'].'**';
                $catforums = $this->forums->get_forums($category['catID']);
                foreach ($catforums as $catforum) {
                    $options[$catforum['forumID']] = $catforum['forumName'];
                }
            }
            $output['select:forums'] = @form_dropdown('forumID', $options, '', 'id="category" class="formelement"');
        } elseif ($forums = $this->forums->get_forums()) {
            foreach ($forums as $catforum) {
                $options[$catforum['forumID']] = $catforum['forumName'];
            }
            $output['select:forums'] = @form_dropdown('forumID', $options, '', 'id="category" class="formelement"');
        }

        // set permissions
        $output['moderator'] = (@in_array('forums', @$this->permission->permissions)) ? true : false;

        // set title
        $output['page:title'] = $topic['topicTitle'].' | Community Forums';

        // set feed
        $output['page:feed'] = '<link rel="alternate" type="application/rss+xml" title="RSS" href="/forums/viewtopic/'.$this->uri->segment(3).'/feed" />';

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
        $output['breadcrumb'] = ((isset($forum['catName'])) ? anchor('/forums', $forum['catName']) : anchor('/forums', 'Forums')).' &gt; '.anchor('/forums/viewforum/'.$forum['forumID'], $forum['forumName']).' &gt; '.$topic['topicTitle'];

        // display with cms layer
        $this->pages->view('forums_topic', $output, true);
    }

    public function viewpost($postID)
    {
        $post = $this->forums->get_post($postID);

        // find out how many pages the topic has
        if ($topicSize = $this->forums->get_topic_size($post['topicID'])) {
            // get first 10 posts
            $topic = $this->forums->get_posts($post['topicID']);

            // make sure that the postID is not in the first 10
            foreach (range(0, 10) as $key) {
                $postIDs[$key] = @$topic[$key]['postID'];
            }

            // get the page number, if its bigger than 10
            if ($topicSize >= 10 && !in_array($postID, $postIDs)) {
                $lastPage = floor($topicSize / 10) * 10;
                redirect('/forums/viewtopic/'.$post['topicID'].'/page/'.$lastPage.'#post'.$post['postID']);
            } else {
                redirect('/forums/viewtopic/'.$post['topicID'].'#post'.$post['postID']);
            }
        } else {
            show_error('There was a problem finding this post.');
        }
    }

    public function addtopic($forumID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }
        if (!$forumID || !$data['forum'] = $this->forums->get_forum($forumID)) {
            show_error('Please make sure you post in a valid forum.');
        }

        // get values
        $data['data'] = $this->core->get_values('forums_topics');

        if (count($_POST)) {
            // required
            $this->core->required = array(
                'title' => array('label' => 'Title', 'rules' => 'required|trim'),
                'body' => array('label' => 'Post Body', 'rules' => 'required'),
            );

            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['dateModified'] = date("Y-m-d H:i:s");
            $this->core->set['forumID'] = $forumID;
            $this->core->set['topicTitle'] = $this->input->post('title');
            $this->core->set['userID'] = $this->session->userdata('userID');

            // update
            if ($this->core->update('forums_topics')) {
                // get topicID
                $topicID = $this->db->insert_id();

                // subscribe to topic
                $this->forums->add_subscription($topicID, $this->session->userdata('userID'));

                // reset easy and add post
                unset($this->core->set);
                $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
                $this->core->set['topicID'] = $topicID;
                $this->core->set['userID'] = $this->session->userdata('userID');
                $this->core->update('forums_posts');

                $postID = $this->db->insert_id();

                // update topic count and latest post
                $this->forums->add_topic($forumID, $topicID, $postID);

                // where to redirect to
                redirect('/forums/viewforum/'.$forumID);
            }
        }

        // set permissions
        $output['moderator'] = (@in_array('forums', @$this->permission->permissions)) ? true : false;

        // populate template
        $output['form:title'] = ($this->input->post('title')) ? $this->input->post('title') : '';
        $output['form:body'] = ($this->input->post('body')) ? $this->input->post('body') : '';

        // set breadcrumb
        $output['breadcrumb'] = ((isset($data['forum']['catName'])) ? anchor('/forums', $data['forum']['catName']) : anchor('/forums', 'Forums')).' &gt; '.anchor('/forums/viewforum/'.$data['forum']['forumID'], $data['forum']['forumName']);

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Post Topic';

        // display with cms layer
        $this->pages->view('forums_post_topic', $output, true);
    }

    public function addreply($topicID = '', $postID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }
        if (!$topicID || !$topic = $this->forums->get_topic($topicID)) {
            show_error('Please make sure you post in a valid topic.');
        }
        if ($topic['locked'] && (@!in_array('forums', @$this->permission->permissions))) {
            show_error('You cannot reply to this topic as it is locked.');
        }

        // check they aren't posting again too soon
        if ($this->session->userdata('lastPost') > strtotime('-5 seconds')) {
            $this->form_validation->set_error('Hold on, please wait a few more seconds before posting.');
        }

        // get user info
        $user = $this->forums->get_user($topic['userID']);

        // get forum info
        $forum = $this->forums->get_forum($topic['forumID']);

        // get post for quotes
        if ($postID && $post = $this->forums->get_post($postID)) {
            $quote = '[quote]'.$post['body'].'[/quote]'."\n";
        }

        // get posts
        if ($posts = $this->forums->get_posts($topicID, 10)) {
            foreach ($posts as $post) {
                $output['posts'][] = array(
                    'post:id' => $post['postID'],
                    'post:date' => dateFmt($post['dateCreated']),
                    'post:links' => anchor('/forums/addreply/'.$post['topicID'].'/'.$post['postID'], 'Quote'),
                    'post:body' => bbcode($post['body']),
                    'user:name' => anchor('/users/profile/'.$post['userID'], (($post['displayName']) ? $post['displayName'] : $post['firstName'].' '.$post['lastName'])),
                    'user:group' => ($post['groupName']) ? $post['groupName'] : '',
                    'user:avatar' => anchor('/users/profile/'.$post['userID'], display_image($this->forums->get_avatar($post['avatar']), 'post Avatar', 200, 'class="avatar"', base_url().$this->config->item('staticPath').'/images/noavatar.gif')),
                    'user:posts' => $post['posts'],
                    'user:kudos' => $post['kudos'],
                    'user:signature' => ($post['signature']) ? '<hr /><small>'.bbcode($post['signature']).'</small>' : ''
                );
            }
        };

        if (count($_POST)) {
            // required
            $this->core->required = array(
                'body' => array('label' => 'Post Body', 'rules' => 'required'),
            );

            // set stuff
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['topicID'] = $topicID;
            $this->core->set['userID'] = $this->session->userdata('userID');

            // update
            if ($this->core->update('forums_posts')) {
                // load email lib
                $this->load->library('email');

                // get postID
                $postID = $this->db->insert_id();

                // update topic count and latest post
                $this->forums->add_reply($topicID, $topic['forumID'], $postID);

                // get subscriptions
                $subscribers = $this->forums->get_subscriptions($topicID);

                // subscribe to topic
                if (@!in_array($this->session->userdata('userID'), $subscribers)) {
                    $this->forums->add_subscription($topicID, $this->session->userdata('userID'));
                }

                // email those subscribed
                if ($users = $this->forums->get_emails($subscribers)) {
                    foreach ($users as $sub) {
                        // set header and footer
                        $emailHeader = str_replace('{name}', $sub['firstName'].' '.$sub['lastName'], $this->site->config['emailHeader']);
                        $emailHeader = str_replace('{email}', $sub['email'], $emailHeader);
                        $emailFooter = str_replace('{name}', $sub['firstName'].' '.$sub['lastName'], $this->site->config['emailFooter']);
                        $emailFooter = str_replace('{email}', $sub['email'], $emailFooter);

                        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                        $this->email->to($sub['email']);
                        $this->email->subject('Subscription Notification for '.$topic['topicTitle']);
                        $this->email->message($emailHeader."\n\nSomeone replied to a topic you are subscribed to titled \"".$topic['topicTitle']."\".\n\nYou can view this topic by clicking on the link below:\n\n".site_url('/forums/viewpost/'.$postID)."\n\nThey said:\n\"".$this->input->post('body')."\"\n\n".$emailFooter);
                        $this->email->send();
                    }
                }

                // set last post session var
                $this->session->set_userdata('lastPost', strtotime('now'));

                // where to redirect to
                redirect('/forums/viewpost/'.$postID);
            }
        }

        // set permissions
        $output['moderator'] = (@in_array('forums', @$this->permission->permissions)) ? true : false;

        // populate template
        $output['form:body'] = ($this->input->post('body')) ? $this->input->post('body') : @$quote;

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set title and breadcrumb
        $output['page:title'] = $this->site->config['siteName'].' | Post Reply';
        $output['breadcrumb'] = ((isset($forum['catName'])) ? anchor('/forums', $forum['catName']) : anchor('/forums', 'Forums')).' &gt; '.anchor('/forums/viewforum/'.$forum['forumID'], $forum['forumName']).' &gt; '.anchor('/forums/viewtopic/'.$topicID, $topic['topicTitle']);

        // display with cms layer
        $this->pages->view('forums_post_reply', $output, true);
    }

    public function editpost($postID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }
        if (!$postID || !$post = $this->forums->get_post($postID)) {
            show_error('Please make sure you edit a valid post.');
        }
        if ($post['userID'] != $this->session->userdata('userID') && @!in_array('forums', $this->permission->permissions)) {
            show_error('You are not authorised to edit this post.');
        }

        // required
        $this->core->required = array(
            'body' => array('label' => 'Post Body', 'rules' => 'required'),
        );


        // set object ID
        $objectID = array('postID' => $postID);

        // get values
        $data['data'] = $this->core->get_values('forums_posts', $objectID);

        if (count($_POST)) {
            // set stuff
            $this->core->set['dateModified'] = date("Y-m-d H:i:s");

            // update
            if ($this->core->update('forums_posts', $objectID)) {
                // where to redirect to
                redirect('/forums/viewpost/'.$postID);
            }
        }

        // set permissions
        $output['moderator'] = (@in_array('forums', @$this->permission->permissions)) ? true : false;

        // populate template
        $output['topic:title'] = $post['topicTitle'];
        $output['form:body'] = ($this->input->post('body')) ? $this->input->post('body') : $post['body'];

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Edit Post';

        // display with cms layer
        $this->pages->view('forums_edit_post', $output, true);
    }

    public function edittopic($topicID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }
        if (!$topicID || !$topic = $this->forums->get_topic($topicID)) {
            show_error('Please make sure you edit a valid topic.');
        }
        if ($topic['userID'] != $this->session->userdata('userID') && @!in_array('forums', $this->permission->permissions)) {
            show_error('You are not authorised to edit this topic.');
        }

        // required
        $this->core->required = array(
            'title' => array('label' => 'Title', 'rules' => 'required'),
        );


        // set object ID
        $objectID = array('topicID' => $topicID);

        // get values
        $data['data'] = $this->core->get_values('forums_topics', $objectID);

        if (count($_POST)) {
            // set stuff
            $this->core->set['topicTitle'] = $this->input->post('title');

            // update
            if ($this->core->update('forums_topics', $objectID)) {
                // where to redirect to
                redirect('/forums/viewtopic/'.$topicID);
            }
        }

        // set permissions
        $output['moderator'] = (@in_array('forums', @$this->permission->permissions)) ? true : false;

        // populate template
        $output['form:title'] = htmlentities($topic['topicTitle']);

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Edit Topic';

        // display with cms layer
        $this->pages->view('forums_edit_topic', $output, true);
    }

    public function deletepost($postID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }
        if (!$postID || !$post = $this->forums->get_post($postID)) {
            show_error('Please make sure you delete a valid post.');
        }
        if ($post['userID'] != $this->session->userdata('userID') && @!in_array('forums', $this->permission->permissions)) {
            show_error('You are not authorised to delete this post.');
        }

        // set object ID
        $objectID = array('postID' => $postID);

        // find out if this is the first post in the topic
        $isTopic = ($post['dateCreated'] == $post['topicDate']) ? true : false;

        // find out forum ID
        $topic = $this->forums->get_topic($post['topicID']);

        if (count($_POST) && $this->input->post('delete')) {
            if ($isTopic) {
                // update topic count and latest post
                $this->forums->minus_reply($post['topicID'], $topic['forumID'], true);

                // update
                $this->core->soft_delete('forums_topics', array('topicID' => $post['topicID']));
                $this->core->soft_delete('forums_posts', array('topicID' => $post['topicID']));

                // where to redirect to
                redirect('/forums/viewforum/'.$topic['forumID']);
            } else {
                // update topic count and latest post
                $this->forums->minus_reply($post['topicID'], $topic['forumID']);

                // update
                $this->core->soft_delete('forums_posts', $objectID);

                // where to redirect to
                redirect('/forums/viewtopic/'.$post['topicID']);
            }
        }

        // populate template
        $output['post:body'] = bbcode($post['body']);

        // show that they will be deleting the topic
        if ($isTopic) {
            $this->form_validation->set_error('NOTE: You will be deleting the topic as well as this post.');
        }

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Delete Post';

        // display with cms layer
        $this->pages->view('forums_delete', $output, true);
    }

    public function tag($tag = '')
    {
        // get partials
        $output = $this->partials;

        $data['posts'] = $this->forums->get_posts_by_tag($tag);

        // set content
        $output['forum-content'] = $this->parser->parse('read', $data, true);

        // display with cms layer
        $this->pages->view('forum', $output, true);
    }

    public function search($forumID, $tag = '')
    {
        // get forum info and redirect if forum isn't set
        if (!$forumID || !$forum = $this->forums->get_forum($forumID)) {
            redirect('/forums');
        }

        // get partials
        $output = $this->partials;

        // set tags
        $query = ($tag) ? $tag : $this->input->post('query');

        if ($topicIDs = $this->forums->search_forums($query)) {
            // get topics
            if ($topics = $this->forums->get_topics($forumID, null, $topicIDs)) {
                foreach ($topics as $topic) {
                    $output['topics'][] = array(
                        'topic:title' => $topic['topicTitle'],
                        'topic:link' => site_url('/forums/viewtopic/'.$topic['topicID']),
                        'topic:user' => anchor('/users/profile/'.$topic['userID'], ($this->forums->lookup_user($topic['userID'], true))),
                        'topic:class' => ($topic['sticky']) ? 'sticky' : '',
                        'topic:replies' => $topic['replies'],
                        'topic:views' => $topic['views'],
                        'topic:latest-post' => ($latestPost = $this->forums->get_post($topic['lastPostID'])) ?
                            '<small>Posted: '.dateFmt($latestPost['dateCreated']).' by '.anchor('/users/profile/'.$latestPost['userID'], ($latestPost['displayName']) ? $latestPost['displayName'] : $latestPost['firstName'].' '.$latestPost['lastName']).'</small>' : ''
                    );
                }
            }
        }

        // populate template
        $output['forum:title'] = $forum['forumName'];
        $output['forum:id'] = $forum['forumID'];

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Searching forums for "'.$query.'"';
        $output['page:heading'] = 'Search forum for: "'.$query.'"';

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
        $output['breadcrumb'] = ((isset($forum['catName'])) ? anchor('/forums', $forum['catName']) : anchor('/forums', 'Forums')).' &gt; '.anchor('/forums/viewforum/'.$forum['forumID'], $forum['forumName']);

        // display with cms layer
        $this->pages->view('forums_search', $output, true);
    }

    public function ac_search($forumID)
    {
        $tags = strtolower($_POST["q"]);
        if (!$tags) {
            return false;
        }

        if ($objectIDs = $this->forums->search_forums($tags)) {
            // form dropdown and myql get countries
            if ($searches = $this->forums->get_topics($forumID, 10, $objectIDs)) {
                // go foreach
                foreach ($searches as $search) {
                    $items[$search['topicTitle']] = array('id' => $search['topicID'], 'name' => $search['topicTitle']);
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

    public function captcha_check()
    {
        // Then see if a captcha exists:
        $exp=time()-600;
        $sql = "SELECT COUNT(*) AS count FROM hw_captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?";
        $binds = array($this->input->post('captcha'), $this->input->ip_address(), $exp);
        $query = $this->db->query($sql, $binds);
        $row = $query->row();

        if ($row->count == 0) {
            $this->validation->set_message('captcha_check', 'The Captcha word was not correct.');
            return false;
        } else {
            return true;
        }
    }

    public function topics_feed($forumID, $limit = 10)
    {
        $this->load->helper('xml');

        $data['encoding'] = 'utf-8';
        $data['feed_name'] = $this->site->config['siteName'] . ' Forum';
        $data['feed_url'] = site_url('/forum');
        $data['page_description'] = 'Topics RSS feed for '.$this->site->config['siteName'].'.';
        $data['page_language'] = 'en';
        $data['creator_email'] = $this->site->config['siteEmail'];
        $data['posts'] = $this->forums->get_topics($forumID, $limit);

        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('forum/rss', $data);
    }

    public function posts_feed($topicID, $limit = 10)
    {
        $this->load->helper('xml');

        $data['encoding'] = 'utf-8';
        $data['feed_name'] = $this->site->config['siteName'] . ' Forum';
        $data['feed_url'] = site_url('/forum');
        $data['page_description'] = 'Posts RSS feed for '.$this->site->config['siteName'].'.';
        $data['page_language'] = 'en';
        $data['creator_email'] = $this->site->config['siteEmail'];
        $data['posts'] = $this->forums->get_posts($topicID, $limit);

        $this->output->set_header('Content-Type: application/rss+xml');
        $this->load->view('forum/rss', $data);
    }

    public function subscribe($topicID = '')
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // get forum info and redirect if forum isn't set
        if (!$topicID || !$data['topic'] = $this->forums->get_topic($topicID)) {
            redirect('/forums');
        }

        // get subs for this topic
        $subs = $this->forums->get_subscriptions($topicID);

        // check this user against subs
        if (@!in_array($this->session->userdata('userID'), $subs)) {
            // add subscription
            $this->forums->add_subscription($topicID, $this->session->userdata('userID'));
        } else {
            // remove subscription
            $this->forums->remove_subscription($topicID, $this->session->userdata('userID'));
        }

        redirect('/forums/viewtopic/'.$topicID);
    }
}
