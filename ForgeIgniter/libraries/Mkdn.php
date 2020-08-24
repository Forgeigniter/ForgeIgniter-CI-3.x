<?php defined('BASEPATH') OR exit('No direct script access allowed');
### CodeIgniter Library Interface ###

/*
| NOTES:
|------------------------------------
|
| Usage:
| Initialize the parser and return the result of its transform method.
| This will work fine for derived classes too.
| defaultTransform($text);
|
| Main function. Performs some preprocessing on the input text
| and pass it through the document gamut.
|
| transform($text);
|
*/

set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());

require_once APPPATH . 'third_party/MD/MarkdownInterface.php';
require_once APPPATH . 'third_party/MD/Markdown.php';
require_once APPPATH . 'third_party/MD/MarkdownExtra.php';

class Mkdn extends MD\Markdown {
	function __construct($params = array()) {
	  parent::__construct();
	}
}

#
# Markdown Extra  -  A text-to-HTML conversion tool for web writers
#
# PHP Markdown & Extra
# Copyright (c) 2004-2008 Michel Fortin
# <http://www.michelf.com/projects/php-markdown/>
#
# Original Markdown (PERL)
# Copyright (c) 2004-2006 John Gruber
# <http://daringfireball.net/projects/markdown/>
#
# Class is thanks to gauntface
#
