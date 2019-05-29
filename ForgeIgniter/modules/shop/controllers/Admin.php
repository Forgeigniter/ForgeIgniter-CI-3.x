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
    public $redirect = '/admin/shop/products';				// default redirect
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
        $this->load->model('shop_model', 'shop');
        $this->load->library('tags');
    }

    public function index()
    {
        redirect($this->redirect);
    }

    public function products($catID = '')
    {
        // get featured
        $featured = ($catID == 'featured') ? true : false;

        // set order segment
        if (is_numeric($catID) || $catID == 'featured') {
            $this->shop->uri_assoc_segment = 5;

            // output selected category
            $output['catID'] = $catID;
        } else {
            $output['catID'] = '';
        }

        // check catID isnt paging or featured
        $catID = ($catID == 'page' || $catID == 'featured' || $catID == 'orderasc' || $catID == 'orderdesc') ? '' : $catID;

        // set limit
        $limit = (!$catID) ? $this->site->config['paging'] : 999;

        // get products
        $output['products'] = $this->shop->get_products($catID, $this->input->post('searchbox'), $featured, $limit);

        // get categories
        $output['categories'] = $this->shop->get_categories();

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/products', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_product()
    {
        // check permissions for this page
        if (!in_array('shop_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required
        $this->core->required = array(
            'productName' => 'Product name',
            'catalogueID' => array('label' => 'Catalogue ID', 'rules' => 'required|unique[shop_products.catalogueID]|trim')
        );

        if ($this->input->post('cancel')) {
            redirect($this->redirect);
        } else {
            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['userID'] = $this->session->userdata('userID');

            // upload image
            if (@$_FILES['image']['name'] != '') {
                if ($imageData = $this->uploads->upload_image()) {
                    $this->core->set['imageName'] = $imageData['file_name'];
                }
            }

            // get values
            $output['data'] = $this->core->get_values('shop_products');

            // get image errors if there are any
            if ($this->uploads->errors) {
                $this->form_validation->set_error($this->uploads->errors);
            } else {
                // tidy tags
                $tags = [];
                if ($this->input->post('tags')) {
                    foreach (explode(',', $this->input->post('tags')) as $tag) {
                        $tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
                    }
                    $tags = implode(', ', $tags);
                }

                // set tags
                $this->core->set['tags'] = $tags;

                // update
                if ($this->core->update('shop_products') && count($_POST)) {
                    // get insert id
                    $productID = $this->db->insert_id();

                    // add variation 1
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation1-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 1, $this->input->post('variation1-'.$x), $this->input->post('variation1_price-'.$x));
                        }
                    }

                    // add variation 2
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation2-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 2, $this->input->post('variation2-'.$x), $this->input->post('variation2_price-'.$x));
                        }
                    }

                    // add variation 3
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation3-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 3, $this->input->post('variation3-'.$x), $this->input->post('variation3_price-'.$x));
                        }
                    }

                    // update categories
                    $this->shop->update_cats($productID, $this->input->post('catsArray'));

                    // update tags
                    $this->tags->update_tags('shop_products', $productID, $tags);

                    // where to redirect to
                    redirect($this->redirect);
                }
            }

            // get categories
            $output['categories'] = $this->shop->get_categories();

            // get products
            $output['files'] = $this->shop->get_files();

            // get bands
            $output['bands'] = $this->shop->get_bands();

            // set default stock
            $output['data']['stock'] = 1;
        }

        // templates
        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/add_product', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function edit_product($productID)
    {
        // check permissions for this page
        if (!in_array('shop_edit', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required
        $this->core->required = array(
            'productName' => 'Product name',
            'catalogueID' => array('label' => 'Catalogue ID', 'rules' => 'required|unique[shop_products.catalogueID]|trim')
        );

        // where
        $objectID = array('productID' => $productID);

        // get values
        $output['data'] = $this->core->get_values('shop_products', $objectID);

        if ($this->input->post('cancel')) {
            redirect($this->redirect);
        } else {
            // upload image
            if (@$_FILES['image']['name'] != '') {
                if ($imageData = $this->uploads->upload_image()) {
                    $this->core->set['imageName'] = $imageData['file_name'];
                }
            }

            // get image errors if there are any
            if ($this->shop->errors) {
                $output['errors'] = $this->shop->errors;
            } else {
                // set stock
                if ($this->input->post('status') == 'O' || ($this->site->config['shopStockControl'] && !$this->input->post('stock'))) {
                    $this->core->set['stock'] = 0;
                    $this->core->set['status'] = 'O';
                }

                // tidy tags
                $tags = [];
                if ($this->input->post('tags')) {
                    foreach (explode(',', $this->input->post('tags')) as $tag) {
                        $tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
                    }
                    $tags = implode(', ', $tags);
                }

                // set tags
                $this->core->set['tags'] = $tags;

                // update
                if ($this->core->update('shop_products', $objectID) && count($_POST)) {
                    // clear variations
                    $this->shop->clear_variations($productID);

                    // add variation 1
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation1-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 1, $this->input->post('variation1-'.$x), $this->input->post('variation1_price-'.$x));
                        }
                    }

                    // add variation 2
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation2-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 2, $this->input->post('variation2-'.$x), $this->input->post('variation2_price-'.$x));
                        }
                    }

                    // add variation 3
                    for ($x=1; $x<6; $x++) {
                        if ($this->input->post('variation3-'.$x)) {
                            $varID = $this->shop->add_variation($productID, 3, $this->input->post('variation3-'.$x), $this->input->post('variation3_price-'.$x));
                        }
                    }

                    // update categories
                    $this->shop->update_cats($productID, $this->input->post('catsArray'));

                    // update tags
                    $this->tags->update_tags('shop_products', $productID, $tags);

                    // set success message
                    $this->session->set_flashdata('success', 'Your changes were saved.');

                    // view page
                    if ($this->input->post('view')) {
                        redirect('/shop/'.$productID.'/'.strtolower(url_title($this->input->post('productName'))));
                    } else {
                        // where to redirect to
                        redirect('/admin/shop/edit_product/'.$productID);
                    }
                }
            }

            // set message
            if ($message = $this->session->flashdata('success')) {
                $output['message'] = '<p>'.$message.'</p>';
            }

            // set image path!
            $image = $this->uploads->load_image($productID, true, true);
            $output['imagePath'] = $image['src'];
            $image = $this->uploads->load_image($productID, false, true);
            $output['imageThumbPath'] = $image['src'];

            // get categories
            $output['categories'] = $this->shop->get_categories();

            // get categories for this product
            $output['data']['categories'] = $this->shop->get_cats_for_product($productID);

            // get variations
            $output['variation1'] = $this->shop->get_variations($productID, 1);
            $output['variation2'] = $this->shop->get_variations($productID, 2);
            $output['variation3'] = $this->shop->get_variations($productID, 3);

            // get bands
            $output['bands'] = $this->shop->get_bands();

            // get products
            $output['files'] = $this->shop->get_files();

            // templates
            $this->load->view($this->includes_path.'/header');
            $this->load->view('admin/edit_product', $output);
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_product($productID)
    {
        // check permissions for this page
        if (!in_array('shop_delete', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->core->soft_delete('shop_products', array('productID' => $productID)));
        {
            // remove category mappings
            $this->shop->update_cats($productID);

            // where to redirect to
            redirect($this->redirect);
        }
    }

    public function preview()
    {
        // get parsed body
        $html = $this->template->parse_body($this->input->post('body'));

        // filter for scripts
        $html = preg_replace('/<script(.*)<\/script>/is', '<em>This block contained scripts, please refresh page.</em>', $html);

        // output
        $this->output->set_output($html);
    }

    public function categories()
    {
        // check permissions for this page
        if (!in_array('shop_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // get parents
        if ($parents = $this->shop->get_category_parents()) {
            // get children
            foreach ($parents as $parent) {
                $children[$parent['catID']] = $this->shop->get_category_children($parent['catID']);
            }
        }

        // send data to view
        $output['parents'] = @$parents;
        $output['children'] = @$children;

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/categories', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_cat()
    {
        // check permissions for this page
        if (!in_array('shop_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'catName' => 'Title',
        );

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set stuff
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");
                $this->core->set['catSafe'] = url_title(strtolower(trim($this->input->post('catName'))));

                // update
                if ($this->core->update('shop_cats')) {
                    redirect('/admin/shop/categories');
                }
            }
        }

        // get parents
        $output['parents'] = $this->shop->get_category_parents();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/category_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_cat($catID)
    {
        // check permissions for this page
        if (!in_array('shop_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'catName' => 'Title',
        );

        // where
        $objectID = array('catID' => $catID);

        // get values from version
        $row = $this->shop->get_category($catID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set stuff
                $this->core->set['dateModified'] = date("Y-m-d H:i:s");
                $this->core->set['catSafe'] = url_title(strtolower(trim($this->input->post('catName'))));

                // update
                if ($this->core->update('shop_cats', $objectID)) {
                    redirect('/admin/shop/categories');
                }
            }
        }

        // get parents
        $output['parents'] = $this->shop->get_category_parents();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/category_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_cat($catID)
    {
        // check permissions for this page
        if (!in_array('shop_cats', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('catID' => $catID);

        if ($this->core->soft_delete('shop_cats', $objectID)) {
            // delete sub categories
            $objectID = array('parentID' => $catID);

            $this->core->soft_delete('shop_cats', $objectID);

            // where to redirect to
            redirect('/admin/shop/categories');
        }
    }

    public function order($field = '')
    {
        $this->core->order(key($_POST), $field);
    }

    public function orders($status = 'U')
    {
        // check permissions for this page
        if (!in_array('shop_orders', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output['orders'] = $this->shop->get_orders($status, null, $this->input->post('searchbox'));

        $output['trackingStatus'] = $status;
        $output['statusArray'] = $this->shop->statusArray;

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/orders', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function view_order($transactionID)
    {
        // check permissions for this page
        if (!in_array('shop_orders', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // set object ID
        $objectID = array('transactionID' => $transactionID);

        // get values
        $output['data'] = $this->core->get_values('shop_transactions', $objectID);

        // grab data and display
        $output['order'] = $this->shop->get_order($transactionID);
        $output['transactionID'] = $transactionID;

        if (count($_POST)) {
            // force unpaid uncheckout to paid and send order email
            if (!$output['data']['paid'] && $this->input->post('trackingStatus') != 'N') {
                modules::run('shop/shop/_create_order', $output['data']['transactionCode']);

                $this->core->set['paid'] = 1;
            } elseif ($this->input->post('trackingStatus') == 'N') {
                $this->core->set['trackingStatus'] = 'U';
                $this->core->set['paid'] = 0;
            }

            // update
            if ($this->core->update('shop_transactions', $objectID)) {
                if ($this->input->post('trackingStatus') == 'D') {
                    // set header and footer
                    $emailHeader = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailHeader']);
                    $emailHeader = str_replace('{email}', $output['order']['email'], $emailHeader);
                    $emailFooter = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailFooter']);
                    $emailFooter = str_replace('{email}', $output['order']['email'], $emailFooter);
                    $emailDispatch = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailDispatch']);
                    $emailDispatch = str_replace('{email}', $output['order']['email'], $emailDispatch);
                    $emailDispatch = str_replace('{order-id}', '#'.$output['order']['transactionCode'], $emailDispatch);

                    // send shipping email to customer
                    $userBody = $emailHeader."\n\n".$emailDispatch."\n\n";
                    $footerBody = $emailFooter;

                    // load email lib and email user and admin
                    $this->load->library('email');

                    $this->email->to($output['order']['email']);
                    $this->email->subject('Your order has been shipped (#'.$output['order']['transactionCode'].')');
                    $this->email->message($userBody.$footerBody);
                    $this->email->from($this->shop->siteVars['siteEmail'], $this->shop->siteVars['siteName']);
                    $this->email->send();
                }

                // set success message
                $this->session->set_flashdata('success', 'Your changes were saved.');

                redirect('/admin/shop/view_order/'.$transactionID);
            }
        }

        // set view flag
        if (!$output['order']['viewed']) {
            $this->shop->view_order($transactionID);
        }

        $output['item_orders'] = $this->shop->get_item_orders($transactionID);
        $output['statusArray'] = $this->shop->statusArray;

        // set message
        if ($message = $this->session->flashdata('success')) {
            $output['message'] = '<p>'.$message.'</p>';
        }

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/view_order', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function delete_order($transactionID)
    {
        // check permissions for this page
        if (!in_array('shop_orders', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // set object ID
        $objectID = array('transactionID' => $transactionID);

        // get values
        $output['data'] = $this->core->get_values('shop_transactions', $objectID);

        if ($this->core->delete('shop_transactions', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/orders');
        }
    }

    public function bands()
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output = $this->core->viewall('shop_bands', null, 'multiplier', 99);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/bands', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_band()
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'bandName' => 'Band Name',
            'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_bands.multiplier]')
        );

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_bands')) {
                    redirect('/admin/shop/bands');
                }
            }
        }

        // set default
        $output['data']['multiplier'] = 1;

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/band_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_band($bandID)
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'bandName' => 'Band Name',
            'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_bands.multiplier]')
        );

        // where
        $objectID = array('bandID' => $bandID);

        // get values from version
        $row = $this->shop->get_band($bandID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_bands', $objectID)) {
                    redirect('/admin/shop/bands');
                }
            }
        }

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/band_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_band($bandID)
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('bandID' => $bandID);

        if ($this->core->delete('shop_bands', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/bands');
        }
    }

    public function postages()
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output = $this->core->viewall('shop_postages', null, 'total', 99);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/postages', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_postage()
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'total' => 'total',
            'cost' => 'Cost'
        );

        // populate form
        $output['data'] = $this->core->get_values();
        $output['data']['total'] = '0.00';
        $output['data']['cost'] = '5.00';

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_postages')) {
                    redirect('/admin/shop/postages');
                }
            }
        }

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/postage_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_postage($postageID)
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'total' => 'total',
            'cost' => 'Cost'
        );

        // where
        $objectID = array('postageID' => $postageID);

        // get values from version
        $row = $this->shop->get_postage($postageID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_postages', $objectID)) {
                    redirect('/admin/shop/postages');
                }
            }
        }

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/postage_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_postage($postageID)
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('postageID' => $postageID);

        if ($this->core->delete('shop_postages', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/postages');
        }
    }

    public function modifiers()
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output['shop_modifiers'] = $this->shop->get_modifiers();

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/modifiers', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_modifier($bandID = '')
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'modifierName' => 'Name',
            'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_modifiers.multiplier]')
        );

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_modifiers')) {
                    redirect('/admin/shop/modifiers');
                }
            }
        }

        // set default
        $output['data']['multiplier'] = 1;
        $output['bands'] = $this->shop->get_bands();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/modifier_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_modifier($modifierID, $bandID = '')
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'modifierName' => 'Name',
            'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_modifiers.multiplier]')
        );

        // where
        $objectID = array('modifierID' => $modifierID);

        // get values from version
        $row = $this->shop->get_modifier($modifierID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_modifiers', $objectID)) {
                    redirect('/admin/shop/modifiers');
                }
            }
        }

        // get bands
        $output['bands'] = $this->shop->get_bands();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/modifier_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_modifier($modifierID)
    {
        // check permissions for this page
        if (!in_array('shop_shipping', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('modifierID' => $modifierID);

        if ($this->core->delete('shop_modifiers', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/modifiers');
        }
    }

    public function discounts()
    {
        // check permissions for this page
        if (!in_array('shop_discounts', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output = $this->core->viewall('shop_discounts', null, 'expiryDate');

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/discounts', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_discount()
    {
        // check permissions for this page
        if (!in_array('shop_discounts', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'code' => array('label' => 'Code', 'rules' => 'required|unique[shop_discounts.code]|trim'),
            'discount' => 'Discount',
            'expiryDate' => 'Expiry Date'
        );

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            // set dates
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['expiryDate'] = date("Y-m-d 23:59:59", strtotime($this->input->post('expiryDate').' 11.59PM'));

            // set object ID
            if ($this->input->post('catID')) {
                $this->core->set['objectID'] = $this->input->post('catID');
            }
            if ($this->input->post('productID') > 0) {
                $this->core->set['objectID'] = implode(',', $this->input->post('productID'));
            }

            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_discounts')) {
                    redirect('/admin/shop/discounts');
                }
            }
        }

        // get products
        $output['products'] = $this->shop->get_all_products();

        // get categories
        $output['categories'] = $this->shop->get_categories();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/discount_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_discount($discountID)
    {
        // check permissions for this page
        if (!in_array('shop_discounts', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // required fields
        $this->core->required = array(
            'code' => array('label' => 'Code', 'rules' => 'required|unique[shop_discounts.code]|trim'),
            'discount' => 'Discount',
            'expiryDate' => 'Expiry Date'
        );

        // where
        $objectID = array('discountID' => $discountID);

        // get values from version
        $row = $this->shop->get_discount($discountID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            // set dates
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['expiryDate'] = date("Y-m-d 23:59:59", strtotime($this->input->post('expiryDate').' 11.59PM'));

            // set object ID
            if ($this->input->post('catID') > 0) {
                $this->core->set['objectID'] = $this->input->post('catID');
            }
            if ($this->input->post('productID') > 0) {
                $this->core->set['objectID'] = implode(',', $this->input->post('productID'));
            }

            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_discounts', $objectID)) {
                    redirect('/admin/shop/discounts');
                }
            }
        }

        // get products
        $output['products'] = $this->shop->get_all_products();

        // get categories
        $output['categories'] = $this->shop->get_categories();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/discount_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_discount($discountID)
    {
        // check permissions for this page
        if (!in_array('shop_discounts', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('discountID' => $discountID);

        if ($this->core->delete('shop_discounts', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/discounts');
        }
    }

    public function reviews()
    {
        // check permissions for this page
        if (!in_array('shop_reviews', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output['reviews'] = $this->shop->get_reviews();

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/reviews', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function approve_review($reviewID)
    {
        // check permissions for this page
        if (!in_array('shop_reviews', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->shop->approve_review($reviewID)) {
            redirect('/admin/shop/reviews');
        }
    }

    public function delete_review($objectID)
    {
        // check permissions for this page
        if (!in_array('shop_reviews', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // check permissions for this page
        if (!in_array('shop_delete', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        if ($this->core->soft_delete('shop_reviews', array('reviewID' => $objectID))) {
            // where to redirect to
            redirect('/admin/shop/reviews/');
        }
    }

    public function upsells()
    {
        // check permissions for this page
        if (!in_array('shop_upsells', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // grab data and display
        $output = $this->core->viewall('shop_upsells', null, 'upsellOrder', 99);

        $this->load->view($this->includes_path.'/header');
        $this->load->view('admin/upsells', $output);
        $this->load->view($this->includes_path.'/footer');
    }

    public function add_upsell()
    {
        // check permissions for this page
        if (!in_array('shop_upsells', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // populate form
        $output['data'] = $this->core->get_values();

        // deal with post
        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set dates
                $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

                // set product IDs
                if ($this->input->post('productIDs') > 0) {
                    $this->core->set['productIDs'] = implode(',', $this->input->post('productIDs'));
                }

                // update
                if ($this->core->update('shop_upsells')) {
                    redirect('/admin/shop/upsells');
                }
            }
        }

        // get products
        $output['products'] = $this->shop->get_all_products();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/upsell_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function edit_upsell($upsellID)
    {
        // check permissions for this page
        if (!in_array('shop_upsells', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('upsellID' => $upsellID);

        // get values from version
        $row = $this->shop->get_upsell($upsellID);

        // populate form
        $output['data'] = $this->core->get_values($row);

        // deal with post
        if (count($_POST)) {
            // set dates
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

            // set product IDs
            if ($this->input->post('productIDs') > 0) {
                $this->core->set['productIDs'] = implode(',', $this->input->post('productIDs'));
            }

            if ($this->core->check_errors()) {
                // update
                if ($this->core->update('shop_upsells', $objectID)) {
                    redirect('/admin/shop/upsells');
                }
            }
        }

        // get products
        $output['products'] = $this->shop->get_all_products();

        // templates
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/header');
        }
        $this->load->view('admin/upsell_form', $output);
        if (!$this->core->is_ajax()) {
            $this->load->view($this->includes_path.'/footer');
        }
    }

    public function delete_upsell($upsellID)
    {
        // check permissions for this page
        if (!in_array('shop_upsells', $this->permission->permissions)) {
            redirect('/admin/dashboard/permissions');
        }

        // where
        $objectID = array('upsellID' => $upsellID);

        if ($this->core->delete('shop_upsells', $objectID)) {
            // where to redirect to
            redirect('/admin/shop/upsells');
        }
    }

    public function renew_downloads($transactionID)
    {
        if ($this->shop->renew_downloads($transactionID)) {
            // set success message
            $this->session->set_flashdata('success', 'The expiry date for downloads on this order has been renewed for another 5 days.');

            // where to redirect to
            redirect('/admin/shop/view_order/'.$transactionID);
        }
    }

    public function export_orders()
    {
        // export orders as CSV
        $this->load->dbutil();

        $query = $this->shop->export_orders();

        $csv = $this->dbutil->csv_from_result($query);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " .(string)(strlen($csv)));
        header("Content-Disposition: attachment; filename=shop-orders-".date('U').".csv");
        header("Content-Description: File Transfer");

        $this->output->set_output($csv);
    }

    public function ac_products()
    {
        $q = strtolower($_POST["q"]);
        if (!$q) {
            return;
        }

        // form dropdown
        $results = $this->shop->get_products(null, $q);

        // go foreach
        foreach ((array)$results as $row) {
            $items[$row['catalogueID']] = $row['productName'];
        }

        // output
        $output = '';
        foreach ($items as $key=>$value) {
            $output .= "$key|$value\n";
        }

        $this->output->set_output($output);
    }

    public function ac_orders()
    {
        $q = strtolower($_POST["q"]);
        if (!$q) {
            return;
        }

        // form dropdown
        $results = $this->shop->get_orders(null, null, $q);

        // go foreach
        foreach ((array)$results as $row) {
            $items[$row['transactionCode']] = trim($row['firstName'].' '.$row['lastName']);
        }

        // output
        $output = '';
        foreach ($items as $key=>$value) {
            $output .= "$key|$value\n";
        }

        $this->output->set_output($output);
    }
}
