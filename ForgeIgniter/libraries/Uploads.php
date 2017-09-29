<?php defined('BASEPATH') OR exit('No direct script access allowed');
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

class Uploads {

	var $CI;
	var $errors;
	var $siteID;
	var $uploadsPath;
	var $allowedTypes = 'gif|jpg|png|pdf|zip|mp3|mp4|js';
	var $maxSize = '5000';
	var $maxWidth = '5000';
	var $maxHeight = '5000';
	var $thumbSize = '300';

	function __construct()
	{
		$this->CI =& get_instance();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}

		// set the path
		$this->uploadsPath = $this->CI->config->item('uploadsPath');

		// create uploads directories and files if they're not created already
		if (!is_dir('.'.$this->uploadsPath))
		{
			@mkdir('.'.$this->uploadsPath);
			@chmod('.'.$this->uploadsPath, 0777);
			@mkdir('.'.$this->uploadsPath.'/avatars');
			@chmod('.'.$this->uploadsPath.'/avatars', 0777);

			$html = 'Directory access is forbidden.';
			$fp = @fopen('.'.$this->uploadsPath.'/index.html', 'w');
			@fputs($fp, $html);
			@fclose($fp);

			$fp = @fopen('.'.$this->uploadsPath.'/avatars/index.html', 'w');
			@fputs($fp, $html);
			@fclose($fp);
		}
	}

	function upload_image($thumbnail = TRUE, $maxsize = 1600, $name = 'image')
	{
		// image config
		$config['upload_path'] = '.'.$this->uploadsPath;
		$config['allowed_types'] = $this->allowedTypes;
		$config['max_size']	= $this->maxSize;
		$config['max_width']  = $this->maxWidth;
		$config['max_height']  = $this->maxHeight;
		$config['encrypt_name']  = true;

		// load upload class
		$this->CI->load->library('upload', $config);
		$this->CI->load->library('image_lib');

		// upload image
		if ($this->CI->upload->do_upload($name))
		{
			// get image data
			$imageData = $this->CI->upload->data();

			if ($imageData['image_type'] == '' || !$imageData['is_image'])
			{
				$this->CI->form_validation->set_error('The file you are trying to upload is not an image');
				return FALSE;
			}

			// make thumbnail
			if ($thumbnail === TRUE && ($imageData['image_width'] > $this->thumbSize || $imageData['image_height'] > $this->thumbSize))
			{
				$config['image_library'] = 'gd2';
				$config['source_image'] = $config['upload_path'].'/'.$imageData['file_name'];
				$config['create_thumb'] = true;
				$config['maintain_ratio'] = true;
				$config['width'] = $this->thumbSize;
				$config['height'] = $this->thumbSize;

				$this->CI->image_lib->initialize($config);
				$this->CI->image_lib->resize();
			}

			// resize the main image if its big :-)
			if ($imageData['image_width'] > $maxsize || $imageData['image_height'] > $maxsize)
			{
				// clear the cache
				if ($thumbnail === TRUE)
				{
					$this->CI->image_lib->clear();
				}
				$config['image_library'] = 'gd2';
				$config['source_image'] = $config['upload_path'].'/'.$imageData['file_name'];
				$config['create_thumb'] = false;
				$config['maintain_ratio'] = true;
				$config['width'] = $maxsize;
				$config['height'] = $maxsize;

				$this->CI->image_lib->initialize($config);
				$this->CI->image_lib->resize();
			}

			return $imageData;
		}
		else
		{
			$this->errors = $this->CI->upload->display_errors();

			return FALSE;
		}
	}

	function upload_file($name = 'file')
	{
		// image config
		$config['upload_path'] = '.'.$this->uploadsPath;
		$config['allowed_types'] = $this->allowedTypes;
		$config['max_size']	= $this->maxSize;
		$config['max_width']  = $this->maxWidth;
		$config['max_height']  = $this->maxHeight;
		$config['encrypt_name']  = true;

		// load upload class
		$this->CI->load->library('upload', $config);

		// upload image
		if ($this->CI->upload->do_upload($name))
		{
			// get image data
			$fileData = $this->CI->upload->data();

			return $fileData;
		}
		else
		{
			$this->errors = $this->CI->upload->display_errors();

			return FALSE;
		}
	}

	function load_image($image, $thumbnail = false, $product = false)
	{
		$pathToUploads = $this->uploadsPath;

		// grab from db
		if ($product)
		{
			$query = $this->CI->db->get_where('shop_products', array('productID' => $image, 'siteID' => $this->siteID));

			if ($query->num_rows())
			{
				$row = $query->row_array();

				$image = $row['imageName'];
				$imagePath = $pathToUploads.'/'.$image;
				$row['src'] = $imagePath;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			$imagePath = $pathToUploads.'/'.$image;

			// get file
			$query = $this->CI->db->get_where('images', array('imageRef' => $image, 'siteID' => $this->siteID, 'deleted' => 0));

			if ($query->num_rows())
			{
				$row = $query->row_array();

				$image = $row['filename'];
				$imagePath = $pathToUploads.'/'.$image;
				$row['src'] = $imagePath;
			}
			else
			{
				return FALSE;
			}
		}

		// show thumbnail
		if ($thumbnail)
		{
			$ext = substr($image,strrpos($image,'.'));
			$thumbPath = str_replace($ext, '', $imagePath).'_thumb'.$ext;

			$row['src'] = (file_exists('.'.$thumbPath)) ? $thumbPath : $imagePath;
		}

		// check file exists
		if (file_exists('.'.$imagePath))
		{
			return $row;
		}
		else
		{
			return FALSE;
		}
	}

	function load_file($file, $ref = FALSE)
	{
		// check there is something there!
		if (!$file)
		{
			return FALSE;
		}

		$pathToUploads = $this->uploadsPath;

		// if its a premium item unpack the key
		$keyPipe = $this->decode($file);
		if (preg_match('/\|/', $keyPipe))
		{
			$premium = TRUE;
			$key = @explode('|', $keyPipe);
			if (@!$key[0] || @!$key[1])
			{
				return FALSE;
			}
			else
			{
				$file = $key[0];
				$transactionID = $key[1];
			}
		}
		else
		{
			$premium = FALSE;
		}

		// find out if the file is coming from a reference or not
		if ($ref === TRUE)
		{
			// get file
			$this->CI->db->where('fileRef', $file);
			$this->CI->db->where('siteID', $this->siteID);
			$this->CI->db->where('deleted', 0);
			$query = $this->CI->db->get('files', 1);

			if ($query->num_rows())
			{
				$row = $query->row_array();

				$row['src'] = $pathToUploads.'/'.$row['filename'];
				$row['extension'] = substr($row['filename'], strrpos($row['filename'],'.'));

				// update views
				$this->CI->db->set('downloads', 'downloads+1', false);
				$this->CI->db->where('fileID', $row['fileID']);
				$this->CI->db->where('siteID', $this->siteID);
				$this->CI->db->update('files');

				// check its not a premium item
				$this->CI->db->where('fileID', $row['fileID']);
				$this->CI->db->where('siteID', $this->siteID);
				$this->CI->db->where('deleted', 0);
				$shopQuery = $this->CI->db->get('shop_products', 1);

				if ($shopQuery->num_rows())
				{
					$this->CI->db->where('transactionID', @$transactionID);
					$this->CI->db->where('expiryDate >', date("Y-m-d H:i:s"));
					$orderQuery = $this->CI->db->get('shop_transactions', 1);

					// it is a premium item so check the key is valid
					if (!$premium)
					{
						$row['error'] = 'premium';
					}
					elseif (!$orderQuery->num_rows())
					{
						$row['error'] = 'expired';
					}
				}

				// return file details
				return $row;
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			$filePath = $pathToUploads.'/'.$file;
		}

		// get file
		return $filePath;
	}

	function delete_file($filename)
	{
		$uploadPath = '.'.$this->uploadsPath.'/'.$filename;

		if (file_exists($uploadPath))
		{
			unlink($uploadPath);

			// delete thumbnail
			$ext = substr($filename,strrpos($filename,'.'));
			$uploadPath = $uploadPath = str_replace($ext, '', $uploadPath).'_thumb'.$ext;
			if (file_exists($uploadPath))
			{
				unlink($uploadPath);
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	function decode($base64)
	{
		return base64_decode(strtr($base64, '-_', '+/'));
	}

}
