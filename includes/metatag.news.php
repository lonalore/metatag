<?php

/**
 * @file
 * Callback functions used for entity news.
 */


/**
 * Determines if the current page is a news list page.
 *
 * @return mixed
 *  True if the current page is a news list page, otherwise false.
 */
function metatag_entity_news_list_detect()
{
	$page = defset('e_PAGE', '');
	$query = defset('e_QUERY', '');

	if($page == 'news.php' && empty($query))
	{
		return true;
	}

	return false;
}

/**
 * Determines if the current page is a news category page.
 *
 * @return mixed
 *  True if the current page is a news category page, otherwise false.
 */
function metatag_entity_news_category_detect()
{
	$page = defset('e_PAGE', '');
	$query = defset('e_QUERY', '');

	if($page == 'news.php' && substr($query, 0, 5) == 'list.')
	{
		return (int) str_replace('list.', '', $query);
	}

	return false;
}

function metatag_entity_news_category_load($category_id)
{
	$db = e107::getDb();
	$db->select('news_category', '*', 'category_id = ' . (int) $category_id);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Determines if the current page is a news tag page.
 *
 * @return mixed
 *  True if the current page is a news tag page, otherwise false.
 */
function metatag_entity_news_tag_detect()
{
	$page = defset('e_PAGE', '');
	$query = defset('e_QUERY', '');

	if($page == 'news.php' && substr($query, 0, 4) == 'tag=')
	{
		return true;
	}

	return false;
}

function metatag_entity_news_tag_load()
{
	return $_GET['tag'];
}

/**
 * Determines if the current page is a news item.
 *
 * @return mixed
 *  News item ID if the current page is a news page, otherwise false.
 */
function metatag_entity_news_detect()
{
	$page = defset('e_PAGE', '');
	$query = defset('e_QUERY', '');

	if($page == 'news.php' && substr($query, 0, 7) == 'extend.')
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
	$db->gen("SELECT * FROM #news AS n
	LEFT JOIN #news_category AS nc ON n.news_category = nc.category_id
	WHERE n.news_id = " . (int) $id, false);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Return with the news item ID.
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item ID, or empty string.
 */
function metatag_entity_news_token_id($entity)
{
	return varset($entity['news_id'], '');
}

/**
 * Return with the news item title.
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item title, or empty string.
 */
function metatag_entity_news_token_title($entity)
{
	return varset($entity['news_title'], '');
}

/**
 * Return with the news item summary.
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item summary, or empty string.
 */
function metatag_entity_news_token_summary($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['news_summary'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Return with the news item thumbnail(s).
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item thumbnail(s), or empty string.
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
					$thumbnails[] = $tp->thumbUrl($url, 'w=1200', false, true);
					$count++;
				}
			}
		}
	}

	return implode('|', $thumbnails);
}

/**
 * Return with the first thumbnail of the news item.
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item thumbnail, or empty string.
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
					$thumbnail = $tp->thumbUrl($url, 'w=1200', false, true);
					break;
				}
			}
		}
	}

	return $thumbnail;
}

/**
 * Return with the news item thumbnail(s). (1200x630px)
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item thumbnail(s), or empty string.
 */
function metatag_entity_news_token_thumbnail_og($entity)
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
					$thumbnails[] = $tp->thumbUrl($url, 'w=1200&h=630', false, true);
					$count++;
				}
			}
		}
	}

	return implode('|', $thumbnails);
}

/**
 * Return with the first thumbnail of the news item. (1200x630px)
 *
 * @param $entity
 *  News item record from database.
 *
 * @return string
 *  News item thumbnail, or empty string.
 */
function metatag_entity_news_token_thumbnail_first_og($entity)
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
					$thumbnail = $tp->thumbUrl($url, 'w=1200&h=630', false, true);
					break;
				}
			}
		}
	}

	return $thumbnail;
}

function metatag_entity_news_token_author_username($entity)
{
	if(!empty($entity['news_author']))
	{
		$author = e107::user($entity['news_author']);

		if(!empty($author))
		{
			return $author['user_loginname'];
		}
	}

	return '';
}

function metatag_entity_news_token_author_display($entity)
{
	if(!empty($entity['news_author']))
	{
		$author = e107::user($entity['news_author']);

		if(!empty($author))
		{
			return $author['user_name'];
		}
	}

	return '';
}

function metatag_entity_news_token_author_real($entity)
{
	if(!empty($entity['news_author']))
	{
		$author = e107::user($entity['news_author']);

		if(!empty($author))
		{
			return $author['user_login'];
		}
	}

	return '';
}

function metatag_entity_news_token_created_short($entity)
{
	if(!empty($entity['news_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['news_datestamp'], 'short');
	}

	return '';
}

function metatag_entity_news_token_created_long($entity)
{
	if(!empty($entity['news_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['news_datestamp'], 'long');
	}

	return '';
}

function metatag_entity_news_token_created_forum($entity)
{
	if(!empty($entity['news_datestamp']))
	{
		$date = e107::getDate();
		return $date->convert_date($entity['news_datestamp'], 'forum');
	}

	return '';
}

function metatag_entity_news_token_created_utc($entity)
{
	if(!empty($entity['news_datestamp']))
	{
		return gmdate("Y-m-d\TH:i:s\Z", $entity['news_datestamp']);
	}

	return '';
}

function metatag_entity_news_token_tag_name($tag)
{
	return $tag;
}

function metatag_entity_news_token_category_id($entity)
{
	return varset($entity['category_id'], '');
}

function metatag_entity_news_token_category_name($entity)
{
	return varset($entity['category_name'], '');
}

/**
 * Token handler for News Category Description.
 *
 * @param $entity
 *   News category record.
 *
 * @return mixed
 */
function metatag_entity_news_token_category_description($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['category_meta_description'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for News Category Description (Truncated).
 *
 * @param $entity
 *   News category record.
 *
 * @return mixed
 */
function metatag_entity_news_token_category_description_truncated($entity)
{
	$tp = e107::getParser();
	$desc = varset($entity['category_meta_description'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}

function metatag_entity_news_token_category_keywords($entity)
{
	return varset($entity['category_meta_keywords'], '');
}
