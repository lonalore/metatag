<?php

/**
 * @file
 * Callback functions used for entity page.
 */


/**
 * Determines if the current page is a page item.
 *
 * TODO - better method to detect pages.
 *
 * @return mixed
 *  Page ID if the current page is a page, otherwise false.
 */
function metatag_entity_page_detect()
{
	if(isset($_GET['id']))
	{
		return (int) $_GET['id'];
	}

	return false;
}

function metatag_entity_page_load($id)
{
	
}

