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

class Wiki extends MX_Controller
{
    public $partials = array();
    public $permissions = array();
    public $sitePermissions = array();

    public function __construct()
    {
        parent::__construct();

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

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // load libs etc
        $this->load->model('wiki_model', 'wiki');
        $this->load->module('pages');
        $this->load->library('parser');
        $this->load->library('mkdn');

        // load partials - categories
        $data['pages'] = $this->wiki->get_pages();
        $data['categories'] = $this->wiki->get_categories();
        $this->partials['wiki:categories'] = $this->parser->parse('partials/categories', $data, true);
    }

    public function index()
    {
        if ($this->uri->segment(2)) {
            // deprecated uri code (now its always just the uri string)
            $num = 2;
            $uri = '';
            while ($segment = $this->uri->segment($num)) {
                $uri .= $segment.'/';
                $num ++;
            }
            $new_length = strlen($uri) - 1;
            $uri = substr($uri, 0, $new_length);
        } else {
            $uri = 'home';
        }

        $this->view($uri);
    }

    public function view($page)
    {
        // get partials
        $output = $this->partials;

        // load wiki page
        $wikipage = $this->wiki->get_page(false, $page);

        // get versions
        if (isset($versions)) {
            $versions = $this->wiki->get_versions($page['pageID']);
        }
        // get page
        $output['wikipage'] = $wikipage;
        $output['wikipage:link'] = site_url('/wiki/edit/'.$this->core->encode($page));
        $output['wikipage:body'] = mkdn($wikipage['body']);

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Wiki - '.$wikipage['pageName'];
        $output['page:heading'] = $wikipage['pageName'];

        // display with cms layer
        $this->pages->view('wiki_page', $output, true);
    }

    public function edit($page)
    {
        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // get page
        $uri = $this->core->decode($page);

        // deal with post
        if (count($_POST)) {
            if ($this->wiki->update_page($uri)) {
                redirect('/wiki/'.$uri);
            }
        }

        // load wiki page
        $wikipage = $this->wiki->get_page(false, $uri);

        // get versions
        if ($versions = $this->wiki->get_versions($wikipage['pageID'])) {
            foreach ($versions as $version) {
                $output['versions'][] = array(
                    'version' => ($wikipage['versionID'] == $version['versionID']) ?
                        '<strong>'.dateFmt($version['dateCreated']).
                            (($user = $this->wiki->lookup_user($version['userID'], true)) ? ', by '.$user : '').
                            (($notes = $version['notes']) ? ' <em>('.$notes.')</em>' : '').
                        '</strong>' :
                        dateFmt($version['dateCreated']).
                            (($user = $this->wiki->lookup_user($version['userID'], true)) ? ', by '.$user : '').
                            (($notes = $version['notes']) ? ' <em>('.$notes.')</em>' : '').
                        ' | '.anchor('/wiki/revert/'.$this->core->encode($uri).'/'.$version['versionID'], 'Revert', 'onclick="return confirm(\'You will lose unsaved changes. Continue?\');"')
                );
            }
        }

        // get categories
        if ($categories = $this->wiki->get_categories()) {
            foreach ($categories as $category) {
                $options[$category['catID']] = $category['catName'];
            }
        }
        $options[0] = 'No Category';


        // populate template
        $output['wikipage:link'] = site_url('/wiki/'.$wikipage['uri']);
        $output['form:title'] = $wikipage['pageName'];
        $output['select:categories'] = @form_dropdown('catID', $options, set_value('catID', $wikipage['catID']), 'id="category" class="formelement"');
        $output['form:body'] = $wikipage['body'];
        $output['form:notes'] = $this->input->post('notes');

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Edit Wiki';
        $output['page:heading'] = 'Edit Page - "'.$uri.'"';

        // display with cms layer
        $this->pages->view('wiki_form', $output, true);
    }

    public function revert($page = '', $versionID = '')
    {
        // check stuff
        if (!$page || !$versionID) {
            show_error('Something went wrong!');
        }

        // get page
        $uri = $this->core->decode($page);

        if (!$page = $this->wiki->get_page(false, $uri)) {
            show_error('No page found!');
        }

        // revert and redirect
        if ($this->wiki->revert_page($page['pageID'], $versionID)) {
            redirect('/wiki/'.$uri);
        } else {
            show_error('Something went wrong!');
        }
    }

    public function pages($catID = '')
    {
        $row = null;
        // get category or fail
        $category = ($catID && $row = $this->wiki->get_categories($catID)) ? $row['catName'] : 'Uncategorised';

        // get partials
        $output = $this->partials;

        // get pages
        $catID = ($catID) ? $catID : false;
        if ($pages = $this->wiki->get_pages($catID)) {
            foreach ($pages as $page) {
                $output['wikipages'][] = array(
                    'wikipage:title' => $page['pageName'],
                    'wikipage:link' => site_url('/wiki/'.$page['uri'])
                );
            }
        }

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Wiki - '.$category;
        $output['page:heading'] = $category;
        $output['page:description'] = mkdn($row['description']);

        // display with cms layer
        $this->pages->view('wiki', $output, true);
    }

    public function search($tag = '')
    {
        // get partials
        $output = $this->partials;

        // set tags
        $query = ($tag) ? $tag : strip_tags($this->input->post('query', true));

        if ($pageIDs = $this->wiki->search_wiki($query)) {
            if ($pages = $this->wiki->get_pages(null, $pageIDs)) {
                foreach ($pages as $page) {
                    $output['wikipages'][] = array(
                        'wikipage:title' => $page['pageName'],
                        'wikipage:link' => site_url('/wiki/'.$page['uri'])
                    );
                }
            }
        }

        // set pagination
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set title
        $output['page:title'] = $this->site->config['siteName'].' | Searching wiki for "'.$query.'"';
        $output['page:heading'] = 'Search wiki for: "'.$query.'"';

        // display with cms layer
        $this->pages->view('wiki_search', $output, true);
    }
}
