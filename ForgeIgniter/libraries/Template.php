<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package   ForgeIgniter
 * @author    ForgeIgniter Team
 * @copyright Copyright (c) 2023, ForgeIgniter
 * @license   http://forgeigniter.com/license
 * @link      http://forgeigniter.com/
 */
// ------------------------------------------------------------------------

class Template
{

    // set defaults
    public $CI;								// CI instance
    public $base_path = '';					// default base path
    public $template = array();

    public function __construct()
    {
        $this->CI =& get_instance();

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        $this->uploadsPath = $this->CI->config->item('uploadsPath');
        
    }

    public function generate_template($pagedata, $file = false)
    {
        // page data
        $this->template['page:title'] = isset($pagedata['title']) ? htmlentities($pagedata['title'], ENT_QUOTES | ENT_IGNORE, "UTF-8") : htmlentities($this->CI->site->config['siteName']);
        $this->template['page:keywords'] = isset($pagedata['keywords']) ? $pagedata['keywords'] : '';
        $this->template['page:description'] = isset($pagedata['description']) ? $pagedata['description'] : '';
        $this->template['page:date'] = isset($pagedata['dateCreated']) ? dateFmt($pagedata['dateCreated']) : '';
        $this->template['page:date-modified'] = isset($pagedata['dateModified']) ? dateFmt($pagedata['dateModified']) : '';
        $this->template['page:uri'] = site_url($this->CI->uri->uri_string());
        $this->template['page:uri-encoded'] = $this->CI->core->encode($this->CI->uri->uri_string());
        $this->template['page:uri:segment(1)'] = $this->CI->uri->segment(1);
        $this->template['page:uri:segment(2)'] = $this->CI->uri->segment(2);
        $this->template['page:uri:segment(3)'] = $this->CI->uri->segment(3);
        $this->template['page:template'] = isset($this->template['page:template']) ? $this->template['page:template'] : '';

        // find out if logged in
        $this->template['logged-in'] = ($this->CI->session->userdata('session_user')) ? true : false;

        // find out if subscribed
        $this->template['subscribed'] = ($this->CI->session->userdata('subscribed')) ? true : false;

        // find out if admin
        $this->template['admin'] = ($this->CI->session->userdata('session_admin')) ? true : false;

        // find out if this is ajax
        $this->template['ajax'] = ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))) ? true : false;

        // find out if browser is iphone
        $this->template['mobile'] = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'Android')) ? true : false;

        // permissions
        if ($this->CI->session->userdata('session_admin')) {
            if ($permissions = $this->CI->permission->get_group_permissions($this->CI->session->userdata('groupID'))) {
                foreach ($permissions as $permission) {
                    @$this->template['permission:'.$permission] = true;
                }
            }
        }

        // feed (if it exists for the module)
        @$this->template['page:feed'] = (isset($pagedata['feed'])) ? $pagedata['feed'] : '';

        // either build template from a file or from db
        if ($file) {
            $templateBody = $this->parse_template($file, false, null, false);
        } else {
            $templateData = $this->CI->core->get_template($pagedata['templateID']);
            $templateBody = $templateData['body'];
        }

        // parse it for everything else
        $this->template['body'] = $this->parse_template($templateBody, false, null, false);

        // get navigation and build menu
        if (preg_match_all('/{navigation(\:([a-z0-9\.-]+))?}/i', $this->template['body'], $matches)) {
            $this->template = $this->parse_navigation('navigation', $this->template);
        }

        return $this->template;
    }

    public function parse_includes($body)
    {
        // get includes
        preg_match_all('/include\:([a-z0-9\.-]+)/i', $body, $includes);

        if ($includes) {
            $includeBody = '';
            foreach ($includes[1] as $include => $value) {
                $includeRow = $this->CI->core->get_include($value);

                $includeBody = $this->parse_body($includeRow['body'], false, null, false);

                $includeBody = $this->CI->parser->conditionals($includeBody, $this->template, true);

                $body = str_replace('{include:'.$value.'}', $includeBody, $body);
            }
        }

        return $body;
    }

    public function parse_navigation($navTag, $template)
    {
        // get all navigation
        $template[$navTag] = $this->parse_nav();

        // get parents
        $template[$navTag.':parents'] = $this->parse_nav(0, false);

        // get uri
        $uri = (!$this->CI->uri->segment(1)) ? 'home' : $this->CI->uri->segment(1);

        // get children of active nav item
        if ($parent = $this->CI->core->get_page(false, $uri)) {
            $template[$navTag.':children'] = $this->parse_nav($parent['pageID']);
        } else {
            $template[$navTag.':children'] = '';
        }

        return $template;
    }

    public function parse_nav($parentID = 0, $showChildren = true)
    {
        $output = '';

        if ($navigation = $this->get_nav_parents($parentID)) {
            $i = 1;
            foreach ($navigation as $nav) {
                // set first and last state on menu
                $class = '';
                $class .= ($i == 1) ? 'first ' : '';
                $class .= (sizeof($navigation) == $i) ? 'last ' : '';

                // look for children
                $children = ($showChildren) ? $this->get_nav_children($nav['navID']) : false;

                // parse the nav item for the link
                $output .= $this->parse_nav_item($nav['uri'], $nav['navName'], $children, $class);

                // parse for children
                if ($children) {
                    $x = 1;
                    $output .= '<ul class="subnav">';
                    foreach ($children as $child) {
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
                        if ($subChildren) {
                            $y = 1;
                            $output .= '<ul class="subnav">';
                            foreach ($subChildren as $subchild) {
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

    public function parse_nav_item($uri, $name, $children = false, $class = '')
    {
        // init stuff
        $output = '';
        $childs = array();

        // tidy children array
        if ($children) {
            foreach ($children as $child) {
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
            @in_array(substr($this->CI->uri->uri_string(), 1), $childs)
        ) {
            $class .= 'active selected ';
        }
        if ($children) {
            $class .= 'expanded ';
        }

        // filter uri to make sure it's cool
        if (substr($uri, 0, 1) == '/') {
            $href = $uri;
        } elseif (stristr($uri, 'http://')) {
            $href = $uri;
        } elseif (stristr($uri, 'www.')) {
            $href = 'http://'.$uri;
        } elseif (stristr($uri, 'mailto:')) {
            $href = $uri;
        } elseif ($uri == 'home') {
            $href = '/';
        } else {
            $href = '/'.$uri;
        }

        // output anchor with span in case of additional styling
        $output .= trim($class).'" id="nav-'.trim($uri).'"><a href="'.site_url($href).'" class="'.trim($class).'"><span>'.htmlentities($name).'</span></a>';

        return $output;
    }

    public function get_nav($navID = '')
    {
        // default where
        $this->CI->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

        // where parent is set
        $this->CI->db->where('parentID', 0);

        // get navigation from pages
        $this->CI->db->select('uri, pageID as navID, pageName as navName');

        $this->CI->db->order_by('pageOrder', 'asc');

        $query = $this->CI->db->get('pages');

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_nav_parents($parentID = 0)
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

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function get_nav_children($navID = '')
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

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function parse_template($body, $condense = false, $link = '', $mkdn = true)
    {
        $body = $this->parse_body($body, $condense, $link, $mkdn);

        return $body;
    }

    public function parse_body($body, $condense = false, $link = '', $mkdn = true)
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
        if ($condense) {
            if ($endchr = strpos($body, '{more}')) {
                $body = substr($body, 0, ($endchr + 6));
                $body = str_replace('{more}', '<p class="more"><a href="'.$link.'" class="button more">Read more</a></p>', $body);
            }
        } else {
            $body = str_replace('{more}', '', $body);
        }

        // parse for clears
        $body = str_replace('{clear}', '<div style="clear:both;"/></div>', $body);

        // parse for pads
        $body = str_replace('{pad}', '<div style="padding-bottom:10px;width:10px;clear:both;"/></div>', $body);

        // parse body for markdown and images
        if ($mkdn === true) {
            // parse for mkdn
            $body = mkdn($body);
        }

        return $body;
    }

    public function parse_modules($body, $template)
    {
        // Get headlines from modules
        $modules = glob(APPPATH . 'modules/*', GLOB_ONLYDIR);
        foreach ($modules as $module) {
            $headlines_file = $module.'/config/headlines.php';
            if (file_exists($headlines_file)) {
                include($headlines_file);
            }
        }

        return $template;
    }

    public function parse_images($body)
    {
        // parse for images
        preg_match_all('/image\:([a-z0-9\-_]+)/i', $body, $images);
        if ($images) {
            foreach ($images[1] as $image => $value) {
                $imageHTML = '';
                if ($imageData = $this->get_image($value)) {
                    $imageHTML = display_image($imageData['src'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic '.$imageData['class'].'"');
                    $imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
                } elseif ($this->CI->session->userdata('session_admin')) {
                    $imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
                }
                $body = str_replace('{image:'.$value.'}', $imageHTML, $body);
            }
        }

        // parse for thumbs
        preg_match_all('/thumb\:([a-z0-9\-_]+)/i', $body, $images);
        if ($images) {
            foreach ($images[1] as $image => $value) {
                $imageHTML = '';
                if ($imageData = $this->get_image($value)) {
                    $imageHTML = display_image($imageData['thumbnail'], $imageData['imageName'], $imageData['maxsize'], 'id="'.$this->CI->core->encode($this->CI->session->userdata('lastPage').'|'.$imageData['imageID']).'" class="pic thumb '.$imageData['class'].'"');
                    $imageHTML = preg_replace('/src=("[^"]*")/i', 'src="/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext']).'"', $imageHTML);
                } elseif ($this->CI->session->userdata('session_admin')) {
                    $imageHTML = '<a href="'.site_url('/admin/images').'" target="_parent"><img src="'.$this->CI->config->item('staticPath').'/images/btn_upload.png" alt="Upload Image" /></a>';
                }
                $body = str_replace('{thumb:'.$value.'}', $imageHTML, $body);
            }
        }

        return $body;
    }

    public function get_image($imageRef)
    {
        $this->CI->db->where('siteID', $this->siteID);
        $this->CI->db->where('deleted', 0);
        $this->CI->db->where('imageRef', $imageRef);
        $query = $this->CI->db->get('images');

        // get data
        if ($query->num_rows()) {
            // path to uploads
            $pathToUploads = $this->uploadsPath;

            $row = $query->row_array();

            $image = $row['filename'];
            $ext = substr($image, strpos($image, '.'));

            $imagePath = $pathToUploads.'/'.$image;
            $thumbPath = str_replace($ext, '', $imagePath).'_thumb'.$ext;

            $row['ext'] = $ext;
            $row['src'] = $imagePath;
            $row['thumbnail'] = (file_exists('.'.$thumbPath)) ? $thumbPath : $imagePath;

            return $row;
        } else {
            return false;
        }
    }

    public function parse_files($body)
    {
        // parse for files
        preg_match_all('/file\:([a-z0-9\-_]+)/i', $body, $files);
        if ($files) {
            foreach ($files[1] as $file => $value) {
                $fileData = $this->get_file($value);

                $body = str_replace('{file:'.$value.'}', anchor('/files/'.$fileData['fileRef'].$fileData['extension'], 'Download', 'class="file '.str_replace('.', '', $fileData['extension']).'"'), $body);
            }
        }

        return $body;
    }

    public function get_file($fileRef)
    {
        // get data
        if ($file = $this->CI->uploads->load_file($fileRef, true)) {
            return $file;
        } else {
            return false;
        }
    }
}
