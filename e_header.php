<?php

/**
 * @file
 * Contains meta handler class.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// Load required main class of plugin.
e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');


/**
 * Class metatag_e_header.
 */
class metatag_e_header
{

	private $meta;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		if(defset('e_ADMIN_AREA', false) === true)
		{
			return;
		}

		$this->meta = new metatag();
		$this->meta->addMetaTags();
	}

}


// Class instantiation.
new metatag_e_header;
