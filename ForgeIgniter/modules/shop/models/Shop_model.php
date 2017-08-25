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

class Shop_model extends CI_Model {

	// paypal pro config
	var $APISignature = '';
	var $APIUser = '';
	var $APIPass = '';

	// sagepay config
	var $vendor = '';

	// defaults
	var $table = '';
	var $uri_assoc_segment = 4;
	var $errors;
	var $statusArray = array();
	var $gateway_url = '';
	var $paypal_url = '';
	var $last_error = '';
	var	$response = '';
	var	$response_log_file = '';
	var	$response_log = '';
	var $response_data = array();	// array contains the POST values for IPN
	var $fields = array();		// array holds the fields to submit to paypal
	var $shopShippingTable = array();
	var $siteVars = array();

	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// get config vars
		$this->siteVars = $this->site->config;

		// get shop config
		$shippingTable = $this->get_postages();
		foreach ((array)$shippingTable as $postage)
		{
			$this->shopShippingTable[] = array($postage['total'], $postage['cost']);
		}
		$this->statusArray = array(
			'U' => 'Unprocessed',
			'L' => 'Allocated',
			'A' => 'Awaiting goods',
			'O' => 'Out of stock',
			'D' => 'Shipped',
			'N' => 'Unpaid Checkouts'
		);

		// test mode
		if (0)
		{
			$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			if ($this->site->config['shopGateway'] == 'paypal') $this->gateway_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			if ($this->site->config['shopGateway'] == 'paypalpro') $this->gateway_url = 'https://api-3t.sandbox.paypal.com/nvp';
			if ($this->site->config['shopGateway'] == 'authorize') $this->gateway_url = 'https://test.authorize.net/gateway/transact.dll';
			if ($this->site->config['shopGateway'] == 'rbsworldpay') $this->gateway_url = 'https://secure-test.wp3.rbsworldpay.com/wcc/purchase';
			if ($this->site->config['shopGateway'] == 'sagepay') $this->gateway_url = 'https://test.sagepay.com/gateway/service/vspserver-register.vsp';
		}

		// set gateway url
		else
		{
			$this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
			if ($this->site->config['shopGateway'] == 'paypal') $this->gateway_url = 'https://www.paypal.com/cgi-bin/webscr';
			if ($this->site->config['shopGateway'] == 'paypalpro') $this->gateway_url = 'https://api-3t.paypal.com/nvp';
			if ($this->site->config['shopGateway'] == 'authorize') $this->gateway_url = 'https://secure.authorize.net/gateway/transact.dll';
			if ($this->site->config['shopGateway'] == 'rbsworldpay') $this->gateway_url = 'https://secure.wp3.rbsworldpay.com/wcc/purchase';
			if ($this->site->config['shopGateway'] == 'sagepay') $this->gateway_url = 'https://live.sagepay.com/gateway/service/vspserver-register.vsp';
		}

		// get API / vendor keys
		$this->APISignature = $this->site->config['shopAPIKey'];
		$this->APIUser = $this->site->config['shopAPIUser'];
		$this->APIPass = $this->site->config['shopAPIPass'];
		$this->vendor = $this->site->config['shopVendor'];

		$this->last_error = '';
		$this->response = '';
		$this->response_log = FALSE;
		$this->response_log_file = BASEPATH . 'logs/paypal_ipn.log';
	}

	function get_categories($catID = '')
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0, 'parentID' => 0));

		$this->db->select('shop_cats.*, parentID as tempParentID, if(parentID>0, parentID+1, catID) AS parentOrder, (SELECT catName from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentName', FALSE);
		$this->db->order_by('catOrder');

		// template type
		$query = $this->db->get('shop_cats');

		if ($query->num_rows())
		{
			// get categories
			$result = $query->result_array();

			foreach ($result as $cat)
			{
				// populate array
				$categories[] = $cat;

				// get children
				if ($children = $this->get_category_children($cat['catID']))
				{
					foreach ($children as $child)
					{
						// populate array
						$categories[] = $child;
					}
				}
			}

			return $categories;
		}
		else
		{
			return FALSE;
		}
	}

	function get_category($catID = '')
	{
		// select
		$this->db->select('shop_cats.*, parentID as tempParentID, (SELECT catName from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentName, (SELECT catSafe from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentSafe', FALSE);

		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get category by ID
		$this->db->where('catID', $catID);

		$query = $this->db->get('shop_cats', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_category_by_reference($catRef = '', $parentRef = '')
	{
		// get parent
		$parent = ($parentRef) ? $this->get_category_by_reference($parentRef) : '';

		// select parent
		$this->db->select('shop_cats.*, parentID as tempParentID, (SELECT catName from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentName, (SELECT catSafe from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentSafe', FALSE);

		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// check for parent
		if ($parent)
		{
			$this->db->where('parentID', $parent['catID']);
		}
		else
		{
			$this->db->where('parentID', 0);
		}

		// get category by reference
		$this->db->where('catSafe', $catRef);

		$query = $this->db->get('shop_cats', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_category_parents()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		$this->db->where('parentID', 0);

		$this->db->order_by('catOrder', 'asc');

		$query = $this->db->get('shop_cats');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_category_children($catID = '')
	{
		// select
		$this->db->select('shop_cats.*, parentID as tempParentID, if(parentID>0, parentID+1, catID) AS parentOrder, (SELECT catName from '.$this->db->dbprefix.'shop_cats WHERE '.$this->db->dbprefix.'shop_cats.catID = tempParentID) AS parentName', FALSE);

		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get category by ID
		$this->db->where('parentID', $catID);

		$this->db->order_by('catOrder', 'asc');

		$query = $this->db->get('shop_cats');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_cats_for_product($productID)
	{
		// get cats for this product
		$this->db->join('shop_cats', 'shop_catmap.catID = shop_cats.catID', 'left');
		$this->db->order_by('catOrder');
		$query = $this->db->get_where('shop_catmap', array('productID' => $productID));

		if ($query->num_rows())
		{
			$catsArray = $query->result_array();
			$cats = array();

			foreach($catsArray as $cat)
			{
				$cats[$cat['catID']] = $cat['catName'];
			}

			return $cats;
		}
		else
		{
			return FALSE;
		}
	}

	function get_cat_ids_for_product($productID)
	{
		// get cats for this product
		$this->db->join('shop_cats', 'shop_catmap.catID = shop_cats.catID', 'left');
		$this->db->order_by('catOrder');
		$query = $this->db->get_where('shop_catmap', array('productID' => $productID));

		if ($query->num_rows())
		{
			$catsArray = $query->result_array();
			$catIDs = array();

			foreach($catsArray as $cat)
			{
				$catIDs[] = $cat['catID'];
			}

			return $catIDs;
		}
		else
		{
			return FALSE;
		}
	}

	function update_cats($productID, $catsArray = '')
	{
		// delete cats
		$this->db->delete('shop_catmap', array('productID' => $productID, 'siteID' => $this->siteID));

		if ($catsArray)
		{
			foreach($catsArray as $catID => $cat)
			{
				if ($cat)
				{
					$query = $this->db->get_where('shop_catmap', array('productID' => $productID, 'catID' => $catID, 'siteID' => $this->siteID));

					if (!$query->num_rows())
					{
						$this->db->insert('shop_catmap', array('productID' => $productID, 'catID' => $catID, 'siteID' => $this->siteID));
					}
				}
			}
		}

		return TRUE;
	}

	function get_bands()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID));

		$this->db->order_by('multiplier', 'asc');

		$query = $this->db->get('shop_bands');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_band($bandID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID));

		// get category by ID
		$this->db->where('bandID', $bandID);

		$query = $this->db->get('shop_bands', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_band_by_multiplier($multiplier = '')
	{
		// default where
		$this->db->where('siteID', $this->siteID);

		// where multiplier
		$this->db->where('multiplier', $multiplier);

		$query = $this->db->get('shop_bands', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_modifiers($multiplier = '')
	{
		// default where
		$this->db->where('shop_modifiers.siteID', $this->siteID);

		// where band
		if ($multiplier)
		{
			$this->db->where('shop_bands.multiplier', $multiplier);
		}

		// join band
		$this->db->select('bandName, shop_bands.multiplier AS bandOrder, shop_modifiers.*', FALSE);
		$this->db->join('shop_bands', 'shop_bands.bandID = shop_modifiers.bandID');

		$this->db->order_by('bandOrder', 'asc');
		$this->db->order_by('shop_modifiers.multiplier', 'asc');

		$query = $this->db->get('shop_modifiers');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_modifier($modifierID)
	{
		// default where
		$this->db->where('shop_modifiers.siteID', $this->siteID);

		// get category by ID
		$this->db->where('modifierID', $modifierID);

		// join band
		$this->db->select('bandName, shop_modifiers.*', FALSE);
		$this->db->join('shop_bands', 'shop_bands.bandID = shop_modifiers.bandID');

		$query = $this->db->get('shop_modifiers', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_modifier_by_multiplier($multiplier = '')
	{
		// default where
		$this->db->where('shop_modifiers.siteID', $this->siteID);

		// where multiplier
		$this->db->where('shop_modifiers.multiplier', $multiplier);

		// join band
		$this->db->select('bandName, shop_modifiers.*', FALSE);
		$this->db->join('shop_bands', 'shop_bands.bandID = shop_modifiers.bandID');

		$query = $this->db->get('shop_modifiers', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_postages()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID));

		$this->db->order_by('total', 'asc');

		$query = $this->db->get('shop_postages');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_postage($postageID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID));

		// get category by ID
		$this->db->where('postageID', $postageID);

		$query = $this->db->get('shop_postages', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_products($catID = '', $search = '', $featured = FALSE, $limit = '')
	{
		// set limit from uri if set
		$limit = (!$limit && $limit != 'all') ? $this->siteVars['shopItemsPerPage'] : $limit;

		// get cat IDs
		if ($catID && !$productsArray = $this->get_catmap_product_ids($catID))
		{
			return FALSE;
		}

		// start cache
		$this->db->start_cache();

		// get products
		if ($catID)
		{
			// where category
			$this->db->where_in('productID', $productsArray);
		}

		// only select products for this admin user
		if ($this->session->userdata('session_admin') && !@in_array('shop_all', $this->permission->permissions))
		{
			$this->db->where('userID', $this->session->userdata('userID'));
		}

		// get published products for admin
		if ($this->uri->segment(1) != 'admin')
		{
			$this->db->where('published', 1);
		}

		// search
		if ($search)
		{
			$this->db->where('(productName LIKE "%'.$this->db->escape_like_str($search).'%" OR subtitle LIKE "%'.$this->db->escape_like_str($search).'%" OR description LIKE "%'.$this->db->escape_like_str($search).'%" OR catalogueID LIKE "%'.$this->db->escape_like_str($search).'%")');
		}

		// featured
		if ($featured)
		{
			$this->db->where('featured', 'Y');
		}

		// set order
		$order = FALSE;
		$uriArray = $this->uri->uri_to_assoc($this->uri_assoc_segment);
		foreach($uriArray as $key => $value)
		{
			if ($value == 'price' || $value == 'productName' || $value == 'catalogueID' || $value == 'dateCreated' || $value == 'stock' || $value == 'published')
			{
				if ($key == 'orderasc')
				{
					$order = TRUE;
					$this->db->order_by($value,'asc');
				}
				elseif ($key == 'orderdesc')
				{
					$order = TRUE;
					$this->db->order_by($value,'desc');
				}
			}
		}
		if (!$order)
		{
			if ($catID || $featured)
			{
				$this->db->order_by('productOrder','asc');
			}
			$this->db->order_by('dateCreated','desc');
		}

		// default wheres
		$this->db->where(array('shop_products.siteID' => $this->siteID, 'deleted' => 0));

		// stop cache
		$this->db->stop_cache();

		// get total rows
		$query = $this->db->get('shop_products');
		$totalRows = $query->num_rows();

		// init paging
		$this->core->set_paging($totalRows, $limit);
		$query = $this->db->get('shop_products', $limit, $this->pagination->offset);

		// flush cache
		$this->db->flush_cache();

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_products_by_tag($tag = '', $limit = '')
	{
		// set limit from uri if set
		$limit = (!$limit && $limit != 'all') ? $this->siteVars['shopItemsPerPage'] : $limit;

		// get rows based on this tag
		$tags = $this->tags->fetch_rows(array(
			'table' => 'shop_products',
			'tags' => array(1, $tag),
			'limit' => $limit,
			'siteID' => $this->siteID
		));
		if (!$tags)
		{
			return FALSE;
		}

		// build tags array
		foreach ($tags as $tag)
		{
			$tagsArray[] = $tag['row_id'];
		}

		// look for products
		$this->db->where_in('productID', $tagsArray);

		// set order
		$order = FALSE;
		$uriArray = $this->uri->uri_to_assoc($this->uri_assoc_segment);
		foreach($uriArray as $key => $value)
		{
			if ($value == 'price' || $value == 'productName' || $value == 'catalogueID' || $value == 'dateCreated')
			{
				if ($key == 'orderasc')
				{
					$order = TRUE;
					$this->db->order_by($value,'asc');
				}
				elseif ($key == 'orderdesc')
				{
					$order = TRUE;
					$this->db->order_by($value,'desc');
				}
			}
		}
		if (!$order)
		{
			$this->db->order_by('productOrder','asc');
			$this->db->order_by('dateCreated','desc');
		}

		// default wheres
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0, 'published' => 1));
		$query = $this->db->get('shop_products', $limit, $this->pagination->offset);

		$output = $query->result_array();

		$this->db->where_in('productID', $tagsArray);
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0, 'published' => 1));
		$query_total = $this->db->get('shop_products');
		$config['total_rows'] = $query_total->num_rows();
		$config['per_page'] = $limit;
		$config['full_tag_open'] = '<div class="pagination"><p>';
		$config['full_tag_close'] = '</p></div>';
		$config['num_links'] = 3;
		$this->pagination->initialize($config);

		if ($query->num_rows())
		{
			return $output;
		}
		else
		{
			return FALSE;
		}
	}

	function get_catmap_product_ids($catID)
	{
		// get rows based on this category
		$this->db->join('shop_cats', 'shop_cats.catID = shop_catmap.catID');
		$this->db->where('shop_cats.catID', $catID);

		// get result
		$result = $this->db->get('shop_catmap');

		if ($result->num_rows())
		{
			$cats = $result->result_array();

			foreach ($cats as $cat)
			{
				$productsArray[] = $cat['productID'];
			}

			return $productsArray;
		}
		else
		{
			return FALSE;
		}
	}

	function get_all_products()
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);

		$this->db->order_by('productName','asc');

		$query = $this->db->get('shop_products');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_latest_products($catSafe = '', $limit = 3)
	{
		$cat = $this->get_category_by_reference($catSafe);

		$this->db->where('shop_products.siteID', $this->siteID);
		$this->db->where('shop_products.deleted', 0);
		$this->db->where('shop_products.published', 1);

		if ($catSafe)
		{
			$this->db->where('shop_catmap.catID', $cat['catID']);

			$this->db->select('shop_products.*');

			$this->db->join('shop_catmap', 'shop_catmap.productID = shop_products.productID');
			$this->db->group_by('shop_products.productID');
		}

		$this->db->order_by('shop_products.dateCreated', 'desc');

		$query = $this->db->get('shop_products', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_latest_featured_products($limit = 3)
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('deleted', 0);
		$this->db->where('featured', 'Y');
		$this->db->where('published', 1);
		$this->db->order_by('productOrder','asc');

		$query = $this->db->get('shop_products', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_popular_products($limit = 3)
	{
		// where
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('t2.siteID', $this->siteID, FALSE);
		$this->db->where('t3.siteID', $this->siteID, FALSE);
		$this->db->where('t1.deleted', 0, FALSE);
		$this->db->where('t1.published', 1, FALSE);
		$this->db->where('t3.paid', 1, FALSE);

		// select
		$this->db->select('t1.*, count(*) as numProducts', FALSE);

		$this->db->from('shop_products t1');
		$this->db->limit($limit);

		// join
		$this->db->join('shop_orders t2', 't1.productID = t2.productID');
		$this->db->join('shop_transactions t3', 't2.transactionID = t3.transactionID');


		$this->db->group_by('productID');
		$this->db->order_by('numProducts', 'desc');

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

	function get_most_viewed_products($limit = 3)
	{
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0, 'published' => 1));
		$this->db->order_by('views', 'desc');

		$query = $this->db->get('shop_products', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_similar_products($productID = '', $catID = '', $limit)
	{
		if (!$catID || !$productID)
		{
			return FALSE;
		}

		$this->db->where('shop_products.siteID', $this->siteID);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('shop_products.productID !=', $productID);

		$this->db->where('shop_catmap.catID', $catID);
		$this->db->join('shop_catmap', 'shop_catmap.productID = shop_products.productID');
		$this->db->group_by('shop_products.productID');

		$this->db->order_by('dateCreated', 'random');

		$query = $this->db->get('shop_products', $limit);

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_product($productID)
	{
		if ($productID)
		{
			$this->db->where(array('shop_products.siteID' => $this->siteID, 'deleted' => 0));

			$this->db->where('shop_products.productID', $productID);

			// join and group
			$this->db->select('shop_products.*');
			$this->db->join('shop_catmap', 'shop_catmap.productID = shop_products.productID', 'left');
			$this->db->group_by('shop_products.productID');

			$query = $this->db->get('shop_products', 1);

			$output = $query->row_array();

			return $output;
		}
		else
		{
			return FALSE;
		}
	}

	function get_discounts($code)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'code' => $code, 'expiryDate >' => date("Y-m-d H:i:s")));

		$query = $this->db->get('shop_discounts', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_discount($discountID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID));

		// get category by ID
		$this->db->where('discountID', $discountID);

		$query = $this->db->get('shop_discounts', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_reviews($productID = '')
	{
		// get reviews based on a post
		if ($productID)
		{
			$this->db->where('t1.productID', $productID, FALSE);
			$this->db->where('t1.active', 1, FALSE);
		}

		// Select
		$this->db->select('t1.*, t2.productName');

		$this->db->from('shop_reviews t1');
		$this->db->limit(50);

		$this->db->where('t1.deleted', 0, FALSE);
		$this->db->where('t1.siteID', $this->siteID, FALSE);

    // join
		$this->db->join('shop_products t2', 't2.productID = t1.productID');

		$this->db->order_by('t1.dateCreated', 'desc');

    // get em
		$query = $this->db->get();

		$reviews = array();

		if ( $query->num_rows() > 0)
		{
			$reviews = $query->result_array();
		}

		return $reviews;
	}

	function get_files()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		$query = $this->db->get('files');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_file($fileID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		$this->db->where('fileID', $fileID);

		$query = $this->db->get('files', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_upsells()
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		$this->db->order_by('upsellOrder', 'desc');

		$query = $this->db->get('shop_upsells');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_upsell($upsellID)
	{
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// get upsell by ID
		$this->db->where('upsellID', $upsellID);

		$this->db->order_by('upsellOrder', 'asc');

		$query = $this->db->get('shop_upsells', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function captcha()
	{
 		$this->load->plugin('captcha');

		// load captcha
		$syls = array('ble', 'ond', 'san', 'tle', 'ile', 'bre', 'aps', 'que', 'yil', 'ste', 'tre', 'ale', 'sho', 'spi', 'dal', 'clo', 'fal', 'gul', 'she');
		$randSyls = array_rand($syls, 2);
		$randomWord = '';
		foreach ($randSyls as $x)
		{
			$randomWord .= $syls[$x];
		}

		$vals = array(
					'word'		 => $randomWord,
					'img_path'	 => './static/uploads/captcha/',
					'img_url'	 => '/static/uploads/captcha/',
					'img_width'	 => '100',
					'img_height' => 30,
					'expiration' => 7200
				);
		$cap = create_captcha($vals);

		$data = array(
					'captcha_id' => '',
					'captcha_time' => $cap['time'],
					'ip_address'  => $this->input->ip_address(),
					'word' => $cap['word']
				);

		$query = $this->db->insert_string('captcha', $data);
		$this->db->query($query);

		return $cap;
	}

	function add_view($productID)
	{
		$this->db->set('views', 'views+1', FALSE);
		$this->db->where('productID', $productID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('shop_products');
	}

	function get_variations($productID, $type = '')
	{
		$this->db->where(array('productID' => $productID));
		$this->db->order_by('variationID');

		if ($type)
		{
			$this->db->where(array('type' => $type));
		}
		$query = $this->db->get('shop_variations');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_variation($variationID)
	{
		$query = $this->db->get_where('shop_variations', array('variationID' => $variationID));

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_items($ids)
	{
		$this->db->where_in('productID', $ids);
		$query = $this->db->get('shop_products');

		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function unpack_item($key, $quantity)
	{
		// get the master array (unserialize) then get the info from db
		$keys = unserialize($key);
		$product = $this->get_product($keys['productID']);
		$variation1 = @$this->get_variation($keys['variation1']);
		$variation2 = @$this->get_variation($keys['variation2']);
		$variation3 = @$this->get_variation($keys['variation3']);

		// create new cart array, based on serial
		$item = @array('productID' => $keys['productID'], 'catID' => $product['catID'], 'catalogueID' => $product['catalogueID'], 'productName' => $product['productName'], 'price' => $product['price'], 'quantity' => $quantity, 'variation1' => $variation1['variation'], 'variation1Price' => $variation1['price'], 'variation2' => $variation2['variation'], 'variation2Price' => $variation2['price'], 'variation3' => $variation3['variation'], 'variation3Price' => $variation3['price'], 'fileID' => $product['fileID'], 'bandID' => $product['bandID'], 'freePostage' => $product['freePostage'], 'stock' => $product['stock']);

		// add variation1 price modifier
		if ($variation1['price'])
		{
			$item['price'] += $variation1['price'];
		}

		// add variation2 price modifier
		if ($variation2['price'])
		{
			$item['price'] += $variation2['price'];
		}

		// add variation2 price modifier
		if ($variation3['price'])
		{
			$item['price'] += $variation3['price'];
		}

		return $item;
	}

	function load_cart()
	{
		// get session data for cart
		$cartSession = $this->session->userdata('cart');

		// get shipping modifiers
		if ($this->session->userdata('shippingModifier'))
		{
			// get shipping band
			$multiplier = $this->session->userdata('shippingModifier');
		}
		else
		{
			// get shipping band
			$multiplier = ($this->session->userdata('shippingBand')) ? $this->session->userdata('shippingBand'): 1;
		}

		// load cart
		if ($cartSession)
		{
			$subtotal = 0;
			$postageSubtotal = 0;
			$discounts = 0;
			$donation = 0;
			$tax = 0;
			$total = 0;

			// find out if there are any discount codes applies
			if ($this->session->userdata('discountCode'))
			{
				$discount = $this->get_discounts($this->session->userdata('discountCode'));
			}

			// create new unserialized array
			foreach ($cartSession as $key => $quantity)
			{
				// get product info
				$cart[$key] = $this->unpack_item($key, $quantity);

				// get price
				$productPrice = $cart[$key]['price'];

				// calculate subtotal separately for postage
				if (!$cart[$key]['fileID'] && !$cart[$key]['freePostage'])
				{
					$postageSubtotal += ($productPrice * $cart[$key]['quantity']);
				}

				// check the quantity is 1 for files
				elseif ($cart[$key]['fileID'])
				{
					$cart[$key]['quantity'] = 1;
				}

				// calculate discounts
				if (isset($discount))
				{
					// calculate on product
					if ($discount['type'] == 'P')
					{
						$objectArray = explode(',', $discount['objectID']);
						if (@in_array($cart[$key]['productID'], $objectArray))
						{
							$productDiscount = ($discount['modifier'] == 'A') ? $discount['discount'] : round($productPrice * $discount['discount'] / 100, 2);

							// find out if the discount is coming off the total price or at a product level
							if ($discount['base'] == 'P')
							{
								$productPrice -= $productDiscount;

								// set total discounted amount
								$discounts += ($productDiscount * $cart[$key]['quantity']);
							}
							else
							{
								$discounts = $productDiscount;
							}
						}
					}

					// calculate on category ID
					if ($discount['type'] == 'C' && $discount['objectID'] == $cart[$key]['catID'])
					{
						$productDiscount = ($discount['modifier'] == 'A') ? $discount['discount'] : round($productPrice * $discount['discount'] / 100, 2);

						// find out if the discount is coming off the total price or at a product level
						if ($discount['base'] == 'P')
						{
							$productPrice -= $productDiscount;

							// set total discounted amount
							$discounts += ($productDiscount * $cart[$key]['quantity']);
						}
						else
						{
							$discounts = $productDiscount;
						}
					}
				}

				// add sub total
				$subtotal += ($productPrice * $cart[$key]['quantity']);
			}

			// make sure the postage sub total has not gone in to minus
			if ($postageSubtotal < 0) $postageSubtotal = 0;

			// calculate postage and packing
			if ($this->siteVars['shopFreePostage'])
			{
				// free postage
				$postage = 0;
			}
			else
			{
				// if the postage sub total is nothing, then charge nothing for postage
				if ($postageSubtotal == 0)
				{
					$postage = 0;
				}

				// free postage over a certain rate (for shipping bands 1 only)
				elseif ($this->siteVars['shopFreePostageRate'] && $multiplier == 1 && $postageSubtotal >= $this->siteVars['shopFreePostageRate'])
				{
					$postage = 0;
				}

				// last postage rate
				elseif ($postageSubtotal >= $this->shopShippingTable[(sizeof($this->shopShippingTable)-1)][0])
				{
					$postage = $this->shopShippingTable[(sizeof($this->shopShippingTable)-1)][1];
				}

				// postage based on table
				else
				{
					for ($x=0; $x<sizeof($this->shopShippingTable); $x++)
					{
						if (($postageSubtotal >= $this->shopShippingTable[$x][0]) && ($postageSubtotal < $this->shopShippingTable[$x+1][0]))
						{
							$postage = $this->shopShippingTable[$x][1];
						}
					}
				}

				// calculate postage times the shipping band multiplier
				if (@!$postage) $postage = 0;
				$postage = $postage * $multiplier;
			}

			// find out if there is a donation (adding it after the postage)
			if ($donation = $this->session->userdata('cart_donation'))
			{
				$subtotal += $donation;
			}

			// get tax
			if ($this->siteVars['shopTax'] > 0)
			{
				if ($this->siteVars['shopTax'] == 2)
				{
					$tax = ($this->session->userdata('state') == $this->siteVars['shopTaxState']) ? round(($subtotal+$postage) * $this->siteVars['shopTaxRate'] / 100, 2) : 0;
				}
				else
				{
					$tax = round(($subtotal+$postage) * $this->siteVars['shopTaxRate'] / 100, 2);
				}
			}

			// calculate discount on total
			if (isset($discount) && $discount['base'] == 'T')
			{
				// set total discounted amount
				$discounts = ($discount['modifier'] == 'A') ? $discount['discount'] : round($subtotal * $discount['discount'] / 100, 2);

				// calculate new sub total
				$subtotal -= ($discount['modifier'] == 'A') ? $discount['discount'] : round($subtotal * $discount['discount'] / 100, 2);
			}

			// return data
			$data['cart'] = $cart;
			$data['multiplier'] = $multiplier;
			$data['discounts'] = $discounts;
			$data['subtotal'] = $subtotal;
			$data['postage'] = $postage;
			$data['donation'] = $donation;
			$data['tax'] = $tax;

			return $data;
		}
		else
		{
			return FALSE;
		}
	}

	function get_product_ids_in_cart()
	{
		// get session data for cart
		$cartSession = $this->session->userdata('cart');

		// load cart
		if ($cartSession)
		{
			// set product IDs array
			$productIDs = array();

			// create new unserialized array
			foreach ($cartSession as $key => $quantity)
			{
				// get product info
				$cart[$key] = $this->unpack_item($key, $quantity);

				// make sure quantity is not greater than 1
				if ($quantity == 1)
				{
					$productIDs[] = $cart[$key]['productID'];
				}
			}

			return $productIDs;
		}
		else
		{
			return FALSE;
		}
	}

	function add_to_cart($productID, $quantity = 1, $variation1 = '', $variation2 = '', $variation3 = '')
	{
		if ($quantity < 1)
		{
			$quantity = 1;
		}

		if ($productID)
		{
			$cart = $this->session->userdata('cart');

			$variation1 = ($variation1) ? $variation1 : $this->input->post('variation1');
			$variation2 = ($variation2) ? $variation2 : $this->input->post('variation2');
			$variation3 = ($variation3) ? $variation3 : $this->input->post('variation3');

			$key = serialize(array('productID' => $productID, 'variation1' => $variation1, 'variation2' => $variation2, 'variation3' => $variation3));

			if (isset($cart[$key]))
			{
				$cart[$key] += $quantity;
				$this->session->set_userdata('cart', $cart);
			}
			else
			{
				$cart[$key] = $quantity;
				$this->session->set_userdata('cart', $cart);
			}

			return true;
		}
		else
		{
			return FALSE;
		}
	}

	function remove_from_cart($key)
	{
		$cart = $this->session->userdata('cart');

		$key = $this->core->decode($key);
		unset($cart[$key]);

		$this->session->set_userdata('cart', $cart);

		return true;
	}

	function update_cart($key, $quantity)
	{
		if ($quantity)
		{
			$cart = $this->session->userdata('cart');
			$key = $this->core->decode($key);
			$cart[$key] = $quantity;
			$this->session->set_userdata('cart', $cart);
		}
		return true;
	}

	function get_user()
	{
		if ($userID = $this->session->userdata('userID'))
		{
			$query = $this->db->get_where('users', array('userID' => $userID), 1);

			$output = $query->row_array();

			return $output;
		}
		else
		{
			return FALSE;
		}
	}

	function get_user_by_email($email)
	{
		// default wheres
		$this->db->where('siteID', $this->siteID);

		$this->db->where('email', $email);

		// grab
		$query = $this->db->get('users', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function insert_transaction()
	{
		$cartSession = $this->session->userdata('cart');
		$cart = $this->load_cart();

		$set = array(
			'dateCreated' => date("Y-m-d H:i:s"),
			'userID' => $this->session->userdata('userID'),
			'amount' => number_format(($cart['subtotal'] + $cart['postage'] + $cart['tax']), 2, '.', ''),
			'postage' => number_format($cart['postage'], 2, '.', ''),
			'discounts' => number_format($cart['discounts'], 2, '.', ''),
			'donation' => number_format($cart['donation'], 2, '.', ''),
			'tax' => number_format($cart['tax'], 2, '.', ''),
			'discountCode' => ($this->session->userdata('discountCode')) ? $this->session->userdata('discountCode') : '',
			'notes' => ($this->session->userdata('shippingNotes')) ? $this->session->userdata('shippingNotes') : ''
		);

		$this->db->set($set);
		$this->db->insert('shop_transactions');

		// Get the ID number of the new record
		$transactionID = $this->db->insert_id();

		// Set the tx code based on the id, and update the db
		$transactionCode = 'ORD'.rand(100,999).$transactionID;
		$this->db->set('transactionCode', $transactionCode);
		$this->db->set('siteID', $this->siteID);
		$this->db->where('transactionID', $transactionID);
		$this->db->update('shop_transactions');

		// insert products ordered
		foreach($cartSession as $key => $quantity)
		{
			$keys = unserialize($key);

			$set = array(
				'`dateCreated`' => date("Y-m-d H:i:s"),
				'`transactionID`' => $transactionID,
				'`productID`' => $keys['productID'],
				'`quantity`' => $quantity,
				'`key`' => $key,
				'`siteID`' => $this->siteID
			);
			$this->db->set($set);
			$this->db->insert('shop_orders');
		}

		$data = array('orderID' => $transactionCode, 'transactionID' => $transactionID);

		return $data;
	}

	function add_variation($productID, $type, $variation = '', $varPrice = 0)
	{
		$this->db->set('productID', $productID);
		$this->db->set('type', $type);
		$this->db->set('siteID', $this->siteID);

		if ($variation)
		{
			$this->db->set('variation', $variation);
			if ($varPrice)
			{
				$this->db->set('price', $varPrice);
			}
			$this->db->insert('shop_variations');

			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}

	function clear_variations($productID = '')
	{
		if ($productID)
		{
			$this->db->where('productID', $productID);
			$this->db->delete('shop_variations');

			return true;
		}
		else
		{
			return FALSE;
		}
	}

	function get_orders($status = '', $userID = '', $search = '')
	{
		$this->db->select('shop_transactions.*, COUNT(orderID) as numItems, users.firstName, users.lastName', FALSE);
		$this->db->join('users', 'shop_transactions.userID = users.userID', 'left');
		$this->db->join('shop_orders', 'shop_transactions.transactionID = shop_orders.transactionID', 'left');
		$this->db->group_by('shop_transactions.transactionID');

		// select based on requested status
		if ($status != 'ALL' && $status != 'N')
		{
			if (array_key_exists($status, $this->statusArray))
			{
				$this->db->where('trackingStatus', $status);
			}
			else
			{
				$this->db->where('trackingStatus', 'U');
			}
		}

		// optionally select unpaid checkouts
		if ($status == 'N' && !$search)
		{
			$this->db->where('trackingStatus', 'U');
			$this->db->where('paid', 0);
			$this->db->limit($this->siteVars['paging']);
		}
		elseif (!$search)
		{
			$this->db->where('paid', 1);
		}

		$this->db->where('shop_transactions.siteID', $this->siteID);

		// search
		if ($search)
		{
			$this->db->where('(firstName LIKE "%'.$this->db->escape_like_str($search).'%" OR lastName LIKE "%'.$this->db->escape_like_str($search).'%" OR transactionCode LIKE "%'.$this->db->escape_like_str($search).'%")');
		}

		// get by user
		if ($userID)
		{
			$this->db->where('shop_transactions.userID', $userID);
		}

		// order (if set)
		$uriArray = $this->uri->uri_to_assoc($this->uri_assoc_segment);
		foreach($uriArray as $key => $value)
		{
			if ($key == 'orderasc')
			{
				$this->db->orderby($value,'asc');
			}
			elseif ($key == 'orderdesc')
			{
				$this->db->orderby($value,'desc');
			}
		}
		$this->db->order_by('dateCreated', 'desc');

		$query = $this->db->get('shop_transactions');
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_order($transactionID, $userID = '')
	{
		$this->db->select('shop_transactions.*, users.*, shop_transactions.dateCreated AS dateCreated', FALSE);
		$this->db->join('users', 'shop_transactions.userID = users.userID', 'left', FALSE);
		$this->db->where('shop_transactions.transactionID', $transactionID);
		$this->db->where('shop_transactions.siteID', $this->siteID);

		if ($userID)
		{
			$this->db->where('shop_transactions.userID', $userID);
		}

		$query = $this->db->get('shop_transactions', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_order_by_order_id($orderID)
	{
		$this->db->join('users', 'shop_transactions.userID = users.userID', 'left', FALSE);
		$this->db->where('shop_transactions.transactionCode', $orderID);
		$this->db->where('shop_transactions.siteID', $this->siteID);

		$query = $this->db->get('shop_transactions', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_item_orders($transactionID)
	{
		$this->db->where('shop_orders.transactionID', $transactionID);

		$query = $this->db->get('shop_orders');

		if ($query->num_rows())
		{
			$result = $query->result_array();

			// create new unserialized array
			foreach ($result as $row)
			{
				$cart[$row['key']] = $this->unpack_item($row['key'], $row['quantity']);
			}

			return $cart;
		}
		else
		{
			return FALSE;
		}
	}

	function get_new_orders()
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('viewed', 0);
		$this->db->where('paid', 1);
		$this->db->where('dateCreated >', date("Y-m-d H:i:s", strtotime('-3 days')));

		$query = $this->db->get('shop_transactions');

		if ($query->num_rows())
		{
			$result = $query->result_array();

			return $result;
		}
		else
		{
			return FALSE;
		}
	}

	function get_subscription($subscriptionID)
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('subscriptionID', $subscriptionID);

		$query = $this->db->get('subscriptions', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_subscription_by_ref($subscriptionRef)
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('subscriptionRef', $subscriptionRef);

		$query = $this->db->get('subscriptions', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_subscribers($q = '')
	{
		$this->db->where('siteID', $this->siteID);

		// search
		if ($q)
		{
			$this->db->where('(email LIKE "%'.$this->db->escape_like_str($q).'%" OR fullName LIKE "%'.$this->db->escape_like_str($q).'%" OR referenceID LIKE "%'.$this->db->escape_like_str($q).'%")');
		}

		$query = $this->db->get('subscribers');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_num_subscribers($subscriptionID)
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('subscriptionID', $subscriptionID);
		$this->db->where('active', 1);

		$query = $this->db->get('subscribers');

		if ($query->num_rows() > 0)
		{
			return $query->num_rows();
		}
		else
		{
			return 0;
		}
	}

	function get_subscriber($subscriberID)
	{
		$this->db->where('siteID', $this->siteID);
		$this->db->where('subscriberID', $subscriberID);

		$query = $this->db->get('subscribers', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_sub_payments($userID)
	{
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('userID', $userID);

		$this->db->select('t2.*, paymentID, t1.dateCreated as paymentDate, t1.amount as paymentAmount, t3.currency as currency', FALSE);
		$this->db->join('subscribers t2', 't2.referenceID = t1 . referenceID');
		$this->db->join('subscriptions t3', 't3.subscriptionID = t2 . subscriptionID');

		$query = $this->db->get('sub_payments t1');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	function get_sub_payment($paymentID)
	{
		$this->db->where('t1.siteID', $this->siteID, FALSE);
		$this->db->where('paymentID', $paymentID);

		$this->db->select('t2.*, t1.dateCreated as paymentDate, t1.amount as paymentAmount, t3.currency as currency', FALSE);
		$this->db->join('subscribers t2', 't2.referenceID = t1 . referenceID');
		$this->db->join('subscriptions t3', 't3.subscriptionID = t2 . subscriptionID');

		$query = $this->db->get('sub_payments t1', 1);

		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}

	function minus_stock($productID, $quantity)
	{
		$this->db->set('stock', 'stock-'.$quantity, FALSE);
		$this->db->where('productID', $productID);
		$this->db->where('siteID', $this->siteID);
		$this->db->update('shop_products');

		$product = $this->get_product($productID);
		if ($product['stock'] == 0)
		{
			$this->db->set('status', 'O');
			$this->db->where('productID', $productID);
			$this->db->where('siteID', $this->siteID);
			$this->db->update('shop_products');
		}

		return TRUE;
	}

	function view_order($transactionID)
	{
		$this->db->set('viewed', '1');
		$this->db->where('transactionID', $transactionID);
		$this->db->update('shop_transactions');

		return TRUE;
	}

	function update_order($transactionID)
	{
		$this->db->set('paid', 1);
		$this->db->set('expiryDate', date("Y-m-d H:i:s", strtotime('+7 days')));
		$this->db->where('transactionID', $transactionID);
		$this->db->update('shop_transactions');

		return TRUE;
	}

	function init_sagepay()
	{
		// parse the paypal URL
		$url_parsed = parse_url($this->gateway_url);

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = '';
		if ($_POST)
		{
			foreach ($_POST as $field=>$value)
			{
				$this->response_data[$field] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&';
			}
		}
		$post_string.="vendor=".$this->vendor."&VPSProtocol=2.23"; // append ipn command

		set_time_limit(60);

		// Initialise output variable
		$output = array();

		// Open the cURL session
		$curlSession = curl_init();

		// Set the URL
		curl_setopt ($curlSession, CURLOPT_URL, $this->gateway_url);
		// No headers, please
		curl_setopt ($curlSession, CURLOPT_HEADER, 0);
		// It's a POST request
		curl_setopt ($curlSession, CURLOPT_POST, 1);
		// Set the fields for the POST
		curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $post_string);
		// Return it direct, don't print it out
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
		// This connection will timeout in 30 seconds
		curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
		//The next two lines must be present for the kit to work with newer version of cURL
		//You should remove them if you have any problems in earlier versions of cURL
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

		//Send the request and store the result in an array

		$rawresponse = curl_exec($curlSession);
		//Store the raw response for later as it's useful to see for integration and understanding
		$_SESSION["rawresponse"]=$rawresponse;
		//Split response into name=value pairs
		$response = preg_split('/\n/', $rawresponse);
		// Check that a connection was made
		if (curl_error($curlSession)){
			// If it wasn't...
			$output['Status'] = "FAIL";
			$output['StatusDetail'] = curl_error($curlSession);
		}

		// Close the cURL session
		curl_close ($curlSession);

		// Tokenise the response
		for ($i=0; $i<count($response); $i++)
		{
			// Find position of first "=" character
			$splitAt = strpos($response[$i], "=");
			// Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
			$output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
		}

		// get first line of status
		$baseStatus = array_shift(preg_split("/ /",$output["Status"]));

		// validate post
		if ($baseStatus == 'OK')
		{
			return $output;
		}
		else
		{
			$this->errors = $output['StatusDetail'];
			return FALSE;
		}
	}

	function validate_ipn()
	{
		// parse the paypal URL
		$url_parsed = parse_url($this->paypal_url);

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = 'cmd=_notify-validate&';
		if ($_POST)
		{
			foreach ($_POST as $field => $value)
			{
				// tidy value
				$value = trim(str_replace("\n", "", $value));
				$value = trim(str_replace("\t", "", $value));

				// build string
				$this->response_data[$field] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&';
			}
		}
		$post_string = substr($post_string, 0, -1); // remove trailing &

		// open the connection to paypal
		$fp = fsockopen($url_parsed['host'],"80",$err_num,$err_str,30);
		if(!$fp)
		{
			// could not open the connection.  If loggin is on, the error message
			// will be in the log.
			$this->last_error = "fsockopen error no. $errnum: $errstr";
			$this->log_response_results(FALSE);

			return FALSE;
		}
		else
		{
			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
			fputs($fp, "Host: $url_parsed[host]\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $post_string . "\r\n\r\n");

			// loop through the response from the server and append to variable
			while(!feof($fp))
				$this->response .= fgets($fp, 1024);

			fclose($fp); // close connection
		}

		if (eregi("VERIFIED",$this->response))
		{
			// check for cancellation
			if ($this->response_date['txn_type'] == 'subscr_eot')
			{
				// cancel subscriber
				$this->cancel_subscriber();

				// load email lib and email admin
				$this->load->library('email');
				$this->email->to($this->site->config['siteEmail']);
				$this->email->subject('A Subscription Expired on '.$this->site->config['siteName']);
				$this->email->message("Dear Administrator,\n\nA user's subscription has expired on ".$this->site->config['siteName'].".\n\nTheir reference ID is:\t#".$this->response_data['subscr_id']."\n\n".$this->site->config['siteURL']);
				$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
				$this->email->send();

				return FALSE;
			}
			else
			{
				// Valid IPN transaction.
				$this->log_response_results(true);

				return TRUE;
			}
		}
		else
		{
			// Invalid IPN transaction.  Check the log for details.
			$this->last_error = 'IPN Validation Failed.';
			$this->log_response_results(FALSE);

			return FALSE;
		}
	}

	function validate_paypalpro()
	{
		// parse the paypal URL
		$url_parsed = parse_url($this->gateway_url);

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = '';
		if ($_POST)
		{
			$expMonth = '';
			$expYear = '';
			foreach ($_POST as $field => $value)
			{
				// tidy value
				$value = trim(str_replace("\n", "", $value));
				$value = trim(str_replace("\t", "", $value));

				// build expiry dates
				if ($field == 'expMonth')
				{
					$expMonth = $value;
				}
				if ($field == 'expYear')
				{
					$expYear = $value;
				}

				// build string
				$this->response_data[$field] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&';
			}
			$post_string.='EXPDATE='.$expMonth.$expYear.'&';
		}
		$post_string.="VERSION=56.0&SIGNATURE=".$this->APISignature."&USER=".$this->APIUser."&PWD=".$this->APIPass."&METHOD=DoDirectPayment&PAYMENTACTION=Sale&IPADDRESS=".$this->input->ip_address(); // append ipn command

		set_time_limit(60);

		// Initialise output variable
		$output = array();

		// Open the cURL session
		$curlSession = curl_init();

		// Set the URL
		curl_setopt ($curlSession, CURLOPT_URL, $this->gateway_url);
		// No headers, please
		curl_setopt ($curlSession, CURLOPT_HEADER, 0);
		// It's a POST request
		curl_setopt ($curlSession, CURLOPT_POST, 1);
		// Set the fields for the POST
		curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $post_string);
		// Return it direct, don't print it out
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
		// This connection will timeout in 30 seconds
		curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
		//The next two lines must be present for the kit to work with newer version of cURL
		//You should remove them if you have any problems in earlier versions of cURL
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

		//Send the request and store the result in an array

		$rawresponse = curl_exec($curlSession);
		//Store the raw response for later as it's useful to see for integration and understanding
		$_SESSION["rawresponse"]=$rawresponse;
		//Split response into name=value pairs
		$response = preg_split("/&/", $rawresponse);

		// Check that a connection was made
		if (curl_error($curlSession)){
			// If it wasn't...
			$output['Status'] = "FAIL";
			$output['StatusDetail'] = curl_error($curlSession);
		}

		// Close the cURL session
		curl_close ($curlSession);

		// Tokenise the response
		for ($i=0; $i<count($response); $i++)
		{
			// Find position of first "=" character
			$splitAt = strpos($response[$i], "=");
			// Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
			$output[trim(substr($response[$i], 0, $splitAt))] = trim(substr(urldecode($response[$i]), ($splitAt+1)));
		}

		// get first line of status
		$baseStatus = array_shift(preg_split("/ /",$output["ACK"]));

		// validate post
		if (strtoupper($baseStatus) == 'SUCCESS' || strtoupper($baseStatus) == 'SUCCESSWITHWARNING')
		{
			$this->response_data['orderID'] = $this->response_data['INVNUM'];
			return $output;
		}
		else
		{
			$this->errors = $output['L_LONGMESSAGE0'];
			return FALSE;
		}
	}

	function validate_authorize()
	{
		// parse the paypal URL
		$url_parsed = parse_url($this->gateway_url);

		// generate the post string from the _POST vars aswell as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		$post_string = '';
		if ($_POST)
		{
			$expMonth = '';
			$expYear = '';
			foreach ($_POST as $field => $value)
			{
				// tidy value
				$value = trim(str_replace("\n", "", $value));
				$value = trim(str_replace("\t", "", $value));

				// build expiry dates
				if ($field == 'expMonth')
				{
					$expMonth = $value;
				}
				if ($field == 'expYear')
				{
					$expYear = $value;
				}

				// build string
				$this->response_data[$field] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&';
			}
			$post_string.='x_exp_date='.$expMonth.$expYear.'&';
		}
		$post_string.="x_login=".$this->APIUser."&x_tran_key=".$this->APIPass."&x_version=3.1&x_delim_char=|&x_delim_data=TRUE&x_type=AUTH_CAPTURE&x_method=CC&x_relay_response=FALSE"; // append ipn command

		set_time_limit(60);

		// Initialise output variable
		$output = array();

		// Open the cURL session
		$curlSession = curl_init();

		// Set the URL
		curl_setopt ($curlSession, CURLOPT_URL, $this->gateway_url);
		// No headers, please
		curl_setopt ($curlSession, CURLOPT_HEADER, 0);
		// It's a POST request
		curl_setopt ($curlSession, CURLOPT_POST, 1);
		// Set the fields for the POST
		curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $post_string);
		// Return it direct, don't print it out
		curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
		// This connection will timeout in 30 seconds
		curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
		//The next two lines must be present for the kit to work with newer version of cURL
		//You should remove them if you have any problems in earlier versions of cURL
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

		//Send the request and store the result in an array

		$rawresponse = curl_exec($curlSession);
		//Store the raw response for later as it's useful to see for integration and understanding
		$_SESSION["rawresponse"]=$rawresponse;
		//Split response into name=value pairs
		$response = preg_split("/\|/", $rawresponse);

		// Check that a connection was made
		if (curl_error($curlSession)){
			// If it wasn't...
			$this->errors = curl_error($curlSession);
		}

		// Close the cURL session
		curl_close ($curlSession);

		// validate post
		if ($response[0] == 1)
		{
			$this->response_data['orderID'] = $response[7];
			return TRUE;
		}
		else
		{
			$this->errors = $response[3];
			return FALSE;
		}
	}

	function validate_rbsworldpay()
	{
		// get post	from worldpay
		if ($_POST)
		{
			$output = '';

			// get data
			foreach ($_POST as $field=>$value)
			{
				$this->response_data[$field] = $value;
			}

			// transaction cancelled
			if (@preg_match('/cancelled/i', $this->response_data['futurePayStatusChange']))
			{
				foreach ($this->response_data as $key => $value)
				{
					$output .= "$key: $value\n";
				}

				// cancel subscriber
				$this->cancel_subscriber();

				// load email lib and email admin
				$this->load->library('email');
				$this->email->to($this->site->config['siteEmail']);
				$this->email->subject('subscriber Cancellation on '.$this->site->config['siteName']);
				$this->email->message("Dear Administrator,\n\nA user has cancelled their subscriber on ".$this->site->config['siteName'].".\n\nTheir reference ID is:\t#".$this->response_data['futurePayId']."\n\n".$this->site->config['siteURL']);
				$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
				$this->email->send();

				return FALSE;
			}

			// is valid
			elseif ($this->response_data['transStatus'] == 'Y')
			{
				return TRUE;
			}

			// an invalid response
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function validate_sagepay()
	{
		// get post	from worldpay
		if ($_POST)
		{
			$output = '';

			// get data
			foreach ($_POST as $field=>$value)
			{
				$this->response_data[$field] = $value;
				$output .= $field.': '.$value."\n";
			}

			// get first line of status
			$baseStatus = array_shift(preg_split("/ /", $this->response_data['Status']));

			// validate post
			if ($baseStatus == 'OK')
			{
				return TRUE;
			}

			// an invalid response
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function validate_payment()
	{
		// validate payment
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			if ($this->response_data['payment_status'] == 'Completed' && $this->response_data['txn_type'] == 'web_accept')
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['item_number'];
				$this->response_data['transactionID'] = $this->response_data['custom'];
				$this->response_data['fullName'] = trim($this->response_data['first_name'].' '.$this->response_data['last_name']);
				$this->response_data['firstName'] = $this->response_data['first_name'];
				$this->response_data['lastName'] = $this->response_data['last_name'];
				$this->response_data['email'] = $this->response_data['payer_email'];
				$this->response_data['shopEmail'] = $this->response_data['receiver_email'];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($this->site->config['shopGateway'] == 'paypalpro')
		{
			if ($this->response_data['payment_status'] == 'Completed' && $this->response_data['txn_type'] == 'web_accept')
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['item_number'];
				$this->response_data['transactionID'] = $this->response_data['custom'];
				$this->response_data['fullName'] = $this->response_data['first_name'].' '.$this->response_data['last_name'];
				$this->response_data['firstName'] = $this->response_data['first_name'];
				$this->response_data['lastName'] = $this->response_data['last_name'];
				$this->response_data['email'] = $this->response_data['payer_email'];
				$this->response_data['shopEmail'] = $this->response_data['receiver_email'];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			if (!$this->response_data['futurePayId'] && $this->response_data['amount'] > 0)
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['cartId'];
				$this->response_data['transactionID'] = $this->response_data['MC_custom'];
				$this->response_data['fullName'] = $this->response_data['name'];
				$names = explode(' ', $this->response_data['name']);
				$this->response_data['firstName'] = @$names[0];
				$this->response_data['lastName'] = @$names[1];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($this->site->config['shopGateway'] == 'sagepay')
		{
			if ($this->response_data['TxAuthNo'] > 0)
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['VendorTxCode'];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function validate_subscription()
	{
		// validate subscription
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			if ($this->response_data['txn_type'] == 'subscr_signup' && $this->response_data['subscr_id'])
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['subscr_id'];
				$this->response_data['fullName'] = trim($this->response_data['first_name'].' '.$this->response_data['last_name']);
				$this->response_data['firstName'] = $this->response_data['first_name'];
				$this->response_data['lastName'] = $this->response_data['last_name'];
				$this->response_data['email'] = $this->response_data['payer_email'];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			if ($this->response_data['futurePayId'] && $this->response_data['amount'] == 0)
			{
				// set order reference ID and transaction ID
				$this->response_data['orderID'] = $this->response_data['futurePayId'];
				$this->response_data['fullName'] = $this->response_data['name'];
				$names = explode(' ', $this->response_data['name']);
				$this->response_data['firstName'] = @$names[0];
				$this->response_data['lastName'] = @$names[1];
				$this->response_data['custom'] = $this->response_data['MC_custom'];

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function validate_sub_payment()
	{
		// validate subscriber payment
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			if ($this->response_data['txn_type'] == 'subscr_payment' && $this->response_data['subscr_id'])
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			if ($this->response_data['futurePayId'] && $this->response_data['amount'] > 0)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function pay_invoice($invoiceID)
	{
		// add todo to project numbers
		$this->db->set('paid', '1');
		$this->db->set('public', '0');
		$this->db->where('invoiceID', $invoiceID);
		$this->db->update('manage_invoices');
	}

	function add_subscriber()
	{
		// add subscriber
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			// get subscriptionID
			$subscriptionID = substr($this->response_data['item_number'], (strpos($this->response_data['item_number'], '-')+1));

			$this->db->set('subscriptionID', $subscriptionID);
			$this->db->set('referenceID', $this->response_data['subscr_id']);
			$this->db->set('userID', $this->response_data['custom']);
			$this->db->set('fullName', trim($this->response_data['first_name'].' '.$this->response_data['last_name']));
			$this->db->set('email', $this->response_data['payer_email']);
			$this->db->set('address',
				$this->response_data['address1']."\n".
				$this->response_data['address2']."\n".
				$this->response_data['city']."\n".
				$this->response_data['state']);
			$this->db->set('postcode', $this->response_data['zip']);
			$this->db->set('country', $this->response_data['country']);
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			$this->db->set('referenceID', $this->response_data['futurePayId']);
			$this->db->set('userID', $this->shop->response_data['MC_custom']);
			$this->db->set('currency', $this->response_data['currency']);
			$this->db->set('fullName', $this->response_data['name']);
			$this->db->set('email', $this->response_data['email']);
			$this->db->set('address', $this->response_data['address']);
			$this->db->set('postcode', $this->response_data['postcode']);
			$this->db->set('country', $this->response_data['countryString']);
		}

		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('lastPayment', date("Y-m-d H:i:s"));
		$this->db->set('siteID', $this->siteID);

		$this->db->insert('subscribers');

		return TRUE;
	}

	function update_subscriber($subscriberID = '')
	{
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			$referenceID = $this->response_data['subscr_id'];
			$amount = $this->response_data['mc_gross'];
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			$referenceID = $this->response_data['futurePayId'];
			$amount = $this->response_data['amount'];
		}

		// update subscriber
		$this->db->where('siteID', $this->siteID);
		$this->db->where('referenceID', $referenceID);
		$this->db->set('lastPayment', date("Y-m-d H:i:s"));
		$this->db->update('subscribers');

		// add subscriber payment
		$this->db->set('dateCreated', date("Y-m-d H:i:s"));
		$this->db->set('siteID', $this->siteID);
		$this->db->set('referenceID', $referenceID);
		$this->db->set('amount', $amount);

		$this->db->insert('sub_payments');

		return TRUE;
	}

	function cancel_subscriber()
	{
		if ($this->site->config['shopGateway'] == 'paypal')
		{
			$referenceID = $this->response_data['subscr_id'];
		}
		elseif ($this->site->config['shopGateway'] == 'rbsworldpay')
		{
			$referenceID = $this->response_data['futurePayId'];
		}

		// cancel subscriber
		$query = $this->db->get_where('subscribers', array('referenceID' => $referenceID));
		$row = $query->row_array();

		$this->db->where('siteID', $this->siteID);
		$this->db->where('referenceID', $referenceID);

		$this->db->set('active', 0);

		$this->db->update('subscribers');

		$this->downgrade_user($row['userID']);

		return TRUE;
	}

	function log_response_results($success)
	{
		if (!$this->response_log) return;  // is logging turned off?

		// Timestamp
		$text = '['.date('m/d/Y g:i A').'] - ';

		// Success or failure being logged?
		if ($success) $text .= "SUCCESS!\n";
		else $text .= 'FAIL: '.$this->last_error."\n";

		// Log the POST variables
		$text .= "IPN POST Vars from Paypal:\n";
		foreach ($this->response_data as $key=>$value)
			$text .= "$key=$value, ";

		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n ".$this->response;

		// Write to log
		$fp=fopen($this->response_log_file,'a');
		fwrite($fp, $text . "\n\n");

		fclose($fp);  // close file
	}

	function set_reset_key($userID = '', $key = '')
	{
		if ($key && $userID)
		{
			// set password reset key
			$this->db->set('resetkey', $key);
			$this->db->where(array('siteID' => $this->siteID, 'userID' => $userID));
			$this->db->limit(1);

			$this->db->update('users');

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function check_key($key = '')
	{
		if ($key)
		{
			// check reset key
			$this->db->where(array('siteID' => $this->siteID, 'resetkey' => $key));
			$query = $this->db->get('users', 1);

			if ($query->num_rows())
			{
				return $query->row_array();
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	function upgrade_user($userID, $plan = '')
	{
		// update users based on subscriber action
		if ($this->site->config['subscriberAction'] > 0)
		{
			$this->db->set('groupID', $this->site->config['subscriberAction']);
		}
		else
		{
			$this->db->set('active', 1);
		}

		// set subscribed status
		$this->db->set('subscribed', 1);
		$this->db->set('plan', $plan);

		$this->db->where('siteID', $this->siteID);
		$this->db->where('userID', $userID);

		$this->db->update('users');

		return TRUE;
	}

	function downgrade_user($userID)
	{
		// update users based on subscriber action
		if ($this->site->config['subscriberAction'] > 0)
		{
			$this->db->set('groupID', 0);
		}
		elseif ($this->site->config['activation'])
		{
			$this->db->set('active', 0);
		}

		// set subscribed status
		$this->db->set('subscribed', 0);

		$this->db->where('siteID', $this->siteID);
		$this->db->where('userID', $userID);

		$this->db->update('users');

		return TRUE;
	}

	function approve_review($reviewID)
	{
		$this->db->set('active', 1);
		$this->db->where('siteID', $this->siteID);
		$this->db->where('reviewID', $reviewID);

		$this->db->update('shop_reviews');

		return TRUE;
	}

	function renew_downloads($transactionID)
	{
		$this->db->set('expiryDate', date("Y-m-d H:i:s", strtotime('+7 days')));
		$this->db->where('siteID', $this->siteID);
		$this->db->where('transactionID', $transactionID);

		$this->db->update('shop_transactions');

		return TRUE;
	}

	function lookup_user_by_email($email)
	{
		// default where
		$this->db->where('siteID', $this->siteID);
		$this->db->where('email', $email);

		$this->db->select('userID, firstName, lastName, email, notifications');

		$query = $this->db->get('users', 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row_array();
			return $row;
		}
		else
		{
			return FALSE;
		}
	}

	function export_orders()
	{
		// default where
		$this->db->where('shop_transactions.siteID', $this->siteID);
		$this->db->where('paid', 1);

		// select
		$this->db->select('shop_transactions.dateCreated as Date, transactionCode as OrderID, amount, postage, discounts as Discounts, donation as Donation, tax as Tax, firstName as FirstName, lastName as LastName, address1 as Address1, address2 as Address, address3 as Address3, city as City, state as State, postcode as Postcode, country as Country, email as Email, notes as Notes');

		// join
		$this->db->join('users', 'users.userID = shop_transactions.userID');

		// order
		$this->db->order_by('shop_transactions.dateCreated', 'asc');

		$query = $this->db->get('shop_transactions');

		if ($query->num_rows() > 0)
		{
			return $query;
		}
		else
		{
			return FALSE;
		}
	}
}
