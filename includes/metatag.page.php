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

function metatag_entity_page_list_chapters_load($book_id)
{
	$book = false;

	$db = e107::getDb();
	$db->select('page_chapters', '*', 'chapter_id = "' . (int) $book_id . '"');

	while($row = $db->fetch())
	{
		$book = $row;
	}

	return $book;
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

function metatag_entity_page_list_pages_load($chapter_id)
{
	$chapter = false;

	$db = e107::getDb();
	$db->select('page_chapters', '*', 'chapter_id = ' . (int) $chapter_id);

	while($row = $db->fetch())
	{
		$chapter = $row;
	}

	return $chapter;
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

function metatag_entity_page_load($page_id)
{
	$page = false;

	$db = e107::getDb();
	$db->select('page', '*', 'page_id = "' . (int) $page_id . '"');

	while($row = $db->fetch())
	{
		$page = $row;
	}
	
	return $page;
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
