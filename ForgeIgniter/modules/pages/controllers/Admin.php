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
defined('BASEPATH') or exit('No direct script access allowed');

// ------------------------------------------------------------------------

class Admin extends MX_Controller
{

    // set defaults
    public $table = 'pages';								// table to update
    public $includes_path = '/includes/admin';				// path to includes for header and footer
    public $redirect = '/admin/pages/viewall';				// default redirect
    public $objectID = 'pageID';							// default unique ID
    public $permissions = array();

    public function __construct()
    {
        parent::__construct();

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_admin')) {
            redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // get permissions and redirect if they don't have access to this module
        if (!$this->permission->permissions) {
            redirect('/admin/dashboard/permissions');
        }
        if (!in_array($this->uri->segment(2), $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // load libs
        $this->load->library('parser');
        $this->load->model('pages_model', 'pages');
        $this->config->load('ckeditor_config', true);
    }

    public function index()
    {
        redirect($this->redirect);
    }

    public function viewall()
    {
        // set defaults
        $output['parents'] = array();
        $output['children'] = array();
        $output['subchildren'] = array();

        // get parents
        if ($output['parents'] = $this->pages->get_page_parents()) {
            // get children
            foreach ($output['parents'] as $parent) {
                if ($output['children'][$parent['pageID']] = $this->pages->get_page_children($parent['pageID'])) {
                    foreach ($output['children'][$parent['pageID']] as $child) {
                        $output['subchildren'][$child['pageID']] = $this->pages->get_page_children($child['pageID']);
                    }
                }
            }
        }

        // load views
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/viewall', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add()
    {
        $pageID = $this->pages->add_temp_page();
        redirect('/admin/pages/edit/'.$pageID);
    }

    public function edit($pageID)
    {
        if (!$pagedata = $this->core->get_page($pageID)) {
            show_error('Not a valid page!');
        }

        // check permissions for this page
        if (!in_array('pages_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required
        $this->core->required = array(
            'pageName' => array('label' => 'Page name', 'rules' => 'required'),
            'uri' => array('label' => 'Path', 'rules' => 'required|unique[pages.uri]|strtolower|trim'),
        );

        // where
        $objectID = array($this->objectID => $pageID);

        // get values (always before post handling)
        $output['data'] = $this->core->get_values($this->table, $objectID);
        $output['templates'] = $this->pages->get_templates('page');
        $output['groups'] = $this->permission->get_groups('admin');
        $output['versions'] = $this->core->get_versions($pageID);
        $output['drafts'] = $this->core->get_drafts($pageID);

        // get parents
        if ($output['parents'] = $this->pages->get_page_parents()) {
            // get children
            foreach ($output['parents'] as $parent) {
                $output['children'][$parent['pageID']] = $this->pages->get_page_children($parent['pageID']);
            }
        }

        // uri
        $uri = strtolower($this->input->post('uri'));

        // deal with post
        if (count($_POST)) {
            if ($this->input->post('cancel')) {
                if ($this->input->post('pageName') == '') {
                    $this->core->delete($this->table, array('pageID' => $pageID));
                }

                redirect($this->redirect);
            } else {
                // undelete
                $this->core->set['deleted'] = 0;

                // set date
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");

                // set page title
                if (!$this->input->post('title')) {
                    $this->core->set['title'] = $this->input->post('pageName');
                }

                // check uri
                if ($this->input->post('uri')) {
                    $this->core->set['uri'] = $uri;
                }

                // publish page
                if ($this->input->post('target') == 'publish') {
                    // update version
                    $this->core->set['versionID'] = $pagedata['draftID'];
                    $this->core->set['datePublished'] = date("Y-m-d H:i:s");

                    $this->core->publish_draft($pagedata['draftID']);

                    // set page active
                    $this->core->set['active'] = 1;
                }

                // get date of most recent block
                $latestBlock = $this->core->get_latest_block($pagedata['draftID']);
                if ($latestBlock && (strtotime($latestBlock['dateCreated']) > strtotime($pagedata['dateModified']))) {
                    // save draft
                    if ($draftID = $this->core->add_draft($pageID)) {
                        $this->core->set['draftID'] = $draftID;
                    }
                }

                // update
                if ($this->core->update($this->table, $objectID)) {
                    // view page
                    if ($this->input->post('target') == 'view') {
                        redirect('/'.$uri);
                    } elseif ($this->input->post('target') == 'publish') {
                        $this->session->set_flashdata('success', 'Your page was published.');

                        redirect('/admin/pages/edit/'.$pageID);
                    } else {
                        $this->session->set_flashdata('success', 'Your changes were saved.');

                        redirect('/admin/pages/edit/'.$pageID);
                    }
                }
            }
        }

        // set message
        if ($message = $this->session->flashdata('success')) {
            $output['message'] = '<p>'.$message.'</p>';
        }

        // check that this is not the live version and then add page version
        if ($output['versions']) {
            foreach ($output['versions'] as $version) {
                $versionIDs[] = $version['versionID'];
            }
        }
        if ((!$pagedata['versionID'] && !$pagedata['draftID']) || @in_array($pagedata['draftID'], $versionIDs)) {
            $this->core->add_draft($pageID);

            // carry across any old messages
            if ($message) {
                $this->session->set_flashdata('success', $message);
            }

            // redirect
            redirect($this->uri->uri_string());
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/edit', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function publish($pageID)
    {
        if (!$pagedata = $this->core->get_page($pageID)) {
            show_error('Not a valid page!');
        }

        // check permissions for this page
        if (!in_array('pages_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // publish draft
        $this->core->publish_draft($pagedata['draftID']);

        // publish page
        if ($this->core->publish_page($pageID, $pagedata['draftID'])) {
            $this->session->set_flashdata('success', 'Your page was published.');
        }

        redirect('/admin/pages/edit/'.$pageID);
    }

    public function generate_uri()
    {
        $output = '';

        if ($parentID = $this->input->post('parentID')) {
            $parent = $this->pages->get_page($parentID);
            $output .= $parent['uri'].'/';
        }

        $output .= strtolower(url_title($this->input->post('uri')));

        $this->output->set_output($output);
    }

    public function delete($objectID)
    {
        // check permissions for this page
        if (!in_array('pages_delete', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->core->soft_delete($this->table, array($this->objectID => $objectID)));
        {
            $this->core->delete('page_blocks', array($this->objectID => $objectID));

            // set children to no parent
            $this->pages->update_children($objectID);

            // where to redirect to
            redirect($this->redirect);
        }
    }

    public function revert_version($pageID = '', $versionID = '')
    {
        // check permissions for this page
        if (!in_array('pages_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // check stuff
        if (!$pageID || !$versionID) {
            show_error('Something went wrong!');
        }

        if (!$pagedata = $this->core->get_page($pageID)) {
            show_error('No page found!');
        }

        // revert and redirect
        if ($this->core->revert_version($pageID, $versionID)) {
            $this->session->set_flashdata('success', 'The page was reverted to a different published version.');

            redirect('/admin/pages/edit/'.$pageID);
        } else {
            show_error('Something went wrong!');
        }
    }

    public function revert_draft($pageID = '', $draftID = '')
    {
        // check permissions for this page
        if (!in_array('pages_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // check stuff
        if (!$pageID || !$draftID) {
            show_error('Something went wrong!');
        }

        if (!$pagedata = $this->core->get_page($pageID)) {
            show_error('No page found!');
        }

        // revert and redirect
        if ($this->core->revert_draft($pageID, $draftID)) {
            $this->session->set_flashdata('success', 'The page was reverted to a different draft.');

            redirect('/admin/pages/edit/'.$pageID);
        } else {
            show_error('Something went wrong!');
        }
    }

    public function templates($type = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // deal with post
        if (count($_FILES)) {
            // allowed ZIP mime types
            $allowedZips = array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/octet-stream');

            if ($this->input->post('upload_zip')) {
                if (substr($_FILES['zip']['name'], -3) == 'zip' && in_array($_FILES['zip']['type'], $allowedZips)) {
                    // get started
                    $success = false;
                    $includes = false;
                    $this->load->library('zip');
                    $this->load->library('encrypt');

                    $zip = zip_open($_FILES['zip']['tmp_name']);
                    if ($zip) {
                        // cycle through the zip
                        while ($zip_entry = zip_read($zip)) {
                            if (!preg_match('/(\_)+MACOSX/', zip_entry_name($zip_entry))) {
                                if (zip_entry_filesize($zip_entry) > 200000) {
                                    $this->form_validation->set_error('<p>Some files were too big to upload. Please only use files under 200kb.</p>');
                                } else {
                                    if (preg_match('/\.(html|html|css|js)$/i', zip_entry_name($zip_entry))) {
                                        // get filename
                                        $filename = basename(zip_entry_name($zip_entry));

                                        // read template
                                        $content = '';
                                        if (zip_entry_open($zip, $zip_entry, "r")) {
                                            $body = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                                            zip_entry_close($zip_entry);

                                            $this->pages->import_template($filename, $body);

                                            $success = true;
                                        }
                                    } elseif (preg_match('/\.(jpg|gif|png)$/i', zip_entry_name($zip_entry))) {
                                        // format filename
                                        $filenames = explode('.', zip_entry_name($zip_entry));
                                        $filename = trim(basename($filenames[0]));
                                        $extension = end($filenames);

                                        // get file name
                                        $imageRef = url_title(trim(strtolower($filename)));

                                        // check ref is unique and upload
                                        if ($this->form_validation->unique($imageRef, 'images.imageRef')) {
                                            // set stuff
                                            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
                                            $this->core->set['imageName'] = 'Graphic';
                                            $this->core->set['filename'] = md5($filename).'.'.$extension;
                                            $this->core->set['imageRef'] = $imageRef;
                                            $this->core->set['filesize'] = floor(zip_entry_filesize($zip_entry) / 1024);
                                            $this->core->set['groupID'] = 1;
                                            $this->core->set['userID'] = $this->session->userdata('userID');

                                            // update and then unset easy
                                            $this->core->update('images');

                                            // upload file
                                            $fp = fopen('.'.$this->uploads->uploadsPath.'/'.md5($filename).'.'.$extension, "w+");
                                            if (zip_entry_open($zip, $zip_entry, "r")) {
                                                $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                                                zip_entry_close($zip_entry);
                                            }
                                            fwrite($fp, $buf);
                                            fclose($fp);

                                            $success = true;
                                        }
                                    }
                                }
                            }
                        }
                        zip_close($zip);
                    }

                    // redirect
                    if ($success === true) {
                        redirect('/admin/pages/templates/');
                    }
                } else {
                    $this->form_validation->set_error('<p>There was a problem opening the zip file, sorry.</p>');
                }
            }
        }

        // filter
        $where = '';
        if ($type == 'module' || $type == 'page') {
            $where = $type;
        }
        $output['type'] = $where;

        // grab data and display
        $output['templates'] = $this->pages->get_templates($where);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/templates', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_template()
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'templateName' => 'Template name',
            'modulePath' => array('label' => 'Module', 'rules' => 'unique[templates.modulePath]')
        );

        // get values
        $output['data'] = $this->core->get_values();

        // update
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set date
                $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

                if ($this->core->update('templates')) {
                    // get new templateID
                    $templateID = $this->db->insert_id();

                    // add template version
                    $versionID = $this->pages->add_template_version($templateID);

                    // set message
                    $this->session->set_flashdata('message', 'Your new template was created successfully.');

                    // where to redirect to
                    redirect('/admin/pages/edit_template/'.$templateID);
                }
            }
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/add_template', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_template($templateID)
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'templateName' => 'Template name',
            'modulePath' => array('label' => 'Module', 'rules' => 'unique[templates.modulePath]')
        );

        // where
        $objectID = array('templateID' => $templateID);

        // get values from version
        $row = $this->pages->get_template($templateID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set stuff
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");

                // update
                if ($this->core->update('templates', $objectID)) {
                    // add template version
                    if ($versionID = $this->pages->add_template_version($templateID)) {
                        $output['data']['versionID'] = $versionID;
                    }

                    // set message
                    $this->session->set_flashdata('message', 'Your changes have been saved.');
                }
            }
        }

        // get versions
        $output['versions'] = $this->pages->get_template_versions($templateID);

        // if reverted show a message
        if ($message = $this->session->flashdata('message')) {
            $output['message'] = '<p>'.$message.'</p>';
        }

        // is ajax?
        if ($this->core->is_ajax()) {
            // get errors if there are any
            if ($errors = validation_errors()) {
                $ajaxMessage = 'There was a problem editing the form. Please make sure that the template name is set and the module path is unique.';
            } else {
                $ajaxMessage = 'Your changes have been saved.';
            }

            $this->output->set_output($ajaxMessage);
        }

        // normal templates
        else {
            // templates
            $this->load->view($this->includes_path.'/header');
            $this->load->view('admin/edit_template', $output);
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_template($templateID)
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('templateID' => $templateID);

        if ($this->core->soft_delete('templates', $objectID)) {
            // where to redirect to
            redirect('/admin/pages/templates');
        }
    }

    public function revert_template($templateID = '', $revisionID = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // check stuff
        if (!$templateID || !$revisionID) {
            show_error('Something went wrong!');
        }

        if (!$template = $this->pages->get_template($templateID)) {
            show_error('No template found!');
        }

        // revert and redirect
        if ($this->pages->revert_template($templateID, $revisionID)) {
            $this->session->set_flashdata('message', 'The template was reverted to a different version.');

            redirect('/admin/pages/edit_template/'.$templateID);
        } else {
            show_error('Something went wrong!');
        }
    }

    public function includes($type = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // set type
        if ($type == 'css') {
            $type = 'C';
            $file = 'css';
        } elseif ($type == 'js') {
            $type = 'J';
            $file = 'js';
        } else {
            $type = 'H';
            $file = 'includes';
        }

        // grab data and display
        $output['includes'] = $this->pages->get_includes($type);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/'.$file, $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_include($type = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'includeRef' => array('label' => 'Reference', 'rules' => 'required|unique[includes.includeRef]')
        );

        // get values
        $output['data'] = $this->core->get_values();

        // update
        if (count($_POST)) {
            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

            if ($this->core->update('includes')) {
                // get new includeID
                $includeID = $this->db->insert_id();

                // add include version
                $versionID = $this->pages->add_include_version($includeID);

                // set message
                $this->session->set_flashdata('message', 'Your new include was created successfully.');

                // where to redirect to
                redirect('/admin/pages/edit_include/'.$includeID);
            }
        }

        // set type
        $output['type'] = $type;

        // includes
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/add_include', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_include($includeID)
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'includeRef' => array('label' => 'Reference', 'rules' => 'required|unique[includes.includeRef]')
        );

        // where
        $objectID = array('includeID' => $includeID);

        // get values from version
        $row = $this->pages->get_include(null, $includeID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            // set stuff
            $this->core->set['dateModified'] = date("Y-m-d H:i:s");

            // update
            if ($this->core->update('includes', $objectID)) {
                // add include version
                if ($versionID = $this->pages->add_include_version($includeID)) {
                    $output['data']['versionID'] = $versionID;
                }

                // set message
                $this->session->set_flashdata('message', 'Your changes have been saved.');
            }
        }

        // get versions
        $output['versions'] = $this->pages->get_include_versions($includeID);

        // if reverted show a message
        if ($message = $this->session->flashdata('message')) {
            $output['message'] = '<p>'.$message.'</p>';
        }

        // set type
        $output['type'] = $row['type'];

        // is ajax?
        if ($this->core->is_ajax()) {
            // get errors if there are any
            if ($errors = validation_errors()) {
                $ajaxMessage = 'There was a problem editing the form. Please make sure that the include ref is set and is unique.';
            } else {
                $ajaxMessage = 'Your changes have been saved.';
            }

            $this->output->set_output($ajaxMessage);
        }

        // normal templates
        else {
            // templates
            $this->load->view($this->includes_path.'/header');
            $this->load->view('admin/edit_include', $output);
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_include($includeID, $type = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('includeID' => $includeID);

        if ($this->core->soft_delete('includes', $objectID)) {
            // where to redirect to
            redirect('/admin/pages/includes/'.$type);
        }
    }

    public function revert_include($includeID = '', $revisionID = '')
    {
        // check permissions for this page
        if (!in_array('pages_templates', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // check stuff
        if (!$includeID || !$revisionID) {
            show_error('Something went wrong!');
        }

        if (!$include = $this->pages->get_include(null, $includeID)) {
            show_error('No include found!');
        }

        // revert and redirect
        if ($this->pages->revert_include($includeID, $revisionID)) {
            $this->session->set_flashdata('message', 'The include was reverted to a different version.');

            redirect('/admin/pages/edit_include/'.$includeID);
        } else {
            show_error('Something went wrong!');
        }
    }

    public function navigation()
    {
        // check permissions for this page
        if (!in_array('pages_navigation', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // get parents
        if ($parents = $this->template->get_nav_parents('custom')) {
            // get children
            foreach ($parents as $parent) {
                $children[$parent['navID']] = $this->template->get_nav_children($parent['navID'], 'custom');
            }
        }

        // send data to view
        $output['parents'] = @$parents;
        $output['children'] = @$children;

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/navigation', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_nav()
    {
        // check permissions for this page
        if (!in_array('pages_navigation', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'navName' => 'Title',
            'uri' => array('label' => 'Path', 'rules' => 'required|trim|strtolower')
        );

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set stuff
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");

                // check there's no slash at beginning
                if (preg_match('/^\//', $this->input->post('uri'))) {
                    $this->core->set['uri'] = preg_replace('/^\//', '', $this->input->post('uri'));
                }

                // update
                if ($this->core->update('navigation')) {
                    redirect('/admin/pages/navigation');
                }
            }
        }

        // get parents
        $output['parents'] = $this->template->get_nav_parents('custom');

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/nav_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_nav($navID)
    {
        // check permissions for this page
        if (!in_array('pages_navigation', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'navName' => 'Title',
            'uri' => array('label' => 'Path', 'rules' => 'required|trim|strtolower')
        );

        // where
        $objectID = array('navID' => $navID);

        // get values from version
        $row = $this->template->get_nav($navID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set stuff
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");

                // check there's no slash at beginning
                if (strlen($this->input->post('uri')) > 1 && preg_match('/^\//', $this->input->post('uri'))) {
                    $this->core->set['uri'] = preg_replace('/^\//', '', $this->input->post('uri'));
                }

                // update
                if ($this->core->update('navigation', $objectID)) {
                    redirect('/admin/pages/navigation');
                }
            }
        }

        // get parents
        $output['parents'] = $this->template->get_nav_parents('custom');

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/nav_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_nav($navID)
    {
        // check permissions for this page
        if (!in_array('pages_navigation', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('navID' => $navID);

        if ($this->core->soft_delete('navigation', $objectID)) {
            // where
            $this->core->soft_delete('navigation', array('parentID' => $navID));

            // where to redirect to
            redirect('/admin/pages/navigation');
        }
    }

    public function order($field = '')
    {
        $this->core->order(key($_POST), $field);
    }

    public function view_template($templateID, $pageID = '')
    {
        // get pagedata
        if (!$pagedata = $this->core->get_page($pageID)) {
            show_error('Something went wrong!');
        }

        // check this page isn't a module
        $modules = array('blog', 'community/members', 'community/files', 'events', 'forums', 'manage', 'shop', 'wiki');
        if (in_array($pagedata['uri'], $modules)) {
            show_error('You have set a module as your path. You can only edit the display of modules in the Templates section.');
        }

        if ($pageID) {
            $output = $this->core->generate_page($pageID, true, $templateID);
        } else {
            $template = $this->pages->get_template($templateID);
            $output = $this->core->generate_template($template);
        }

        // parse body for relative paths
        $output['body'] = str_replace('="./', '="'.site_url('/'), $output['body']);
        $output['body'] = str_replace('="gfx', '="'.site_url('/gfx'), $output['body']);
        $output['body'] = str_replace('="images', '="'.site_url('/images'), $output['body']);
        $output['body'] = str_replace('="js', '="'.site_url('/js'), $output['body']);
        $output['body'] = str_replace('="css', '="'.site_url('/css'), $output['body']);

        $this->parser->parse('view_template', $output);
    }

    public function add_block($versionID, $block)
    {
        // check the block has content and is not null
        if (count($_POST)) {
            if ($_POST['body'] != 'undefined' && strlen($_POST['body']) > 0) {
                // remove the code added by the javascript to allow for empty posts
                $body = str_replace('[!!ADDBLOCK!!]', '', $_POST['body']);

                // check character set
                $body = htmlentities($body, null, 'UTF-8');
                $body = html_entity_decode($body, null, 'UTF-8');

                // add block
                @$this->core->add_block($body, $versionID, $block);

                // parse for includes and images
                $body = $this->template->parse_body($body);

                $body = preg_replace('/<script(.*)<\/script>/is', '<em>This block contained scripts, please refresh page.</em>', $body);

                // return block
                $this->output->set_output($body);
            }
        } else {
            redirect('/');
        }
    }

    public function module($modulePath = '')
    {
        if ($modulePath) {
            $file = '';

            $split = @preg_split('/_/', $modulePath);

            $file = @file_get_contents(APPPATH.'modules/'.$split[0].'/views/templates/'.$modulePath.'.php');

            $this->output->set_output($file);
        }
    }
}
