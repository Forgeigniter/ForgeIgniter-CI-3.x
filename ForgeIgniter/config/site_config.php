<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2020, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * site config global variables
 * $this->config->item('')
**/

// set paths
$config['includesPath']     =	'includes/admin';      // path to admin header and footer files
$config['uploadsPath']      =	'/static/uploads';     // where to upload files (must be 777)
$config['staticPath']       =	'static';			         // where are the images hosted
$config['adminStaticPath']  =	'static/admin';		     // Admin Theme Path
$config['logoPath']         =	'';					           // the administration logo
$config['stagingSites']     =	FALSE;				         // whether to create upload folders for each site automatically (for MSM)

$config['themePath'] = 'static/themes/';             // Theme Path
