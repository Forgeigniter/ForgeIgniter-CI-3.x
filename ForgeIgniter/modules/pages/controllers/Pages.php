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

class Pages extends MX_Controller {

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
		if ($this->uri->segment(1))
		{
			// deprecated uri code (now its always just the uri string)
			$num = 1;
			$uri = '';
			while ($segment = $this->uri->segment($num))
			{
				$uri .= $segment.'/';
				$num ++;
			}
			$new_length = strlen($uri) - 1;
			$uri = substr($uri, 0, $new_length);
		}
		else
		{
			$uri = 'home';
		}

		$this->view($uri);
	}

	function view($page, $sendthru = '', $module = FALSE, $return = FALSE)
	{
		// set default parse file
		$parseFile = 'default';

		// check the page is not ajax or a return
		if (!$this->core->is_ajax() && !$return)
		{
			// check to see if the user is logged in as admin and has rights to edit the page inline
			if ($this->session->userdata('session_admin'))
			{
				$parseFile = 'view_template_inline';
			}
		}

		// handle web form
		if (count($_POST) && !$module)
		{
			if ($message = $this->core->web_form())
			{
				$sendthru['message'] = $message;
				$this->template->template['message'] = $sendthru['message'];
			}
			else
			{
				$sendthru['errors'] = validation_errors();
				$this->template->template['errors'] = $sendthru['errors'];
			}
		}

		// see if the cms is to generate a page from a module or a function of the site
		if ($module)
		{
			// set template tag
			$this->template->template['page:template'] = $page;

			// look up the page to see if there is any overriding meta data
			if ($metadata = $this->core->get_page(FALSE, substr($this->uri->uri_string(), 1)))
			{
				// redirect if set
				if ($metadata['redirect'])
				{
					$metadata['redirect'] = preg_replace('/^\//', '', $metadata['redirect']);
					redirect($metadata['redirect']);
				}

				if ($metadata['active'] ||
					(!$metadata['active'] && $this->session->userdata('session_admin') &&
						((@in_array('pages_edit', $this->permission->permissions) && in_array('pages_all', $this->permission->permissions)) ||
						(!@in_array('pages_all', $this->permission->permissions) && $this->session->userdata('groupID') && $metadata['groupID'] == $this->session->userdata('groupID')))
					)
				)
				{
					// set a title as long as its not a default
					if ($metadata['title'] != $metadata['pageName'])
					{
						$sendthru['page:title'] = $metadata['title'];
					}

					// set meta data
					$sendthru['page:keywords'] = $metadata['keywords'];
					$sendthru['page:description'] = $metadata['description'];
				}
				else
				{
					show_404();
				}
			}

			// get template by name
			if ($pagedata = $this->core->get_module_template($page))
			{
				// get template and blocks from cms
				$module = $this->template->generate_template($pagedata);

				// merge the sendthru data with page data
				$template = (is_array($sendthru)) ? array_merge($module, $sendthru) : $module;

				// set a null title
				$template['page:title'] = (!isset($sendthru['page:title'])) ? $this->site->config['siteName'] : $sendthru['page:title'];

				// output data
				if ($return === FALSE)
				{
					$this->parser->parse($parseFile, $template);
				}
				else
				{
					return $this->parser->parse($parseFile, $template, TRUE);
				}
			}

			// else just show it from a file template
			else
			{
				// get module name
				$module = (is_string($module)) ? $module : $this->uri->segment(1);

				// get module template
				if ($file = @file_get_contents(APPPATH.'modules/'.$module.'/views/templates/'.$page.'.php'))
				{
					// make a template out of the file
					$module = $this->template->generate_template(FALSE, $file);

					// merge the sendthru data with page data
					$template = (is_array($sendthru)) ? array_merge($module, $sendthru) : $module;

					// set a null title
					$template['page:title'] = (!isset($sendthru['page:title'])) ? $this->site->config['siteName'] : $sendthru['page:title'];

					// output data
					if ($return === FALSE)
					{
						$this->parser->parse($parseFile, $template);
					}
					else
					{
						return $this->parser->parse($parseFile, $template, TRUE);
					}
				}
				else
				{
					show_error('Templating error!');
				}
			}
		}

		// else just grab the page from cms
		elseif ($this->session->userdata('session_admin') && $pagedata = $this->core->get_page(FALSE, $page))
		{
			// redirect if set
			if ($pagedata['redirect'])
			{
				$pagedata['redirect'] = preg_replace('/^\//', '', $pagedata['redirect']);
				redirect($pagedata['redirect']);
			}

			// show cms with admin functions
			if ((@in_array('pages_edit', $this->permission->permissions) && in_array('pages_all', $this->permission->permissions)) ||
			(!@in_array('pages_all', $this->permission->permissions) && $this->session->userdata('groupID') && $pagedata['groupID'] == $this->session->userdata('groupID')))
			{
				$versionIDs = array();

				// check that this is not the live version and then add page version
				if ($versions = $this->core->get_versions($pagedata['pageID']))
				{
					foreach ($versions as $version)
					{
						$versionIDs[] = $version['versionID'];
					}
				}
				if ((!$pagedata['versionID'] && !$pagedata['draftID']) || @in_array($pagedata['draftID'], $versionIDs))
				{
					$this->core->add_draft($pagedata['pageID']);
					redirect($this->uri->uri_string());
				}

				// set no cache headers
				$this->output->set_header('Cache-Control: no-Store, no-Cache, must-revalidate');
				$this->output->set_header('Expires: -1');

				// show admin inline editor
				$output = $this->core->generate_page($pagedata['pageID'], TRUE);

				// merge output with any other data
				$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

				// output images
				$where = '';
				if (!@in_array('images_all', $this->permission->permissions))
				{
					$where['userID'] = $this->session->userdata('userID');
				}
				$images = $this->core->viewall('images', $where, array('dateCreated', 'desc'), 99);
				$output['images'] = $images['images'];

				// parse with main cms template
				if ($return === FALSE)
				{
					$this->parser->parse($parseFile, $output);
				}
				else
				{
					return $this->parser->parse($parseFile, $output, TRUE);
				}
			}

			// otherwise they are admin but they don't have permission to this page
			else
			{
				// just get normal page
				$output = $this->core->generate_page($pagedata['pageID']);

				// merge output with any other data
				$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

				// parse with main cms template
				if ($return === FALSE)
				{
					$this->parser->parse($parseFile, $output);
				}
				else
				{
					return $this->parser->parse($parseFile, $output, TRUE);
				}
			}
		}

		// display normal page
		elseif ($pagedata = $this->core->get_active_page($page))
		{
			// redirect if set
			if ($pagedata['redirect'])
			{
				$pagedata['redirect'] = preg_replace('/^\//', '', $pagedata['redirect']);
				redirect($pagedata['redirect']);
			}

			// add view
			$this->core->add_view($pagedata['pageID']);

			// merge output with any other data
			$pagedata = (is_array($sendthru)) ? array_merge($pagedata, $sendthru) : $pagedata;

			// just get normal page
			$output = $this->core->generate_page($pagedata['pageID']);

			// merge output with any other data
			$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

			// set no cache headers
			$this->output->set_header('Content-Type: text/html');

			// parse with main cms template
			if ($return === FALSE)
			{
				$this->parser->parse($parseFile, $output);
			}
			else
			{
				return $this->parser->parse($parseFile, $output, TRUE);
			}
		}

		// if nothing then 404 it!
		else
		{
			show_404();
		}
	}

	// file viewer
	function files($type = '', $ref = '')
	{
		// format filename
		$filenames = @explode('.', $ref);
		$extension = end($filenames);
		$filename = str_replace('.'.$extension, '', $ref);

		// css
		if ($type == 'css')
		{
			if ($include = $this->core->get_include($ref))
			{
				$this->output->set_header('Content-Type: text/css');
				$this->output->set_header('Expires: ' . gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');

				$this->output->set_output($include['body']);
			}
			else
			{
				show_404();
			}
		}

		// js
		elseif ($type == 'js')
		{
			if ($include = $this->core->get_include($ref))
			{
				$this->output->set_header('Content-Type: text/javascript');
				$this->output->set_header('Expires: ' . gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');

				$this->output->set_output($include['body']);
			}
			else
			{
				show_404();
			}
		}

		// images
		elseif ($type == 'images' || $type == 'gfx' | $type == 'thumbs')
		{
			if ($extension == 'gif')
			{
				$this->output->set_header('Content-Type: image/gif');
			}
			elseif ($extension == 'jpg' || $extension == 'jpeg')
			{
				$this->output->set_header('Content-Type: image/pjpeg');
				$this->output->set_header('Content-Type: image/jpeg');
			}
			elseif ($extension == 'png')
			{
				$this->output->set_header('Content-Type: image/png');
			}
			else
			{
				show_404();
			}

			// output image
			if ($image = $this->uploads->load_image($filename))
			{
				// set thumbnail
				$image = ($type == 'thumbs' && $thumb = $this->uploads->load_image($filename, TRUE)) ? $thumb : $image;

				$imageOutput = file_get_contents('.'.$image['src']);

				$fs = stat('.'.$image['src']);

				$this->output->set_header("Etag: ".sprintf('"%x-%x-%s"', $fs['ino'], $fs['size'],base_convert(str_pad($fs['mtime'],16,"0"),10,16)));
				$this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');
				$this->output->set_output($imageOutput);
			}
			else
			{
				show_404();
			}
		}

		// uploaded files
		elseif ($type == 'files')
		{
			// get the file, by reference or by filename
			if (@!$filenames[1])
			{
				$file = $this->uploads->load_file($ref, TRUE);
			}
			else
			{
				$file = $this->uploads->load_file($filename, TRUE);
			}

			if ($file)
			{
				if (@$file['error'] == 'expired')
				{
					show_error('Sorry, this download has now expired. Please contact support.');
				}
				elseif (@$file['error'] == 'premium')
				{
					show_error('This is a premium item and must be purchased in the shop.');
				}
				else
				{
					// set headers
					if ($extension == 'ico')
					{
						$this->output->set_header('Content-Type: image/x-icon');
					}
					elseif ($extension == 'swf')
					{
						$this->output->set_header('Content-Type: application/x-shockwave-flash');
					}
					else
					{
						$this->output->set_header("Pragma: public");
						$this->output->set_header("Expires: -1");
						$this->output->set_header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						$this->output->set_header("Content-Type: application/force-download");
						$this->output->set_header("Content-Type: application/octet-stream");
						$this->output->set_header("Content-Length: " .(string)(filesize('.'.$file['src'])) );
						$this->output->set_header("Content-Disposition: attachment; filename=".$file['fileRef'].$file['extension']);
						$this->output->set_header("Content-Description: File Transfer");
					}

					// output file contents
					$output = file_get_contents('.'.$file['src']);
					$this->output->set_output($output);
				}
			}
			else
			{
				show_404();
			}
		}

		// else 404 it
		else
		{
			show_404();
		}
	}

	function _captcha_check()
	{
		if (!$this->core->captcha_check())
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}
