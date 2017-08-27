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

class Admin extends CI_Controller {

	// set defaults
	var $table = 'images';								// table to update
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/images/viewall';			// default redirect
	var $objectID = 'imageID';							// default unique ID									
	var $permissions = array();
	var $sitePermissions = array();	
	var $selections = array();
	
	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// get site permissions and redirect if it don't have access to this module
		$this->permission->sitePermissions = $this->permission->get_group_permissions($this->site->config['groupID']);
				
		// get permissions and redirect if they don't have access to this module
		if (!$this->permission->permissions)
		{
			if (@$this->core->is_ajax())
			{
				die('<p>Sorry, you do not have permissions to do what you just tried to do. <a class="halogycms_close" href="#">Close</a></p>');
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
				die('<p>Sorry, you do not have permissions to do what you just tried to do. <a class="halogycms_close" href="#">Close</a></p>');
			}
			else
			{			
				redirect('/admin/dashboard/permissions');
			}
		}

		// get preset selections for this module
		$selections = $this->session->userdata('selections');
		$this->selections = (is_array($selections)) ? @$selections[$this->uri->segment(2)] : '';

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// load libs etc
		$this->load->model('images_model', 'images');
	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
	function viewall($folderID = '')
	{
		if (count($_FILES))
		{			
			// allowed ZIP mime types
			$allowedZips = array('application/x-zip', 'application/zip', 'application/x-zip-compressed');
			
			if ($this->input->post('upload_zip'))
			{
				if (substr($_FILES['zip']['name'],-3) == 'zip' && in_array($_FILES['zip']['type'], $allowedZips))
				{
					// get started
					$success = FALSE;
					$this->load->library('zip');
					$this->load->library('encrypt');
					$this->load->library('image_lib');
	
					// unzip files
					$uploadsPath = $this->uploads->uploadsPath;
					
					$zip = zip_open($_FILES['zip']['tmp_name']);
					if ($zip)
					{
						// cycle through the zip
						while ($zip_entry = zip_read($zip))
						{
							if (!preg_match('/(\_)+MACOSX/', zip_entry_name($zip_entry)) && preg_match('/\.(jpg|gif|png)$/i', zip_entry_name($zip_entry)))
							{
								if (zip_entry_filesize($zip_entry) > 300000)
								{
									$this->form_validation->set_error('<p>Some files were too big to upload. Please only use small gfx files under 300kb.</p>');
								}
								else
								{
									// format filename
									$filenames = explode('.', zip_entry_name($zip_entry));
									$filename = trim(basename($filenames[0]));
									$extension = end($filenames);
									
									// get file name
									$imageRef = url_title(trim(strtolower($filename)));
		
									// check ref is unique and upload
									if ($this->form_validation->unique($imageRef, 'images.imageRef'))
									{																
										// set stuff
										$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
										$this->core->set['imageName'] = 'Graphic';
										$this->core->set['filename'] = md5($filename).'.'.$extension;
										$this->core->set['imageRef'] = $imageRef;
										$this->core->set['filesize'] = floor(zip_entry_filesize($zip_entry) / 1024);
										$this->core->set['groupID'] = 1;
										$this->core->set['userID'] = $this->session->userdata('userID');

										// update and then unset easy
										if ($this->core->update('images'));
																				
										// upload file
										$fp = fopen('.'.$uploadsPath.'/'.md5($filename).'.'.$extension, "w+");				
										if (zip_entry_open($zip, $zip_entry, "r"))
										{
											$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
											zip_entry_close($zip_entry);
										}
										fwrite($fp, $buf);
										fclose($fp);
										
										// get image size
										$imageSize = @getimagesize('.'.$uploadsPath.'/'.md5($filename).'.'.$extension);

										// make a thumbnail
										if ($imageSize[0] > $this->uploads->thumbSize || $imageSize[1] > $this->uploads->thumbSize)
										{
											$config['image_library'] = 'gd2';
											$config['source_image'] = '.'.$uploadsPath.'/'.md5($filename).'.'.$extension;
											$config['create_thumb'] = true;
											$config['maintain_ratio'] = true;
											$config['width'] = $this->uploads->thumbSize;
											$config['height'] = $this->uploads->thumbSize;
										
											$this->image_lib->initialize($config);
											$this->image_lib->resize();
										}
	
										$success = TRUE;							
									}
								}
							}
						}
						zip_close($zip);
					}
	
					// redirect
					if ($success === TRUE)
					{
						redirect('/admin/images/viewall/'.(($this->input->post('folderID')) ? $this->input->post('folderID') : ''));
					}
				}
				else
				{
					$this->form_validation->set_error('<p>There was a problem opening the zip file, sorry.</p>');
				}				
			}

			// upload image
			elseif ($oldFileName = @$_FILES['image']['name'])
			{
				$this->uploads->allowedTypes = 'jpg|gif|png';
				
				// get image name
				$imageName = ($this->input->post('imageName')) ? $this->input->post('imageName') : preg_replace('/.([a-z]+)$/i', '', $oldFileName);
				
				// set image reference and only add to db if its unique
				$imageRef = url_title(trim(substr(strtolower($imageName),0,30)));
		
				if ($this->form_validation->unique($imageRef, 'images.imageRef'))
				{	
					if ($imageData = $this->uploads->upload_image())
					{
						$this->core->set['filename'] = $imageData['file_name'];
						$this->core->set['filesize'] = $imageData['file_size'];						
					}
		
					// get image errors if there are any
					if ($this->uploads->errors)
					{
						$this->form_validation->set_error($this->uploads->errors);
					}
					else
					{						
						// set image ref
						$this->core->set['class'] = 'default';
						$this->core->set['imageRef'] = $imageRef;
						$this->core->set['imageName'] = ($this->input->post('imageName')) ? $this->input->post('imageName') : 'Image';
						$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
						$this->core->set['userID'] = $this->session->userdata('userID');												
				
						// update
						if ($this->core->update('images'))
						{
							// where to redirect to
							redirect('/admin/images/viewall/'.(($this->input->post('folderID')) ? $this->input->post('folderID') : ''));
						}			
					}
				}
				else
				{
					$this->form_validation->set_error('<p>The image reference you entered has already been used, please try another.</p>');
				}		
			}			
		}

		// search
		if ($this->input->post('searchbox'))
		{
			$output['images'] = $this->images->search_images($this->input->post('searchbox'));
		}
		
		// get images
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
	
			// check they have permissions to see all images
			if (!@in_array('images_all', $this->permission->permissions))
			{
				$where['userID'] = $this->session->userdata('userID');
			}
			
			// grab data and display
			$output = $this->core->viewall($this->table, $where, NULL, 15);
		}

		// get folderID if set	
		$output['folderID'] = $folderID;		

		// get quota
		$output['quota'] = $this->site->get_quota();

		// get categories
		$output['folders'] = $this->images->get_folders();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit($imageID, $redirect = '', $popup = FALSE)
	{
		// required
		$this->core->required = array(
			'imageRef' => array('label' => 'Image name', 'rules' => 'required|unique[images.imageRef]')
		);
		
		// set object ID
		$objectID = array($this->objectID => $imageID);

		// get values
		$output['data'] = $this->core->get_values($this->table, $objectID);

		// handle post
		if (count($_POST))
		{
			// set image reference and only add to db if its unique
			$imageRef = url_title(trim(substr(strtolower($this->input->post('imageRef')),0,30)));
			
			if ($oldFileName = @$_FILES['image']['name'])
			{
				$this->uploads->allowedTypes = 'jpg|gif|png';
						
				if (!$this->form_validation->unique($imageRef, 'images.imageRef') && $this->input->post('imageRef') != $output['data']['imageRef'])
				{	
					$this->uploads->errors = '<p>The image reference you entered has already been used, please try another.</p>';
				}
				else
				{
					if ($imageData = $this->uploads->upload_image())
					{
						$this->core->set['filename'] = $imageData['file_name'];
						$this->core->set['filesize'] = $imageData['file_size'];
					}					
				}
			}

			// get image errors if there are any
			if ($this->uploads->errors)
			{
				$this->form_validation->set_error($this->uploads->errors);
			}
			else
			{			
				// set image ref
				$this->core->set['imageRef'] = $imageRef;
				$this->core->set['dateModified'] = date("Y-m-d H:i:s");	
		
				// update
				if ($this->core->update('images', $objectID))
				{
					// if its not coming from ajax then just go to admin
					if ($redirect && !$popup)
					{						
						$redirect = $this->core->decode($redirect);
					}
					elseif (!$redirect && !$popup)
					{						
						$redirect = '/admin/images/viewall';
					}
					
					// where to redirect to
					redirect($redirect);
				}			
			}
		}

		// define view (based on popup)
		$view = ($popup) ? 'admin/popup' : 'admin/edit';
		
		// get categories
		$output['folders'] = $this->images->get_folders();
		
		// templates
		if (!@$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view($view, $output);
		if (!@$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete($objectID, $redirect = '')
	{
		// delete image
		$query = $this->db->get_where($this->table, array($this->objectID => $objectID));
		if ($row = $query->row_array())
		{
			$this->uploads->delete_file($row['filename']);
		}
		
		if ($this->core->delete($this->table, array($this->objectID => $objectID)));
		{	
			$redirect = ($redirect) ? $this->core->decode($redirect) : $this->redirect;
		
			// where to redirect to
			redirect($redirect);
		}
	}

	function popup($encodedID)
	{
		// decodes the image ID and splits it in to the URI and image ID
		$decode = explode('|', $this->core->decode($encodedID));

		$uri = $decode[0];
		$imageID = $decode[1];

		$this->edit($imageID, $uri, TRUE);
	}

	function browser()
	{
		// set default wheres
		$where = array('siteID' => $this->siteID, 'deleted' => 0);

		// check they have permissions to see all images
		if (!@in_array('images_all', $this->permission->permissions))
		{
			$where['userID'] = $this->session->userdata('userID');
		}

		// grab data and display
		$output = $this->core->viewall($this->table, array('folderID' => 0), 'imageRef', 999);

		// get folders
		if ($folders = $this->images->get_folders())
		{
			foreach($folders as $folder)
			{
				// grab data and display
				$data = $this->core->viewall($this->table, array('folderID' => $folder['folderID']), 'imageRef', 999);
				$output['folders'][$folder['folderID']]['folderName'] = $folder['folderName'];
				$output['folders'][$folder['folderID']]['images'] = $data['images'];
			}
		}

		$this->load->view('admin/browser',$output);
	}
	
	function folders()
	{
		// check permissions for this page
		if (!in_array('images', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required fields
		$this->core->required = array('folderName' => 'Folder Name');

		// set date
		$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
		$this->core->set['folderSafe'] = strtolower(url_title($this->input->post('folderName')));

		// get values
		$output = $this->core->get_values('image_folders');

		// update
		if ($this->core->update('image_folders') && count($_POST))
		{
			// where to redirect to
			redirect('/admin/images/folders');
		}

		$output['folders'] = $this->images->get_folders();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/folders',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_folder()
	{
		// check permissions for this page
		if (!in_array('images', $this->permission->permissions))
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
					$this->core->set['folderSafe'] = strtolower(url_title($value['folderName']));
					$this->core->update('image_folders', $objectID);
				}
			}
		}

		// where to redirect to
		redirect('/admin/images/folders');		
	}	

	function delete_folder($folderID)
	{
		// check permissions for this page
		if (!in_array('images', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('folderID' => $folderID);	
		
		if ($this->core->soft_delete('image_folders', $objectID))
		{
			// set children to no parent
			$this->images->update_children($folderID);
			
			// where to redirect to
			redirect('/admin/images/folders');
		}		
	}

	function order($field = '')
	{
		$this->core->order(key($_POST), $field);
	}
	
	function ac_images()
	{	
		$q = strtolower($_POST["q"]);
		if (!$q) return;
		
		// form dropdown
		$results = $this->images->search_images($q);
		
		// go foreach
		foreach((array)$results as $row)
		{
			$items[$row['imageRef']] = $row['imageName'];
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