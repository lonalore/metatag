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

/**
 * Return with the news thumbnail(s).
 *
 * @param $entity
 *  News record from database.
 *
 * @return string
 *  News thumbnail(s), or empty string.
 */
function metatag_entity_news_token_thumbnail($entity)
{
	$thumbnails = array();

	if(!empty($entity['news_thumbnail']))
	{
		$urls = explode(",", $entity['news_thumbnail']);

		if(!empty($urls))
		{
			$tp = e107::getParser();

			$count = 0;
			foreach($urls as $url)
			{
				if($count > 5)
				{
					break;
				}

				if(substr($url, 0, 3) == "{e_")
				{
					// Do nothing.
				}
				else
				{
					$url = SITEURL . e_IMAGE . "newspost_images/" . $url;
				}

				if($tp->isImage($url))
				{
					$thumbnails[] = $tp->thumbUrl($url, 'w=500', false, true);
					$count++;
				}
			}
		}
	}

	return implode('|', $thumbnails);
}

/**
 * Return with the first thumbnail of the news.
 *
 * @param $entity
 *  News record from database.
 *
 * @return string
 *  News thumbnail, or empty string.
 */
function metatag_entity_news_token_thumbnail_first($entity)
{
	$thumbnail = '';

	if(!empty($entity['news_thumbnail']))
	{
		$urls = explode(",", $entity['news_thumbnail']);

		if(!empty($urls))
		{
			$tp = e107::getParser();

			foreach($urls as $url)
			{
				if(substr($url, 0, 3) == "{e_")
				{
					// Do nothing.
				}
				else
				{
					$url = SITEURL . e_IMAGE . "newspost_images/" . $url;
				}

				if($tp->isImage($url))
				{
					$thumbnail = $tp->thumbUrl($url, 'w=500', false, true);
					break;
				}
			}
		}
	}

	return $thumbnail;
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
