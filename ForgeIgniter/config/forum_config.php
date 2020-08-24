<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| Forum Configuration Settings.
|
*/

	// Pagination Settings

	$config['total_rows'] = 200;
	$config['per_page'] = 20;
	// Display Page Number
	$config['display_pages'] = TRUE;
	// Previous Page
	$config['prev_tag_open'] = "<li>";
	$config['prev_tagl_close'] = "</li>";
	$config['prev_link'] = "&lt; Previous";
	// Current Page
	$config['cur_tag_open'] = "<li><a href='#' class='pi-active'>";
	$config['cur_tag_close'] = "</a></li>";
	// Digit
	$config['num_tag_open'] = "<li>";
	$config['num_tag_close'] = "</li>";
	// Next Page
	$config['next_tag_open'] = "<li>";
	$config['next_tag_close'] = "</li>";
	$config['next_link'] = "Next &gt;";
	// Last Link
	$config['last_tag_open'] = "<li>";
	$config['last_tag_close'] = "</li>";
	$config['last_link'] = "Last";


	// Avatar Settings

	// Size (px)
	$config['avatar_size'] = 200;
	// Class
	$config['avatar_class'] = "";
	// Default avatar image path
	$config['avatar_path'] = "/images/noavatar.gif";
