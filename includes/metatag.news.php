<?php

/**
 * @file
 * Callback functions used for entity news.
 */


/**
 * Determines if the current page is a news item.
 *
 * TODO - better method to detect news pages.
 *
 * @return mixed
 *  News item ID if the current page is a news page, otherwise false.
 */
function metatag_entity_news_detect()
{
	$query = e_QUERY;

	if(substr($query, 0, 7) == 'extend.')
	{
		$id = (int) str_replace('extend.', '', $query);

		if($id > 0)
		{
			return $id;
		}
	}

	return false;
}

/**
 * Loads a news item from the database.
 *
 * @param $id
 *  News item ID.
 *
 * @return array
 *  Contains news record from database, or empty array.
 */
function metatag_entity_news_load($id)
{
	$db = e107::getDb();
	$db->select('news', '*', 'news_id = ' . (int) $id);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Return with the news title.
 *
 * @param $entity
 *  News record from database.
 *
 * @return string
 *  News title, or empty string.
 */
function metatag_entity_news_token_title($entity)
{
	return varset($entity['news_title'], '');
}

/**
 * Return with the news summary.
 *
 * @param $entity
 *  News record from database.
 *
 * @return string
 *  News summary, or empty string.
 */
function metatag_entity_news_token_summary($entity)
{
	return varset($entity['news_summary'], '');
}

function metatag_entity_news_token_author_username($entity)
{

}

function metatag_entity_news_token_author_display($entity)
{

}

function metatag_entity_news_token_author_real($entity)
{

}

function metatag_entity_news_token_created_short($entity)
{

}

function metatag_entity_news_token_created_long($entity)
{

}

function metatag_entity_news_token_created_forum($entity)
{

}
