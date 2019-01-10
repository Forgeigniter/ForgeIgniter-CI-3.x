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

class Admin extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/dashboard';
	var $permissions = array();

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	function index()
	{
		redirect(site_url($this->redirect));
	}

	function dashboard($days = '')
	{
		// logout if not admin
		if ($this->session->userdata('session_user') && !$this->permission->permissions)
		{
			show_error('Sorry, you do not have permission to administer this website. Please go back or '.anchor('/admin/logout', 'log out').'.');
		}
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// load model and libs
		$this->load->model('forge_model', 'forge');
		$this->load->library('parser');

		// show any errors that have resulted from a redirect
		if ($days === 'permissions')
		{
			$this->form_validation->set_error('Sorry, you do not have permissions to do what you just tried to do.');
		}

		// set message
		$output['message'] = '';

		// get new blog comments
		$newComments = $this->forge->get_blog_new_comments();
		if ($newComments)
		{
			$output['message'] .= '<p>You have <strong>'.$newComments.' new pending comment(s).</strong> You can <a href="'.site_url('/admin/blog/comments').'">view your comments here</a>.</p>';
		}

		// get new blog comments
		$newTickets = $this->forge->get_new_tickets();
		if ($newTickets)
		{
			$output['message'] .= '<p>You have <strong>'.$newTickets.' new ticket(s).</strong> You can <a href="'.site_url('/admin/webforms/tickets').'">view your tickets here</a>.</p>';
		}

		// get new orders
		if (@in_array('shop', $this->permission->sitePermissions))
		{
			$this->load->model('shop/shop_model', 'shop');

			if ($newOrders = $this->shop->get_new_orders())
			{
				$output['message'] .= '<p>You have <strong>'.sizeof($newOrders).' new order(s).</strong> You can <a href="'.site_url('/admin/shop/orders').'">view your orders here</a>.</p>';
			}
		}

		// look to see if there are any pages
		if (!$this->forge->get_num_pages())
		{
			// import default template for new sites
			$this->load->model('sites_model', 'sites');
			$this->sites->add_templates($this->siteID);
			$output['message'] = '<p><strong>Congratulations</strong> - your new site is set up and ready to go!</strong> You can view your site <a href="'.site_url('/').'">here</a>.</p>';
		}
		else
		{
			// set error if default password is still used
			$user = $this->core->lookup_user($this->session->userdata('userID'));
			if ($user['password'] == 'f35364bc808b079853de5a1e343e7159')
			{
				$this->form_validation->set_error('You are still using the default Superuser password. You can change your password <a href="'.site_url('/admin/users/edit/'.$this->session->userdata('userID')).'">here</a>');
			}
		}

		// Is Install still there ?
		if(file_exists(FCPATH."ForgeIgniter\install\index.php"))
		{
				$this->form_validation->set_error('Please delete ForgeIgniter/install folder.');

		}

		// get stats
		$data['recentActivity'] = $this->forge->get_recent_activity();
		$data['todaysActivity'] = $this->forge->get_activity('today');
		$data['yesterdaysActivity'] = $this->forge->get_activity('yesterday');
		$output['activity'] = $this->parser->parse('activity_ajax', $data, TRUE);

		// get stats
		$output['days'] = (is_numeric($days)) ? $days : '30';
		$output['numPageViews'] = $this->forge->get_num_page_views();
		$output['numPages'] = $this->forge->get_num_pages();
		$output['quota'] = $this->site->get_quota();
		$output['numUsers'] = ($count = $this->forge->get_num_users()) ? $count : 0;
		$output['numUsersToday'] = ($count = $this->forge->get_num_users_today()) ? $count : 0;
		$output['numUsersYesterday'] = ($count = $this->forge->get_num_users_yesterday()) ? $count : 0;
		$output['numUsersWeek'] = ($count = $this->forge->get_num_users_week()) ? $count : 0;
		$output['numUsersLastWeek'] = ($count = $this->forge->get_num_users_last_week()) ? $count : 0;
		$output['numBlogPosts'] = $this->forge->get_blog_posts_count();
		$output['popularPages'] = $this->forge->get_popular_pages();
		$output['popularBlogPosts'] = $this->forge->get_popular_blog_posts();
		$output['popularShopProducts'] = $this->forge->get_popular_shop_products();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('dashboard', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function stats($limit = 30)
	{
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{
			$visitations = 0;
			$signups = 0;

			$this->db->select("COUNT(*) as visitations, UNIX_TIMESTAMP(MIN(date))*1000 as dateMicro, DATE_FORMAT(date,'%y%m%d') as dateFmt", FALSE);
			$this->db->where('siteID', $this->siteID);
			$this->db->where('date >=', "DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL ".$this->db->escape($limit)." DAY)", FALSE);
			$this->db->order_by('dateFmt', 'desc');
			$this->db->group_by('dateFmt');

			$query = $this->db->get('tracking');

			if ($query->num_rows())
			{
				$visitations = array();

				$i=0;
				$result = $query->result_array();
				foreach($result as $row)
				{
					$i++;
					$visitations[$i] = '['.$row['dateMicro'].','.$row['visitations'].']';
				}
				$visitations = implode(',', $visitations);
			}

			$this->db->select("COUNT(*) as signups, UNIX_TIMESTAMP(MIN(dateCreated))*1000 as dateMicro, DATE_FORMAT(dateCreated,'%y%m%d') as dateFmt", FALSE);
			$this->db->where('siteID', $this->siteID);
			$this->db->where('dateCreated >=', "DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL ".$this->db->escape($limit)." DAY)", FALSE);
			$this->db->order_by('dateFmt', 'desc');
			$this->db->group_by('dateFmt');

			$query = $this->db->get('users');

			if ($query->num_rows())
			{
				$signups = array();

				$i=0;
				$result = $query->result_array();
				foreach($result as $row)
				{
					$i++;
					$signups[$i] = '['.$row['dateMicro'].','.$row['signups'].']';
				}
				$signups = implode(',', $signups);
			}

			$this->output->set_output('{ "visits" : ['.$visitations.'] ,  "signups" : ['.$signups.'] }');
		}
	}

	function activity_ajax()
	{
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{
			// load model
			$this->load->model('forge_model', 'forge');

			// get stats
			$output['recentActivity'] = $this->forge->get_recent_activity();
			$output['todaysActivity'] = $this->forge->get_activity('today');
			$output['yesterdaysActivity'] = $this->forge->get_activity('yesterday');

			$this->load->view('activity_ajax', $output);
		}
	}

	function tracking()
	{
		// logout if not admin
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		$this->load->view($this->includes_path.'/header');
		$this->load->view('tracking');
		$this->load->view($this->includes_path.'/footer');
	}

	function tracking_ajax()
	{
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{
			$output = $this->core->viewall('tracking', null, array('trackingID', 'desc'));

			$this->load->view('tracking_ajax', $output);
		}
	}

	function login($redirect = '')
	{
		// load libs etc
		$this->load->library('auth');

		if (!$this->session->userdata('session_admin'))
		{
			if ($_POST)
			{
				// set redirect to default if not given
				if ($redirect == '')
				{
					$redirect = $this->redirect;
				}
				else
				{
					$redirect = $this->core->decode($redirect);
				}

				// set admin session name, if given
				if ($this->auth->login($this->input->post('username'), $this->input->post('password'), 'session_user'))
				{
					// for use with ce
					if ($this->session->userdata('groupID') != 0 && $this->permission->get_group_permissions($this->session->userdata('groupID')))
					{
						$this->session->set_userdata('session_admin', TRUE);
					}

					// update quota
					$quota = $this->site->get_quota();
					$this->core->set['quota'] = ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0;
					$this->core->update('sites', array('siteID' => $this->siteID));

					redirect($redirect);
				}

				// get error message
				else
				{
					$this->form_validation->set_error($this->auth->error);
				}
			}
		}
		else
		{
			if ($redirect != '')
			{
				redirect($redirect);
			}
		}

		// view
		$this->load->view($this->includes_path.'/header');
		$this->load->view('login');
		$this->load->view($this->includes_path.'/footer');
	}

	function logout($redirect = '')
	{
		// load libs etc
		$this->load->library('auth');

		// set redirect to default if not given
		if ($redirect == '')
		{
			$redirect = '';
		}
		else
		{
			$redirect = $this->core->decode($redirect);
		}
		$this->auth->logout($redirect);
	}

	function site()
	{
		// logout if not admin
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// check they are administrator
		if ($this->session->userdata('groupID') != $this->site->config['groupID'] && $this->session->userdata('groupID') >= 0)
		{
			redirect('/admin/dashboard/permissions');
		}

		// set object ID
		$objectID = array('siteID' => $this->siteID);

		// get values
		$output['data'] = $this->core->get_values('sites', $objectID);

		// set defaults
		$output['data']['shopVariation1'] = ($this->input->post('shopVariation1')) ? $this->input->post('shopVariation1') : $this->site->config['shopVariation1'];
		$output['data']['shopVariation2'] = ($this->input->post('shopVariation2')) ? $this->input->post('shopVariation2') : $this->site->config['shopVariation2'];
		$output['data']['shopVariation3'] = ($this->input->post('shopVariation3')) ? $this->input->post('shopVariation3') : $this->site->config['shopVariation3'];
		$output['data']['emailHeader'] = ($this->input->post('emailHeader')) ? $this->input->post('emailHeader') : $this->site->config['emailHeader'];
		$output['data']['emailFooter'] = ($this->input->post('emailFooter')) ? $this->input->post('emailFooter') : $this->site->config['emailFooter'];
		$output['data']['emailTicket'] = ($this->input->post('emailTicket')) ? $this->input->post('emailTicket') : $this->site->config['emailTicket'];
		$output['data']['emailAccount'] = ($this->input->post('emailAccount')) ? $this->input->post('emailAccount') : $this->site->config['emailAccount'];
		$output['data']['emailOrder'] = ($this->input->post('emailOrder')) ? $this->input->post('emailOrder') : $this->site->config['emailOrder'];
		$output['data']['emailDispatch'] = ($this->input->post('emailDispatch')) ? $this->input->post('emailDispatch') : $this->site->config['emailDispatch'];
		$output['data']['emailDonation'] = ($this->input->post('emailDonation')) ? $this->input->post('emailDonation') : $this->site->config['emailDonation'];
		$output['data']['emailSubscription'] = ($this->input->post('emailSubscription')) ? $this->input->post('emailSubscription') : $this->site->config['emailSubscription'];

		// handle post
		if (count($_POST))
		{
			// check some things aren't being posted
			if ($this->input->post('siteID') || $this->input->post('siteDomain') || $this->input->post('groupID'))
			{
				show_error('You do not have permission to change those things.');
			}

			// required
			$this->core->required = array(
				'siteName' => array('label' => 'Name of Site', 'rules' => 'required|trim'),
				'siteURL' => array('label' => 'URL', 'rules' => 'required|trim'),
				'siteEmail' => array('label' => 'Email', 'rules' => 'required|valid_email|trim'),
			);

			// set date
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");

			// update
			if ($this->core->update('sites', $objectID))
			{
				// where to redirect to
				$output['message'] = '<p>Your details have been updated.</p>';
			}
		}

		// get permission groups
		$output['groups'] = $this->permission->get_groups();

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('site',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function setup()
	{
		echo 'tset';
	}

	function backup()
	{
		// check permissions for this page
		if ($this->session->userdata('groupID') >= 0)
		{
			redirect('/admin/dashboard');
		}

		$filename = 'forge_backup_'.date('Y-m-d_H-i', time());

		// Set up our default preferences
		$prefs = array(
							'tables'		=> $this->db->list_tables(),
							'ignore'		=> array('ha_ci_sessions', 'ha_captcha', 'ha_permissions', 'ha_zipcodes'),
							'filename'		=> $filename.'.sql',
							'format'		=> 'gzip', // gzip, zip, txt
							'add_drop'		=> FALSE,
							'add_insert'	=> TRUE,
							'newline'		=> "\n"
						);

		// Is the encoder supported?  If not, we'll either issue an
		// error or use plain text depending on the debug settings
		if (($prefs['format'] == 'gzip' AND ! @function_exists('gzencode'))
		 OR ($prefs['format'] == 'zip'  AND ! @function_exists('gzcompress')))
		{
			if ($this->db->db_debug)
			{
				return $this->db->display_error('db_unsuported_compression');
			}

			$prefs['format'] = 'txt';
		}

		// Load the Zip class and output it
		$this->load->library('zip');
		$this->zip->add_data($prefs['filename'], $this->_backup($prefs));
		$backup = $this->zip->get_zip();

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($filename.'.zip', $backup);
	}

	function _backup($params = array())
	{
		if (count($params) == 0)
		{
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($params);

		// Build the output
		$output = '';
		foreach ((array)$tables as $table)
		{
			// Is the table in the "ignore" list?
			if (in_array($table, (array)$ignore, TRUE))
			{
				continue;
			}

			// Get the table schema
			$query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.'.$table);

			// No result means the table name was invalid
			if ($query === FALSE)
			{
				continue;
			}

			// Write out the table schema
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

 			if ($add_drop == TRUE)
 			{
				$output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}

			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{
					$output .= $val.';'.$newline.$newline;
				}
			}

			// If inserts are not needed we're done...
			if ($add_insert == FALSE)
			{
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query("SELECT * FROM $table WHERE siteID = ".$this->siteID);

			if ($query->num_rows() == 0)
			{
				continue;
			}

			// Fetch the field names and determine if the field is an
			// integer type.  We use this info to decide whether to
			// surround the data with quotes or not

			$i = 0;
			$field_str = '';
			$is_int = array();
			while ($field = mysql_fetch_field($query->result_id))
			{
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'),
										TRUE)
										) ? TRUE : FALSE;

				// Create a string of field names
				$field_str .= '`'.$field->name.'`, ';
				$i++;
			}

			// Trim off the end comma
			$field_str = preg_replace( "/, $/" , "" , $field_str);


			// Build the insert string
			foreach ($query->result_array() as $row)
			{
				$val_str = '';

				$i = 0;
				foreach ($row as $v)
				{
					// Is the value NULL?
					if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{
						// Escape the data if it's not an integer
						if ($is_int[$i] == FALSE)
						{
							$val_str .= $this->db->escape($v);
						}
						else
						{
							$val_str .= $v;
						}
					}

					// Append a comma
					$val_str .= ', ';
					$i++;
				}

				// Remove the comma at the end of the string
				$val_str = preg_replace( "/, $/" , "" , $val_str);

				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}

			$output .= $newline.$newline;
		}

		return $output;
	}

}
