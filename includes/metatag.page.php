<?php

/**
 * @file
 * Callback functions used for entity page.
 */


/**
 * Determines if the current page is a "List Books" page.
 *
 * @return mixed
 *  True if the current page is a "List Books" page.
 *  Otherwise false.
 */
function metatag_entity_page_list_books_detect()
{
	$page = defset('e_PAGE', '');

	if($page == 'page.php' && !e_QUERY)
	{
		return true;
	}

	return false;
}

/**
 * Determines if the current page is a "List Chapters
 * within a specific Book" page.
 *
 * @return mixed
 *  Book ID or false.
 */
function metatag_entity_page_list_chapters_detect()
{
	$page = defset('e_PAGE', '');

	if($page == 'page.php' && isset($_GET['bk']))
	{
		return (int) $_GET['bk'];
	}

	return false;
}

/**
 * Determines if the current page is a "Page - List Pages
 * within a specific Chapter" page.
 *
 * @return mixed
 *  Chapter ID or false.
 */
function metatag_entity_page_list_pages_detect()
{
	$page = defset('e_PAGE', '');

	if($page == 'page.php' && isset($_GET['ch']))
	{
		return (int) $_GET['ch'];
	}

	return false;
}

/**
 * Determines if the current page is a page item.
 *
 * @return mixed
 *  Page ID if the current page is a page, otherwise false.
 */
function metatag_entity_page_detect()
{
	$page = defset('e_PAGE', '');

	if($page == 'page.php' && isset($_GET['id']))
	{
		return (int) $_GET['id'];
	}

	return false;
}

function metatag_entity_page_load($id)
{

}

function metatag_entity_page_token_author_username($data)
{

}

function metatag_entity_page_token_author_display($data)
{

}

function metatag_entity_page_token_author_real($data)
{

}

function metatag_entity_page_token_created_short($data)
{

}

function metatag_entity_page_token_created_long($data)
{

}

function metatag_entity_page_token_created_forum($data)
{

}
