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

class Users extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/users/profile';					// default redirect
	var $permissions = array();
	var $partials = array();

	function __construct()
	{
		parent::__construct();

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

		//  load models and libs
		$this->load->library('tags');
		$this->load->model('community_model', 'community');
		$this->load->model('users_model', 'users');
		$this->load->library('auth');
		$this->load->library('email');

		// load modules
		$this->load->module('pages');
	}

	function index()
	{
		$this->profile();
	}

	function login($redirect = '')
	{
		$output = array();

		// set redirect to default if not given
		if ($redirect == '')
		{
			$redirect = $this->redirect;
		}
		else
		{
			$redirect = $this->core->decode($redirect);
		}

		if (!$this->session->userdata('session_user'))
		{
			// login
			if ($this->input->post('password'))
			{
				$username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));

				// set admin session name, if given
				if ($output = $this->auth->login($username, $this->input->post('password'), 'session_user', FALSE, $this->input->post('remember')))
				{
					// for use with ce
					if ($this->session->userdata('groupID') > 0 && $this->permission->get_group_permissions($this->session->userdata('groupID')))
					{
						$this->session->set_userdata('session_admin', TRUE);
					}

					// redirect
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

		// set title
		$output['page:title'] = $this->site->config['siteName'].' Login';

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// display with cms layer
		$this->pages->view('community_login', $output, 'community');
	}

	function logout($redirect = '')
	{
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

	function create_account($redirect = '')
	{
		// email is always required
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|unique[users.email]|trim|strtolower');

		// require password confirm?
		if (isset($_POST['confirmPassword']))
		{
			$this->form_validation->set_rules('confirmPassword', 'Password', 'required|matches[confirmPassword]');
		}
		else
		{
			$this->form_validation->set_rules('password', 'Password', 'required');
		}

		// require first name?
		if (isset($_POST['firstName']))
		{
			$this->form_validation->set_rules('firstName', 'First Name', 'required');
		}

		// require first name?
		if (isset($_POST['lastName']))
		{
			$this->form_validation->set_rules('lastName', 'Last Name', 'required');
		}

		// run create account script
		if (count($_POST) && $this->form_validation->run())
		{
			// create user
			$this->core->create_user();

			// set default redirect
			if (!$redirect)
			{
				$redirect = $this->core->encode('/users/');
			}

			// set login username
			$username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));

			// set header and footer
			$emailHeader = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailHeader']);
			$emailHeader = str_replace('{email}', $this->input->post('email'), $emailHeader);
			$emailFooter = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailFooter']);
			$emailFooter = str_replace('{email}', $this->input->post('email'), $emailFooter);
			$emailAccount = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailAccount']);
			$emailAccount = str_replace('{email}', $this->input->post('email'), $emailAccount);
			$emailAccount = str_replace('{password}', $this->input->post('password'), $emailAccount);


			// send email
			$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
			$this->email->to($this->input->post('email'));
			$this->email->subject('New account set up on '.$this->site->config['siteName']);
			$this->email->message($emailHeader."\n\n".$emailAccount."\n\n".$emailFooter);
			$this->email->send();

			// set admin session name, if given
			if (!$this->site->config['activation'])
			{
				$this->load->library('auth');
				$this->auth->login($username, $this->input->post('password'), 'session_user', $this->core->decode($redirect));
			}
			else
			{
				// at least set the name and email in to a session
				$this->session->set_userdata('email', $this->input->post('email'));
				$this->session->set_userdata('firstName', $this->input->post('firstName'));
				$this->session->set_userdata('lastName', $this->input->post('lastName'));

				redirect($this->core->decode($redirect));
			}
		}

		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Create Account';

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// populate template
		$output['form:email'] = set_value('email', $this->input->post('email'));
		$output['form:displayName'] = set_value('displayName', $this->input->post('displayName'));
		$output['form:firstName'] = set_value('firstName', $this->input->post('firstName'));
		$output['form:lastName'] = set_value('lastName', $this->input->post('lastName'));
		$output['form:postcode'] = set_value('postcode', $this->input->post('postcode'));
		$output['select:country'] = @display_countries('country', set_value('country', $this->input->post('country')), 'id="country" class="formelement"');

		// display with cms layer
		$this->pages->view('community_create_account', $output, 'community');
	}

	function account($redirect = '')
	{
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// required
		$this->core->required = array(
			'email' => array('label' => 'Email', 'rules' => 'valid_email|unique[users.email]|required|trim'),
			'firstName' => array('label' => 'First Name', 'rules' => 'required|trim|ucfirst'),
			'lastName' => array('label' => 'Last Name', 'rules' => 'required|trim|ucfirst'),
			'address1' => array('label' => 'Address1', 'rules' => 'trim|ucfirst'),
			'address2' => array('label' => 'Address2', 'rules' => 'trim|ucfirst'),
			'address3' => array('label' => 'Address3', 'rules' => 'trim|ucfirst'),
			'city' => array('label' => 'City / State', 'rules' => 'trim|ucfirst'),
			'displayName' => array('label' => 'Display Name', 'rules' => 'unique[users.displayName]|max_length[15]|min_length[3]|alpha_dash|trim')
		);

		// set object ID
		$objectID = array('userID' => $this->session->userdata('userID'));

		// get values
		$data = $this->core->get_values('users', $objectID);

		if (count($_POST))
		{
			// set default error message
			$error = '';

			// upload image
			if (@$_FILES['image']['name'] != '')
			{
				// set upload config
				$img_upload_path = site_url().'static/uploads/avatars';
				$this->uploads->allowedTypes = 'gif|jpg|png';
				$this->uploads->uploadsPath .= '/avatars';
				$this->uploads->maxSize = '100000';
				$this->uploads->maxWidth = '2000';
				$this->uploads->maxHeight = '2000';

				// upload avatar
				if ($imageData = $this->uploads->upload_image(FALSE))
				{
					$this->core->set['avatar'] = $imageData['file_name'];
				}

				// set error
				$error = ($this->uploads->errors) ? 'Problem with your image: '.$this->uploads->errors : '';
			}

			// upload logo
			if (@$_FILES['logo']['name'] != '')
			{
				// set upload config
				$this->uploads->allowedTypes = 'gif|jpg|png';
				$this->uploads->uploadsPath .= '/avatars';
				$this->uploads->maxSize = '100000';
				$this->uploads->maxWidth = '2000';
				$this->uploads->maxHeight = '2000';

				// upload logo
				if ($imageData = $this->uploads->upload_image(FALSE, NULL, 'logo'))
				{
					$this->core->set['companyLogo'] = $imageData['file_name'];
				}

				// set error
				$error = ($this->uploads->errors) ? 'Problem with your logo: '.$this->uploads->errors : '';
			}

			// get image errors if there are any
			if ($error)
			{
				$this->form_validation->set_error($error);
			}
			else
			{
				// security check
				if ($this->input->post('username')) $this->core->set['username'] = $data['username'];
				if ($this->input->post('subscribed')) $this->core->set['subscribed'] = $data['subscribed'];
				if ($this->input->post('siteID')) $this->core->set['siteID'] = $this->siteID;
				if ($this->input->post('userID')) $this->core->set['userID'] = $data['userID'];
				if ($this->input->post('resellerID')) $this->core->set['resellerID'] = $data['resellerID'];
				if ($this->input->post('kudos')) $this->core->set['kudos'] = $data['kudos'];
				if ($this->input->post('posts')) $this->core->set['posts'] = $data['posts'];

				// update
				if ($this->core->update('users', $objectID))
				{
					// get updated row
					$row = $this->core->viewall('users', $objectID, NULL, 1);

					// remove the password field
					unset($row['users'][0]['password']);

					// set session data
					$this->session->set_userdata($row['users'][0]);

					// update image data in session
					if (isset($imageData))
					{
						$this->session->set_userdata('avatar', $imageData['file_name']);
					}

					// set success message
					$this->session->set_flashdata('success', 'Your details have been updated.');

					// redirect
					if ($redirect)
					{
						redirect('/users/'.$redirect);
					}
					else
					{
						redirect('/users/account');
					}
				}
			}
		}

		// set title
		$output['page:title'] = $this->site->config['siteName'].' - Account';

		// load errors
		$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;

		// if reverted show a message
		if ($message = $this->session->flashdata('success'))
		{
			$output['message'] = $message;
		}

		// populate template
		$output['user:avatar'] = anchor('/users/profile/'.$data['userID'], display_image($this->users->get_avatar($data['avatar']), 'User Avatar', 150, 'class="bordered"', base_url().$this->config->item('staticPath').'/images/noavatar.gif'));
		$output['user:logo'] = anchor('/users/profile/'.$data['userID'], display_image($this->users->get_avatar($data['companyLogo']), 'Company Logo', 150, 'class="bordered"'));
		$output['form:email'] = set_value('email', $data['email']);
		$output['form:displayName'] = set_value('displayName', $data['displayName']);
		$output['form:firstName'] = set_value('firstName', $data['firstName']);
		$output['form:lastName'] = set_value('lastName', $data['lastName']);
		$output['form:bio'] = set_value('bio', $data['bio']);
		$output['form:website'] = set_value('website', $data['website']);
		$output['form:signature'] = set_value('signature', $data['signature']);
		$output['form:companyName'] = set_value('companyName', $data['companyName']);
		$output['form:companyEmail'] = set_value('companyEmail', $data['companyEmail']);
		$output['form:companyWebsite'] = set_value('companyWebsite', $data['companyWebsite']);
		$output['form:companyDescription'] = set_value('companyDescription', $data['companyDescription']);
		$output['form:address1'] = set_value('address1', $data['address1']);
		$output['form:address2'] = set_value('address2', $data['address2']);
		$output['form:address3'] = set_value('address3', $data['address3']);
		$output['form:city'] = set_value('city', $data['city']);
		$output['form:postcode'] = set_value('postcode', $data['postcode']);
		$output['form:phone'] = set_value('phone', $data['phone']);
		$output['select:country'] = @display_countries('country', set_value('country', $data['country']), 'id="country" class="formelement"');
		$values = array(
			'V' => 'Everyone can see my profile',
			'H' => 'Hide my profile and feed'
		);
		$output['select:privacy'] = @form_dropdown('privacy',$values,set_value('privacy', $data['privacy']), 'id="privacy" class="formelement"');
		$values = array(
			0 => 'No',
			1 => 'Yes',
		);
		$output['select:notifications'] = @form_dropdown('notifications',$values,set_value('notifications', $data['notifications']), 'id="notifications" class="formelement"');
		$output['select:currency'] = @form_dropdown('currency', currencies(), set_value('currency', $data['currency']), 'id="currency" class="formelement"');
		$output['select:language'] = @form_dropdown('language', languages(), set_value('language', $data['language']), 'id="language" class="formelement"');

		// display with cms layer
		$this->pages->view('community_account', $output, 'community');
	}

	function delete_avatar()
	{
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/');
		}

		// check the user is themself
		if ($user = $this->users->get_user($this->session->userdata('userID')))
		{
			// remove reference to avatar
			if ($this->users->delete_avatar())
			{
				if ($this->uploads->delete_file('avatars/'.$user['avatar']))
				{
					redirect('users/account');
				}
				else
				{
					show_error('Something went wrong!');
				}
			}
			else
			{
				show_error('Something went wrong!');
			}
		}
	}

	function delete_logo()
	{
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/');
		}

		// check the user is themself
		if ($user = $this->users->get_user($this->session->userdata('userID')))
		{
			// remove reference to avatar
			if ($this->users->delete_logo())
			{
				if ($this->uploads->delete_file('avatars/'.$user['companyLogo']))
				{
					redirect('users/account');
				}
				else
				{
					show_error('Something went wrong!');
				}
			}
			else
			{
				show_error('Something went wrong!');
			}
		}
	}

	function profile($userID = '')
	{
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_user'))
		{
			redirect('/users/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// redirect so that the permalink is set to their ID
		if (!$userID)
		{
			redirect('/users/profile/'.$this->session->userdata('userID'));
		}

		// get this user data
		if (!$data['user'] = $this->users->get_user($userID))
		{
			show_error('No user was found!');
		}

		// load helper
		$this->load->helper('bbcode');

		// show logged in user profile
		if ($userID == $this->session->userdata('userID'))
		{
			// set title
			$output['page:title'] = 'Your Profile - '.$this->site->config['siteName'];

			// set view file
			$viewFile = 'community_home';
		}

		// show user data for selected user
		else
		{
			// get user data
			if ($data['user'] = $this->users->get_user($userID))
			{
				// set title
				$output['page:title'] = $this->site->config['siteName'].' | '.$data['user']['firstName'].'\'s Profile';

				// set view file (based on privacy)
				if ($data['user']['privacy'] == 'H' && $data['user']['userID'] != $this->session->userdata('userID'))
				{
					$viewFile = 'community_view_profile_private';
				}
				else
				{
					$viewFile = 'community_view_profile';
				}
			}
			else
			{
				show_404();
			}
		}
		$site_base_path = realpath('');
		// populate template
		$output['user:id'] = $userID;
		$output['user:name'] = ($data['user']['displayName']) ? $data['user']['displayName'] : $data['user']['firstName'].' '.$data['user']['lastName'];
		$output['user:avatar'] = anchor('/users/profile/'.$data['user']['userID'], display_image($this->users->get_avatar($data['user']['avatar']), 'User Avatar', 100, 'class="bordered"', site_url().$this->config->item('staticPath').'/images/noavatar.gif'));
		$output['user:country'] = lookup_country($data['user']['country']);

		// load bio
		$data['user']['bio'] .= ($userID == $this->session->userdata('userID')) ? '  [[url=/users/account#changebio]Update[/url]]' : '';
		$output['user:bio'] = (($data['user']['privacy'] == 'V' || $data['user']['userID'] == $this->session->userdata('userID')) && $data['user']['bio']) ? bbcode($data['user']['bio']) : FALSE;

		// load website
		$output['user:website'] = ($data['user']['website']) ? $data['user']['website'] : '';

		// load company
		$output['user:company'] = ($data['user']['companyName']) ? $data['user']['companyName'] : '';
		$output['user:company-website'] = ($data['user']['companyWebsite']) ? $data['user']['companyWebsite'] : '';
		$data['user']['companyDescription'] .= ($userID == $this->session->userdata('userID')) ? ' [[url=/users/account#changework]Update[/url]]' : '';
		$output['user:company-description'] = (($data['user']['privacy'] == 'V' || $data['user']['userID'] == $this->session->userdata('userID')) && $data['user']['companyDescription']) ? bbcode($data['user']['companyDescription']) : FALSE;

		// load content
		$output['profile:navigation'] = $this->parser->parse('partials/profile_navigation', $data, TRUE);

		// set page heading
		$output['page:heading'] = $data['user']['firstName'].' '.$data['user']['lastName'] . (($data['user']['displayName']) ? ' <small>('.$data['user']['displayName'].')</small>' : '');

		// display with cms layer
		$this->pages->view($viewFile, $output, 'community');
	}

	function search($tag = '')
	{
		// get partials
		$output = $this->partials;

		// set tags
		$query = ($tag) ? $tag : strip_tags($this->input->post('query', TRUE));

		if ($userIDs = $this->users->search_users($query))
		{
			// get members
			if ($users = $this->users->get_users($userIDs))
			{
				foreach($users as $user)
				{
					$output['members'][] = array(
						'member:avatar' => anchor('/users/profile/'.$user['userID'], display_image($this->users->get_avatar($user['avatar']), 'User Avatar', 80, 'class="avatar"', site_url().$this->config->item('staticPath').'/images/noavatar.gif')),
						'member:name' => ($user['displayName']) ? $user['displayName'] : $user['firstName'].' '.$user['lastName'],
						'member:link' => site_url('/users/profile/'.$user['userID'])
					);
				}
			}
		}

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Searching Users for "'.$query.'"';
		$output['page:heading'] = 'Search Users for: "'.$query.'"';

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// display with cms layer
		$this->pages->view('community_members', $output, 'community');
	}

	function ac_search()
	{
		$tags = strtolower($_POST["q"]);
        if (!$tags)
        {
        	return FALSE;
        }

		if ($objectIDs = $this->users->search_users($tags))
		{
			// form dropdown and myql get countries
			if ($searches = $this->users->get_users($objectIDs))
			{
				// go foreach
				foreach($searches as $search)
				{
					$items[$search['firstName'].' '.$search['lastName'].' '.$search['displayName'].''] = array('id' => $search['userID'], 'name' => $search['firstName'].' '.$search['lastName']);
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

	function forgotten()
	{
		// get partials
		$output = $this->partials;

		// get image errors if there are any
		if (count($_POST))
		{
			// check user exists and send email
			if ($user = $this->users->get_user_by_email($this->input->post('email')))
			{
				// set key
				$key = md5($user['userID'].time());
				$this->users->set_reset_key($user['userID'], $key);

				// set header and footer
				$emailHeader = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailHeader']);
				$emailHeader = str_replace('{email}', $user['email'], $emailHeader);
				$emailFooter = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailFooter']);
				$emailFooter = str_replace('{email}', $user['email'], $emailFooter);

				// send email
				$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
				$this->email->to($user['email']);
				$this->email->subject('Password reset request on '.$this->site->config['siteName']);
				$this->email->message($emailHeader."\n\nA password reset request has been submitted on ".$this->site->config['siteName'].". If you did not request to have your password reset please ignore this email.\n\nIf you did want to reset your password please click on the link below.\n\n".site_url('users/reset/'.$key)."\n\n".$emailFooter);
				$this->email->send();

				$output['message'] = 'Thank you. An email was sent out with instructions on how to reset your password.';
			}
			else
			{
				$output['errors'] = '<p>There was a problem finding that email on our database, please contact support.</p>';
			}
		}

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Forgotten Password';
		$output['page:heading'] = 'Forgotten Password';

		// display with cms layer
		$this->pages->view('community_forgotten', $output, 'community');
	}

	function reset($key = '')
	{
		// get partials
		$output = $this->partials;

		// required
		$this->core->required = array(
			'password' => array('label' => 'Password', 'rules' => 'required|matches[confirmPassword]'),
			'confirmPassword' => array('label' => 'Confirm Password', 'rules' => 'required'),
		);

		// check user exists and send email
		if (!$user = $this->users->check_key($key))
		{
			show_error('That key was invalid, please contact support.');
		}
		else
		{
			// set object ID
			$objectID = array('userID' => $user['userID']);

			// get values
			$data = $this->core->get_values('users', $objectID);

			if (count($_POST))
			{
				// unset key
				$this->core->set['resetkey'] = '';

				// security check
				if ($this->input->post('username')) $this->core->set['username'] = $data['username'];
				if ($this->input->post('premium')) $this->core->set['premium'] = $data['premium'];
				if ($this->input->post('siteID')) $this->core->set['siteID'] = $this->siteID;
				if ($this->input->post('userID')) $this->core->set['userID'] = $data['userID'];
				if ($this->input->post('resellerID')) $this->core->set['resellerID'] = $data['resellerID'];
				if ($this->input->post('kudos')) $this->core->set['kudos'] = $data['kudos'];
				if ($this->input->post('posts')) $this->core->set['posts'] = $data['posts'];

				// update
				if ($this->core->update('users', $objectID))
				{
					// set header and footer
					$emailHeader = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailHeader']);
					$emailHeader = str_replace('{email}', $user['email'], $emailHeader);
					$emailFooter = str_replace('{name}', $user['firstName'].' '.$user['lastName'], $this->site->config['emailFooter']);
					$emailFooter = str_replace('{email}', $user['email'], $emailFooter);

					// send email
					$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
					$this->email->to($user['email']);
					$this->email->subject('Your password was reset on '.$this->site->config['siteName']);
					$this->email->message($emailHeader."\n\nYour password for ".$this->site->config['siteName']." has been reset!\n\n".$emailFooter);
					$this->email->send();

					$output['message'] = 'Thank you. Your password was reset.';
				}
			}

			// load errors
			$output['errors'] = (validation_errors()) ? validation_errors() : FALSE;
		}


		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Reset Password';
		$output['page:heading'] = $this->site->config['siteName'].' | Reset Password';

		// display with cms layer
		$this->pages->view('community_reset', $output, 'community');
	}
}
