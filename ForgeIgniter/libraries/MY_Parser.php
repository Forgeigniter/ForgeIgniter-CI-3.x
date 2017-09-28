<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * MY Parser Class
 *
 * @package		ForgeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		Haloweb Ltd
 */

class MY_Parser extends CI_Parser {

	/**
	 *  Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template,
	 * replacing them with the data in the second param
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	*/

	function parse($template, $data, $return = FALSE, $include = FALSE)
	{
		$CI = & get_instance();

		if ($template == '')
		{
			return FALSE;
		}

		if ($include === FALSE)
		{
			$template = $CI->load->view($template, $data, TRUE);
		}

		if (isset($data) && $data != '')
		{
			$replace = array();
			foreach ($data as $key => $val)
			{
				$replace = array_merge(
					$replace,
					is_array($val)
						? $this->_parse_pair($key, $val, $template)
						: $this->_parse_single($key, (string) $val, $template)
				);
			}

			$template = strtr($template, $replace);
		}

		// Check for conditional statements
		$template = $this->conditionals($template, $data);

		// populate form tags
		preg_match_all('/{form:(.+)}/i', $template, $posts);
		if ($posts)
		{
			foreach ($posts[1] as $post => $value)
			{
				if ($postValue = $CI->input->post($value))
				{
					$template = str_replace('{form:'.$value.'}', $postValue, $template);
				}
				else
				{
					$template = str_replace('{form:'.$value.'}', '', $template);
				}
			}
		}

		//$template = preg_replace('/{(.+)}/i', '', $template);

		if ($return == FALSE)
		{
			$CI->output->append_output($template);
		}

		return $template;
	}

	// --------------------------------------------------------------------

	/**
	 *  Parse conditional statments
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	*/

	function conditionals($template, $data)
	{
		if (preg_match_all('/'.$this->l_delim.'if (.+)'.$this->r_delim.'(.+)'.$this->l_delim.'\/if'.$this->r_delim.'/siU', $template, $conditionals, PREG_SET_ORDER))
		{
			if (count($conditionals) > 0)
			{
				// filter through conditionals
				foreach($conditionals as $condition)
				{
					// get conditional and the string inside
					$code = (isset($condition[0])) ? $condition[0] : FALSE;
					$condString = (isset($condition[1])) ? str_replace(' ', '', $condition[1]) : FALSE;
					$insert = (isset($condition[2])) ? $condition[2] : '';

					// check code is valid
					if (!preg_match('/('.$this->l_delim.'|'.$this->r_delim.')/', $condString, $condProblem))
					{
						if (!empty($code) || $condString !== FALSE || !empty($insert))
						{
							if (preg_match("/^!(.*)$/", $condString, $matches))
							{
								$condVar = (!$data[trim($matches[1])]) ? 0 : $data[trim($matches[1])];

								$result = (!$condVar) ? TRUE : FALSE;
							}
							elseif (preg_match("/([a-z0-9\-_:\(\)]+)(\!=|=|==|>|<)([a-z0-9\-_\/]+)/", $condString, $matches))
							{
								$condVar = (!$data[$matches[1]]) ? 0 : $data[trim($matches[1])];

								if ($matches[2] == '==' || $matches[2] == '=')
								{
									$result = ($condVar === $matches[3]) ? TRUE : FALSE;
								}
								elseif ($matches[2] == '!=')
								{
									$result = ($condVar !== $matches[3]) ? TRUE : FALSE;
								}
								elseif ($matches[2] == '>')
								{
									$result = ($condVar > $matches[3]) ? TRUE : FALSE;
								}
								elseif ($matches[2] == '<')
								{
									$result = ($condVar < $matches[3]) ? TRUE : FALSE;
								}
							}
							else
							{
								// if the variable is set
								if (isset($data[$condString]) && is_array($data[$condString]))
								{
									$result = (count($data[$condString]) > 0) ? TRUE : FALSE;
								}
								else
								{
									$result = (isset($data[$condString]) && $data[$condString] != '') ? TRUE : FALSE;
								}
							}

							// filter for else
							$insert = preg_split('/'.$this->l_delim.'else'.$this->r_delim.'/siU', $insert);

							if ( ! empty($result))
							{
								// show the string inside
								$template = str_replace($code, $insert[0], $template);
							}
							else
							{
								if (is_array($insert))
								{
									$insert = (isset($insert[1])) ? $insert[1] : '';
									$template = str_replace($code, $insert, $template);
								}
								else
								{
									$template = str_replace($code, '', $template);
								}
							}
						}
					}
					else
					{
						// remove any conditionals we cant process
						$template = str_replace($code, '', $template);
					}
				}
			}

			//print_r($conditionals);
		}

		return $template;
	}

	// --------------------------------------------------------------------

	/**
	 *  Matches a variable pair
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	mixed
	 */

	function _match_pair($string, $variable)
	{
		$variable = str_replace('(', '\(', $variable);
		$variable = str_replace(')', '\)', $variable);

		if ( ! preg_match("|".$this->l_delim . $variable . $this->r_delim."(.+?)".$this->l_delim . '/' . $variable . $this->r_delim."|s", $string, $match))
		{
			return FALSE;
		}

		return $match;
	}

}
// END Parser Class

/* End of file */