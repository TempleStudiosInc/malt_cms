<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Contentfield extends ORM {
	
	protected $_belongs_to = array(
	    'content' => array()
	);
	
}
