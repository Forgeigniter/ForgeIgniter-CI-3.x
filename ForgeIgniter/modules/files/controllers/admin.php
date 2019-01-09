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

class Admin extends MX_Controller {

	// set defaults
	var $table = 'files';								// table to update
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/files/viewall';			// default redirect
	var $objectID = 'fileID';							// default unique ID
	var $permissions = array();

	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// get permissions and redirect if they don't have access to this module
		if (!$this->permission->permissions)
		{
			if (@$this->core->is_ajax())
			{
				die('<p>Sorry, you do not have permissions to do what you just tried to do. <a class="ficms_close" href="#">Close</a>.</p>');
			}
			else
			{
				redirect('/admin/dashboard/permissions');
			}
		}
		if (!in_array($this->uri->segment(2), $this->permission->permissions))
		{
			if (@$this->core->is_ajax())
			{
				die('<p>Sorry, you do not have permissions to do what you just tried to do. <a class="ficms_close" href="#">Close</a>.</p>');
			}
			else
			{
				redirect('/admin/dashboard/permissions');
			}
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load libs etc
		$this->load->model('files_model', 'files');
	}

	function index()
	{
		redirect($this->redirect);
	}

	function viewall($folderID = '')
	{
		if (count($_FILES))
		{
			// upload file
			if ($oldFileName = @$_FILES['file']['name'])
			{
				$this->uploads->allowedTypes = 'pdf|doc|mp3|zip|js|swf|flv|mp4|js|css|ico|txt|xls|ppt|ttf|cff|svg|woff|eot';

				if ($fileData = $this->uploads->upload_file())
				{
					$this->core->set['filename'] = $fileData['file_name'];
				}

				// get file errors if there are any
				if ($this->uploads->errors)
				{
					$this->form_validation->set_error($this->uploads->errors);
				}
				else
				{
					// format filename
					$filenames = explode('.', $oldFileName);
					$extension = end($filenames);
					$filename = str_replace('.'.$extension, '', $oldFileName);

					// set file reference and only add to db if its unique
					$fileRef = url_title(trim(strtolower($filename)));

					if ($this->form_validation->unique($fileRef, 'files.fileRef'))
					{
						// set file ref
						$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
						$this->core->set['fileRef'] = $fileRef;
						$this->core->set['filesize'] = $fileData['file_size'];
						$this->core->set['userID'] = $this->session->userdata('userID');

						// update
						if ($this->core->update('files'))
						{
							// where to redirect to
							redirect('/admin/files/viewall');
						}
					}
					else
					{
						$this->form_validation->set_error('This file has already been uploaded. Try renaming your local file.');
					}
				}
			}
		}

		// search
		if ($this->input->post('searchbox'))
		{
			$output['files'] = $this->files->search_files($this->input->post('searchbox'));
		}

		else
		{
			// set default wheres
			$where = array('siteID' => $this->siteID, 'deleted' => 0);

			// get preset selections for this dropdown
			if ($folderID == '' && @array_key_exists('folderID', $this->selections))
			{
				$folderID = $this->selections['folderID'];
			}

			// folderID
			if ($folderID != '')
			{
				// get ones uploaded by this user
				if ($folderID == 'me')
				{
					$where['userID'] = $this->session->userdata('userID');
				}

				// make sure that all is not selected
				elseif ($folderID != 'all' && $folderID != 'page' && $folderID != 'me')
				{
					$where['folderID'] = $folderID;
				}

				// set preset selections for this dropdown
				$this->session->set_userdata('selections', array($this->uri->segment(2) => array('folderID' => $folderID)));
			}

			// check they have permissions to see all files
			if (!@in_array('files_all', $this->permission->permissions))
			{
				$where['userID'] = $this->session->userdata('userID');
			}

			// grab data and display
			$output = $this->core->viewall($this->table, $where, NULL, 24);
		}

		// get folderID if set
		$output['folderID'] = $folderID;

		// get quota
		$output['quota'] = $this->site->get_quota();

		// get categories
		$output['folders'] = $this->files->get_folders();

		// view files
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit($fileID, $redirect = '', $popup = FALSE)
	{
		// set object ID
		$objectID = array($this->objectID => $fileID);

		// get values
		$output['data'] = $this->core->get_values($this->table, $objectID);

		// handle post
		if (count($_POST))
		{
			// get file errors if there are any
			if ($this->uploads->errors)
			{
				$this->form_validation->set_error($this->uploads->errors);
			}
			else
			{
				// update
				if ($this->core->update('files', $objectID))
				{
					// if its not coming from ajax then just go to admin
					if (!$redirect && !$popup)
					{
						$redirect = '/admin/files/viewall';
					}

					// where to redirect to
					redirect($redirect);
				}
			}
		}

		// define view (based on popup)
		$view = ($popup) ? 'admin/popup' : 'admin/edit';

		// get categories
		$output['folders'] = $this->files->get_folders();

		// templates
		if (!@$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view($view, $output);
		if (!@$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete($objectID)
	{
		// delete file
		$query = $this->db->get_where($this->table, array($this->objectID => $objectID));
		if ($row = $query->row_array())
		{
			$this->uploads->delete_file($row['filename']);
		}

		if ($this->core->delete($this->table, array($this->objectID => $objectID)));
		{
			// where to redirect to
			redirect($this->redirect);
		}
	}

	function browser()
	{
		// set default wheres
		$where = array('siteID' => $this->siteID, 'deleted' => 0);

		// check they have permissions to see all files
		if (!@in_array('files_all', $this->permission->permissions))
		{
			$where['userID'] = $this->session->userdata('userID');
		}

		// grab data and display
		$output = $this->core->viewall($this->table, array('folderID' => 0), 'fileRef', 999);

		// get folders
		if ($folders = $this->files->get_folders())
		{
			foreach($folders as $folder)
			{
				// grab data and display
				$data = $this->core->viewall($this->table, array('folderID' => $folder['folderID']), 'fileRef', 999);
				$output['folders'][$folder['folderID']]['folderName'] = $folder['folderName'];
				$output['folders'][$folder['folderID']]['files'] = $data['files'];
			}
		}

		$this->load->view('admin/browser',$output);
	}

	function folders()
	{
		// check permissions for this page
		if (!in_array('files', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array('folderName' => 'Folder Name');

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");

		// get values
		$output = $this->core->get_values('file_folders');

		// update
		if ($this->core->update('file_folders') && count($_POST))
		{
			// where to redirect to
			redirect('/admin/files/folders');
		}

		$output['folders'] = $this->files->get_folders();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/folders',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_folder()
	{
		// check permissions for this page
		if (!in_array('files', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// go through post and edit each list item
		$listArray = $this->core->get_post();
		if (count($listArray))
		{
			foreach($listArray as $ID => $value)
			{
				if ($ID != '' && sizeof($value) > 0)
				{
					// set object ID
					$objectID = array('folderID' => $ID);
					$this->core->set['folderName'] = $value['folderName'];
					$this->core->update('file_folders', $objectID);
				}
			}
		}

		// where to redirect to
		redirect('/admin/files/folders');
	}

	function delete_folder($folderID)
	{
		// check permissions for this page
		if (!in_array('files', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// where
		$objectID = array('folderID' => $folderID);

		if ($this->core->soft_delete('file_folders', $objectID))
		{
			// set children to no parent
			$this->files->update_children($folderID);

			// where to redirect to
			redirect('/admin/files/folders');
		}
	}

	function order($field = '')
	{
		$this->core->order(key($_POST), $field);
	}

	function ac_files()
	{
		$q = strtolower($_POST["q"]);
		if (!$q) return;

		// form dropdown
		$results = $this->files->search_files($q);

		// go foreach
		foreach((array)$results as $row)
		{
			$items[$row['fileRef']] = $row['fileRef'];
		}

		// output
		$output = '';
		foreach ($items as $key=>$value)
		{
			$output .= "$key|$value\n";
		}

		$this->output->set_output($output);
	}

}
