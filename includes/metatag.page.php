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
	$entity = false;

	$db = e107::getDb();
	$db->select('page_chapters', '*', 'chapter_id = "' . (int) $book_id . '"');

	while($row = $db->fetch())
	{
		$entity['book'] = $row;
	}

	return $entity;
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
	$entity = false;

	$db = e107::getDb();
	$db->select('page_chapters', '*', 'chapter_id = ' . (int) $chapter_id);

	while($row = $db->fetch())
	{
		$entity['chapter'] = $row;
	}

	return $entity;
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
	$query = defset('e_QUERY', '');

	if($page == 'page.php' && isset($_GET['id']))
	{
		return (int) $_GET['id'];
	}

	if($page == 'page.php' && (int) $query)
	{
		return (int) $query;
	}

	return false;
}

function metatag_entity_page_load($page_id)
{
	$entity = false;

	$db = e107::getDb();

	$db->select('page', '*', 'page_id = "' . (int) $page_id . '"');
	while($row = $db->fetch())
	{
		$entity['page'] = $row;
	}

	if(varset($entity['page']['page_chapter'], 0) > 0)
	{
		$db->select('page_chapters', '*', 'chapter_id = "' . (int) $entity['page']['page_chapter'] . '"');
		while($row = $db->fetch())
		{
			$entity['chapter'] = $row;
		}
	}

	if(varset($entity['page']['page_author'], 0) > 0)
	{
		$entity['author'] = e107::user($entity['page']['page_author']);
	}

	return $entity;
}

function metatag_entity_page_token_id($entity)
{
	return varset($entity['page']['page_id'], '');
}

function metatag_entity_page_token_title($entity)
{
	return varset($entity['page']['page_title'], '');
}

function metatag_entity_page_token_body($entity)
{
	if(isset($entity['page']['page_text']))
	{
		return e107::getParser()->toText($entity['page']['page_text']);
	}

	return '';
}

function metatag_entity_page_token_keywords($entity)
{
	return varset($entity['page']['page_metakeys'], '');
}

/**
 * Token handler for Page Description.
 *
 * @param $entity
 *   Page record.
 *
 * @return mixed
 */
function metatag_entity_page_token_description($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['page']['page_metadscr'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for Page Description (Truncated).
 *
 * @param $entity
 *   Page record.
 *
 * @return mixed
 */
function metatag_entity_page_token_description_truncated($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['page']['page_metadscr'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}

function metatag_entity_page_token_created_short($entity)
{
	if(!empty($entity['page']['page_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['page']['page_datestamp'], 'short');
	}

	return '';
}

function metatag_entity_page_token_created_long($entity)
{
	if(!empty($entity['page']['page_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['page']['page_datestamp'], 'long');
	}

	return '';
}

function metatag_entity_page_token_created_forum($entity)
{
	if(!empty($entity['page']['page_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['page']['page_datestamp'], 'forum');
	}

	return '';
}

function metatag_entity_page_token_created_utc($entity)
{
	if(!empty($entity['page']['page_datestamp']))
	{
		return gmdate("Y-m-d\TH:i:s\Z", $entity['page']['page_datestamp']);
	}

	return '';
}

function metatag_entity_page_token_author_username($entity)
{
	return varset($entity['author']['user_loginname'], '');
}

function metatag_entity_page_token_author_display($entity)
{
	return varset($entity['author']['user_name'], '');
}

function metatag_entity_page_token_author_real($entity)
{
	return varset($entity['author']['user_login'], '');
}

function metatag_entity_page_token_chapter_name($entity)
{
	return varset($entity['chapter']['chapter_name'], '');
}

/**
 * Token handler for Page Chapter Description.
 *
 * @param $entity
 *   Page Chapter record.
 *
 * @return mixed
 */
function metatag_entity_page_token_chapter_description($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['chapter']['chapter_description'], 'asd');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for Page Chapter Description (Truncated).
 *
 * @param $entity
 *   Page Chapter record.
 *
 * @return mixed
 */
function metatag_entity_page_token_chapter_description_truncated($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['chapter']['chapter_description'], 'asd');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}

function metatag_entity_page_token_chapter_keywords($entity)
{
	return varset($entity['chapter']['chapter_keywords'], '');
}

function metatag_entity_page_token_book_name($entity)
{
	return varset($entity['book']['chapter_name'], '');
}

/**
 * Token handler for Book Description.
 *
 * @param $entity
 *   Book record.
 *
 * @return mixed
 */
function metatag_entity_page_token_book_description($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['book']['chapter_description'], 'asd');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for Book Description (Truncated).
 *
 * @param $entity
 *   Book record.
 *
 * @return mixed
 */
function metatag_entity_page_token_book_description_truncated($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['book']['chapter_description'], 'asd');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}

function metatag_entity_page_token_book_keywords($entity)
{
	return varset($entity['book']['chapter_keywords'], '');
}
