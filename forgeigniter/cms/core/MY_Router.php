<?php
defined('BASEPATH') or exit('No direct script access allowed');

// load the MX_Router class
require_once APPPATH."libraries/MX/Router.php";

class MY_Router extends MX_Router
{

  // Case Insensitive Routing
  /* 
    public function _parse_routes()
    {
        foreach ($this->uri->segments as &$segment) {
            $segment = strtolower($segment);
        }

        return parent::_parse_routes();
    }
  */
}