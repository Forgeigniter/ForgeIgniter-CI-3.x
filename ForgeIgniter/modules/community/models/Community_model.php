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

class Community_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }
    }

    public function get_users()
    {
        // default wheres
        $this->db->where('siteID', $this->siteID);
        $this->db->where('active', 1);

        // order
        $this->db->order_by('firstName');

        // grab
        $query = $this->db->get('users');

        if ($query->num_rows()) {
            $result = $query->result_array();

            return $result;
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
                return $row['firstName'].' '.$row['lastName'];
            } else {
                return $row;
            }
        } else {
            return false;
        }
    }

    public function count_users()
    {
        // default wheres
        $this->db->where('siteID', $this->siteID);
        $this->db->where('active', 1);

        $this->db->select('COUNT(*) as countUsers');

        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['countUsers'];
        } else {
            return false;
        }
    }
}
