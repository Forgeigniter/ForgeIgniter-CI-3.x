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

class Shop extends MX_Controller
{

    // set defaults
    public $includes_path = '/includes/admin';		// path to includes for header and footer
    public $permissions = array();
    public $sitePermissions = array();
    public $partials = array();

    public function __construct()
    {
        parent::__construct();

        // get permissions for the logged in admin
        if ($this->session->userdata('session_admin')) {
            $this->permission->permissions = $this->permission->get_group_permissions($this->session->userdata('groupID'));
        } else {
            // get site permissions and redirect if it don't have access to this module
            if (!$this->permission->sitePermissions) {
                show_error('You do not have permission to view this page');
            }
            if (!in_array($this->uri->segment(1), $this->permission->sitePermissions)) {
                show_error('You do not have permission to view this page');
            }
        }

        // get siteID, if available
        if (defined('SITEID')) {
            $this->siteID = SITEID;
        }

        // load libs
        $this->load->library('auth');
        $this->load->library('tags');

        // load models for this controller
        $this->load->model('shop_model', 'shop');

        // load modules
        $this->load->module('pages');

        // load partials
        if ($products = $this->shop->get_products('', '', true)) {
            // load content
            $this->partials['shop:featured'] = $this->_populate_products($products);
        }

        // get latest products
        if ($latestProducts = $this->shop->get_latest_products('', $this->site->config['headlines'])) {
            // load content
            $this->partials['shop:latest'] = $this->_populate_products($latestProducts);
        }

        // get popular products
        if ($popularProducts = $this->shop->get_popular_products($this->site->config['headlines'])) {
            // load content
            $this->partials['shop:popular'] = $this->_populate_products($popularProducts);
        }

        // get most viewed products
        if ($mostViewedProducts = $this->shop->get_most_viewed_products($this->site->config['headlines'])) {
            // load content
            $this->partials['shop:mostviewed'] = $this->_populate_products($mostViewedProducts);
        }

        // get tags
        if ($popularTags = $this->tags->get_popular_tags('shop_products')) {
            foreach ($popularTags as $tag) {
                $this->partials['shop:tags'][] = array(
                    'tag' => $tag['tag'],
                    'tag:link' => site_url('/shop/tag/'.$tag['safe_tag']),
                    'tag:count' => $tag['count']
                );
            }
        }

        // populate template
        $this->partials['rowpad:featured'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($products)); $x++) {
            $this->partials['rowpad:featured'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
        $this->partials['rowpad:latest'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($latestProducts)); $x++) {
            $this->partials['rowpad:latest'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
        $this->partials['rowpad:popular'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($popularProducts)); $x++) {
            $this->partials['rowpad:popular'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
        $this->partials['rowpad:mostviewed'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($mostViewedProducts)); $x++) {
            $this->partials['rowpad:mostviewed'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
    }

    public function index()
    {
        redirect('/shop/featured');
    }

    public function featured()
    {
        // get partials
        $output = $this->partials;

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title
        $output['page:title'] = 'Featured Products'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = 'Featured Products';

        // display with cms layer
        $this->pages->view('shop_featured', $output, true);
    }

    public function browse($cat = '', $parent = '')
    {
        // get partials
        $output = $this->partials;

        // get category
        if (is_numeric($cat)) {
            $category = $this->shop->get_category($cat);
        } else {
            $category = $this->shop->get_category_by_reference($cat, $parent);
        }

        // get products
        if ($category) {
            // set catID
            $catID = $category['catID'];

            // get paging
            if ($this->input->post('shopPaging')) {
                $this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
            }
            $limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

            // get products
            if ($products = $this->shop->get_products($catID, null, false, $limit)) {
                // load content
                $output['shop:products'] = $this->_populate_products($products);
            }

            // populate template
            $output['rowpad'] = '';
            for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($products)); $x++) {
                $output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
            }
            $output['shop:paging'] = $limit;
            $output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;

            // populate categories
            $output['category:id'] = $category['catID'];
            $output['category:title'] = $category['catName'];
            $output['category:description'] = $this->template->parse_body($category['description']);
            $output['category:link'] = ($category['parentID']) ? base_url().'shop/'.$category['parentSafe'].'/'.$category['catSafe'] : base_url().'shop/'.$category['catSafe'];
            $output['category:parent:id'] = ($category['parentID']) ? $category['parentID'] : '';
            $output['category:parent:title'] = ($category['parentID']) ? $category['parentName'] : '';
            $output['category:parent:link'] = ($category['parentID']) ? base_url().'shop/'.$category['parentSafe'] : '';

            // set pagination and breadcrumb
            $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

            // set page title as category
            $output['page:title'] = (($category['parentName']) ? $category['parentName'].' - ' : '').$category['catName'].(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
            $output['page:heading'] = (($category['parentName']) ? anchor('/shop/'.$category['parentSafe'], $category['parentName']).' &gt; ' : '').$category['catName'];

            // set meta description
            if ($category['description']) {
                $output['page:description'] = $category['description'];
            }

            // display with cms layer
            $this->pages->view('shop_browse', $output, true);
        } else {
            show_404();
        }
    }

    public function tag($tag = [])
    {
        // get partials
        $output = $this->partials;

        // get paging
        if ($this->input->post('shopPaging')) {
            $this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
        }
        $limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

        // get products
        if ($products = $this->shop->get_products_by_tag($tag, $limit)) {
            // load content
            $output['shop:products'] = $this->_populate_products($products);
        }

        // populate template
        $output['rowpad'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($products)); $x++) {
            $output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
        $output['shop:paging'] = $limit;
        $output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title as category
        $output['page:title'] = ucwords(str_replace('-', ' ', $tag)).(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = ucwords(str_replace('-', ' ', $tag));

        // display with cms layer
        $this->pages->view('shop_browse', $output, true);


        // get partials
        $output = $this->partials;
    }

    public function search()
    {
        // get partials
        $output = $this->partials;

        // set search session var
        if ($this->input->post('query')) {
            $this->session->set_userdata('shopSearch', $this->input->post('query'));
        }

        // get search
        $search = $this->session->userdata('shopSearch');

        // get paging
        if ($this->input->post('shopPaging')) {
            $this->session->set_userdata('shopPaging', $this->input->post('shopPaging'));
        }
        $limit = ($this->session->userdata('shopPaging')) ? $this->session->userdata('shopPaging') : $this->shop->siteVars['shopItemsPerPage'];

        // get products
        if ($products = $this->shop->get_products('', $search, false, $limit)) {
            // load content
            $output['shop:products'] = $this->_populate_products($products);
        }

        // populate template
        $output['rowpad'] = '';
        for ($x = 0; is_array($x) && $x < ($this->shop->siteVars['shopItemsPerRow'] - count($products)); $x++) {
            $output['rowpad'] .= '<td width="'.floor((1 / $this->shop->siteVars['shopItemsPerRow']) * 100).'%">&nbsp;</td>';
        }
        $output['shop:paging'] = $limit;
        $output['shop:total-products'] = ($products) ? $this->pagination->total_rows : 0;

        // populate categories
        $output['category:title'] = 'Search for "'.$search.'"';

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title as category
        $output['page:title'] = 'Search the Shop'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = 'Search for "'.$search.'"';

        // display with cms layer
        $this->pages->view('shop_browse', $output, true);
    }

    public function viewproduct($productID)
    {
        // get partials
        $output = $this->partials;

        // load data
        if (!$product = $this->shop->get_product($productID)) {
            show_error('No product found!');
        }

        // add a hit
        $this->shop->add_view($productID);

        // get data
        $image = $this->uploads->load_image($productID, false, true);
        $output['product:image-path'] = base_url().$image['src'];

        $image = $this->uploads->load_image($productID, true, true);
        $output['product:thumb-path'] = base_url().$image['src'];

        // get category data
        if ($categories = $this->shop->get_cat_ids_for_product($productID)) {
            // just get the first element
            $categories = array_reverse($categories);

            // filter through getting last element
            foreach ($categories as $catID) {
                $category = $this->shop->get_category($catID);

                $output['category:id'] = $category['catID'];
                $output['category:title'] = $category['catName'];
                $output['category:description'] = $this->template->parse_body($category['description']);
                $output['category:link'] = ($category['parentID']) ? base_url().'shop/'.$category['parentSafe'].'/'.$category['catSafe'] : base_url().'shop/'.$category['catSafe'];
                $output['category:parent:id'] = ($category['parentID']) ? $category['parentID'] : '';
                $output['category:parent:title'] = ($category['parentID']) ? $category['parentName'] : '';
                $output['category:parent:link'] = ($category['parentID']) ? base_url().'shop/'.$category['parentSafe'] : '';
            }
        }

        // get similar products
        if ($similar = $this->shop->get_similar_products($productID, @$category['catID'], $this->site->config['headlines'])) {
            // fill up template array
            $i = 0;
            foreach ($similar as $similarProduct) {
                // get body and excerpt
                $similarProductBody = (strlen($this->_strip_markdown($similarProduct['description'])) > 100) ? substr($this->_strip_markdown($similarProduct['description']), 0, 100).'...' : nl2br($this->_strip_markdown($similarProduct['description']));
                $similarProductExcerpt = nl2br($this->_strip_markdown($similarProduct['excerpt']));

                // get images
                if (!$similarProductImage = $this->uploads->load_image($similarProduct['productID'], false, true)) {
                    $similarProductImage['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
                }

                // get images
                if (!$similarProductThumb = $this->uploads->load_image($similarProduct['productID'], true, true)) {
                    $similarProductThumb['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
                }

                // populate template
                $output['product:similar'][$i]['similar:id'] = $similarProduct['productID'];
                $output['product:similar'][$i]['similar:link'] = site_url('shop/'.$similarProduct['productID'].'/'.strtolower(url_title($similarProduct['productName'])));
                $output['product:similar'][$i]['similar:title'] = $similarProduct['productName'];
                $output['product:similar'][$i]['similar:subtitle'] = $similarProduct['subtitle'];
                $output['product:similar'][$i]['similar:date'] = dateFmt($similarProduct['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
                $output['product:similar'][$i]['similar:body'] = $similarProductBody;
                $output['product:similar'][$i]['similar:excerpt'] = $similarProductExcerpt;
                $output['product:similar'][$i]['similar:price'] = currency_symbol().number_format($similarProduct['price'], 2);
                $output['product:similar'][$i]['similar:cell-width'] = floor((1 / $this->site->config['headlines']) * 100);
                $output['product:similar'][$i]['similar:image-path'] = base_url().$similarProductImage['src'];
                $output['product:similar'][$i]['similar:thumb-path'] = base_url().$similarProductThumb['src'];
                $output['product:similar'][$i]['similar:stock'] = $similarProduct['stock'];
                $output['product:similar'][$i]['similar:class'] = ($i % 2) ? ' alt ' : '';

                $i++;
            }
        } else {
            $output['product:similar'] = array();
        }

        // get varations data
        $data['variation1'] = $this->shop->get_variations($productID, 1);
        $data['variation2'] = $this->shop->get_variations($productID, 2);
        $data['variation3'] = $this->shop->get_variations($productID, 3);

        // populate template
        $output['product:id'] = $product['productID'];
        $output['product:link'] = base_url().'shop/'.$product['productID'].'/'.strtolower(url_title($product['productName']));
        $output['product:title'] = $product['productName'];
        $output['product:subtitle'] = $product['subtitle'];
        $output['product:body'] = $this->template->parse_body($product['description']);
        $output['product:price'] = currency_symbol().number_format($product['price'], 2);
        $output['product:excerpt'] = $this->template->parse_body($product['excerpt']);
        $output['product:stock'] = $product['stock'];
        $output['product:category'] = (isset($category) && $category) ? $category['catName'] : '';

        // get tags
        if ($product['tags']) {
            $tags = explode(',', $product['tags']);

            $i = 0;
            foreach ($tags as $tag) {
                $output['product:tags'][$i]['tag:link'] = site_url('shop/tag/'.$this->tags->make_safe_tag($tag));
                $output['product:tags'][$i]['tag'] = $tag;

                $i++;
            }
        }

        $output['form:name'] = set_value('fullName', $this->session->userdata('firstName').' '.$this->session->userdata('lastName'));
        $output['form:email'] = set_value('email', $this->session->userdata('email'));
        $output['form:review'] = $this->input->post('review');

        // get reviews
        if ($reviews = $this->shop->get_reviews($product['productID'])) {
            $i = 0;
            foreach ($reviews as $review) {
                // populate template
                $output['product:reviews'][$i]['review:class'] = ($i % 2) ? ' alt ' : '';
                $output['product:reviews'][$i]['review:id'] = $review['reviewID'];
                $output['product:reviews'][$i]['review:gravatar'] = site_url('/static/uploads/avatars/noavatar.gif');
                //Gravatar not working...
                //$output['product:reviews'][$i]['review:gravatar'] = 'http://www.gravatar.com/avatar.php?gravatar_id='.md5(trim($review['email'])).'&default='.urlencode(site_url('/static/uploads/avatars/noavatar.gif'));
                $output['product:reviews'][$i]['review:author'] = $review['fullName'];
                $output['product:reviews'][$i]['review:date'] = dateFmt($review['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
                $output['product:reviews'][$i]['review:body'] = nl2br(strip_tags($review['review']));
                $output['product:reviews'][$i]['review:rating'] = $review['rating'];

                $i++;
            }
        }

        // set status
        if ($product['status'] == 'S') {
            $output['product:status'] = '<span class="instock">In stock</span>';
        }
        if ($product['status'] == 'O' || ($this->site->config['shopStockControl'] && !$product['stock'])) {
            $output['product:status'] = '<span class="outofstock">Out of stock</span>';
            $output['product:stock'] = 0;
        }
        if ($product['status'] == 'P') {
            $output['product:status'] = '<span class="preorder">Available for pre-order</span>';
        }

        // set message
        if ($message = $this->session->flashdata('success')) {
            $output['message'] = $message;
        }

        // set title
        $output['page:title'] = $product['productName'].(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // set meta description
        if ($product['excerpt']) {
            $output['page:description'] = $product['excerpt'];
        }

        // load partials
        $output['product:variations'] = @$this->parser->parse('partials/variations', $data, true);

        // output product ID for CMS button
        $output['productID'] = $productID;

        // display with cms layer
        $this->pages->view('shop_product', $output, true);
    }

    public function cart($quantity = '', $id = '')
    {
        // get partials
        $output = $this->partials;

        // handle upsell
        if ($this->input->post('upsellID')) {
            $upsell = $this->shop->get_upsell($this->input->post('upsellID'));

            // remove products
            if ($upsell['remove']) {
                foreach ((array)$this->session->userdata('cart') as $key => $quantity) {
                    $cartProduct = $this->shop->unpack_item($key, $quantity);
                    $key = $this->core->encode($key);

                    foreach (explode(',', $upsell['productIDs']) as $removeProductID) {
                        if ($cartProduct['productID'] == $removeProductID) {
                            $this->shop->remove_from_cart($key);
                        }
                    }
                }
            }

            // add to cart
            $this->shop->add_to_cart($upsell['productID'], 1);
        }

        // cart functions (whats posted)
        else {
            if ($quantity == 'add' && $this->input->post('productID')) {
                $this->shop->add_to_cart($this->input->post('productID'), $this->input->post('quantity'));
            }
            if ($quantity == 'remove') {
                $this->shop->remove_from_cart($id);
            }
            if ($quantity == 'update') {
                foreach ((array)$this->session->userdata('cart') as $key => $quantity) {
                    $key = $this->core->encode($key);
                    $updateItem = $this->input->post('quantity');
                    $this->shop->update_cart($key, $updateItem[$key]);
                }
            }
            if ($quantity == 'remove_donation') {
                $this->session->unset_userdata('cart_donation');
            }
        }

        // find out if there is a discount code applied
        if (isset($_POST['discountCode'])) {
            $this->session->set_userdata('discountCode', $this->input->post('discountCode'));
        }

        // find out if there is a donation
        if ($donation = $this->input->post('donation')) {
            $this->session->set_userdata('cart_donation', $donation);
        }

        // get shipping bands and modifiers
        $shippingBand = $this->input->post('shippingBand');
        $shippingModifier = $this->input->post('shippingModifier');

        // set shipping bands and modifiers
        if ($shippingBand || $shippingModifier) {
            if ($shippingBand != $this->session->userdata('shippingBand')) {
                $this->session->set_userdata('shippingBand', $shippingBand);
                $this->session->unset_userdata('shippingModifier');
            } elseif ($shippingModifier != $this->session->userdata('shippingModifier')) {
                $this->session->set_userdata('shippingModifier', $shippingModifier);
            }
        } elseif (!$this->session->userdata('shippingBand')) {
            $this->session->set_userdata('shippingBand', 1);
        }

        // set shipping band notes
        if ($this->session->userdata('shippingBand') > 1 || $this->session->userdata('shippingModifier')) {
            $shippingBand = $this->shop->get_band_by_multiplier($this->session->userdata('shippingBand'));
            $shippingNotes = 'Shipping method: '.$shippingBand['bandName'];

            if ($this->session->userdata('shippingModifier')) {
                $shippingModifier = $this->shop->get_modifier_by_multiplier($this->session->userdata('shippingModifier'));
                $shippingNotes .= ' ('.$shippingModifier['modifierName'].')';
            }

            $this->session->set_userdata('shippingNotes', $shippingNotes);
        } else {
            $this->session->unset_userdata('shippingNotes');
        }

        // redirects
        if ($this->input->post('checkout')) {
            redirect('/shop/checkout');
        }

        // load cart
        $data = $this->shop->load_cart();

        // populate template
        $output['cart:discounts'] = ($data['discounts'] > 0) ? currency_symbol().number_format(@$data['discounts'], 2) : '';
        $output['cart:subtotal'] = currency_symbol().number_format(@$data['subtotal'], 2);
        $output['cart:postage'] = currency_symbol().number_format(@$data['postage'], 2);
        $output['cart:tax'] = ($data['tax'] > 0) ? currency_symbol().number_format(@$data['tax'], 2) : '';
        $output['cart:total'] = currency_symbol().number_format((@$data['subtotal'] + @$data['postage'] + @$data['tax']), 2);

        // set totals to session
        $this->session->set_userdata('cart_postage', @$data['postage']);
        $this->session->set_userdata('cart_total', @$data['subtotal']);

        // get shipping bands
        $data['shippingBand'] = ($this->input->post('shippingBand')) ? $this->input->post('shippingBand') : $this->session->userdata('shippingBand');

        // get shipping modifiers
        if ($data['bands'] = $this->shop->get_bands()) {
            // multiplier
            $multiplier = ($this->session->userdata('shippingBand')) ? $this->session->userdata('shippingBand') : 1;

            $data['shippingModifier'] = $this->session->userdata('shippingModifier');
            $data['modifiers'] = $this->shop->get_modifiers($multiplier);
        }

        // load content
        $output['cart:items'] = @$this->parser->parse('partials/cart', $data, true);
        $output['cart:bands'] = @$this->parser->parse('partials/bands', $data, true);
        $output['cart:modifiers'] = @$this->parser->parse('partials/modifiers', $data, true);
        $output['form:discount-code'] = $this->session->userdata('discountCode');
        $output['form:donation'] = ($this->session->userdata('cart_donation')) ? number_format($this->session->userdata('cart_donation'), 2, '.', '') : '';

        // set default upsell vars
        $upsell = '';

        // get upsell
        if ($this->shop->get_product_ids_in_cart() && $upsells = $this->shop->get_upsells()) {
            // filter through each upsell
            foreach ($upsells as $row) {
                $upsellArray = array();

                // get upsell based on total value
                if ($row['type'] == 'V') {
                    if ($data['subtotal'] > $row['value']) {
                        $upsell = $this->shop->get_product($row['productID']);
                        $upsell['upsellID'] = $row['upsellID'];
                    }
                }

                // get upsell based on the number of products
                elseif ($row['type'] == 'N') {
                    if (sizeof($data['cart']) > $upsell['numProducts']) {
                        $upsell = $this->shop->get_product($row['productID']);
                        $upsell['upsellID'] = $row['upsellID'];
                    }
                }

                // get upsell based on the products in cart
                elseif ($row['type'] == 'P') {
                    $upsellProducts = explode(',', $row['productIDs']);
                    foreach ($upsellProducts as $upsellProductID) {
                        if (in_array($upsellProductID, $this->shop->get_product_ids_in_cart())) {
                            $upsellArray[] = $upsellProductID;
                        }
                    }
                    if (sizeof($upsellArray) == sizeof($upsellProducts)) {
                        $upsell = $this->shop->get_product($row['productID']);
                        $upsell['upsellID'] = $row['upsellID'];
                    }
                }
            }
        }

        // load upsell
        $output['upsell:id'] = ($upsell) ? $upsell['upsellID'] : '';
        $output['upsell:product-id'] = ($upsell) ? $upsell['productID'] : '';
        $output['upsell:link'] = ($upsell) ? '/shop/'.$upsell['productID'].'/'.strtolower(url_title($upsell['productName'])) : '';
        $output['upsell:title'] = ($upsell) ? $upsell['productName'] : '';
        $output['upsell:subtitle'] = ($upsell) ? $upsell['subtitle'] : '';
        $output['upsell:body'] = ($upsell) ? $this->template->parse_body($upsell['description']) : '';
        $output['upsell:price'] = ($upsell) ? currency_symbol().number_format($upsell['price'], 2) : '';
        $output['upsell:excerpt'] = ($upsell) ? $this->template->parse_body($upsell['excerpt']) : '';
        $output['upsell:stock'] = ($upsell) ? $upsell['stock'] : '';
        $image = ($upsell) ? $this->uploads->load_image($upsell['productID'], false, true) : '';
        $output['upsell:image-path'] = ($image) ? $image['src'] : base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
        $image = ($upsell) ? $this->uploads->load_image($upsell['productID'], true, true) : '';
        $output['upsell:thumb-path'] = ($image) ? $image['src'] : base_url().$this->config->item('staticPath').'/images/nopicture.jpg';

        // get user data
        $user = $this->shop->get_user();

        // get user data
        $output['user:email'] = @$user['email'];
        $output['user:name'] = @trim($user['firstName'].' '.$user['lastName']);
        $output['user:first-name'] = @$user['firstName'];
        $output['user:last-name'] = @$user['lastName'];
        $output['user:address1'] = @$user['address1'];
        $output['user:address2'] = @$user['address2'];
        $output['user:address3'] = @$user['address3'];
        $output['user:city'] = @$user['city'];
        $output['user:state'] = @lookup_state($user['state']);
        $output['user:postcode'] = @$user['postcode'];
        $output['user:country'] = @lookup_country($user['country']);
        $output['user:country-code'] = @$user['country'];
        $output['user:phone'] = @$user['phone'];

        // get user data
        $output['user:billing-address1'] = @$user['billingAddress1'];
        $output['user:billing-address2'] = @$user['billingAddress2'];
        $output['user:billing-address3'] = @$user['billingAddress3'];
        $output['user:billing-city'] = @$user['billingCity'];
        $output['user:billing-state'] = @lookup_state($user['billingState']);
        $output['user:billing-postcode'] = @$user['billingPostcode'];
        $output['user:billing-country'] = @lookup_country($user['billingCountry']);
        $output['user:billing-country-code'] = @$user['billingCountry'];

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set page title
        $output['page:title'] = 'Shopping Cart'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_cart', $output, true);
    }

    public function checkout()
    {
        // get partials
        $output = $this->partials;

        // if the gateway is sagepay or authorize then we need to post first and then redirect
        if (count($_POST)) {
            if ($this->site->config['shopGateway'] == 'paypalpro') {
                if ($response = $this->shop->validate_paypalpro()) {
                    // send order email
                    $this->_create_order();
                    header("Location: /shop/success");
                    exit();
                } else {
                    $this->form_validation->set_error($this->shop->errors);
                }
            } elseif ($this->site->config['shopGateway'] == 'authorize') {
                if ($response = $this->shop->validate_authorize()) {
                    // send order email
                    $this->_create_order();
                    header("Location: /shop/success");
                    exit();
                } else {
                    $this->form_validation->set_error($this->shop->errors);
                }
            } elseif ($this->site->config['shopGateway'] == 'sagepay') {
                if (!$this->input->post('Amount')) {
                    $this->form_validation->set_error('No amount was specified in the form, please go back and try again or contact us.');
                } elseif ($response = $this->shop->init_sagepay()) {
                    header("Location: ".$response['NextURL']);
                    exit();
                } else {
                    $this->form_validation->set_error($this->shop->errors);
                }
            }
        }

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // get user data
        $user = $this->shop->get_user();

        // check an address is set up
        if (!@$user['address1'] || !@$user['city']) {
            $this->form_validation->set_error('No address appears to be set up yet. Please make sure you update your delivery address.');
        }

        // check a state is set up
        if (@$user['country'] == 'US' && !@$user['state']) {
            $this->form_validation->set_error('No State appears to be set for delivery address. Please make sure you update your delivery address.');
        }

        // check a zipcode is set up
        if (@$user['country'] == 'US' && !@$user['postcode']) {
            $this->form_validation->set_error('No Zipcode appears to be set for delivery address. Please make sure you update your delivery address.');
        }

        // check a state is set up
        if (@$user['billingCountry'] == 'US' && !@$user['billingState']) {
            $this->form_validation->set_error('No State appears to be set for billing address. Please make sure you update your billing address.');
        }

        // check a zipcode is set up
        if (@$user['billingCountry'] == 'US' && !@$user['billingPostcode']) {
            $this->form_validation->set_error('No Zipcode appears to be set for billing address. Please make sure you update your billing address.');
        }

        // check country is set
        if (!@$user['country']) {
            $this->form_validation->set_error('You haven\'t yet set your country. Please make sure you update your shipping address.');
        }

        if ($data = $this->shop->load_cart()) {
            // get transaction data
            $transaction = $this->shop->insert_transaction();

            // populate template
            $output['cart:discounts'] = ($data['discounts'] > 0) ? currency_symbol().number_format(@$data['discounts'], 2) : '';
            $output['cart:subtotal'] = currency_symbol().number_format($data['subtotal'], 2);
            $output['cart:postage'] = currency_symbol().number_format($data['postage'], 2);
            $output['cart:tax'] = ($data['tax'] > 0) ? currency_symbol().number_format($data['tax'], 2) : '';
            $output['cart:total'] = currency_symbol().number_format(($data['subtotal'] + $data['postage'] + $data['tax']), 2);
            $output['cart:amount'] = number_format(($data['subtotal'] + $data['postage'] + $data['tax']), 2);

            // output transaction data
            $output['transaction:id'] = $transaction['transactionID'];
            $output['transaction:order-id'] = $transaction['orderID'];
            $output['transaction:subtotal'] = $data['subtotal'];
            $output['transaction:postage'] = $data['postage'];
            $output['transaction:amount'] = ($data['subtotal'] + $data['postage'] + $data['tax']);
            $output['transaction:currency'] = $this->site->config['currency'];

            // get transaction data (for partial)
            $data['transaction'] = $transaction;
            $data['user'] = $user;
            $data['subtotal'] = $output['cart:subtotal'];
            $data['amount'] = $output['cart:amount'];
            $data['currency'] = $this->site->config['currency'];

            // get user data
            $output['user:email'] = @$user['email'];
            $output['user:name'] = @trim($user['firstName'].' '.$user['lastName']);
            $output['user:first-name'] = @$user['firstName'];
            $output['user:last-name'] = @$user['lastName'];
            $output['user:address1'] = @$user['address1'];
            $output['user:address2'] = @$user['address2'];
            $output['user:address3'] = @$user['address3'];
            $output['user:city'] = @$user['city'];
            $output['user:state'] = @lookup_state($user['state']);
            $output['user:postcode'] = @$user['postcode'];
            $output['user:country'] = @lookup_country($user['country']);
            $output['user:country-code'] = @$user['country'];
            $output['user:phone'] = @$user['phone'];

            // get user data
            $output['user:billing-address1'] = @$user['billingAddress1'];
            $output['user:billing-address2'] = @$user['billingAddress2'];
            $output['user:billing-address3'] = @$user['billingAddress3'];
            $output['user:billing-city'] = @$user['billingCity'];
            $output['user:billing-state'] = @lookup_state($user['billingState']);
            $output['user:billing-postcode'] = @$user['billingPostcode'];
            $output['user:billing-country'] = @lookup_country($user['billingCountry']);
            $output['user:billing-country-code'] = @$user['billingCountry'];

            // check there is stock for all items in cart
            if ($this->site->config['shopStockControl']) {
                // check they aren't ordering more than there is stock
                foreach ((array)$this->session->userdata('cart') as $key => $quantity) {
                    // check there is stock for all items in cart
                    if ($this->site->config['shopStockControl']) {
                        // get ordered products
                        $product = $this->shop->unpack_item($key, $quantity);
                        if ($quantity > $product['stock']) {
                            $this->form_validation->set_error('You cannot add any more of this product ("'.$product['productName'].'"). Please remove this item, or adjust the quantity.');
                        }
                    }
                }

                // get ordered products and check item hasn't gone out of stock
                $itemOrders = $this->shop->get_item_orders($transaction['transactionID']);
                foreach ($itemOrders as $order) {
                    if ($order['stock'] == 0) {
                        $this->form_validation->set_error('You have an item in your cart ("'.$order['productName'].'") that has gone out of stock during the checkout process. Please remove this item, or contact us for more information.');
                        $errors = true;
                    }
                }
            }

            // check shipping bands
            if ($bandsResult = $this->shop->get_bands()) {
                $bands = array();
                foreach ($bandsResult as $band) {
                    $bands[$band['bandID']] = $band['multiplier'];
                }

                // check there are no restricted items in there
                foreach ($data['cart'] as $item) {
                    if ($item['bandID'] > 0 && reset($bands) != $this->session->userdata('shippingBand')) {
                        $this->form_validation->set_error('You have an item in your cart ("'.$item['productName'].'") that we cannot send to your selected shipping band. Please remove this item, or contact us for more information.');
                    }
                }

                // check they are not selecting a shipping band that is not really theirs
                if ($this->site->config['siteCountry'] && $this->session->userdata('shippingBand') == reset($bands) && @$user['country'] != $this->site->config['siteCountry']) {
                    $this->form_validation->set_error('Your country and your selected shipping band do not match, please amend either your country (Update Address) or your shipping band (Update Order).');
                }
            }
        } else {
            show_error('Your cart is empty! You cannot checkout, please go back.');
        }

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // load content
        $output['cart:items'] = @$this->parser->parse('partials/cart', $data, true);
        $output['shop:checkout'] = @$this->parser->parse('partials/checkout', $data, true);

        // post to the same page if paypal pro
        if ($this->site->config['shopGateway'] == 'paypalpro') {
            $output['shop:gateway'] = site_url($this->uri->uri_string());
        }

        // set page title
        $output['page:title'] = 'Checkout'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        @$this->pages->view('shop_checkout', $output, true);
    }

    public function create_account($redirect = '')
    {
        // get partials
        $output = $this->partials;

        // set default redirect
        if (!$redirect) {
            $redirect = $this->core->encode('/shop/checkout');
        }

        // required
        $this->core->required = array(
            'email' => array('label' => 'Email address', 'rules' => 'required|valid_email|unique[users.email]|trim'),
            'password' => array('label' => 'Password', 'rules' => 'required|matches[confirmPassword]'),
            'confirmPassword' => array('label' => 'Confirm Password', 'rules' => 'required'),
            'firstName' => array('label' => 'First name', 'rules' => 'required|trim|ucfirst'),
            'lastName' => array('label' => 'Last name', 'rules' => 'required|trim|ucfirst'),
            'address1' => array('label' => 'Address1', 'rules' => 'required|trim|ucfirst'),
            'address2' => array('label' => 'Address2', 'rules' => 'trim|ucfirst'),
            'address3' => array('label' => 'Town', 'rules' => 'trim|ucfirst'),
            'city' => array('label' => 'City / State', 'rules' => 'required|trim|ucfirst'),
            'postcode' => array('label' => 'ZIP/Postcode', 'rules' => 'required|trim|strtoupper'),
            'phone' => array('label' => 'Phone', 'rules' => 'required|trim')
        );

        // security check
        if ($this->input->post('username')) {
            $this->core->set['username'] = '';
        }
        if ($this->input->post('premium')) {
            $this->core->set['premium'] = '';
        }
        if ($this->input->post('siteID')) {
            $this->core->set['siteID'] = $this->siteID;
        }
        if ($this->input->post('userID')) {
            $this->core->set['userID'] = '';
        }
        if ($this->input->post('resellerID')) {
            $this->core->set['resellerID'] = '';
        }
        if ($this->input->post('kudos')) {
            $this->core->set['kudos'] = '';
        }
        if ($this->input->post('posts')) {
            $this->core->set['posts'] = '';
        }

        // set folder (making sure it's not an admin folder)
        $permissionGroupsArray = $this->permission->get_groups('admin');
        foreach ((array)$permissionGroupsArray as $group) {
            $permissionGroups[$group['groupID']] = $group['groupName'];
        }
        if ($this->input->post('groupID') > 0 && !@in_array($this->input->post('groupID'), $permissionGroups)) {
            $this->core->set['groupID'] = $this->input->post('groupID');
        }

        // set date
        $this->core->set['dateCreated'] = date("Y-m-d H:i:s");

        // get values
        $data = $this->core->get_values('users');

        // update table
        if (count($_POST) && $this->core->update('users')) {
            // optionally subscribe user to mailing list(s)
            if (is_dir(APPPATH.'modules/emailer')) {
                // load lib
                $this->load->module('emailer');

                // check they are allowing subscription
                if ($this->input->post('subscription') != 'P' && $this->input->post('subscription') != 'N') {
                    // requires posted email, and listID
                    $this->emailer->subscribe();
                }
            }

            // set header and footer
            $emailHeader = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailHeader']);
            $emailHeader = str_replace('{first-name}', $this->input->post('firstName'), $emailHeader);
            $emailHeader = str_replace('{last-name}', $this->input->post('lastName'), $emailHeader);
            $emailHeader = str_replace('{email}', $this->input->post('email'), $emailHeader);
            $emailFooter = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailFooter']);
            $emailFooter = str_replace('{first-name}', $this->input->post('firstName'), $emailFooter);
            $emailFooter = str_replace('{last-name}', $this->input->post('lastName'), $emailFooter);
            $emailFooter = str_replace('{email}', $this->input->post('email'), $emailFooter);
            $emailAccount = str_replace('{name}', trim($this->input->post('firstName').' '.$this->input->post('lastName')), $this->site->config['emailAccount']);
            $emailAccount = str_replace('{first-name}', $this->input->post('firstName'), $emailAccount);
            $emailAccount = str_replace('{last-name}', $this->input->post('lastName'), $emailAccount);
            $emailAccount = str_replace('{email}', $this->input->post('email'), $emailAccount);

            // send email
            $this->load->library('email');
            $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
            $this->email->to($this->input->post('email'));
            $this->email->subject('New account set up on '.$this->site->config['siteName']);
            $this->email->message($emailHeader."\n\n".$emailAccount."\n\n----------------------------------\nYour email: ".$this->input->post('email')."\nYour password: ".$this->input->post('password')."\n----------------------------------\n\n".$emailFooter);
            $this->email->send();

            // set login username
            $username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));

            // set admin session name, if given
            if (!$this->auth->login($username, $this->input->post('password'), 'session_user', $this->core->decode($redirect))) {
                $this->form_validation->set_error($this->auth->error);
            }
        }

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // populate template
        $output['form:email'] = ($this->session->flashdata('email')) ? $this->session->flashdata('email') : set_value('email', $this->input->post('email'));
        $output['form:displayName'] = set_value('displayName', $this->input->post('displayName'));
        $output['form:firstName'] = set_value('firstName', $this->input->post('firstName'));
        $output['form:lastName'] = set_value('lastName', $this->input->post('lastName'));
        $output['form:phone'] = set_value('phone', $this->input->post('phone'));
        $output['form:address1'] = set_value('address1', $this->input->post('address1'));
        $output['form:address2'] = set_value('address2', $this->input->post('address2'));
        $output['form:address3'] = set_value('address3', $this->input->post('address3'));
        $output['form:city'] = set_value('city', $this->input->post('city'));
        $output['select:state'] = @display_states('state', set_value('state', $this->input->post('state')), 'id="state" class="formelement"');
        $output['form:postcode'] = set_value('postcode', $this->input->post('postcode'));
        $output['select:country'] = @display_countries('country', (($this->input->post('country')) ? $this->input->post('country') : $this->site->config['siteCountry']), 'id="country" class="formelement"');
        $output['form:billingAddress1'] = set_value('billingAddress1', $this->input->post('billingAddress1'));
        $output['form:billingAddress2'] = set_value('billingAddress2', $this->input->post('billingAddress2'));
        $output['form:billingAddress3'] = set_value('billingAddress3', $this->input->post('billingAddress3'));
        $output['form:billingCity'] = set_value('billingCity', $this->input->post('billingCity'));
        $output['select:billingState'] = @display_states('billingState', $data['billingState'], 'id="billingState" class="formelement"');
        $output['form:billingPostcode'] = set_value('billingPostcode', $this->input->post('billingPostcode'));
        $output['select:billingCountry'] = @display_countries('billingCountry', (($this->input->post('billingCountry')) ? $this->input->post('billingCountry') : $this->site->config['siteCountry']), 'id="billingCountry" class="formelement"');

        // set page title
        $output['page:title'] = 'Create Account'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        @$this->pages->view('shop_create_account', $output, true);
    }

    public function account($redirect = '')
    {
        // get partials
        $output = $this->partials;

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // set object ID
        $objectID = array('userID' => $this->session->userdata('userID'));

        // required
        $this->core->required = array(
            'email' => array('label' => 'Email', 'rules' => 'valid_email|unique[users.email]|required|trim'),
            'password' => array('label' => 'Password', 'rules' => 'matches[confirmPassword]'),
            'firstName' => array('label' => 'First name', 'rules' => 'required|trim|ucfirst'),
            'lastName' => array('label' => 'Last name', 'rules' => 'required|trim|ucfirst'),
            'address1' => array('label' => 'Address1', 'rules' => 'required|required|trim|ucfirst'),
            'address2' => array('label' => 'Address2', 'rules' => 'trim|ucfirst'),
            'address3' => array('label' => 'Address3', 'rules' => 'trim|ucfirst'),
            'city' => array('label' => 'City / State', 'rules' => 'required|trim|ucfirst'),
            'postcode' => array('label' => 'ZIP/Postcode', 'rules' => 'required|trim|strtoupper'),
            'phone' => array('label' => 'Phone', 'rules' => 'required|trim')
        );

        // get values
        $data = $this->core->get_values('users', $objectID);

        // force postcode to upper case
        $this->core->set['postcode'] = strtoupper($this->input->post('postcode'));

        // security check
        if ($this->input->post('username')) {
            $this->core->set['username'] = $data['username'];
        }
        if ($this->input->post('premium')) {
            $this->core->set['premium'] = $data['premium'];
        }
        if ($this->input->post('siteID')) {
            $this->core->set['siteID'] = $this->siteID;
        }
        if ($this->input->post('userID')) {
            $this->core->set['userID'] = $data['userID'];
        }
        if ($this->input->post('resellerID')) {
            $this->core->set['resellerID'] = $data['resellerID'];
        }
        if ($this->input->post('kudos')) {
            $this->core->set['kudos'] = $data['kudos'];
        }
        if ($this->input->post('posts')) {
            $this->core->set['posts'] = $data['posts'];
        }

        // update
        if (count($_POST) && $this->core->update('users', $objectID)) {
            // get updated row session
            $user = $this->shop->get_user();

            // remove the password field
            unset($user['password']);

            // set session data
            $this->session->set_userdata($user);

            if ($redirect) {
                redirect('/shop/'.$redirect);
            } else {
                $output['message'] = 'Your details have been updated.';
            }
        }

        // populate template
        $output['form:email'] = set_value('email', $data['email']);
        $output['form:displayName'] = set_value('displayName', $data['displayName']);
        $output['form:firstName'] = set_value('firstName', $data['firstName']);
        $output['form:lastName'] = set_value('lastName', $data['lastName']);
        $output['form:phone'] = set_value('phone', $data['phone']);
        $output['form:address1'] = set_value('address1', $data['address1']);
        $output['form:address2'] = set_value('address2', $data['address2']);
        $output['form:address3'] = set_value('address3', $data['address3']);
        $output['form:city'] = set_value('city', $data['city']);
        $output['select:state'] = @display_states('state', $data['state'], 'id="state" class="formelement"');
        $output['form:postcode'] = set_value('postcode', $data['postcode']);
        $output['select:country'] = @display_countries('country', set_value('country', $data['country']), 'id="country" class="formelement"');
        $output['form:billingAddress1'] = set_value('billingAddress1', $data['billingAddress1']);
        $output['form:billingAddress2'] = set_value('billingAddress2', $data['billingAddress2']);
        $output['form:billingAddress3'] = set_value('billingAddress3', $data['billingAddress3']);
        $output['form:billingCity'] = set_value('billingCity', $data['billingCity']);
        $output['select:billingState'] = @display_states('billingState', $data['billingState'], 'id="billingState" class="formelement"');
        $output['form:billingPostcode'] = set_value('billingPostcode', $data['billingPostcode']);
        $output['select:billingCountry'] = @display_countries('billingCountry', set_value('billingCountry', $data['billingCountry']), 'id="billingCountry" class="formelement"');

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set page title
        $output['page:title'] = 'My Account'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_account', $output, true);
    }

    public function subscriptions()
    {
        // get partials
        $output = $this->partials;

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // grab data and display
        if ($payments = $this->shop->get_sub_payments($this->session->userdata('userID'))) {
            foreach ($payments as $payment) {
                $output['payments'][] = array(
                    'payment:subscription-id' => $payment['referenceID'].((!$payment['active']) ? ' (Cancelled)' : ''),
                    'payment:date' => dateFmt($payment['paymentDate']),
                    'payment:amount' => currency_symbol(true, $payment['currency']).number_format($payment['paymentAmount'], 2),
                    'payment:link' => site_url('/shop/invoice/subscription/'.$payment['paymentID'])
                );
            }
        }

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title
        $output['page:title'] = 'My Subscriptions'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_subscriptions', $output, true);
    }

    public function orders()
    {
        // get partials
        $output = $this->partials;

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // grab data and display
        if ($orders = $this->shop->get_orders('ALL', $this->session->userdata('userID'))) {
            foreach ($orders as $order) {
                $output['orders'][] = array(
                    'order:id' => $order['transactionCode'],
                    'order:date' => dateFmt($order['dateCreated']),
                    'order:amount' => currency_symbol().number_format($order['amount'], 2),
                    'order:link' => site_url('/shop/view_order/'.$order['transactionID'])
                );
            }
        }

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title
        $output['page:title'] = 'My Orders'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_orders', $output, true);
    }

    public function view_order($transactionID)
    {
        // get partials
        $output = $this->partials;

        // check user is logged in, if not send them away from this controller
        if (!$this->session->userdata('session_user')) {
            redirect('/shop/login/'.$this->core->encode($this->uri->uri_string()));
        }

        // grab data and display
        if (!$order = $this->shop->get_order($transactionID, $this->session->userdata('userID'))) {
            show_error('Not a valid order!');
        }

        // grab data and display
        if ($item_orders = $this->shop->get_item_orders($transactionID)) {
            foreach ($item_orders as $item) {
                // check if its a file
                if ($item['fileID']) {
                    $file = $this->shop->get_file($item['fileID']);
                }

                $output['items'][] = array(
                    'item:id' => $item['productID'],
                    'item:title' => $item['productName'],
                    'item:link' => site_url('/shop/'.$item['productID'].'/'.strtolower(url_title($item['productName']))),
                    'item:details' => (
                        ($item['fileID']) ?
                        '('.anchor('/files/'.$this->core->encode($file['fileRef'].'|'.$transactionID), 'Download').')' :
                        (($item['variation1']) ? ' ('.$this->site->config['shopVariation1'].': '.$item['variation1'].')' : '').
                        (($item['variation2']) ? ' ('.$this->site->config['shopVariation2'].': '.$item['variation2'].')' : '').
                        (($item['variation3']) ? ' ('.$this->site->config['shopVariation3'].': '.$item['variation3'].')' : '')
                    ),
                    'item:quantity' => $item['quantity'],
                    'item:amount' => currency_symbol().number_format(($item['price'] * $item['quantity']), 2)
                );
            }

            // output donation if there is any
            if ($order['donation'] > 0) {
                $output['items'][sizeof($output['items'])] = array(
                    'item:id' => '',
                    'item:title' => 'Donation',
                    'item:link' => '#',
                    'item:details' => '',
                    'item:quantity' => 1,
                    'item:amount' => currency_symbol().number_format($order['donation'], 2)
                );
            }
        }

        // populate template
        $output['order:id'] = $order['transactionCode'];
        $output['order:first-name'] = ($order['firstName']) ? $order['firstName'] : '';
        $output['order:last-name'] = ($order['lastName']) ? $order['lastName'] : '';
        $output['order:address1'] = ($order['address1']) ? $order['address1'] : '';
        $output['order:address2'] = ($order['address2']) ? $order['address2'] : '';
        $output['order:address3'] = ($order['address3']) ? $order['address3'] : '';
        $output['order:city'] = ($order['city']) ? $order['city'] : '';
        $output['order:country'] = ($order['country']) ? lookup_country($order['country']) : '';
        $output['order:postcode'] = ($order['postcode']) ? $order['postcode'] : '';
        $output['order:phone'] = ($order['phone']) ? $order['phone'] : 'N/A';
        $output['order:email'] = ($order['email']) ? $order['email'] : 'N/A';
        $output['order:discounts'] = ($order['discounts'] > 0) ? currency_symbol().number_format($order['discounts'], 2) : '';
        $output['order:subtotal'] = currency_symbol().number_format($order['amount'] - $order['postage'] - $order['tax'], 2);
        $output['order:postage'] = currency_symbol().number_format($order['postage'], 2);
        $output['order:tax'] = ($order['tax'] > 0) ? currency_symbol().number_format($order['tax'], 2) : '';
        $output['order:total'] = currency_symbol().number_format($order['amount'], 2);
        $output['order:status'] = $order['trackingStatus'];
        $output['order:notes'] = ($order['notes']) ? nl2br($order['notes']) : false;

        // set pagination and breadcrumb
        $output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

        // set page title
        $output['page:title'] = 'View Order'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_view_order', $output, true);
    }

    public function login($redirect = '')
    {
        // get partials
        $output = $this->partials;

        $sessionName = 'session_user';
        $redirect = ($redirect) ? $redirect: $this->core->encode('/shop/cart');

        if (!$this->session->userdata($sessionName)) {
            // login
            if ($this->input->post('password')) {
                $username = array('field' => 'email', 'label' => 'Email address', 'value' => $this->input->post('email'));

                // set admin session name, if given
                if (!$this->auth->login($username, $this->input->post('password'), $sessionName, $this->core->decode($redirect))) {
                    $this->form_validation->set_error($this->auth->error);
                }
            }

            // look up email
            if ($email = $this->input->post('email')) {
                // if registered show login form
                if ($this->shop->lookup_user_by_email($email)) {
                    $output['registered'] = true;
                    $output['user:email'] = $email;
                }

                // else redirect to create account page
                else {
                    // set flash data for email
                    $this->session->set_flashdata('email', $email);
                    redirect('/shop/create_account');
                }
            }
        } else {
            redirect($this->core->decode($redirect));
        }

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // set page title
        $output['page:title'] = 'Login to Shop'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        if (!$this->input->post('email')) {
            // get email
            $this->pages->view('shop_prelogin', $output, true);
        } else {
            // login with email
            $this->pages->view('shop_login', $output, true);
        }
    }

    public function logout()
    {
        $this->auth->logout();
    }

    public function forgotten()
    {
        // get partials
        $output = $this->partials;

        // load email lib
        $this->load->library('email');

        // get image errors if there are any
        if (count($_POST)) {
            // check user exists and send email
            if ($user = $this->shop->get_user_by_email($this->input->post('email'))) {
                // set key
                $key = md5($user['userID'].time());
                $this->shop->set_reset_key($user['userID'], $key);

                // set header and footer
                $emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
                $emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
                $emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
                $emailHeader = str_replace('{email}', $user['email'], $emailHeader);
                $emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
                $emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
                $emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
                $emailFooter = str_replace('{email}', $user['email'], $emailFooter);

                // send email
                $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                $this->email->to($user['email']);
                $this->email->subject('Password reset request on '.$this->site->config['siteName']);
                $this->email->message($emailHeader."\n\nA password reset request has been submitted on ".$this->site->config['siteName'].". If you did not request to have your password reset please ignore this email.\n\nIf you did want to reset your password please click on the link below.\n\n".site_url('shop/reset/'.$key)."\n\n".$emailFooter);
                $this->email->send();

                $output['message'] = 'Thank you. An email was sent out with instructions on how to reset your password.';
            } else {
                $output['errors'] = '<p>There was a problem finding that email on our database, please contact support.</p>';
            }
        }

        // set title
        $output['page:title'] = 'Forgotten Password'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = 'Forgotten Password';

        // display with cms layer
        $this->pages->view('shop_forgotten', $output, 'shop');
    }

    public function reset($key = '')
    {
        // get partials
        $output = $this->partials;

        // load email lib
        $this->load->library('email');

        // required
        $this->core->required = array(
            'password' => array('label' => 'Password', 'rules' => 'required|matches[confirmPassword]'),
            'confirmPassword' => array('label' => 'Confirm Password', 'rules' => 'required'),
        );

        // check user exists and send email
        if (!$user = $this->shop->check_key($key)) {
            show_error('That key was invalid, please contact support.');
        } else {
            // set object ID
            $objectID = array('userID' => $user['userID']);

            // get values
            $data = $this->core->get_values('users', $objectID);

            if (count($_POST)) {
                // unset key
                $this->core->set['resetkey'] = '';

                // security check
                if ($this->input->post('username')) {
                    $this->core->set['username'] = $data['username'];
                }
                if ($this->input->post('premium')) {
                    $this->core->set['premium'] = $data['premium'];
                }
                if ($this->input->post('siteID')) {
                    $this->core->set['siteID'] = $this->siteID;
                }
                if ($this->input->post('userID')) {
                    $this->core->set['userID'] = $data['userID'];
                }
                if ($this->input->post('resellerID')) {
                    $this->core->set['resellerID'] = $data['resellerID'];
                }
                if ($this->input->post('kudos')) {
                    $this->core->set['kudos'] = $data['kudos'];
                }
                if ($this->input->post('posts')) {
                    $this->core->set['posts'] = $data['posts'];
                }

                // update
                if ($this->core->update('users', $objectID)) {
                    // set header and footer
                    $emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
                    $emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
                    $emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
                    $emailHeader = str_replace('{email}', $user['email'], $emailHeader);
                    $emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
                    $emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
                    $emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
                    $emailFooter = str_replace('{email}', $user['email'], $emailFooter);

                    // send email
                    $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                    $this->email->to($user['email']);
                    $this->email->subject('Your password was reset on '.$this->site->config['siteName']);
                    $this->email->message($emailHeader."\n\nYour password for ".$this->site->config['siteName']." has been reset, please keep this information safe.\n\nYour new password is: ".$this->input->post('password')."\n\n".$emailFooter);
                    $this->email->send();

                    $output['message'] = 'Thank you. Your password was reset.';
                }
            }

            // load errors
            $output['errors'] = (validation_errors()) ? validation_errors() : false;
        }


        // set title
        $output['page:title'] = 'Reset Password'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');
        $output['page:heading'] = 'Reset Password';

        // display with cms layer
        $this->pages->view('shop_reset', $output, 'shop');
    }

    public function recommend($productID)
    {
        // get partials
        $output = $this->partials;

        // make sure toUserID is set
        if (!$product = $this->shop->get_product($productID)) {
            show_error('Not a valid product!');
        }

        // required
        $this->core->required = array(
            'fullName' => array('label' => 'Full Name', 'rules' => 'required|trim|ucfirst'),
            'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
            'toName' => array('label' => 'To Name', 'rules' => 'required|trim|ucfirst'),
            'toEmail' => array('label' => 'To Email', 'rules' => 'required|valid_email')
        );

        // get values
        $output = $this->core->get_values();

        if (count($_POST)) {
            if ($this->core->check_errors()) {
                // set header and footer
                $emailHeader = str_replace('{name}', $this->input->post('fullName'), $this->site->config['emailHeader']);
                $emailHeader = str_replace('{email}', $this->input->post('email'), $emailHeader);
                $emailFooter = str_replace('{name}', $this->input->post('toName'), $this->site->config['emailFooter']);
                $emailFooter = str_replace('{email}', $this->input->post('toEmail'), $emailFooter);

                // send email
                $this->load->library('email');
                $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                $this->email->to($this->input->post('toEmail'));
                $this->email->subject('A Friend Has Recommended a Product on '.$this->site->config['siteName']);
                $this->email->message($emailHeader."\n\nA friend thinks that you might be interested in a product on ".$this->site->config['siteName'].".\n\nYou can view the product by clicking on the link below:\n\n".site_url('shop/'.$productID.'/'.strtolower(url_title($product['productName']))).(($this->input->post('messages')) ? "They sent you a message as well:\n\n".$this->input->post('message') : '')."\n\n".$emailFooter);
                $this->email->send();

                // set success message
                $this->session->set_flashdata('success', 'Thank you, your recommendation has been sent.');

                // redirect
                redirect('shop/'.$productID.'/'.strtolower(url_title($product['productName'])));
            }
        }

        // populate template
        $output['product:id'] = $product['productID'];
        $output['form:name'] = $this->input->post('fullName');
        $output['form:email'] = $this->input->post('email');
        $output['form:to-name'] = $this->input->post('toName');
        $output['form:to-email'] = $this->input->post('toEmail');
        $output['form:message'] = $this->input->post('message');

        // set title
        $output['page:title'] = 'Recommend Product'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // load content into a popup
        if ($this->core->is_ajax()) {
            // display with cms layer
            $this->pages->view('shop_recommend_popup', $output, true);
        } else {
            // display with cms layer
            $this->pages->view('shop_recommend', $output, true);
        }
    }

    public function review($productID)
    {
        // get partials
        $output = $this->partials;

        // make sure toUserID is set
        if (!$product = $this->shop->get_product($productID)) {
            show_error('Not a valid product!');
        }

        // required
        $this->core->required = array(
            'fullName' => array('label' => 'Full Name', 'rules' => 'required|trim|ucfirst'),
            'email' => array('label' => 'Email', 'rules' => 'required|valid_email'),
            'review' => 'Review'
        );

        // get values
        $output = $this->core->get_values();

        // add review
        if (count($_POST)) {
            // set date
            $this->core->set['dateCreated'] = date("Y-m-d H:i:s");
            $this->core->set['productID'] = $productID;
            $this->core->set['active'] = 0;

            // update
            if ($this->core->update('shop_reviews')) {
                // get insertID
                $reviewID = $this->db->insert_id();

                // get details on product owner
                if (!$user = $this->shop->get_user($product['userID'])) {
                    $user['email'] = $this->site->config['siteEmail'];
                }

                if ($user['notifications']) {
                    // set header and footer
                    $emailHeader = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailHeader']);
                    $emailHeader = str_replace('{first-name}', $user['firstName'], $emailHeader);
                    $emailHeader = str_replace('{last-name}', $user['lastName'], $emailHeader);
                    $emailHeader = str_replace('{email}', $user['email'], $emailHeader);
                    $emailFooter = str_replace('{name}', trim($user['firstName'].' '.$user['lastName']), $this->site->config['emailFooter']);
                    $emailFooter = str_replace('{first-name}', $user['firstName'], $emailFooter);
                    $emailFooter = str_replace('{last-name}', $user['lastName'], $emailFooter);
                    $emailFooter = str_replace('{email}', $user['email'], $emailFooter);

                    // send email
                    $this->load->library('email');
                    $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
                    $this->email->to($user['email']);
                    $this->email->subject('New Product Review on '.$this->site->config['siteName']);
                    $this->email->message($emailHeader."\n\nSomeone has just reviewed your product titled \"".$product['productName']."\".\n\nYou can view and approve this review by clicking on the following URL:\n\n".site_url('/admin/shop/reviews')."\n\nThey said:\n\"".$this->input->post('review')."\"\n\n".$emailFooter);
                    $this->email->send();
                }

                // set success message
                $this->session->set_flashdata('success', 'Thank you, your review has been submitted and is pending approval.');

                // redirect
                redirect('/shop/'.$productID.'/'.strtolower(url_title($product['productName'])));
            }
        }

        // populate template
        $output['product:id'] = $product['productID'];
        $output['form:name'] = $this->input->post('fullName');
        $output['form:email'] = $this->input->post('email');
        $output['form:review'] = $this->input->post('review');

        // set title
        $output['page:title'] = 'Review Product'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // load errors
        $output['errors'] = (validation_errors()) ? validation_errors() : false;

        // load content into a popup
        if ($this->core->is_ajax()) {
            // display with cms layer
            $this->pages->view('shop_review_popup', $output, true);
        } else {
            // display with cms layer
            $this->pages->view('shop_review', $output, true);
        }
    }

    public function cancel()
    {
        // get partials
        $output = $this->partials;

        // cancel transaction and empty cart
        $this->session->unset_userdata('cart');
        $this->session->unset_userdata('cart_ids');
        $this->session->unset_userdata('postage');
        $this->session->unset_userdata('total');

        // set page title
        $output['page:title'] = 'Cancelled'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_cancel', $output, true);
    }

    public function success($paypalstuff = '')
    {
        // get partials
        $output = $this->partials;

        // empty cart
        $this->session->unset_userdata('cart');
        $this->session->unset_userdata('cart_ids');
        $this->session->unset_userdata('postage');
        $this->session->unset_userdata('total');

        // show success page
        $output['ipn'] = $_POST;

        // set page title
        $output['page:title'] = 'Thank You'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_success', $output, true);
    }

    public function donation($paypalstuff = '')
    {
        // get partials
        $output = $this->partials;

        // set page title
        $output['page:title'] = 'Donation'.(($this->site->config['siteName']) ? ' - '.$this->site->config['siteName'] : '');

        // display with cms layer
        $this->pages->view('shop_donation', $output, true);
    }

    public function invoice($type, $id)
    {
        // get partials
        $output = $this->partials;

        // check url
        if ($type == 'subscription') {
            if ($data = $this->shop->get_sub_payment($id)) {
                if ($data['email'] == $this->session->userdata('email') || $this->session->userdata('session_admin')) {
                    // load libs etc
                    $this->load->plugin('pdf');

                    // populate data
                    $data['ref'] = 'I-'.date('Y', strtotime($data['dateCreated'])).$id;

                    // get invoice template
                    $html = $this->load->view('invoice', $data, true);

                    // create pdf
                    create_pdf($html, 'I-'.date('Y').$id);
                } else {
                    show_error('You do not have permission to view this invoice.');
                }
            } else {
                show_error('Not a valid invoice.');
            }
        } else {
            show_404();
        }
    }

    public function ipn()
    {
        // handle Paypal IPN post
        if ($this->shop->validate_ipn()) {
            if ($this->shop->validate_payment()) {
                if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD') {
                    // send order email
                    $this->_create_order();
                } elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON') {
                    // send donation email
                    $this->_donation();
                } elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'INV') {
                    // update invoice payment
                    $this->shop->pay_invoice($this->shop->response_data['transactionID']);
                }
            } elseif ($this->shop->validate_subscription()) {
                // add subscription
                $this->shop->add_subscriber();

                // send subscription email
                $this->_subscription();
            } elseif ($this->shop->validate_sub_payment()) {
                // update subscription
                $this->shop->update_subscriber();
            }
        }
    }

    public function response()
    {
        // handle RBS Worldpay post
        if ($this->site->config['shopGateway'] == 'rbsworldpay') {
            if ($this->shop->validate_rbsworldpay()) {
                if ($this->shop->validate_payment()) {
                    if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD') {
                        $this->_create_order();
                    } elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON') {
                        // send donation email
                        $this->_donation();
                    }
                } elseif ($this->shop->validate_subscription()) {
                    // ForgeIgniter ADD SITE
                    if ($this->siteID == 1 && $this->shop->response_data['desc'] == 'ForgeIgniter Premium') {
                        $this->_forge_premium();
                    }

                    // add subscription
                    $this->shop->add_subscriber();
                } elseif ($this->shop->validate_sub_payment()) {
                    // update subscription
                    $this->shop->update_subscriber();
                }
            }
        }

        // handle SagePay post
        elseif ($this->site->config['shopGateway'] == 'sagepay') {
            if ($this->shop->validate_sagepay()) {
                if ($this->shop->validate_payment()) {
                    if (substr($this->shop->response_data['orderID'], 0, 3) == 'ORD') {
                        $this->_create_order();
                        $this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/success')."\nStatusDetail=Successful\n");
                    } elseif (substr($this->shop->response_data['orderID'], 0, 3) == 'DON') {
                        $this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/donation')."\nStatusDetail=Successful\n");
                    }
                } else {
                    $this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/cancel')."\n");
                }
            } else {
                $this->output->set_output("Status=OK\nRedirectURL=".site_url('/shop/cancel')."\n");
            }
        }
    }

    public function _create_order($orderID = '')
    {
        // get order ID
        $orderID = ($orderID) ? $orderID : $this->shop->response_data['orderID'];

        // get order details
        $orderRow = $this->shop->get_order_by_order_id($orderID);
        $transactionID = $orderRow['transactionID'];

        // get ordered products
        $itemOrders = $this->shop->get_item_orders($transactionID);

        // set header and footer
        $emailHeader = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailHeader']);
        $emailHeader = str_replace('{first-name}', $orderRow['firstName'], $emailHeader);
        $emailHeader = str_replace('{last-name}', $orderRow['lastName'], $emailHeader);
        $emailHeader = str_replace('{email}', $orderRow['email'], $emailHeader);
        $emailFooter = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailFooter']);
        $emailFooter = str_replace('{first-name}', $orderRow['firstName'], $emailFooter);
        $emailFooter = str_replace('{last-name}', $orderRow['lastName'], $emailFooter);
        $emailFooter = str_replace('{email}', $orderRow['email'], $emailFooter);
        $emailOrder = str_replace('{name}', trim($orderRow['firstName'].' '.$orderRow['lastName']), $this->site->config['emailOrder']);
        $emailOrder = str_replace('{first-name}', $orderRow['firstName'], $emailOrder);
        $emailOrder = str_replace('{last-name}', $orderRow['lastName'], $emailOrder);
        $emailOrder = str_replace('{email}', $orderRow['email'], $emailOrder);

        // construct email to customer
        $userBody = $emailHeader."\n\n";
        $userBody .= $emailOrder."\n\n";
        $userBody .= "------------------------------------------\n";

        // construct email to admin
        $adminBody = "Dear administrator,\n\n";
        $adminBody .= "An order (#".$orderID.") has been placed on ".$this->site->config['siteName'].".\n\n";
        $adminBody .= "------------------------------------------\n";

        // grab order and make body
        $orderBody = "Your order:\n\n";
        $orderBody .= "Reference ID #: ".$orderID."\n\n";

        // go through each order
        $downloadBody = '';
        foreach ($itemOrders as $order) {
            // if stock control is enabled then minus the amount of stock
            if ($this->site->config['shopStockControl']) {
                $this->shop->minus_stock($order['productID'], $order['quantity']);
            }

            $variationHTML = '';

            // get variation 1
            if ($order['variation1']) {
                $variationHTML .= ' ('.$this->site->config['shopVariation1'].': '.$order['variation1'].')';
            }

            // get variations 2
            if ($order['variation2']) {
                $variationHTML .= ' ('.$this->site->config['shopVariation2'].': '.$order['variation2'].')';
            }

            // get variations 3
            if ($order['variation3']) {
                $variationHTML .= ' ('.$this->site->config['shopVariation3'].': '.$order['variation3'].')';
            }

            // check if its a file
            if ($order['fileID']) {
                $file = $this->shop->get_file($order['fileID']);
                $downloadBody .= $order['productName']."\n".site_url('/files/'.$this->core->encode($file['fileRef'].'|'.$transactionID))."\n\n";
            }

            $orderBody .= $order['quantity'] . "x | #" . $order['catalogueID'] . " | " . $order['productName'] . $variationHTML . " ";
            $orderBody .= "| ".currency_symbol(false). number_format(($order['price'] * $order['quantity']), 2)."\n";
        }

        // show tax if exists
        if ($orderRow['discounts'] > 0) {
            $orderBody .= "\nDiscounts: (".currency_symbol(false).number_format($orderRow['discounts'], 2).")";
        }

        // check for donations
        if ($orderRow['donation'] > 0) {
            $orderBody .= "\nDonation: ".currency_symbol(false).number_format($orderRow['donation'], 2);
        }

        // show subtotals
        $orderBody .= "\nSub total: ".currency_symbol(false).number_format(($orderRow['amount'] - $orderRow['postage'] - $orderRow['tax']), 2);
        $orderBody .= "\nShipping: ".currency_symbol(false).number_format($orderRow['postage'], 2);

        // show tax if exists
        if ($orderRow['tax'] > 0) {
            $orderBody .= "\nTax: ".currency_symbol(false).number_format($orderRow['tax'], 2);
        }

        // show totals
        $orderBody .= "\nTotal: ".currency_symbol(false).number_format($orderRow['amount'], 2)."\n\n";
        $orderBody .= "------------------------------------------\n\n";

        // show download links
        if (strlen($downloadBody) > 0) {
            $orderBody .= "Download Links:\n\n";
            $orderBody .= $downloadBody;
            $orderBody .= "------------------------------------------\n\n";
        }

        $dispatchBody = "Shipping Address:\n\n";
        $dispatchBody .= ($orderRow['firstName'] && $orderRow['lastName']) ? $orderRow['firstName']." ".$orderRow['lastName']."\n" : '';
        $dispatchBody .= ($orderRow['address1']) ? $orderRow['address1']."\n" : '';
        $dispatchBody .= ($orderRow['address2']) ? $orderRow['address2']."\n" : '';
        $dispatchBody .= ($orderRow['address3']) ? $orderRow['address3']."\n" : '';
        $dispatchBody .= ($orderRow['city']) ? $orderRow['city']."\n" : '';
        $dispatchBody .= ($orderRow['state']) ? lookup_state($orderRow['state'])."\n" : '';
        $dispatchBody .= ($orderRow['postcode']) ? $orderRow['postcode']."\n" : '';
        $dispatchBody .= ($orderRow['country']) ? lookup_country($orderRow['country'])."\n" : '';
        $dispatchBody .= ($orderRow['phone']) ? $orderRow['phone']."\n" : '';
        $dispatchBody .= $orderRow['email']."\n";
        $dispatchBody .= "------------------------------------------\n\n";

        // show billing address if set
        if ($orderRow['billingAddress1'] || $orderRow['billingAddress2'] || $orderRow['billingCity'] || $orderRow['billingPostcode']) {
            $dispatchBody .= "Billing Address:\n\n";
            $dispatchBody .= ($orderRow['firstName'] && $orderRow['lastName']) ? $orderRow['firstName']." ".$orderRow['lastName']."\n" : '';
            $dispatchBody .= ($orderRow['billingAddress1']) ? $orderRow['billingAddress1']."\n" : '';
            $dispatchBody .= ($orderRow['billingAddress2']) ? $orderRow['billingAddress2']."\n" : '';
            $dispatchBody .= ($orderRow['billingAddress3']) ? $orderRow['billingAddress3']."\n" : '';
            $dispatchBody .= ($orderRow['billingCity']) ? $orderRow['billingCity']."\n" : '';
            $dispatchBody .= ($orderRow['billingState']) ? lookup_state($orderRow['billingState'])."\n" : '';
            $dispatchBody .= ($orderRow['billingPostcode']) ? $orderRow['billingPostcode']."\n" : '';
            $dispatchBody .= ($orderRow['billingCountry']) ? lookup_country($orderRow['billingCountry'])."\n" : '';
            $dispatchBody .= "------------------------------------------\n\n";
        }

        // add notes
        $notesBody = ($orderRow['notes']) ? "Notes:\n\n".$orderRow['notes']."\n\n------------------------------------------\n\n" : '';

        $footerBody = $emailFooter;

        $this->shop->update_order($transactionID);

        // load email lib and email user and admin
        $this->load->library('email');

        $this->email->to($orderRow['email']);
        $this->email->subject('Thank you for your order (#'.$orderID.')');
        $this->email->message($userBody.$orderBody.$dispatchBody.$notesBody.$footerBody);
        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        $this->email->send();

        $this->email->clear();

        $this->email->to($this->site->config['siteEmail']);
        $this->email->subject('Someone has placed an order (#'.$orderID.')');
        $this->email->message($adminBody.$orderBody.$dispatchBody.$notesBody.$footerBody);
        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        $this->email->send();

        return true;
    }

    public function _donation()
    {
        $orderID = $this->shop->response_data['orderID'];

        // set header and footer
        $emailHeader = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailHeader']);
        $emailHeader = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailHeader);
        $emailHeader = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailHeader);
        $emailHeader = str_replace('{email}', $this->shop->response_data['email'], $emailHeader);
        $emailFooter = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailFooter']);
        $emailFooter = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailFooter);
        $emailFooter = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailFooter);
        $emailFooter = str_replace('{email}', $this->shop->response_data['email'], $emailFooter);
        $emailDonation = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailDonation']);
        $emailDonation = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailDonation);
        $emailDonation = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailDonation);
        $emailDonation = str_replace('{email}', $this->shop->response_data['email'], $emailDonation);

        // construct email to customer
        $userBody = $emailHeader."\n\n";
        $userBody .= $emailDonation."\n\n";
        $footerBody = $emailFooter;

        // construct email to admin
        $adminBody = "Dear administrator,\n\n";
        $adminBody .= "Someone has made a donation on ".$this->site->config['siteName'].".\n\nThe donation reference is: #".$orderID.".\n\nYou will need to log in to your payment gateway to find out how much they gave.\n\n";

        // load email lib and email user and admin
        $this->load->library('email');

        //$this->email->to($this->shop->response_data['email']);
        //$this->email->subject('Thank you for your donation');
        //$this->email->message($userBody.$footerBody);
        //$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        //$this->email->send();

        //$this->email->clear();

        $this->email->to($this->site->config['siteEmail']);
        $this->email->subject('Someone has made a donation (#'.$orderID.')');
        $this->email->message($adminBody.$footerBody);
        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        $this->email->send();

        return true;
    }

    public function _subscription()
    {
        // get order ID
        $orderID = $this->shop->response_data['orderID'];
        $email = $this->shop->response_data['email'];

        // set header and footer
        $emailHeader = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailHeader']);
        $emailHeader = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailHeader);
        $emailHeader = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailHeader);
        $emailHeader = str_replace('{email}', $this->shop->response_data['email'], $emailHeader);
        $emailFooter = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailFooter']);
        $emailFooter = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailFooter);
        $emailFooter = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailFooter);
        $emailFooter = str_replace('{email}', $this->shop->response_data['email'], $emailFooter);
        $emailSubscription = str_replace('{name}', $this->shop->response_data['fullName'], $this->site->config['emailSubscription']);
        $emailSubscription = str_replace('{first-name}', $this->shop->response_data['firstName'], $emailSubscription);
        $emailSubscription = str_replace('{last-name}', $this->shop->response_data['lastName'], $emailSubscription);
        $emailSubscription = str_replace('{email}', $this->shop->response_data['email'], $emailSubscription);

        // construct email to customer
        $userBody = $emailHeader."\n\n";
        $userBody .= $emailSubscription."\n\n";

        // construct email to admin
        $adminBody = "Dear administrator,\n\n";
        $adminBody .= "A subscription has been created on ".$this->site->config['siteName'].".\n\n";

        // grab order and make body
        $orderBody = "Your subscription reference ID #: ".$orderID."\n\n";

        // get footer
        $footerBody = $emailFooter;

        // get subscriptionID
        $subscriptionID = substr($this->shop->response_data['item_number'], (strpos($this->shop->response_data['item_number'], '-')+1));

        // get subscription
        $subscription = $this->shop->get_subscription($subscriptionID);

        // get subscription
        $plan = ($subscription) ? $subscription['plan'] : '';

        // perform action
        $this->shop->upgrade_user($this->shop->response_data['custom'], $plan);

        // load email lib and email user and admin
        $this->load->library('email');

        $this->email->to($email);
        $this->email->subject('New subscription set up on '.$this->site->config['siteName']);
        $this->email->message($userBody.$orderBody.$footerBody);
        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        $this->email->send();

        $this->email->clear();

        $this->email->to($this->site->config['siteEmail']);
        $this->email->subject('New subscription set up on '.$this->site->config['siteName']);
        $this->email->message($adminBody.$orderBody.$footerBody);
        $this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
        $this->email->send();

        return true;
    }

    public function _populate_products($products)
    {
        if ($products && is_array($products)) {
            $itemsPerRow = $this->shop->siteVars['shopItemsPerRow'];
            $i = 0;
            $x = 0;
            $t = 0;

            foreach ($products as $product) {
                // get body and excerpt
                $productBody = (strlen($this->_strip_markdown($product['description'])) > 100) ? substr($this->_strip_markdown($product['description']), 0, 100).'...' : nl2br($this->_strip_markdown($product['description']));
                $productExcerpt = nl2br($this->_strip_markdown($product['excerpt']));

                // get images
                if (!$image = $this->uploads->load_image($product['productID'], false, true)) {
                    $image['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
                }
                if (!$thumb = $this->uploads->load_image($product['productID'], true, true)) {
                    $thumb['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
                }

                // populate template array
                $data[$x] = array(
                    'product:id' => $product['productID'],
                    'product:link' => base_url().'shop/'.$product['productID'].'/'.strtolower(url_title($product['productName'])),
                    'product:title' => $product['productName'],
                    'product:subtitle' => $product['subtitle'],
                    'product:body' => $productBody,
                    'product:excerpt' => $productExcerpt,
                    'product:image-path' =>	base_url().$image['src'],
                    'product:thumb-path' => base_url().$thumb['src'],
                    'product:cell-width' => floor((1 / $itemsPerRow) * 100),
                    'product:price' => currency_symbol().number_format($product['price'], 2),
                    'product:stock' => $product['stock']
                );

                // get tags
                if ($product['tags']) {
                    $tags = explode(',', $product['tags']);

                    $t = 0;
                    foreach ($tags as $tag) {
                        $data[$x]['product:tags'][$t]['tag:link'] = site_url('shop/tag/'.$this->tags->make_safe_tag($tag));
                        $data[$x]['product:tags'][$t]['tag'] = $tag;

                        $t++;
                    }
                }

                if (($i % $itemsPerRow) == 0 && $i > 1) {
                    $data[$x]['product:rowpad'] = '</tr><tr>'."\n";
                    $i = 0;
                } else {
                    $data[$x]['product:rowpad'] = '';
                }

                $i++;
                $x++;
            }

            return $data;
        } else {
            return false;
        }
    }

    public function _strip_markdown($string)
    {
        return preg_replace('/([*\-#]+)/i', '', preg_replace('/{(.*)}/i', '', $string));
    }
}
