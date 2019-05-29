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

class Forge extends MX_Controller
{

    // set defaults
    public $includes_path = '/includes/admin';				// path to includes for header and footer
    public $redirect = '/forge/sites';					// default redirect
    public $permissions = array();

    public function __construct()
    {
        parent::__construct();

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_admin')) {
            redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // check permissions for this page
        if ($this->session->userdata('groupID') >= 0) {
            redirect('/admin/dashboard');
        }

        // get permissions
        if (!$this->permission->permissions) {
            $this->permission->permissions = array();
        }

        // get site permissions
        $this->permission->sitePermissions = $this->permission->get_group_permissions($this->site->config['groupID']);

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // load model
        $this->load->model('forge_model', 'forge');
    }

    public function index()
    {
        redirect($this->redirect);
    }

    public function sites()
    {
        // override core siteID stuff
        $this->core->adminOverRide = true;

        // superuser grab
        $where = '';
        if ($this->session->userdata('groupID') > 0) {
            $where .= 'resellerID = "'.$this->site->config['resellerID'].'" AND ';
        }

        // if search
        if (count($_POST) && ($query = $this->input->post('searchbox'))) {
            $where .= '(siteDomain LIKE "%'.$query.'%" OR siteName LIKE "%'.$query.'%")';
        } elseif ($this->session->userdata('groupID') > 0) {
            $where = substr($where, 0, -4);
        }

        // grab data and display
        $output = $this->core->viewall('sites', $where);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('sites', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_site()
    {
        // override core siteID stuff
        $this->core->adminOverRide = true;

        // load libs
        $this->load->model('sites_model', 'sites');

        // required
        $this->core->required = array(
            'siteName' => array('label' => 'Name of Site', 'rules' => 'required|trim'),
            'siteDomain' => array('label' => 'Domain', 'rules' => 'required|really_unique[sites.siteDomain]|callback__check_domain|strtolower|trim'),
            'siteEmail' => array('label' => 'Site Email', 'rules' => 'required|valid_email|trim'),
            'username' => array('label' => 'Username', 'rules' => 'required|really_unique[users.username]|trim'),
            'password' => array('label' => 'Password', 'rules' => 'required'),
            'adminEmail' => array('label' => 'Admin Email', 'rules' => 'required|valid_email|trim'),
            'firstName' => array('label' => 'First Name', 'rules' => 'required|trim'),
            'lastName' => array('label' => 'Last Name', 'rules' => 'required|trim'),
        );

        // get values
        $output['data'] = $this->core->get_values('sites');

        // get permissions
        $output['permissions'] = $this->permission->get_permissions();

        // handle post
        if (count($_POST)) {
            // tidy domain
            $siteDomain = trim(strtolower(preg_replace('/^(http)s?:\/+((w+)\.)?|^www\.|\/+/i', '', $this->input->post('siteDomain'))));
            $altDomain = trim(strtolower(preg_replace('/^(http)s?:\/+((w+)\.)?|^www\.|\/+/i', '', $this->input->post('altDomain'))));

            // set date created
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['siteDomain'] = $siteDomain;
            $this->core->set['altDomain'] = $altDomain;

            // update
            if ($this->core->update('sites')) {
                // get siteID
                $siteID = $this->db->insert_id();

                // add group
                $groupID = $this->permission->add_group('Administrator', $siteID);

                // add permissions
                $this->permission->add_permissions($groupID, $siteID);

                // reset core and update group ID
                $this->core->set['groupID'] = $groupID;
                $this->core->update('sites', array('siteID' => $siteID));

                // create templates and includes
                $this->sites->add_templates($siteID);

                // reset core and add a new user
                $this->core->set['siteID'] = $siteID;
                $this->core->set['groupID'] = $groupID;
                $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
                $this->core->set['email'] = $this->input->post('adminEmail');
                $this->core->update('users');

                // where to redirect to
                redirect('/forge/sites/');
            }
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('add', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_site($siteID)
    {
        // override core siteID stuff
        $this->core->adminOverRide = true;

        // load site lib
        $this->load->model('sites_model', 'sites');

        // set object ID
        $objectID = array('siteID' => $siteID);

        // get values
        $output['data'] = $this->core->get_values('sites', $objectID);

        // populate permissions
        $perms = $this->permission->get_permission_map($output['data']['groupID']);
        foreach ((array)$perms as $perm) {
            $output['data']['perm'.$perm['permissionID']] = 1;
        }

        // get permissions
        $output['permissions'] = $this->permission->get_permissions();

        // handle post
        if (count($_POST)) {
            // required
            $this->core->required = array(
                'siteDomain' => array('label' => 'Site Domain', 'rules' => 'required|trim|really_unique[sites.siteDomain]'),
                'siteName' => array('label' => 'Name of Site', 'rules' => 'required|trim'),
            );

            // set date
            $this->core->set['dateModified'] = date("Y-m-d H:i:s");

            // update
            if ($this->core->update('sites', $objectID)) {
                // add group if not there
                if (!$output['data']['groupID']) {
                    $groupID = $this->permission->add_group('Administrator', $siteID);
                    $output['data']['groupID'] = $groupID;

                    // update group ID
                    $this->core->set['groupID'] = $groupID;
                    $this->core->update('sites', array('siteID' => $siteID));
                }

                // add permissions
                $this->permission->add_permissions($output['data']['groupID'], $siteID);

                // where to redirect to
                redirect('/forge/sites');
            }
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('edit', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function delete_site($siteID)
    {
        // load site lib
        $this->load->model('sites_model', 'sites');

        // delete site
        $this->sites->delete_site($siteID);

        // where to redirect to
        redirect('/forge/sites');
    }

    public function ac_sites()
    {
        // load site lib
        $this->load->model('sites_model', 'sites');

        $q = strtolower($_POST["q"]);
        if (!$q) {
            return;
        }

        // form dropdown
        $results = $this->sites->get_sites($q);

        // go foreach
        foreach ((array)$results as $row) {
            $items[$row['siteDomain']] = $row['siteName'];
        }

        foreach ($items as $key=>$value) {
            /* If you want to force the results to the query
            if (strpos(strtolower($key), $tags) !== false)
            {
                echo "$key|$id|$name\n";
            }*/
            $this->output->set_output("$key|$value\n");
        }
    }

    public function _check_domain($str)
    {
        $this->form_validation->set_message('_check_domain', 'The %s field did not contain a proper domain (e.g. mysite.com).');
        return (!preg_match('/^(http)s?:\/+((w+)\.)?|^www\.|\/+|\.([A-Z]+)$/i', $str)) ? false : true;
    }
}
