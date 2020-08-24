<?php
defined('BASEPATH') or exit('No direct script access allowed');
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

class Site
{
    public $siteID;
    public $siteDomain;
    public $config = array();
    public $plans = array();

    public function __construct()
    {
        // init vars
        $this->CI =& get_instance();

        // initialise site
        $this->_init_site();

        // find out what type of page request this is
        if (!preg_match('/\.(gif|jpg|jpeg|png|css|js|ico|shtml)$/i', $this->CI->uri->uri_string())) {
            // load session lib
            $this->CI->load->library('permission');
            $this->CI->load->library('parser');
            $this->CI->load->library('form_validation');
            $this->CI->load->library('pagination');
            $this->CI->load->library('template');

            // log in with cookie
            $this->_login_cookie();

            // check that the request is not admin or ajax
            if (!preg_match('/\/admin\//i', site_url($this->CI->uri->uri_string()))) {
                // init tracking
                $this->_track_user();
            }
        }
    }

    public function get_quota()
    {
        // get image quota
        $this->CI->db->where('siteID', $this->config['siteID']);
        $this->CI->db->select('SUM(filesize) as quota');
        $query = $this->CI->db->get('images');
        $row = $query->row_array();
        $quota = $row['quota'];

        // get file quota
        $this->CI->db->where('siteID', $this->config['siteID']);
        $this->CI->db->select('SUM(filesize) as quota');
        $query = $this->CI->db->get('files');
        $row = $query->row_array();
        $quota += $row['quota'];

        return $quota;
    }

    public function _init_site()
    {
        // get hash of base URL
        $siteHash = md5($this->CI->config->item('base_url'));

        // get site domain
        $siteDomain = substr($this->CI->config->item('base_url'), 0, -1);
        $siteDomain = strtolower(preg_replace('/^(http)s?:\/+(www.)?/i', '', $siteDomain));
        $this->siteDomain = $siteDomain;

        // if multisite is enabled, then make sure uploads folder is based on domain
        if ($this->CI->config->item('stagingSites') === true) {
            $this->CI->config->set_item('uploadsPath', $this->CI->config->item('uploadsPath').'/'.$siteDomain);
        }

        // look up site
        if ($this->CI->db->get('sites')->num_rows() !== 0) {
            // look in db
            $this->CI->db->where('siteDomain', $siteDomain);
            $this->CI->db->or_where('altDomain', $siteDomain);
            $query = $this->CI->db->get('sites t1', 1);

            if ($query->num_rows() > 0) {
                // get config for site
                $this->config = $query->row_array();

                // check site is active
                if (!$this->config['active']) {
                    show_error('This site is currently offline, we are sorry for the inconvenience.');
                }

                // define the site variable
                define('SITEID', $this->config['siteID']);
                define('SITEGROUPID', $this->config['groupID']);

                // run defaults function
                $this->_set_defaults();

                return true;
            } else {
                show_error('This domain has not been configured properly.');
            }
        }

        // no sites have been set up yet so lets create one
        else {
            $this->CI->load->library('permission');
            $set = array(
                'siteDomain' => $siteDomain,
                'siteName' => 'My Site',
                'siteURL' => site_url('/'),
                'dateCreated' => date("Y-m-d H:i:s"),
                'groupID' => 1
            );
            $this->CI->db->set($set)->insert('sites');
            $siteID = $this->CI->db->insert_id();
            $this->CI->permission->add_default_permissions('-1', $siteID);
            $groupID = $this->CI->permission->add_group('Administrator', $siteID);
            $this->CI->permission->add_default_permissions($groupID, $siteID);

            redirect('/admin');
        }
    }

    public function _login_cookie()
    {
        // load auth lib
        $this->CI->load->library('form_validation');
        $this->CI->load->library('auth');

        // check no session is set
        if (!$this->CI->session->userdata('session_user')) {
            if ($cookie = get_cookie('forgeigniter')) {
                // get cookie
                $cookie = get_cookie('forgeigniter');
                $session = unserialize(base64_decode(strtr($cookie, '-_', '+/')));

                // set admin session name, if given
                if ($this->CI->auth->do_login($session[0], $session[1], $session[2], true)) {
                    // for use with ce
                    if ($this->CI->session->userdata('groupID') > 0 && $this->CI->permission->get_group_permissions($this->CI->session->userdata('groupID'))) {
                        $this->CI->session->set_userdata('session_admin', true);
                    }
                }

                // get error message
                else {
                    $this->CI->form_validation->set_error($this->CI->auth->error);
                }
            }
        }

        return false;
    }

    public function _track_user()
    {
        // set last page (as long as the request is not admin or ajax)
        if (!preg_match('/\/admin\//i', site_url($this->CI->uri->uri_string()))) {
            $this->CI->session->set_userdata('lastPage', site_url($this->CI->uri->uri_string()));
        }

        // don't do this if the user is admin
        if (!$this->CI->session->userdata('session_admin') && $this->CI->input->user_agent()) {
            $userdata = ($this->CI->session->userdata('firstName')) ? serialize(array(
                'dateCreated' => $this->CI->session->userdata('dateCreated'),
                'userID' => $this->CI->session->userdata('userID'),
                'username' => $this->CI->session->userdata('username'),
                'firstName' => $this->CI->session->userdata('firstName'),
                'lastName' => $this->CI->session->userdata('lastName')
            )) : '';

            // find out if this user has been to the site today
            $userKey = md5(substr($this->CI->input->ip_address(), 0, strrpos($this->CI->input->ip_address(), '.')).substr($this->CI->input->user_agent(), 0, 50));
            $this->CI->db->where('siteID', $this->config['siteID']);
            $this->CI->db->where('userKey', $userKey);
            $this->CI->db->where('date > ', "DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL 0 DAY)", false);
            $query = $this->CI->db->get('tracking');

            // get last page
            $lastPage = '/'.$this->CI->uri->uri_string();

            // if not, enter a row in the db
            if ($query->num_rows() == 0) {
                $this->CI->db->set('date', date("Y-m-d H:i:s"));
                $this->CI->db->set('userKey', $userKey);
                $this->CI->db->set('ipAddress', $this->CI->input->ip_address());
                $this->CI->db->set('userAgent', substr($this->CI->input->user_agent(), 0, 50));
                if (isset($_SERVER['HTTP_REFERER'])) {
                    $this->CI->db->set('referer', $_SERVER['HTTP_REFERER']);
                }
                $this->CI->db->set('lastPage', $lastPage);
                $this->CI->db->set('userdata', $userdata);
                $this->CI->db->set('siteID', $this->config['siteID']);

                $this->CI->db->insert('tracking');
            }

            // otherwise update the page views
            else {
                $row = $query->row_array();

                $this->CI->db->set('views', 'views+1', false);
                $this->CI->db->set('lastPage', $lastPage);
                if ($userdata) {
                    $this->CI->db->set('userdata', $userdata);
                }
                $this->CI->db->where('siteID', $this->config['siteID']);
                $this->CI->db->where('trackingID', $row['trackingID']);
                $this->CI->db->update('tracking');
            }
        }
    }

    public function _set_defaults()
    {
        // set plans
        if ($this->config['plan'] == 1) {
            $this->plans['storage'] = 20000;
        } elseif ($this->config['plan'] == 2) {
            $this->plans['storage'] = 500000;
        } elseif ($this->config['plan'] == 3) {
            $this->plans['storage'] = 1000000;
        } elseif ($this->config['plan'] == 4) {
            $this->plans['storage'] = 2000000;
        } elseif ($this->config['plan'] == 5) {
            $this->plans['storage'] = 5000000;
        } else {
            $this->plans['storage'] = -1;
        }

        // shop defaults
        if (!$this->config['shopVariation1']) {
            $this->config['shopVariation1'] = 'Colour';
        }
        if (!$this->config['shopVariation2']) {
            $this->config['shopVariation2'] = 'Size';
        }
        if (!$this->config['shopVariation3']) {
            $this->config['shopVariation3'] = 'Other';
        }

        // email defaults
        if (!$this->config['emailHeader']) {
            $this->config['emailHeader'] = "Dear {name},";
        }
        if (!$this->config['emailFooter']) {
            $this->config['emailFooter'] = "Best Regards,\n".$this->config['siteName']."\n".$this->config['siteURL']."\n\n";
        }
        if (!$this->config['emailTicket']) {
            $this->config['emailTicket'] = "Thank you for contacting us, a new ticket has been created. This is an automated response confirming the receipt of your message. We will attend to your enquiry soon as possible. The details of your enquiry are below for your records. When replying, please keep the ticket ID in the subject to ensure that your replies are dealt with correctly.";
        }
        if (!$this->config['emailOrder']) {
            $this->config['emailOrder'] = "This is a confirmation to say that your order on ".$this->config['siteName']." has been placed and is currently being processed. We will email you again once your order has been shipped.\n\nIf you have any queries about your order, please do not hesitate to contact us at ".$this->config['siteEmail']." quoting your unique order reference number. Thank you for your custom.";
        }
        if (!$this->config['emailAccount']) {
            $this->config['emailAccount'] = "Your account for ".$this->config['siteName']." has been set up. Thank you for registering with us.\n\nPlease keep the information below safe.";
        }
        if (!$this->config['emailDonation']) {
            $this->config['emailDonation'] = "Thank you for your donation placed on ".$this->config['siteName'].".";
        }
        if (!$this->config['emailSubscription']) {
            $this->config['emailSubscription'] = "This is a confirmation to say that your subscription has been created on ".$this->config['siteName'].". You can update your subscription and view invoices by logging in to your account. Please note that your subscription will renew at the intervals stated on the website unless you cancel the subscription prior to the renewal date. See our website for more information. To login to your account please click on the URL below:\n\n".site_url('/shop/account')."\n\nYour subscription details are below, thank you for your custom.";
        }
        if (!$this->config['emailDispatch']) {
            $this->config['emailDispatch'] = "This is a notification to say that your order {order-id} on ".$this->config['siteName']." has been shipped.\n\nYou can track your order and view past orders by clicking on the link below.\n\n".site_url('/shop/orders')."\n\nIf you have any other queries about your order, please do not hesitate to contact us at ".$this->config['siteEmail']." quoting your unique order reference number.";
        }

        return true;
    }
}
