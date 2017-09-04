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
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/blog/viewall';				// default redirect
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
			redirect('/admin/dashboard/permissions');
		}
		if (!in_array($this->uri->segment(2), $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		//  load models and libs
		$this->load->model('blog_model', 'blog');
		$this->load->library('tags');
	}

	function index()
	{
		redirect($this->redirect);
	}

	function viewall()
	{
		// default where
		$where = array();

		// set by userID if 'access all' permission is not set
		if (!in_array('blog_all', $this->permission->permissions))
		{
			$where['userID'] = $this->session->userdata('userID');
		}

		// grab data and display
		$output = $this->core->viewall('blog_posts', $where, array('dateCreated', 'desc'));

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/viewall',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_post()
	{
		// check permissions for this page
		if (!in_array('blog_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get values
		$output['data'] = $this->core->get_values('blog_posts');

		// get categories
		$output['categories'] = $this->blog->get_categories();

		if (count($this->input->post()))
		{
			// required
			$this->core->required = array(
				'postTitle' => array('label' => 'Title', 'rules' => 'required|trim'),
				'body' => 'Body'
			);

			// tidy tags
			$tags = NULL;
			if ($this->input->post('tags'))
			{
				foreach (explode(',', $this->input->post('tags')) as $tag)
				{
					$tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
				}
				$tags = implode(', ', $tags);
			}

			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['userID'] = $this->session->userdata('userID');
			$this->core->set['uri'] = url_title(strtolower($this->input->post('postTitle')));
			$this->core->set['tags'] = $tags;

			// update
			if ($this->core->update('blog_posts'))
			{
				$postID = $this->db->insert_id();

				// update categories
				$this->blog->update_cats($postID, $this->input->post('catsArray'));

				// update tags
				$this->tags->update_tags('blog_posts', $postID, $tags);

				// where to redirect to
				redirect($this->redirect);
			}
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_post', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_post($postID)
	{
		// check permissions for this page
		if (!in_array('blog_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// set object ID
		$objectID = array('postID' => $postID);

		// get values
		$output['data'] = $this->core->get_values('blog_posts', $objectID);

		// get categories
		$output['categories'] = $this->blog->get_categories();

		// get categories for this post
		$output['data']['categories'] = $this->blog->get_cats_for_post($postID);

		if (count($this->input->post()))
		{
			// required
			$this->core->required = array(
				'postTitle' => array('label' => 'Title', 'rules' => 'required|trim'),
				'body' => 'Body'
			);

			// set date
			if ($this->input->post('publishDate'))
			{
				$seconds = dateFmt($output['data']['dateCreated'], 'H:i:s');
				$this->core->set['dateCreated'] = date("Y-m-d H:i:s", strtotime($this->input->post('publishDate').' '.$seconds));
			}

			// tidy tags
			$tags = NULL;
			if ($this->input->post('tags'))
			{
				foreach (explode(',', $this->input->post('tags')) as $tag)
				{
					$tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
				}
				$tags = implode(', ', $tags);
			}

			// set stuff
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
			$this->core->set['uri'] = url_title(strtolower($this->input->post('postTitle')));
			$this->core->set['tags'] = $tags;

			// update
			if ($this->core->update('blog_posts', $objectID))
			{
				// update categories
				$this->blog->update_cats($postID, $this->input->post('catsArray'));

				// update tags
				$this->tags->update_tags('blog_posts', $postID, $tags);

				// set success message
				$this->session->set_flashdata('success', TRUE);

				// view page
				if ($this->input->post('view'))
				{
					redirect('/blog/'.dateFmt($output['data']['dateCreated'], 'Y/m').'/'.url_title(strtolower($this->input->post('postTitle'))));
				}
				else
				{
					// where to redirect to
					redirect('/admin/blog/edit_post/'.$postID);
				}
			}
		}

		// set message
		if ($this->session->flashdata('success'))
		{
			$output['message'] = '<p>Your changes were saved.</p>';
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_post', $output);
		$this->load->view($this->includes_path.'/footer');
	}

	function delete_post($objectID)
	{
		// check permissions for this page
		if (!in_array('blog_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		if ($this->core->soft_delete('blog_posts', array('postID' => $objectID)))
		{
			// remove category mappings
			$this->blog->update_cats($objectID);

			// where to redirect to
			redirect($this->redirect);
		}
	}

	function preview()
	{
		// get parsed body
		$html = $this->template->parse_body($this->input->post('body'));

		// filter for scripts
		$html = preg_replace('/<script(.*)<\/script>/is', '<em>This block contained scripts, please refresh page.</em>', $html);

		// output
		$this->output->set_output($html);
	}

	function comments()
	{
		// grab data and display
		$output['comments'] = $this->blog->get_latest_comments();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/comments',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function approve_comment($commentID)
	{
		if ($this->blog->approve_comment($commentID))
		{
			redirect('/admin/blog/comments');
		}
	}

	function delete_comment($objectID)
	{
		// check permissions for this page
		if (!in_array('blog_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		if ($this->core->soft_delete('blog_comments', array('commentID' => $objectID)))
		{
			// where to redirect to
			redirect('/admin/blog/comments/');
		}
	}

	function categories()
	{
		// check permissions for this page
		if (!in_array('blog_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get values
		$output = $this->core->get_values('blog_cats');

		// get categories
		$output['categories'] = $this->blog->get_categories();

		if (count($this->input->post()))
		{
			// required fields
			$this->core->required = array('catName' => 'Category name');

			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['catSafe'] = url_title(strtolower(trim($this->input->post('catName'))));

			// update
			if ($this->core->update('blog_cats'))
			{
				// where to redirect to
				redirect('/admin/blog/categories');
			}
		}

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/categories',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_cat()
	{
		// check permissions for this page
		if (!in_array('blog_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// go through post and edit each list item
		$listArray = $this->core->get_post();
		if (count($listArray))
		{
			foreach($listArray as $ID => $value)
			{
				if ($ID != '' && sizeof($value) > 0 && $value['catName'])
				{
					// set object ID
					$objectID = array('catID' => $ID);
					$this->core->set['catName'] = $value['catName'];
					$this->core->set['catSafe'] = url_title(strtolower(trim($value['catName'])));
					$this->core->update('blog_cats', $objectID);
				}
			}
		}

		// where to redirect to
		redirect('/admin/blog/categories');
	}

	function delete_cat($catID)
	{
		// check permissions for this page
		if (!in_array('blog_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// where
		$objectID = array('catID' => $catID);

		if ($this->core->soft_delete('blog_cats', $objectID))
		{
			// where to redirect to
			redirect('/admin/blog/categories');
		}
	}

	function order($field = '')
	{
		$this->core->order(key($this->input->post()), $field);
	}

}
