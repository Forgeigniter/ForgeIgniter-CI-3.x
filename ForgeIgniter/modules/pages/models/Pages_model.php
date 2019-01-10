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

class Pages_model extends CI_Model {

	var $siteID;

	function __construct()
	{
		parent::__construct();

		$this->table = 'pages';

		if (!$this->siteID)
		{
			$this->siteID = SITEID;
		}
	}

	function get_pages()
	{
		$this->db->where('pages.siteID', $this->siteID);
		$this->db->where('deleted', 0);

		// if user has limited access, find those pages
		if (!in_array('pages_all', $this->permission->permissions))
		{
			$this->db->where('groupID', $this->session->userdata('groupID'));
		}

		// join versions
		$this->db->select('pages.*, page_versions.userID', FALSE);
		$this->db->join('page_versions', 'pages.draftID = page_versions.versionID', 'left');

		$this->db->order_by('pageOrder');

		$query = $this->db->get($this->table);

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_page_parents()
	{
		// default where
		$this->db->where('pages.siteID', $this->siteID);
		$this->db->where('deleted', 0);

		// if user has limited access, find those pages
		if (!in_array('pages_all', $this->permission->permissions))
		{
			$this->db->where('groupID', $this->session->userdata('groupID'));
		}

		// where parent is set
		$this->db->where('parentID', 0);

		// find out if its modified
		$this->db->select(' (SELECT COUNT(*) FROM '.$this->db->dbprefix.'page_blocks WHERE '.$this->db->dbprefix.'page_blocks.versionID = '.$this->db->dbprefix.'pages.draftID AND '.$this->db->dbprefix.'page_blocks.dateCreated > DATE_ADD(dateModified, INTERVAL 5 SECOND)) AS newBlocks', FALSE);

		// find out if its modified
		$this->db->select(' (SELECT COUNT(*) FROM '.$this->db->dbprefix.'page_versions WHERE '.$this->db->dbprefix.'page_versions.pageID = '.$this->db->dbprefix.'pages.pageID AND '.$this->db->dbprefix.'page_versions.dateCreated > DATE_ADD(datePublished, INTERVAL 5 SECOND)) AS newVersions', FALSE);

		// join versions
		$this->db->select('pages.*, page_versions.userID', FALSE);
		$this->db->join('page_versions', 'pages.draftID = page_versions.versionID', 'left');

		$this->db->order_by('pageOrder', 'asc');

		$query = $this->db->get('pages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_page_children($pageID = '')
	{
		// default where
		$this->db->where('pages.siteID', $this->siteID);
		$this->db->where('deleted', 0);

		// if user has limited access, find those pages
		if (!in_array('pages_all', $this->permission->permissions))
		{
			$this->db->where('groupID', $this->session->userdata('groupID'));
		}

		// get page by ID
		$this->db->where('parentID', $pageID);

		// find out if its modified
		$this->db->select(' (SELECT COUNT(*) FROM '.$this->db->dbprefix.'page_blocks WHERE '.$this->db->dbprefix.'page_blocks.versionID = '.$this->db->dbprefix.'pages.draftID AND '.$this->db->dbprefix.'page_blocks.dateCreated > DATE_ADD(dateModified, INTERVAL 5 SECOND)) AS newBlocks', FALSE);

		// find out if its modified
		$this->db->select(' (SELECT COUNT(*) FROM '.$this->db->dbprefix.'page_versions WHERE '.$this->db->dbprefix.'page_versions.pageID = '.$this->db->dbprefix.'pages.pageID AND '.$this->db->dbprefix.'page_versions.dateCreated > DATE_ADD(datePublished, INTERVAL 5 SECOND)) AS newVersions', FALSE);

		// join versions
		$this->db->select('pages.*, page_versions.userID', FALSE);
		$this->db->join('page_versions', 'pages.draftID = page_versions.versionID', 'left');

		$this->db->order_by('pageOrder', 'asc');

		$query = $this->db->get('pages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_page($pageID)
	{
		$this->db->where('pages.siteID', $this->siteID);
		$this->db->where('deleted', 0);
		$this->db->where('pages.pageID', $pageID);

		// if user has limited access, find those pages
		if (!in_array('pages_all', $this->permission->permissions))
		{
			$this->db->where('groupID', $this->session->userdata('groupID'));
		}

		// join versions
		$this->db->select('pages.*, page_versions.userID', FALSE);
		$this->db->join('page_versions', 'pages.draftID = page_versions.versionID', 'left');

		$this->db->order_by('pageOrder');

		$query = $this->db->get($this->table, 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_template($templateID = '')
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t1.deleted', 0, FALSE);
		$this->db->where('t1.templateID', $templateID, FALSE);

		// select
		$this->db->select('t1.*, t2.body, t2.dateCreated, t2.userID');

		$this->db->from('templates t1');
		$this->db->limit(1);

		// join revisions
		$this->db->join('template_versions t2', 't2.versionID = t1.versionID', 'left');

		// get em
		$query = $this->db->get();

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_templates($type = '')
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t1.deleted', 0, FALSE);

		// don't show modules
		if ($type == 'page')
		{
			$this->db->where('modulePath', '');
		}
		elseif ($type == 'module')
		{
			$this->db->where('modulePath !=', '');
		}

		// select
		$this->db->select('t1.*, t2.body, t2.dateCreated, t2.userID');

		$this->db->from('templates t1');

		// join revisions
		$this->db->join('template_versions t2', 't2.versionID = t1.versionID', 'left');

		// order
		$this->db->order_by('modulePath', 'asc');
		$this->db->order_by('templateName', 'asc');

		// get all templates
		$query = $this->db->get();

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_template_count($templateID)
	{
		// count
		$this->db->select('COUNT(*) as numPages');

		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where
		$this->db->where('templateID', $templateID);

		$query = $this->db->get('pages');

		if ($query->num_rows())
		{
			$row = $query->row_array();
			return $row['numPages'];
		}
		else
		{
			return false;
		}
	}

	function get_template_versions($templateID)
	{
		$this->db->where('objectID', $templateID);

		$this->db->order_by('dateCreated', 'desc');

		$query = $this->db->get('template_versions', 30);

		// get data
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function get_include($includeRef = '', $includeID = '')
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t1.deleted', 0, FALSE);

		// get by reference
		if ($includeRef)
		{
			$this->db->where('includeRef', $includeRef);
		}

		// get by ID
		elseif ($includeID)
		{
			$this->db->where('includeID', $includeID);
		}

		// or fail
		else
		{
			return FALSE;
		}

		// select
		$this->db->select('t1.*, t2.body, t2.dateCreated, t2.userID');

		// table name and limit
		$this->db->from('includes t1');
		$this->db->limit(1);

		// join revisions
		$this->db->join('include_versions t2', 't2.versionID = t1.versionID', 'left');

		// get em
		$query = $this->db->get();

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_includes($type = '')
	{
		// default where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t1.deleted', 0, FALSE);

		// get by type
		if ($type)
		{
			$this->db->where('type', $type);
		}

		// select
		$this->db->select('t1.*, t2.body, t2.dateCreated, t2.userID');

		$this->db->from('includes t1');

		// join revisions
		$this->db->join('include_versions t2', 't2.versionID = t1.versionID', 'left');

		// order
		$this->db->order_by('includeRef', 'asc');

		// get all includes
		$query = $this->db->get();

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_include_versions($includeID)
	{
		$this->db->where('objectID', $includeID);

		$this->db->order_by('dateCreated', 'desc');

		$query = $this->db->get('include_versions', 30);

		// get data
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function add_temp_page()
	{
		// create the page
		$this->db->set('siteID', $this->siteID);
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('pageName', '');
		$this->db->set('deleted', 1);
		$this->db->insert('pages');
		$pageID = $this->db->insert_id();

		// create the draft
		$this->core->add_draft($pageID);

		return $pageID;
	}

	function add_page_nav($pageName, $path)
	{
		// check nav isnt there already
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);
		if ($path == 'home')
		{
			$this->db->where('(uri = "/" OR uri = "home")');
		}
		else
		{
			$this->db->where('uri', $path);
		}
		$query = $this->db->get('navigation', 1);

		// add nav
		if (!$query->num_rows())
		{
			$this->db->set('siteID', $this->siteID);
			$this->db->set('dateCreated', date("Y-m-d H:i:s"));
			$this->db->set('uri', $path);
			$this->db->set('navName', $pageName);
			$this->db->insert('navigation');
		}

		return TRUE;
	}

	function import_template($file, $body)
	{
		// set flags
		$success = FALSE;
		$includes = FALSE;

		// get file info
		$filenames = explode('.', $file);
		$filename = substr($file, 0, strpos($file, '.'.end($filenames)));
		$extension = end($filenames);

		// add html template (and includes)
		if ($extension == 'html' || $extension == 'htm')
		{
			// parse template
			$body = preg_replace('/<title>(.*)<\/title>/i', '<title>{page:title}</title>', $body);
			$body = preg_replace('/meta name="keywords" content="(.*)"/i', 'meta name="keywords" content="{page:keywords}"', $body);
			$body = preg_replace('/meta name="description" content="(.*)"/i', 'meta name="description" content="{page:description}"', $body);
			$body = preg_replace('/<!--NAVIGATION-->/i', '{navigation}', $body);

			// get template name
			$templateName = url_title(trim(ucfirst($filename)));

			// see if template is a module
			$module = (in_array(strtolower($templateName), $this->template->moduleTemplates)) ? strtolower($templateName) : false;

			// get theme name
			$theme = '';
			$themeRef = '';
			if (preg_match('/meta name="theme" content="(.*)"|meta content="(.*)" name="theme"/i', $body, $matches))
			{
				$theme = substr(trim($matches[1]), 0, 15);
				$themeRef = url_title(trim(strtolower($theme))).'-';
			}

			// find out if header is in there
			if (preg_match('/<!--CONTENT-->/i', $body))
			{
				$split = preg_split('/<!--CONTENT-->/i', $body);
				$header = preg_replace('/<!--CONTENT-->/i', '', $split[0]);
				$content = $split[1];

				// get file name
				$includeRef = $themeRef.'header';

				$this->add_include($includeRef, $header, 'H');

				$includes = TRUE;
			}

			// find out if footer is in there
			if (preg_match('/<!--ENDCONTENT-->/i', $body))
			{
				$split = preg_split('/<!--ENDCONTENT-->/i', $body);
				$footer = $split[1];

				// remove footer from content
				$content = str_replace($footer, '', $content);
				$content = preg_replace('/<!--ENDCONTENT-->/i', '', $content);

				// get file name
				$includeRef = $themeRef.'footer';

				$this->add_include($includeRef, $footer, 'H');

				$includes = TRUE;
			}

			// put the header and footer tags in
			if ($includes)
			{
				$content = "{include:".$themeRef."header}\n\n$content\n\n{include:".$themeRef."footer}";
			}

			// otherwise just set the template content
			else
			{
				$content = $body;
			}

			// look for blocks
			preg_match_all('/<!--BLOCK-->/i', $content, $matches);
			for ($x=0; $x<sizeof($matches[0]); $x++)
			{
				$content = preg_replace('/<!--BLOCK-->/i', '{block'.($x+1).'}', $content, 1);
			}

			// get file name
			$templateName = ($theme) ? '['.$theme.'] '.$templateName : $templateName;

			$templateID = $this->add_template($templateName, $content, $module);
		}

		// add css file
		elseif ($extension == 'css')
		{
			// get file name
			$includeRef = $filename.'.css';

			$this->add_include($includeRef, $body, 'C');
		}

		// add js file
		elseif ($extension == 'js')
		{
			// get file name
			$includeRef = $filename.'.js';

			$this->add_include($includeRef, $body, 'J');
		}

		return (@$templateID) ? $templateID : TRUE;
	}

	function add_template($templateName, $body, $module = '')
	{
		// find out if template exists
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);
		if ($module)
		{
			$this->db->where('modulePath', $module);
		}
		else
		{
			$this->db->where('templateName', $templateName);
			$this->db->where('modulePath', '');
		}
		$query = $this->db->get('templates', 1);

		// add template version
		if ($query->num_rows())
		{
			$row = $query->row_array();
			$templateID = $row['templateID'];

			// add template version
			$versionID = $this->add_template_version($templateID, $body);
		}

		// add template
		else
		{
			$this->db->set('siteID', $this->siteID);
			$this->db->set('dateCreated', date("Y-m-d H:i:s"));
			$this->db->set('templateName', $templateName);
			if ($module)
			{
				$this->db->set('modulePath', $module);
			}
			$this->db->insert('templates');
			$templateID = $this->db->insert_id();

			// add template version
			$versionID = $this->add_template_version($templateID, $body);
		}

		return $templateID;
	}

	function add_template_version($templateID, $body = '')
	{
		// set body
		$body = ($this->input->post('body')) ? $this->input->post('body') : $body;

		// filter body
		$body = htmlentities(iconv('UTF-8', 'UTF-8//IGNORE', $body), NULL, 'UTF-8');
		$body = html_entity_decode($body, NULL, 'UTF-8');

		// check page
		if (!$data = $this->get_template($templateID))
		{
			return FALSE;
		}

		// check version is not the same as latest one
		if ($versions = $this->get_template_versions($templateID))
		{
			if ($versions[0]['body'] == $body)
			{
				return FALSE;
			}
		}

		// check version is not the same as current one
		if ($data['body'] == $body)
		{
			return FALSE;
		}

		// add version
		$this->db->set('objectID', $templateID);
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('userID', $this->session->userdata('userID'));
		$this->db->set('body', $body);
		$this->db->set('siteID', $this->siteID);

		$this->db->insert('template_versions');

		// get version ID
		$versionID = $this->db->insert_id();

		// update page draft
		$this->db->set('versionID', $versionID);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('templateID', $templateID);
		$this->db->update('templates');

		return $versionID;
	}

	function revert_template($templateID, $versionID)
	{
		// update the template with version
		$this->db->set('versionID', $versionID);
		$this->db->where('templateID', $templateID);
		$this->db->update('templates');

		return TRUE;
	}

	function add_include($includeRef, $body, $type, $siteID = TRUE)
	{
		// find out if include exists
		if ($siteID)
		{
			$this->db->where('siteID', $this->siteID);
		}
		$this->db->where('deleted', 0);
		$this->db->where('includeRef', $includeRef);
		$query = $this->db->get('includes', 1);

		// add include version
		if ($query->num_rows())
		{

			$row = $query->row_array();

			// add template version
			$versionID = $this->add_include_version($row['includeID'], $body);
		}

		// add include
		else
		{
			if ($siteID)
			{
				$this->db->set('siteID', $this->siteID);
			}
			$this->db->set('dateCreated', date("Y-m-d H:i:s"));
			$this->db->set('includeRef', $includeRef);
			$this->db->set('type', $type);
			$this->db->insert('includes');
			$includeID = $this->db->insert_id();

			// add template version
			$versionID = $this->add_include_version($includeID, $body);
		}

		return TRUE;
	}

	function add_include_version($includeID, $body = '')
	{
		// set body
		$body = ($this->input->post('body')) ? $this->input->post('body') : $body;

		// filter body
		$body = htmlentities($body, NULL, 'UTF-8');
		$body = html_entity_decode($body, NULL, 'UTF-8');

		// check page
		if (!$data = $this->get_include(NULL, $includeID))
		{
			return FALSE;
		}

		// check version is not the same as latest one
		if ($versions = $this->get_include_versions($includeID))
		{
			if ($versions[0]['body'] == $body)
			{
				return FALSE;
			}
		}

		// check version is not the same as current one
		if ($data['body'] == $body)
		{
			return FALSE;
		}

		// add version
		$this->db->set('objectID', $includeID);
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('userID', $this->session->userdata('userID'));
		$this->db->set('body', $body);
		$this->db->set('siteID', $this->siteID);

		$this->db->insert('include_versions');

		// get version ID
		$versionID = $this->db->insert_id();

		// update page draft
		$this->db->set('versionID', $versionID);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('includeID', $includeID);
		$this->db->update('includes');

		return $versionID;
	}

	function revert_include($includeID, $versionID)
	{
		// update the include with version
		$this->db->set('versionID', $versionID);
		$this->db->where('includeID', $includeID);
		$this->db->update('includes');

		return TRUE;
	}

	function update_children($pageID)
	{
		// update page draft
		$this->db->set('parentID', 0);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('parentID', $pageID);
		$this->db->update('pages');

		return TRUE;
	}

}
