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

function metatag_entity_news_load($id)
{

}

function metatag_entity_news_token_author_username($data)
{

}

function metatag_entity_news_token_author_display($data)
{

}

function metatag_entity_news_token_author_real($data)
{

}

function metatag_entity_news_token_created_short($data)
{

}

function metatag_entity_news_token_created_long($data)
{

}

function metatag_entity_news_token_created_forum($data)
{

}
