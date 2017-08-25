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

// ------------------------------------------------------------------------

class Tickets_model extends CI_Model {

	var $siteID;
	
	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function get_all_web_forms()
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);	

		$this->db->order_by('formName');	
			
		$query = $this->db->get('web_forms');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_web_form($formID = '')
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);	

		$this->db->where('formID', $formID);
			
		$query = $this->db->get('web_forms', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function view_ticket($ticketID)
	{
		$this->db->set('viewed', '1');
		$this->db->where('ticketID', $ticketID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('tickets');
	}
	
}