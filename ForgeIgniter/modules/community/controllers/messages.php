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

class Messages extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/messages';							// default redirect
	var $permissions = array();
	var $partials = array();

	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// get site permissions and redirect if it don't have access to this module
		if (!$this->permission->sitePermissions)
		{
			show_error('You do not have permission to view this page');
		}
		if (!in_array('community', $this->permission->sitePermissions))
		{
			show_error('You do not have permission to view this page');
		}		

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}	

		// load libs etc
		$this->load->library('tags');		
		$this->load->model('community_model', 'community');
		$this->load->model('messages_model', 'messages');
		$this->load->model('users_model', 'users');

		// load modules
		$this->load->module('pages');
	}

	function index()
	{
		$this->view_messages();
	}

	function view_messages()
	{	
		// load helper
		$this->load->helper('bbcode');		
	
		if ($messages = $this->messages->get_messages())
		{
			foreach($messages as $message)
			{
				$output['messages'][] = array(
					'message:class' => ($message['unread'] && $message['userID'] != $this->session->userdata('userID')) ? ' unread ' : '',
					'user:avatar' => anchor('/users/profile/'.$message['userID'], display_image($this->users->get_avatar($message['avatar']), 'User Avatar', 40, 'class="avatar"', $this->config->item('staticPath').'/images/noavatar.gif')),
					'user:name' => ($message['displayName']) ? $message['displayName'] : $message['firstName'].' '.$message['lastName'],
					'user:link' => site_url('/users/profile/'.$message['userID']),
					'message:link' => site_url('/messages/read/'.(($message['parentID'] > 0) ? $message['parentID'].'#reply'.$message['lastMessageID'] : $message['messageID'])),
					'message:title' => $message['subject'],
					'message:date' => dateFmt($message['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y, H:i' : 'jS M Y, H:i'),
					'message:body' => (strlen(bbcode($message['message'])) > 80) ? substr(bbcode($message['message']), 0, 100).'...' : bbcode($message['message']),
					'message:id' => $message['messageID']
				);
			}
		}

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Messages';	
		$output['page:heading'] = 'Messages';

		// display with cms layer	
		$this->pages->view('community_messages', $output, 'community');	
	}

	function read($messageID)
	{	
		// load helper
		$this->load->helper('bbcode');		
	
		if (!$message = $this->messages->get_message($messageID))
		{
			show_error('You are not authorised to read this message.');
		}

		// add read
		$this->messages->read_message($messageID);

		// get replies
		if ($replies = $this->messages->get_replies($messageID))
		{
			foreach($replies as $reply)
			{
				$output['message:replies'][] = array(
					'reply:id' => $reply['messageID'],
					'reply:avatar' => anchor('/users/profile/'.$reply['userID'], display_image($this->users->get_avatar($reply['avatar']), 'User Avatar', 60, 'class="avatar"', $this->config->item('staticPath').'/images/noavatar.gif')),
					'reply:link' => site_url('/users/profile/'.$reply['userID']),
					'reply:name' => ($reply['displayName']) ? $reply['displayName'] : $reply['firstName'].' '.$reply['lastName'],
					'reply:title' => $reply['subject'],
					'reply:date' => dateFmt($reply['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y, H:i' : 'jS M Y, H:i'),
					'reply:body' => bbcode($reply['message'])
				);
			}
		}

		// get user details
		$output['user:avatar'] = anchor('/users/profile/'.$message['userID'], display_image($this->users->get_avatar($message['avatar']), 'User Avatar', 80, 'class="avatar"', $this->config->item('staticPath').'/images/noavatar.gif'));
		$output['user:name'] = (($message['displayName']) ? $message['displayName'] : $message['firstName'].' '.$message['lastName']);
		$output['user:link'] = site_url('/users/profile/'.$message['userID']);

		// populate template
		$output['message:title'] = $message['subject'];
		$output['message:body'] = bbcode($message['message']);
		$output['message:id'] = $message['messageID'];
		
		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Messages';	

		// display with cms layer	
		$this->pages->view('community_messages_read', $output, 'community');	
	}	

	function send_message($toUserID = '', $popup = FALSE)
	{
		// make sure toUserID is set
		if (!$toUserID || !($data['user'] = $this->users->get_user($toUserID)) || $toUserID == $this->session->userdata('userID'))
		{
			show_error('There was no user to send to!');
		}

		// required
		$this->core->required = array(
			'subject' => array('label' => 'Subject', 'rules' => 'required'),		
			'message' => array('label' => 'Message', 'rules' => 'required')
		);

		// get values
		$data['data'] = $this->core->get_values('community_messages');	
		
		if (count($_POST))
		{			
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['userID'] = $this->session->userdata('userID');

			// add message to db
			if ($this->core->update('community_messages'))
			{
				// get message ID
				$messageID = $this->db->insert_id();
				
				// add message map
				$this->messages->add_messagemap($toUserID, $messageID);

				// get user data
				$data['user'] = $this->users->get_user($toUserID);

				if ($data['user']['notifications'])
				{
					// set header and footer
					$emailHeader = str_replace('{name}', $data['user']['firstName'].' '.$data['user']['lastName'], $this->site->config['emailHeader']);
					$emailHeader = str_replace('{email}', $data['user']['email'], $emailHeader);
					$emailFooter = str_replace('{name}', $data['user']['firstName'].' '.$data['user']['lastName'], $this->site->config['emailFooter']);
					$emailFooter = str_replace('{email}', $data['user']['email'], $emailFooter);
										
					// send email
					$this->load->library('email');
					$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
					$this->email->to($data['user']['email']);			
					$this->email->subject('New Message on '.$this->site->config['siteName']);
					$this->email->message($emailHeader."\n\n".$this->session->userdata('firstName')." ".$this->session->userdata('lastName')." has sent you a message. You can log in using the link below and read your new message.\n\n".site_url('/messages')."\n\n----------------------------------------\n\nThey said:\n\n".$this->input->post('message')."\n\n----------------------------------------\n\n".$emailFooter);
					$this->email->send();
				}

				// redirect
				redirect('messages');
			}
		}

		// populate template
		$output['form:to'] = ($this->input->post('to')) ? $this->input->post('to') : $data['user']['firstName'].' '.$data['user']['lastName'];
		$output['form:recipient-id'] = $data['user']['userID'];		
		$output['form:subject'] = set_value('subject', $this->input->post('subject'));
		$output['form:message'] = set_value('mesage', $this->input->post('message'));	

		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Send Message';

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// load content into a popup
		if ($popup !== FALSE && $popup == 'popup')
		{
			// display with cms layer	
			$this->pages->view('community_messages_popup', $output, 'community');
		}
		else
		{
			// display with cms layer	
			$this->pages->view('community_messages_form', $output, 'community');
		}
	}

	function send_reply($messageID = '')
	{
		// make sure messageID is set
		if (!$messageID || !$data['message'] = $this->messages->get_message($messageID))
		{
			show_error('Something went wrong!');
		}

		// required
		$this->core->required = array(
			'message' => array('label' => 'Message', 'rules' => 'required')
		);

		// get values
		$data['data'] = $this->core->get_values('community_messages');
		
		if (count($_POST))
		{			
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['userID'] = $this->session->userdata('userID');
			$this->core->set['subject'] = 'RE: '.$data['message']['subject'];

			// add message to db
			if ($this->core->update('community_messages'))
			{
				// get message ID
				$replyID = $this->db->insert_id();

				// set toUserID
				$toUserID = $this->messages->get_recipient($messageID);
				
				// add message map
				$this->messages->add_messagemap($toUserID, $replyID, $messageID);

				// get user data
				$data['user'] = $this->users->get_user($data['message']['userID']);

				if ($data['user']['notifications'] && $data['message']['userID'] != $this->session->userdata('userID'))
				{
					// set header and footer
					$emailHeader = str_replace('{name}', $data['user']['firstName'].' '.$data['user']['lastName'], $this->site->config['emailHeader']);
					$emailHeader = str_replace('{email}', $data['user']['email'], $emailHeader);
					$emailFooter = str_replace('{name}', $data['user']['firstName'].' '.$data['user']['lastName'], $this->site->config['emailFooter']);
					$emailFooter = str_replace('{email}', $data['user']['email'], $emailFooter);
										
					// send email
					$this->load->library('email');
					$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
					$this->email->to($data['user']['email']);			
					$this->email->subject('New Message on '.$this->site->config['siteName']);
					$this->email->message($emailHeader."\n\n".$this->session->userdata('firstName')." ".$this->session->userdata('lastName')." has sent you a message. You can log in using the link below and read your new message.\n\n".site_url('/messages')."\n\n".$emailFooter);
					$this->email->send();
				}				

				// redirect
				redirect('messages/read/'.$messageID.'#reply'.$replyID);
			}
		}

		// populate template
		$output['form:to'] = set_value('to', $this->input->post('to'));
		$output['form:subject'] = set_value('subject', $this->input->post('subject'));
		$output['form:message'] = set_value('mesage', $this->input->post('message'));		

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Send Reply';

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// display with cms layer	
		$this->pages->view('community_messages_form', $output, 'community');
	}

	function search($query = '')
	{				
		// load helper
		$this->load->helper('bbcode');
		
		// get partials
		$output = $this->partials;	

		// set query
		$data['query'] = ($query) ? $query : $this->input->post('query');

		// search
		if ($messages = $this->messages->search_messages($data['query']))
		{
			foreach($messages as $message)
			{
				$output['messages'][] = array(
					'message:class' => ($message['unread'] && $message['userID'] != $this->session->userdata('userID')) ? ' unread ' : '',
					'user:avatar' => anchor('/users/profile/'.$message['userID'], display_image($this->users->get_avatar($message['avatar']), 'User Avatar', 40, 'class="avatar"', $this->config->item('staticPath').'/images/noavatar.gif')),
					'user:name' => ($message['displayName']) ? $message['displayName'] : $message['firstName'].' '.$message['lastName'],
					'user:link' => site_url('/users/profile/'.$message['userID']),
					'message:link' => site_url('/messages/read/'.(($message['parentID'] > 0) ? $message['parentID'].'#reply'.$message['lastMessageID'] : $message['messageID'])),
					'message:title' => $message['subject'],
					'message:date' => dateFmt($message['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y, H:i' : 'jS M Y, H:i'),
					'message:body' => (strlen(bbcode($message['message'])) > 80) ? substr(bbcode($message['message']), 0, 100).'...' : bbcode($message['message']),
					'message:id' => $message['messageID']
				);
			}
		}		

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Searching Messages for "'.$data['query'].'"';
		$output['page:heading'] = 'Search Messages for: "'.$data['query'].'"';	

		// display with cms layer	
		$this->pages->view('community_messages', $output, 'community');
	}

	function delete_message($messageID)
	{
		// make sure messageID is set
		if (!$messageID)
		{
			show_error('No message!');
		}

		// delete message
		if ($this->messages->delete_message($messageID))
		{
			// redirect
			redirect('messages');
		}
	}

	function ac_search()
	{
		$query = strtolower($_POST["q"]);
        if (!$query)
        {
        	return FALSE;
        }
	
		// form dropdown and myql get countries
		if ($searches = $this->messages->search_messages($query))
		{
			// go foreach
			foreach($searches as $search)
			{
				$items[$search['subject']] = array('id' => $search['messageID'], 'name' => $search['subject']);
			}
			foreach ($items as $key=>$value)
			{
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