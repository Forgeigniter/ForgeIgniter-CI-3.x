<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 */

// ------------------------------------------------------------------------

/**
 * CX Tags Class Modified to work with ForgeIgniter.
 *
 * @package		CXTags
 * @category	Models
 * @author		David Pennington <xeoncross.com>
 * @link		http://code.google.com/p/cxtags/
 * @license		http://www.gnu.org/copyleft/gpl.html
 * @version		1.1.0
 *
 * This class allows any table, and any type of system, to
 * add, edit, and delete tags from any object/row without
 * interfering with other tables that are also using tags.
 *
 *
 * Forexample, you have 58 articles on your blog that each
 * have 2-4 tags attached to them. Now you add a "Image Gallery"
 * to your site to showoff your photos. You want to "tag" each
 * photo with some key words (city, dark, night) that will help
 * people sort through all the images. With this class both
 * your articles and your images (and anything else you add) can
 * use the same "tags" table without interfering with each other.
 * Just because blog article 23 has the tag "sports" doesn't mean
 * that image 23 will return the tag "sports".
 *
 *
 *
 * About the variables:
 * (the fictitious tables "posts" and "images" used for examples)
 *
 * TABLE
 * The name of a table using tags (i.e. "posts", "images", etc..)
 * this allows us to tell the difference between a tag for row
 * 23 of the "images" table and row 23 of the "posts" table.
 *
 * ROW_ID
 * The row_id is the unique ID of a row from the table set in TABLE.
 * This id corasponds to a some row like "row 23" of the "posts" table.
 *
 * siteID
 * The ID of the user that created the tag. (for use with a "users" table)
 * This means that multiple users can each tag a TABLE row with their own
 * tags. This is optional so if you don't plan on starting another "de
 *
 * SAFE_TAG
 * This is a URL/file/etc. safe version of the TAG. (only [a-z0-9_])
 *
 * TAG
 * A cleaned (alphanumeric and spaces) catialized tag (only for display)
 *
 * TAG_ID
 * The Unique Id of the tag
 *
 * DATE
 * The date the tag was attached to a row
 *
 *
 * ABOUT THE CLASS FUNCTIONS
 * The following functions need a table name and a safe_tag
 * string or a tag_id to work. Never try to pass the plain "tag"
 * field to any of these functions as it won't work.
 *
 *
/***************************** MySQL TABLES

CREATE TABLE IF NOT EXISTS `ci_tags` (
  `id` int NOT NULL auto_increment,
  `safe_tag` varchar(30) NOT NULL,
  `tag` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `safe_tag` (`safe_tag`)
) ENGINE=MyISAM ;

CREATE TABLE IF NOT EXISTS `ci_tags_ref` (
  `tag_id` int unsigned NOT NULL,
  `row_id` int unsigned NOT NULL,
  `siteID` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  `table` varchar(20) NOT NULL
) ENGINE=MyISAM ;

******************************************
 */

class Tags {

	//The names of the tables
	var $CI;
	var $tags_ref			= 'tags_ref';
	var $tags				= 'tags';
	var $tags_prefix		= null;
	var $tags_ref_prefix	= null;
	var $siteID				= null;

	function __construct() {

		$this->CI =& get_instance();

		//prefix the tags tables with the right thing
		$this->tags_prefix		= $this->CI->db->dbprefix($this->tags);
		$this->tags_ref_prefix	= $this->CI->db->dbprefix($this->tags_ref);

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}

	/*
	 * Fetch all tags for a given row/object and table
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 * 		'row_id' => '1',	//ID of the row that we want the tags for
	 *		'siteID' => null,	//Optional ID of a single user
	 *		'limit' => 10,		//Optional Max number of results
	 *		'offset' => 0		//Optional Offset of results
	 *	);
	 * @param	array	data about the tag(s) we are fetching
	 * @return	mixed
	 */
	function fetch_tags($data) {
		//If there is no table
		if(!$data['table'] || !$data['row_id']) {
			return;
		}

		//Select the Tag info
		$this->CI->db->select('tag, safe_tag, id, date');
		$this->CI->db->distinct();
		$this->CI->db->join($this->tags,
			$this->tags_ref_prefix. '.tag_id = '. $this->tags_prefix. '.id', 'inner');
		$this->CI->db->where('table', $data['table']);
		$this->CI->db->where('row_id', $data['row_id']);

		//If a limit is implied
		if(isset($data['limit']) && $data['limit']) {
			$this->CI->db->limit($data['limit'],
			(isset($data['offset']) ? $data['offset'] : null)
			);
		}

		//If a siteID is given
		if(isset($data['siteID']) && $data['siteID']) {
			$this->CI->db->where($this->tags_ref_prefix. '.siteID', $data['siteID']);
		}

		$result = $this->CI->db->get($this->tags_ref);

		if ($result->num_rows())
		{
			return $result->result_array();
		}
		else
		{
			return FALSE;
		}

	}

	/*
	 * Fetch the most popular/used tags
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 *		'siteID' => null,	//Optional ID of a single user
	 *		'limit' => 10,		//Optional Max number of results
	 *		'offset' => 0		//Optional Offset of results
	 *	);
	 * @param	array	data about the tag(s) we are fetching
	 * @return	mixed
	 */
	function fetch_popular_tags($data) {
		//If there is no table
		if(!$data['table']) {
			return;
		}

		//Select the Tag info
		$this->CI->db->select('tag, safe_tag, id, date, COUNT(*) as count');
		//$this->CI->db->distinct();
		$this->CI->db->join($this->tags,
			$this->tags_ref_prefix. '.tag_id = '. $this->tags_prefix. '.id', 'inner');
		$this->CI->db->where('table', $data['table']);
		$this->CI->db->order_by('count DESC, tag ASC');
		$this->CI->db->group_by('tag');

		//If a limit is NOT implied
		if(!isset($data['limit']) || !$data['limit']) {
			$data['limit'] = 50;
		}

		//Only fetch up to limit number of rows
		$this->CI->db->limit($data['limit'],
		(isset($data['offset']) ? $data['offset'] : null)
		);

		//If a siteID is given
		if(isset($data['siteID']) && $data['siteID']) {
			$this->CI->db->where($this->tags_ref_prefix. '.siteID', $data['siteID']);
		}

		$result = $this->CI->db->get($this->tags_ref);

		if ($result->num_rows())
		{
			return $result->result_array();
		}
		else
		{
			return FALSE;
		}

	}

	/*
	 * Get tag data based on tag_id OR safe_tag
	 *
	 * @param	string	Name of the table to use
	 * @param	mixed	Int (tag_id) or string (safe_tag)
	 * @return	mixed
	 */
	function fetch_tag($piece=null) {

		//If no tag is given
		if(!$piece) { return; }

		//Is the $piece an ID or a TAG name?
		if(is_int($piece)) {
			$this->CI->db->where('id', $piece);
		} else {
			$this->CI->db->where('safe_tag', $piece);
		}

		$result = $this->CI->db->get($this->tags);

		if ($result->num_rows())
		{
			return $result->result_array();
		}
		else
		{
			return FALSE;
		}

	}

	/*
	 * Fetch all rows/objects that use one or more safe_tag(s) or tag_id(s)
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 * 		'tags' => '1',		//tag_id, tag, or an array of either
	 * 		'siteID' = > null,	//Optional only return rows for siteID
	 *		'limit' => 10,		//Optional Max number of results
	 *		'offset' => 0		//Optional Offset of results
	 *	);
	 * @param	array	data about the tag(s) we are fetching
	 * @return	mixed
	 */
	function fetch_rows($data) {
		//If there is no table
		if(!$data['table'] || !$data['tags']) {
			return;
		}

		//Add the WHERE clause for the tags
		$this->where_tags($data['tags']);

		//Select the Tag info
		//$this->CI->db->select('tag, safe_tag, date, row_id, siteID');
		//Don't need tag/user info because the GROUP BY will only show 1
		//tag/user for each row (even if there are may)
		$this->CI->db->select('row_id');
		$this->CI->db->group_by("row_id");
		$this->CI->db->join($this->tags_ref,
		$this->tags_ref_prefix. '.tag_id = '. $this->tags_prefix. '.id', 'inner');
		$this->CI->db->where('table', $data['table']);

		//If a limit is implied
		if(isset($data['limit']) && $data['limit']) {
			$this->CI->db->limit($data['limit'],
			(isset($data['offset']) ? $data['offset'] : null)
			);
		}

		//If a siteID is given
		if(isset($data['siteID']) && $data['siteID']) {
			$this->CI->db->where($this->tags_ref_prefix. '.siteID', $data['siteID']);
		}

		$result = $this->CI->db->get($this->tags);

		if ($result->num_rows())
		{
			return $result->result_array();
		}
		else
		{
			return FALSE;
		}

	}

	/*
	 * Count all the tags for a user and/or table
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 *		'siteID' => null,	//Optional ID of a single user
	 *	);
	 * @param	array	data about the tag(s) we are counting
	 * @return	mixed
	 */
	function count_tags($data) {
		//If there is no table
		if(!$data['table']) {
			return;
		}

		/*
		 * SELECT COUNT(DISTINCT `safe_tag`) as count FROM ci_tags
		 * INNER JOIN ci_tags_ref ON (id = tag_id) WHERE `siteID` = 0 AND `table` = 'posts'
		 */

		//Select the Tag info
		$this->CI->db->select('COUNT(*) as count');
		$this->CI->db->join($this->tags,
			$this->tags_ref_prefix. '.tag_id = '. $this->tags_prefix. '.id', 'inner');
		$this->CI->db->where('table', $data['table']);
		$this->CI->db->order_by('count DESC');

		//If a siteID is given
		if(isset($data['siteID']) && $data['siteID']) {
			$this->CI->db->where($this->tags_ref_prefix. '.siteID', $data['siteID']);
		}

		//fetch the number of rows
		$result = $this->CI->db->get($this->tags_ref);

		return $result->result();

	}

	/*
	 * Insert tags for a row/object
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 * 		'tags' => array('tag 1', 'tag 2'),	//An array of tags
	 * 		'row_id' => 23,
	 * 		'siteID' = > null,	//Optional only return rows for siteID
	 *	);
	 * @param	array	data about the tag(s) we are creating
	 * @return	mixed
	 */
	function add_tags($data=array()) {

		//If there is no table, row, or tags...
		if(!$data['table'] || !$data['tags'] || !$data['row_id']) {
			return;
		}

		//The array of tags -minus the $finalized_tags
		$tags = array();
		//This will store the table ID and safe_tag of each tag
		$finalized_tags = array();
		//This will store the "Cleaned" version for our where_tag() function
		$safe_tags		= array();


		//STEP 1: Create the "safe" version of each tag
		foreach($data['tags'] as $key => $tag) {

			$safe_tag = $this->make_safe_tag($tag);

			//Add this tag to an array called $tags
			$tags[$safe_tag] = trim($tag);

			//Add it to an array of ONLY safe_tags
			$safe_tags[] = $safe_tag;

		}

		//STEP 2: Search DB for the tags already in there
		$this->CI->db->select('id, tag, safe_tag');
		$this->where_tags($safe_tags);
		$query = $this->CI->db->get($this->tags);

		//If some of these tags already exist
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {

				//Add this row to the finalized tags
				$finalized_tags[$row->safe_tag] = array(
		   			'tag' => $row->tag,
		   			'id' => $row->id
				);

			}

			/**
			 * Now that we have an array of the tags from the DB
			 * we can unset them from the $tags array so they aren't
			 * added to the table a second time!
			 */

			foreach($finalized_tags as $safe_tag => $tag) {
				if(isset($tags[$safe_tag])) {
					unset($tags[$safe_tag]);
				}
			}
		}

		//STEP 3: Insert each tag into our table since it isn't there already
		foreach($tags as $safe_tag => $tag) {

			// tidy tag
			$tag = ucwords(strtolower($tag));

			//Insert the tag into the database
			$this->CI->db->insert($this->tags, array('safe_tag' => $safe_tag, 'tag' => $tag));

			//Now that the tag is in the DB we need to add it to the finalized tags
			$finalized_tags[$safe_tag] = array('tag' => $tag, 'id' => $this->CI->db->insert_id());
		}


		//STEP 4: Attach each tag to the row

		//Row data that won't change doesn't need to be repeated!
		$row_data = array(
			'`table`' => $data['table'],
			'row_id' => $data['row_id']
		);

		/* MOD: haloweb -- dont duplicate data in row by getting tags
		if ($tags_data = $this->fetch_tags($row_data))
		{
			foreach ($tags_data as $row)
			{
				$row_tags[$row['safe_tag']] = $row['tag'];
			}
		}
		*/

		//If a siteID is given
		if(isset($data['siteID']) && $data['siteID']) {
			$row_data['siteID'] = $data['siteID'];
		}

		//For each tag - attach it to the row/object
		foreach($finalized_tags as $safe_tag => $tag) {

			//Data about the row
			$row_data['date'] = date("Y-m-d H:i:s");
			$row_data['tag_id'] = $tag['id'];

			/*
			if (!in_array($safe_tag, $row_tags))
			{
				$this->CI->db->insert($this->tags_ref, $row_data);
			}
			*/

			//FINALLY INSERT THE ROW! ...(jeez)...
			$this->CI->db->insert($this->tags_ref, $row_data);
		}

		//return true;

		// Not sure why someone would need this..
		// but return the finalized array
		return $finalized_tags;

	}

	/*
	 * Delete tag relationships to a row/object
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 * 		'row_id' => 23,
	 * 		'siteID' => null	//Optional only delete rows for siteID
	 *	);
	 * @param	array	data about the tag_ref we are deleting
	 * @return	mixed
	 */
	function delete_tag_ref($data) {
		//If there is no table or row_id
		if(!$data['table'] || !$data['row_id']) {
			return;
		}

		//If a user is set - only delete tags for that user
		if(isset($data['siteID']) && $data['siteID']) {
			$this->CI->db->where('siteID', $data['siteID']);
		}

		//Delete all tags_ref where this table and row are found
		$this->CI->db->where('table', $data['table']);
		$this->CI->db->where('row_id', $data['row_id']);
		$this->CI->db->delete($this->tags_ref);

		//return the rows deleted
		$rows = $this->CI->db->affected_rows();

		//Delete tags that are no-longer referenced by any row
		$this->delete_tags();

		return $rows;

	}

	/*
	 * Delete all tags from a given user
	 *
	 * $data = array(
	 * 		'table' => 'posts',	//Name of the table row_id is from
	 * 		'siteID' = > null,	//Optional only delete rows for siteID
	 *	);
	 * @param	array	data about the tag_ref we are deleting
	 * @return	mixed
	 */
	function delete_user_tags($data) {
		//If there is no table or row_id
		if(!$data['table'] || !$data['siteID']) {
			return;
		}

		//Where the tag is used by this user
		$this->CI->db->where('siteID', $data['siteID']);

		//Delete all tags_ref where this table and row are found
		$this->CI->db->delete($this->tags_ref);

		//return the rows deleted
		$rows = $this->CI->db->affected_rows();

		//Delete tags that are no-longer referenced by any row
		$this->delete_tags();

		return $rows;

	}

	/*
	 * Delete tags not referenced by any row/object.
	 *
	 * Because CI does NOT support DELETE...JOIN syntax
	 * will will have to do a SELECT first and then delete
	 * the result rows.
	 *
	 * @return	Int
	 */
	function delete_tags() {
		/*
		 //Join it to the tags_ref to make sure no rows are found
		 $this->CI->db->join('tags_ref', 'tags.id = tags_ref.tag_id');

		 //the tag_id is NULL (not found)
		 $this->CI->db->where('tag_id', null);
		 $this->CI->db->delete('tags');

		 //return the rows deleted
		 return $this->CI->db->affected_rows();
		 */

		$this->CI->db->select('id');
		//Join it to the tags_ref to make sure no rows are found
		$this->CI->db->join($this->tags_ref,
			$this->tags_ref_prefix. '.tag_id = '. $this->tags_prefix. '.id', 'left');
		//the tag_id is NULL (not found)
		$this->CI->db->where('tag_id', null);
		$result = $this->CI->db->get($this->tags);

		//If there are NO lost tags - just return
		if(!$result->num_rows()) {
			return 0;
		}

		//Colect the ids
		foreach($result->result() as $row) {
			$ids[] = $row->id;
		}

		//Delete all ids found in this list
		$this->CI->db->where_in('id', $ids);
		$this->CI->db->delete($this->tags);

		//return the rows deleted
		return $this->CI->db->affected_rows();

	}

	function get_tags($table, $ID)
	{
		// get tags
		$tags = $this->fetch_tags(array(
			'table' => $table,
			'row_id' => $ID
		));

		if ($tags)
		{
			foreach($tags as $tag)
			{
				$tagsArray[] = $tag['tag'];
			}
			return $tagsArray;
		}
		else
		{
			return FALSE;
		}
	}

	function get_popular_tags($table, $limit = 10)
	{
		// get tags
		$tags = $this->fetch_popular_tags(array(
			'table' => $table,
			'siteID' => $this->siteID,
			'limit' => $limit
		));

		if ($tags)
		{
			return $tags;
		}
		else
		{
			return FALSE;
		}
	}

	function update_tags($table, $ID = '', $tags = '')
	{
		// add tags
		if ($tags)
		{
			$data = array(
				'table' => $table,
				'row_id' => $ID
			);

			if ($this->siteID)
			{
				$data['siteID'] = $this->siteID;
			}

			$this->delete_tag_ref($data);

			$tagsArray = explode(',', $tags);
			foreach($tagsArray as $key => $tag)
			{
				$tag = trim($tag);
				if (isset($tag) && $tags != '' && strlen($tag) > 0)
				{
					$tidyTagsArray[] = $tag;
				}
			}
			$tags = array(
		 		'table' => $table,
		 		'tags' => $tidyTagsArray,
				'row_id' => $ID
			);
			if ($this->siteID)
			{
				$tags['siteID'] = $this->siteID;
			}
			$this->add_tags($tags);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function search($table, $tags = '')
	{
		$tags = explode(' ', $tags);

		$tagdata = array(
	 		'table' => $table,				//Name of the table row_id is from
	 		'tags' => $tags,				//tag_id, tag, or an array of either
	 		'limit' => null,				//Optional Max number of results
			'offset' => null,				//Optional Offset of results
		);
		if ($this->siteID)
		{
			$tagdata['siteID'] = $this->siteID;
		}

		if ($result = $this->fetch_rows($tagdata))
		{
			foreach($result as $row => $key)
			{
				$ids[] = $key['row_id'];
			}

			return $ids;
		}
		else
		{
			return FALSE;
		}
	}

	function tag_cloud($table, $limit = 10, $maxSize = 150, $minSize = 100)
	{
		// get tag data
		$tagdata = array(
			'table' => $table,
			'siteID' => $this->siteID,
			'limit' => $limit
		);
		$result = $this->fetch_popular_tags($tagdata);
		$tags = $result->result_array();

		// populate tag count array
		if ($tags)
		{
			// get sizes of tags
			foreach($tags as $tag)
			{
				$tagsCount[$tag['tag']] = $tag['count'];
			}

			// get the largest and smallest array values
			$maxQty = max(array_values($tagsCount));
			$minQty = min(array_values($tagsCount));

			// find range of values
			$range = $maxQty['count'] - $minQty['count'];

			// don't divide by zero
			if (0 == $range)
			{
				$range = 1;
			}

			// determine the font-size increment
			$step = ($maxSize - $minSize) / ($range);

			// populate tag array
			foreach($tags as $tag)
			{
				$size = $minSize + (($tag['count'] - $minQty) * $step);
				$tagsArray[$tag['tag']] = array('safe_tag' => $tag['safe_tag'], 'size' => $size);
			}

			// load output vars
			$data['tags'] = $tagsArray;
			$data['step'] = $step;
			$data['maxQty'] = $maxQty;
			$data['minQty'] = $minQty;
			$data['maxSize'] = $maxSize;
			$data['minSize'] = $minSize;

			return $data;
		}
		else
		{
			return FALSE;
		}
	}

	/*
	 * SUPPORT FUNCTIONS
	 */



	/*
	 * Turn a comma-separated string into an array of elements
	 * @param	string	The string from an input box or something
	 * @return	array
	 */
	function comma_to_array($string='') {
		/*
		 //Can handle even the most messed-up comma strings like below:
		 $tags = "\n\r". 'tag1, this is tag2, or tag3. but we can\'t tag4, tag5, other '. "\n".
		 'tag6, "plus tag7", #tag8,'. "\n\n". ',,,, ,,,,, ,,,'. "\n". ',, '.
		 "\n\n\n". '< this is another, tag9.,, , ';
		 */

		//Make the String lowercase
		$string = trim(strtolower($string));

		//Replace anything that isn't a letter, comma, space, quote, or number!
		$string = preg_replace("/[^a-z0-9, \"']/", '', $string);

		//Remove empty "," so that we don't make empty elements
		$string = preg_replace("/,[^a-z0-9]*,/", ',', $string);

		//If there is an ending comma.... kill it!
		$string = rtrim($string, ',');

		//Turn the string into an array of tags
		$string = explode(',', $string);

		//Remove extra spaces from front and back of each element and capitalize
		foreach($string as $key => $tag) {
			$string[$key] = trim(ucwords($tag));
		}

		return $string;
	}

	/*
	 * Make a tag safe for file & URL usage
	 * @param	string	the tag to clean
	 * @return	string	cleaned tag
	 */
	function make_safe_tag($tag='') {
		$tag = strtolower($tag);
		//remove anything not alphanumeric OR "_"
		$tag = preg_replace("/([^a-z0-9_\-]+)/i", '-', $tag);
		//remove duplicate "_"
		$tag = preg_replace("/(-{2,})+/", '-', $tag);
		//remove posible start/end "_"
		$tag = trim($tag, '-');
		return $tag;
	}

	/*
	 * Adds a WHERE Clause to a query. Pass this function a single
	 * tag_id/safe_tag - or an array of tag_ids/safe_tags.
	 *
	 * @param	mixed	string or array of tag_id's or safe_tags
	 */
	function where_tags($tags) {
		//If we have been given an array of tags to match
		if(is_array($tags)) {
			$ints = null;
			$strings = null;
			//Check each tag to see if it is an ID or a name
			foreach($tags as $tag) {
				if(is_int($tag)) {
					$ints[] = $tag;
				} else {
					$strings[] = $tag;
				}
			}

			//If some ID's were given
			if($ints) {
				$this->CI->db->where_in('id', $ints);
			}

			//If some tag names where given
			if($strings) {
				//If Int's are in the query we need an OR clause
				if($ints) {
					$this->CI->db->or_where_in('safe_tag', $strings);
				} else {
					$this->CI->db->where_in('safe_tag', $strings);
				}
			}
			//Else we are just looking for a rows that match one tag/ID
		} else {
			if(is_int($tags)) {
				$this->CI->db->where('id', $tags);
			} else {
				$this->CI->db->where('safe_tag', $tags);
			}
		}
	}

}
