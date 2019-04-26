<?php

/**
 * @file
 * Callback functions used for entity front.
 */

/**
 * Determines if the current page is the front page.
 *
 * @return mixed
 *  True if the current page is the front page, otherwise false.
 */
function metatag_entity_front_detect()
{
	if(deftrue('e_FRONTPAGE', false) == true)
	{
		return true;
	}

	return false;
}
