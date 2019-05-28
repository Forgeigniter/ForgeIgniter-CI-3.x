<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * MY_Form_validation Class
 *
 * Extends Form_Validation library
 *
 * Adds one validation rule, "unique" and accepts a
 * parameter, the name of the table and column that
 * you are checking, specified in the forum table.column
 *
 * Note that this update should be used with the
 * form_validation library introduced in CI 1.7.0
 */

class MY_Form_validation extends CI_Form_validation
{
    public function __construct()
    {
        parent::__construct();

        // set password error
        $this->set_message('matches', 'The passwords do not match.');
    }

    // --------------------------------------------------------------------

    /**
     * Unique
     *
     * @access	public
     * @param	string
     * @param	field
     * @return	bool
     */

    public function unique($str, $field)
    {
        $CI =& get_instance();
        list($table, $column) = preg_split("/\./", $field, 2);

        // for shop
        $fields = $CI->db->list_fields($table);
        if (in_array('siteID', $fields) && $table != 'sites') {
            $CI->db->where('siteID', $CI->site->config['siteID']);
        }
        if (in_array('deleted', $fields)) {
            $CI->db->where('deleted', 0);
        }

        $CI->form_validation->set_message('unique', 'The %s that you requested is already taken, please try another.');

        $CI->db->select('COUNT(*) dupe');
        $query = $CI->db->get_where($table, array($column => $str));
        $row = $query->row();

        return ($row->dupe > 0) ? false : true;
    }

    public function really_unique($str, $field)
    {
        $CI =& get_instance();
        list($table, $column) = preg_split("/\./", $field, 2);

        $CI->form_validation->set_message('really_unique', 'The %s that you requested is already taken, please try another.');

        $CI->db->select('COUNT(*) dupe');
        $query = $CI->db->get_where($table, array($column => $str));
        $row = $query->row();

        return ($row->dupe > 0) ? false : true;
    }

    public function set_error($error = '')
    {
        if (empty($error)) {
            return false;
        } else {
            $CI =& get_instance();

            $CI->form_validation->_error_array['custom_error'] = $error;

            return true;
        }
    }
}
