<?php defined('BASEPATH') OR exit('No direct script access allowed');
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

class Template {

	// set defaults
	var $CI;								// CI instance
	var $base_path = '';					// default base path
	var $moduleTemplates = array();
	var $template = array();

	function __construct()
	{
		$this->CI =& get_instance();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		$this->uploadsPath = $this->CI->config->item('uploadsPath');

		// populate module templates array
		$this->moduleTemplates = array(
			'blog',
			'blog_single',
			'blog_search',
			'community_account',
			'community_create_account',
			'community_forgotten',
			'community_home',
			'community_login',
			'community_members',
			'community_messages',
			'community_messages_form',
			'community_messages_popup',
			'community_messages_read',
			'community_reset',
			'community_view_profile',
			'community_view_profile_private',
			'events',
			'events_single',
			'events_search',
			'events_featured',
			'forums',
			'forums_delete',
			'forums_edit_post',
			'forums_edit_topic',
			'forums_forum',
			'forums_post_reply',
			'forums_post_topic',
			'forums_search',
			'forums_topic',
			'shop_account',
			'shop_browse',
			'shop_cancel',
			'shop_cart',
			'shop_checkout',
			'shop_create_account',
			'shop_donation',
			'shop_featured',
			'shop_forgotten',
			'shop_login',
			'shop_orders',
			'shop_prelogin',
			'shop_product',
			'shop_recommend',
			'shop_recommend_popup',
			'shop_reset',
			'shop_review',
			'shop_review_popup',
			'shop_success',
			'shop_view_order',
			'wiki',
			'wiki_form',
			'wiki_page',
			'wiki_search'
		);
	}

	function generate_template($pagedata, $file = false)
	{
		// page data
		@$this->template['page:title'] = (isset($pagedata['title'])) ? htmlentities($pagedata['title'], ENT_QUOTES | ENT_IGNORE, "UTF-8") : htmlentities($this->CI->site->config['siteName']);
		@$this->template['page:keywords'] = (isset($pagedata['keywords'])) ? $pagedata['keywords'] : '';
		@$this->template['page:description'] = (isset($pagedata['description'])) ? $pagedata['description'] : '';
		@$this->template['page:date'] = (isset($pagedata['dateCreated'])) ? dateFmt($pagedata['dateCreated']) : '';
		@$this->template['page:date-modified'] = (isset($pagedata['dateModified'])) ? dateFmt($pagedata['dateModified']) : '';
		@$this->template['page:uri'] = site_url($this->CI->uri->uri_string());
		@$this->template['page:uri-encoded'] = $this->CI->core->encode($this->CI->uri->uri_string());
		@$this->template['page:uri:segment(1)'] = $this->CI->uri->segment(1);
		@$this->template['page:uri:segment(2)'] = $this->CI->uri->segment(2);
		@$this->template['page:uri:segment(3)'] = $this->CI->uri->segment(3);
		@$this->template['page:template'] = ($this->template['page:template']) ? $this->template['page:template'] : '';

		// find out if logged in
		$this->template['logged-in'] = ($this->CI->session->userdata('session_user')) ? TRUE : FALSE;

		// find out if subscribed
		$this->template['subscribed'] = ($this->CI->session->userdata('subscribed')) ? TRUE : FALSE;

		// find out if admin
		$this->template['admin'] = ($this->CI->session->userdata('session_admin')) ? TRUE : FALSE;

		// find out if this is ajax
		$this->template['ajax'] = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))) ? TRUE : FALSE;

		// find out if browser is iphone
		$this->template['mobile'] = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) ? TRUE : FALSE;

		// permissions
		if ($this->CI->session->userdata('session_admin'))
		{
			if ($permissions = $this->CI->permission->get_group_permissions($this->CI->session->userdata('groupID')))
			{
				foreach($permissions as $permission)
				{
					@$this->template['permission:'.$permission] = TRUE;
				}
			}
		}

		// feed (if it exists for the module)
		@$this->template['page:feed'] = (isset($pagedata['feed'])) ? $pagedata['feed'] : '';

		// either build template from a file or from db
		if ($file)
		{
			$templateBody = $this->parse_template($file, FALSE, NULL, FALSE);
		}
		else
		{
			$templateData = $this->CI->core->get_template($pagedata['templateID']);
			$templateBody = $templateData['body'];
		}

		// parse it for everything else
		$this->template['body'] = $this->parse_template($templateBody, FALSE, NULL, FALSE);

		// get navigation and build menu
		if (preg_match_all('/{navigation(\:([a-z0-9\.-]+))?}/i', $this->template['body'], $matches))
		{
			$this->template = $this->parse_navigation('navigation', $this->template);
		}

		return $this->template;
	}

	function parse_includes($body)
	{
		// get includes
		preg_match_all('/include\:([a-z0-9\.-]+)/i', $body, $includes);

		if ($includes)
		{
			$includeBody = '';
			foreach($includes[1] as $include => $value)
			{
				$includeRow = $this->CI->core->get_include($value);

				$includeBody = $this->parse_body($includeRow['body'], FALSE, NULL, FALSE);

				$includeBody = $this->CI->parser->conditionals($includeBody, $this->template, TRUE);

				$body = str_replace('{include:'.$value.'}', $includeBody, $body);
			}
		}

		return $body;
	}

	function parse_navigation($navTag, $template)
	{
		// get all navigation
		$template[$navTag] = $this->parse_nav();

		// get parents
		$template[$navTag.':parents'] = $this->parse_nav(0, FALSE);

		// get uri
		$uri = (!$this->CI->uri->segment(1)) ? 'home' : $this->CI->uri->segment(1);

		// get children of active nav item
		if ($parent = $this->CI->core->get_page(FALSE, $uri))
		{
			$template[$navTag.':children'] = $this->parse_nav($parent['pageID']);
		}
		else
		{
			$template[$navTag.':children'] = '';
		}

		return $template;
	}

	function parse_nav($parentID = 0, $showChildren = TRUE)
	{
		$output = '';

		if ($navigation = $this->get_nav_parents($parentID))
		{
			$i = 1;
			foreach($navigation as $nav)
			{
				// set first and last state on menu
				$class = '';
				$class .= ($i == 1) ? 'first ' : '';
				$class .= (sizeof($navigation) == $i) ? 'last ' : '';

				// look for children
				$children = ($showChildren) ? $this->get_nav_children($nav['navID']) : FALSE;

				// parse the nav item for the link
				$output .= $this->parse_nav_item($nav['uri'], $nav['navName'], $children, $class);

				// parse for children
				if ($children)
				{
					$x = 1;
					$output .= '<ul class="subnav">';
					foreach($children as $child)
					{
						// set first and last state on menu
						$class = '';
						$class .= ($x == 1) ? 'first ' : '';
						$class .= (sizeof($children) == $x) ? 'last ' : '';

						// look for sub children
						$subChildren = $this->get_nav_children($child['navID']);

						// parse nav item
						$navItem = $this->parse_nav_item($child['uri'], $child['navName'], $subChildren, $class);
						$output .= $navItem;

						// parse for children
						if ($subChildren)
						{
							$y = 1;
							$output .= '<ul class="subnav">';
							foreach($subChildren as $subchild)
							{
								// set first and last state on menu
								$class = '';
								$class .= ($y == 1) ? 'first ' : '';
								$class .= (sizeof($subChildren) == $y) ? 'last ' : '';

								$navItem = $this->parse_nav_item($subchild['uri'], $subchild['navName'], '', $class).'</li>';
								$output .= $navItem;
								$y++;
							}
							$output .= '</ul>';
						}
						$output .= '</li>';
						$x++;
					}
					$output .= '</ul>';
				}

				$output .= '</li>';

				$i++;
			}
		}

		return $output;
	}

	function parse_nav_item($uri, $name, $children = FALSE, $class = '')
	{
		// init stuff
		$output = '';
		$childs = array();

		// tidy children array
		if ($children)
		{
			foreach($children as $child)
			{
				$childs[] = $child['uri'];
			}
		}

		// set active state on menu
		$currentNav = $uri;
		$output .= '<li class="';
		if (($currentNav != '/' && $currentNav == $this->CI->uri->uri_string()) ||
			$currentNav == $this->CI->uri->segment(1) ||
			(($currentNav == '' || $currentNav == 'home' || $currentNav == '/') &&
				($this->CI->uri->uri_string() == '' || $this->CI->uri->uri_string() == '/home' || $this->CI->uri->uri_string() == '/')) ||
			@in_array(substr($this->CI->uri->uri_string(),1), $childs)
		)
		{
			$class .= 'active selected ';
		}
		if ($children)
		{
			$class .= 'expanded ';
		}

		// filter uri to make sure it's cool
		if (substr($uri,0,1) == '/')
		{
			$href = $uri;
		}
		elseif (stristr($uri,'http://'))
		{
			$href = $uri;
		}
		elseif (stristr($uri,'www.'))
		{
			$href = 'http://'.$uri;
		}
		elseif (stristr($uri,'mailto:'))
		{
			$href = $uri;
		}
		elseif ($uri == 'home')
		{
			$href = '/';
		}
		else
		{
			$href = '/'.$uri;
		}

		// output anchor with span in case of additional styling
		$output .= trim($class).'" id="nav-'.trim($uri).'"><a href="'.site_url($href).'" class="'.trim($class).'"><span>'.htmlentities($name).'</span></a>';

		return $output;
	}

	function get_nav($navID = '')
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->CI->db->where('parentID', 0);

		// get navigation from pages
		$this->CI->db->select('uri, pageID as navID, pageName as navName');

		$this->CI->db->order_by('pageOrder', 'asc');

		$query = $this->CI->db->get('pages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function get_nav_parents($parentID = 0)
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->CI->db->where('parentID', $parentID);

		// where parent is set
		$this->CI->db->where('active', 1);

		// get navigation from pages
		$this->CI->db->select('uri, pageID as navID, pageName as navName');

		// nav has to be active because its parents
		$this->CI->db->where('navigation', 1);

		$this->CI->db->order_by('pageOrder', 'asc');

		$query = $this->CI->db->get('pages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return false;
		}
	}

	function get_nav_children($navID = '')
	{
		// default where
		$this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get nav by ID
		$this->CI->db->where('parentID', $navID);

		// where parent is set
		$this->CI->db->where('active', 1);

		// select
		$this->CI->db->select('uri, pageID as navID, pageName as navName');

		// where viewable
		$this->CI->db->where('navigation', 1);

		// page order
		$this->CI->db->order_by('pageOrder', 'asc');

		// grab
		$query = $this->CI->db->get('pages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function parse_template($body, $condense = FALSE, $link = '', $mkdn = TRUE)
	{
		$body = $this->parse_body($body, $condense, $link, $mkdn);

		return $body;
	}

	function parse_body($body, $condense = FALSE, $link = '', $mkdn = TRUE)
	{
		// parse for images
		$body = $this->parse_images($body);

		// parse for files
		$body = $this->parse_files($body);

		// parse for files
		$body = $this->parse_includes($body);

		// parse for modules
		$this->template = $this->parse_modules($body, $this->template);

		// site globals
		$body = str_replace('{site:name}', $this->CI->site->config['siteName'], $body);
		$body = str_replace('{site:domain}', $this->CI->site->config['siteDomain'], $body);
		$body = str_replace('{site:url}', $this->CI->site->config['siteURL'], $body);
		$body = str_replace('{site:email}', $this->CI->site->config['siteEmail'], $body);
		$body = str_replace('{site:tel}', $this->CI->site->config['siteTel'], $body);
		$body = str_replace('{site:currency}', $this->CI->site->config['currency'], $body);
		$body = str_replace('{site:currency-symbol}', currency_symbol(), $body);

		// logged in userdata
		$body = str_replace('{userdata:id}', ($this->CI->session->userdata('userID')) ? $this->CI->session->userdata('userID') : '', $body);
		$body = str_replace('{userdata:email}', ($this->CI->session->userdata('email')) ? $this->CI->session->userdata('email') : '', $body);
		$body = str_replace('{userdata:username}', ($this->CI->session->userdata('username')) ? $this->CI->session->userdata('username') : '', $body);
		$body = str_replace('{userdata:name}', ($this->CI->session->userdata('firstName') && $this->CI->session->userdata('lastName')) ? $this->CI->session->userdata('firstName').' '.$this->CI->session->userdata('lastName') : '', $body);
		$body = str_replace('{userdata:first-name}', ($this->CI->session->userdata('firstName')) ? $this->CI->session->userdata('firstName') : '', $body);
		$body = str_replace('{userdata:last-name}', ($this->CI->session->userdata('lastName')) ? $this->CI->session->userdata('lastName') : '', $body);

		// other useful stuff
		$body = str_replace('{date}', dateFmt(date("Y-m-d H:i:s"), ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'), $body);
		$body = str_replace('{date:unixtime}', time(), $body);

		// condense
		if ($condense)
		{
			if ($endchr = strpos($body, '{more}'))
			{
				$body = substr($body, 0, ($endchr + 6));
				$body = str_replace('{more}', '<p class="more"><a href="'.$link.'" class="button more">Read more</a></p>', $body);
			}
		}
		else
		{
			$body = str_replace('{more}', '', $body);
		}

		// parse for clears
		$body = str_replace('{clear}', '<div style="clear:both;"/></div>', $body);

		// parse for pads
		$body = str_replace('{pad}', '<div style="padding-bottom:10px;width:10px;clear:both;"/></div>', $body);

		// parse body for markdown and images
		if ($mkdn === TRUE)
		{
			// parse for mkdn
			$body = mkdn($body);
		}

		return $body;
	}

	function parse_modules($body, $template)
	{
		// get web forms
		if (preg_match_all('/{webform:([A-Za-z0-9_\-]+)}/i', $body, $matches))
		{
			// filter matches
			$webformID = preg_replace('/{|}/', '', $matches[0][0]);
			$webform = $this->CI->core->get_web_form_by_ref($matches[1][0]);
			$template[$webformID] = '';
			$required = array();

			// get web form
			if ($webform)
			{
				// set fields
				if ($webform['fieldSet'] == 1)
				{
					$required[] = 'fullName';
					$required[] = 'subject';
					$required[] = 'message';

					// populate template
					$template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>

						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>

						<div class="formrow field-subject">
							<label for="subject">Subject</label>
							<input type="text" id="subject" name="subject" value="'.$this->CI->input->post('subject').'" class="formelement" />
						</div>

						<div class="formrow field-message">
							<label for="message">Message</label>
							<textarea id="message" name="message" class="formelement small">'.$this->CI->input->post('message').'</textarea>
						</div>
					';
				}

				// set fields
				if ($webform['fieldSet'] == 2)
				{
					$required[] = 'fullName';

					// populate template
					$template[$webformID] .= '
						<div class="formrow field-fullName">
							<label for="fullName">Full Name</label>
							<input type="text" id="fullName" name="fullName" value="'.$this->CI->input->post('fullName').'" class="formelement" />
						</div>

						<div class="formrow field-email">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" value="'.$this->CI->input->post('email').'" class="formelement" />
						</div>

						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
				}

				// set fields
				if ($webform['fieldSet'] == 0)
				{
					// populate template
					$template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
					';
				}

				// set account
				if ($webform['account'] == 1)
				{
					// populate template
					$template[$webformID] .= '
						<input type="hidden" name="subject" value="'.$webform['formName'].'" />
						<input type="hidden" name="message" value="'.$webform['outcomeMessage'].'" />
						<input type="hidden" name="groupID" value="'.$webform['groupID'].'" />
					';
				}

				// set required
				if ($required)
				{
					$template[$webformID] .= '
						<input type="hidden" name="required" value="'.implode('|', $required).'" />
					';
				}

				// output encoded webform ID
				$template[$webformID] .= '
					<input type="hidden" name="formID" value="'.$this->CI->core->encode($matches[1][0]).'" />
				';
			}
			else
			{
				$template[$webformID] = '';
			}
		}

		// get blog headlines
		if (preg_match_all('/{headlines:blog(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load blog model
			$this->CI->load->model('blog/blog_model', 'blog');

			// filter through matches
			for ($x = 0; $x < sizeof($matches[0]); $x++)
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][$x]);
				$limit = ($matches[6][$x]) ? $matches[6][$x] : $this->CI->site->config['headlines'];
				$headlines = ($matches[3][$x]) ? $this->CI->blog->get_posts_by_category($matches[3][$x], $limit) : $this->CI->blog->get_posts($limit);

				// get latest posts
				if ($headlines)
				{
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get rid of any template tags
						$headlineBody = $this->parse_body($headline['body'], TRUE, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));
						$headlineExcerpt = $this->parse_body($headline['excerpt'], TRUE, site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']));

						// populate loop
						$template[$headlineID][$i] = array(
							'headline:link' => site_url('blog/'.dateFmt($headline['dateCreated'], 'Y/m').'/'.$headline['uri']),
							'headline:title' => $headline['postTitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:day' => dateFmt($headline['dateCreated'], 'd'),
							'headline:month' => dateFmt($headline['dateCreated'], 'M'),
							'headline:year' => dateFmt($headline['dateCreated'], 'y'),
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,
							'headline:comments-count' => $headline['numComments'],
							'headline:author' => $this->CI->blog->lookup_user($headline['userID'], TRUE),
							'headline:author-id' => $headline['userID'],
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);

						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}
		}

		// get events headlines
		if (preg_match_all('/{headlines:events(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load events model
			$this->CI->load->model('events/events_model', 'events');

			// filter matches
			$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
			$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

			// get latest posts
			if ($headlines = $this->CI->events->get_events($limit))
			{
				// fill up template array
				$i = 0;
				foreach ($headlines as $headline)
				{
					$headlineBody = $this->parse_body($headline['description'], TRUE, site_url('events/viewevent/'.$headline['eventID']));
					$headlineExcerpt = $this->parse_body($headline['excerpt'], TRUE, site_url('events/viewevent/'.$headline['eventID']));

					$template[$headlineID][$i] = array(
						'headline:link' => site_url('events/viewevent/'.$headline['eventID']),
						'headline:title' => $headline['eventTitle'],
						'headline:date' => dateFmt($headline['eventDate'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
						'headline:day' => dateFmt($headline['eventDate'], 'd'),
						'headline:month' => dateFmt($headline['eventDate'], 'M'),
						'headline:year' => dateFmt($headline['eventDate'], 'y'),
						'headline:body' => $headlineBody,
						'headline:excerpt' => $headlineExcerpt,
						'headline:author' => $this->CI->events->lookup_user($headline['userID'], TRUE),
						'headline:author-id' => $headline['userID'],
						'headline:class' => ($i % 2) ? ' alt ' : ''
					);

					$i++;
				}
			}
			else
			{
				$template[$headlineID] = array();
			}
		}

		// get wiki headlines
		if (preg_match_all('/{headlines:wiki(:category(\(([A-Za-z0-9_-]+)\))?)?(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
		{
			// load wiki model
			$this->CI->load->model('wiki/wiki_model', 'wiki');

			// filter matches
			$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
			$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

			// get latest posts
			if ($headlines = $this->CI->wiki->get_pages($limit))
			{
				// fill up template array
				$i = 0;
				foreach ($headlines as $headline)
				{

					$template[$headlineID][$i] = array(
						'headline:link' => site_url('wiki/' .$headline['uri']),
						'headline:title' => $headline['pageName'],
					);

					$i++;
				}
			}
			else
			{
				$template[$headlineID] = array();
			}
		}

		// get gallery
		if (preg_match_all('/{gallery:([A-Za-z0-9_-]+)(:limit\(([0-9]+)\))?}/i', $body, $matches))
		{
			// load libs etc
			$this->CI->load->model('images/images_model', 'images');

			// filter through matches
			for ($x = 0; $x < sizeof($matches[0]); $x++)
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[3][$x]) ? $matches[3][$x] : 9;

				// get latest posts
				if ($gallery = $this->CI->images->get_images_by_folder_ref($matches[1][$x], $limit))
				{
					// fill up template array
					$i = 0;
					foreach ($gallery as $galleryimage)
					{
						if ($imageData = $this->get_image($galleryimage['imageRef']))
						{
							$imageHTML = display_image($imageData['src'], $imageData['imageName']);
							$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);

							$thumbTMLL = display_image($imageData['src'], $imageData['imageName']);
							$thumbHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);

							$template[$headlineID][$i] = array(
								'galleryimage:link' => site_url('images/'.$imageData['imageRef'].$imageData['ext']),
								'galleryimage:title' => $imageData['imageName'],
								'galleryimage:image' => $imageHTML,
								'galleryimage:thumb' => $thumbHTML,
								'galleryimage:filename' => $imageData['imageRef'].$imageData['ext'],
								'galleryimage:date' => dateFmt($imageData['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
								'galleryimage:author' => $this->CI->images->lookup_user($imageData['userID'], TRUE),
								'galleryimage:author-id' => $imageData['userID'],
								'galleryimage:class' => $imageData['class']
							);

							$i++;
						}
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}
		}

		// get shop gateway
		if (preg_match('/{shop:(.+)}|{headlines:shop/i', $body))
		{
			// load messages model
			$this->CI->load->model('shop/shop_model', 'shop');

			// shop globals
			$template['shop:email'] = $this->CI->site->config['shopEmail'];
			$template['shop:paypal'] = $this->CI->shop->paypal_url;
			$template['shop:gateway'] = ($this->CI->site->config['shopGateway'] == 'sagepay' || $this->CI->site->config['shopGateway'] == 'authorize') ? site_url('/shop/checkout') : $this->CI->shop->gateway_url;

			// get shop headlines
			if (preg_match_all('/{headlines:shop(:category\(([A-Za-z0-9_-]+)\))?(:limit\(([0-9]+)\))?}/i', $body, $matches))
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[4][0]) ? $matches[4][0] : $this->CI->site->config['headlines'];
				$catSafe = $matches[2][0];

				// get latest posts
				if ($headlines = $this->CI->shop->get_latest_products($catSafe, $limit))
				{
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get body and excerpt
						$headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
						$headlineExcerpt = nl2br($headline['excerpt']);

						// get images
						if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true))
						{
							$headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}

						// get images
						if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true))
						{
							$headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}

						// populate template
						$template[$headlineID][$i] = array(
							'headline:id' => $headline['productID'],
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:subtitle' => $headline['subtitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:image-path' => $headlineImage['src'],
							'headline:thumb-path' => $headlineThumb['src'],
							'headline:cell-width' => floor(( 1 / $limit) * 100),
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:stock' => $headline['stock'],
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);

						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}

			// get shop headlines
			if (preg_match_all('/{headlines:shop:featured(:limit(\(([0-9]+)\))?)?}/i', $body, $matches))
			{
				// filter matches
				$headlineID = preg_replace('/{|}/', '', $matches[0][0]);
				$limit = ($matches[3][0]) ? $matches[3][0] : $this->CI->site->config['headlines'];

				// get latest posts
				if ($headlines = $this->CI->shop->get_latest_featured_products($limit))
				{
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						// get body and excerpt
						$headlineBody = (strlen($headline['description']) > 100) ? substr($headline['description'], 0, 100).'...' : $headline['description'];
						$headlineExcerpt = nl2br($headline['excerpt']);

						// get images
						if (!$headlineImage = $this->CI->uploads->load_image($headline['productID'], false, true))
						{
							$headlineImage['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}

						// get thumb
						if (!$headlineThumb = $this->CI->uploads->load_image($headline['productID'], true, true))
						{
							$headlineThumb['src'] = $this->CI->config->item('staticPath').'/images/nopicture.jpg';
						}

						$template[$headlineID][$i] = array(
							'headline:id' => $headline['productID'],
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:subtitle' => $headline['subtitle'],
							'headline:date' => dateFmt($headline['dateCreated'], ($this->CI->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
							'headline:body' => $headlineBody,
							'headline:excerpt' => $headlineExcerpt,
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:image-path' => $headlineImage['src'],
							'headline:thumb-path' => $headlineThumb['src'],
							'headline:cell-width' => floor(( 1 / $limit) * 100),
							'headline:price' => currency_symbol().number_format($headline['price'],2),
							'headline:stock' => $headline['stock'],
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);

						$i++;
					}
				}
				else
				{
					$template[$headlineID] = array();
				}
			}

			// get shop cart headlines
			if (preg_match('/({headlines:shop:((.+)?)})+/i', $body))
			{
				// get shopping cart
				$cart = $this->CI->shop->load_cart();

				// get latest posts
				if ($headlines = $cart['cart'])
				{
					// fill up template array
					$i = 0;
					foreach ($headlines as $headline)
					{
						$template['headlines:shop:cartitems'][$i] = array(
							'headline:link' => site_url('shop/'.$headline['productID'].'/'.strtolower(url_title($headline['productName']))),
							'headline:title' => $headline['productName'],
							'headline:quantity' => $headline['quantity'],
							'headline:price' => currency_symbol().(number_format($headline['price'] * $headline['quantity'], 2)),
							'headline:class' => ($i % 2) ? ' alt ' : ''
						);

						$i++;
					}
					$template['headlines:shop:numitems'] = count($headlines);
					$template['headlines:shop:subtotal'] = currency_symbol().number_format($cart['subtotal'], 2);
				}
				else
				{
					$template['headlines:shop:numitems'] = 0;
					$template['headlines:shop:subtotal'] = currency_symbol().number_format(0, 2);
					$template['headlines:shop:cartitems'] = array();
				}
			}

			// get shop navigation
			if (preg_match('/({shop:categories((.+)?)})+/i', $body))
			{
				$template['shop:categories'] = '';

				if ($categories = $this->CI->shop->get_category_parents())
				{
					$i = 1;
					foreach($categories as $nav)
					{
						// get subnav
						if ($children = $this->CI->shop->get_category_children($nav['catID']))
						{
							$template['shop:categories'] .= '<li class="expanded ';
							if ($i == 1)
							{
								$template['shop:categories'] .= 'first ';
							}
							if ($i == sizeof($categories))
							{
								$template['shop:categories'] .= 'last ';
							}
							$template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'">'.htmlentities($nav['catName'], NULL, 'UTF-8').'</a><ul class="subnav">';

							foreach($children as $child)
							{
								$template['shop:categories'] .= '<li class="';
								if ($child['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(2))
								{
									$template['shop:categories'] .= 'active selected';
								}
								$template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'/'.$child['catSafe'].'">'.htmlentities($child['catName'], NULL, 'UTF-8').'</a></li>';
							}
							$template['shop:categories'] .= '</ul>';
						}
						else
						{
							$template['shop:categories'] .= '<li class="';
							if ($nav['catID'] == $this->CI->uri->segment(3) || $nav['catSafe'] == $this->CI->uri->segment(2))
							{
								$template['shop:categories'] .= 'active selected ';
							}
							if ($i == 1)
							{
								$template['shop:categories'] .= 'first ';
							}
							if ($i == sizeof($categories))
							{
								$template['shop:categories'] .= 'last ';
							}
							$template['shop:categories'] .= '"><a href="/shop/'.$nav['catSafe'].'">'.htmlentities($nav['catName'], NULL, 'UTF-8').'</a>';
						}

						$template['shop:categories'] .= '</li>';
						$i++;
					}
				}
			}
		}

		// message centre stuff
		if (preg_match('/({((.+)?)messages:unread((.+)?)})+/i', $body))
		{
			// load messages model
			$this->CI->load->model('community/messages_model', 'messages');

			// get message count
			@$template['messages:unread'] = ($messageCount = $this->CI->messages->get_unread_message_count()) ? $messageCount : 0;
		}

		return $template;
	}

	function parse_images($body)
	{
		// parse for images
		preg_match_all('/image\:([a-z0-9\-_]+)/i', $body, $images);
		if ($images)
		{
			foreach($images[1] as $image => $value)
			{
				$imageHTML = '';
				if ($imageData = $this->get_image($value))
				{
					$imageHTML = display_image($imageData['src'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic '.$imageData['class'].'"');
					$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
				}
				elseif ($this->CI->session->userdata('session_admin'))
				{
					$imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
				}
				$body = str_replace('{image:'.$value.'}', $imageHTML, $body);
			}
		}

		// parse for thumbs
		preg_match_all('/thumb\:([a-z0-9\-_]+)/i', $body, $images);
		if ($images)
		{
			foreach($images[1] as $image => $value)
			{
				$imageHTML = '';
				if ($imageData = $this->get_image($value))
				{
					$imageHTML = display_image($imageData['thumbnail'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic thumb '.$imageData['class'].'"');
					$imageHTML = preg_replace('/src=("[^"]*")/i', 'src="/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext']).'"', $imageHTML);
				}
				elseif ($this->CI->session->userdata('session_admin'))
				{
					$imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
				}
				$body = str_replace('{thumb:'.$value.'}', $imageHTML, $body);
			}
		}

		return $body;
	}

	function get_image($imageRef)
	{
		$this->CI->db->where('siteID', $this->siteID);
		$this->CI->db->where('deleted', 0);
		$this->CI->db->where('imageRef', $imageRef);
		$query = $this->CI->db->get('images');

		// get data
		if ($query->num_rows())
		{
			// path to uploads
			$pathToUploads = $this->uploadsPath;

			$row = $query->row_array();

			$image = $row['filename'];
			$ext = substr($image,strpos($image,'.'));

			$imagePath = $pathToUploads.'/'.$image;
			$thumbPath = str_replace($ext, '', $imagePath).'_thumb'.$ext;

			$row['ext'] = $ext;
			$row['src'] = $imagePath;
			$row['thumbnail'] = (file_exists('.'.$thumbPath)) ? $thumbPath : $imagePath;

			return $row;
		}
		else
		{
			return FALSE;
		}
	}

	function parse_files($body)
	{
		// parse for files
		preg_match_all('/file\:([a-z0-9\-_]+)/i', $body, $files);
		if ($files)
		{
			foreach($files[1] as $file => $value)
			{
				$fileData = $this->get_file($value);

				$body = str_replace('{file:'.$value.'}', anchor('/files/'.$fileData['fileRef'].$fileData['extension'], 'Download', 'class="file '.str_replace('.', '', $fileData['extension']).'"'), $body);
			}
		}

		return $body;
	}

	function get_file($fileRef)
	{
		// get data
		if ($file = $this->CI->uploads->load_file($fileRef, TRUE))
		{
			return $file;
		}
		else
		{
			return false;
		}
	}
}
