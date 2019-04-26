<?php

/**
 * @file
 * Callback functions used for download pages.
 */

/**
 * Determines if the current page is a download category list page.
 *
 * @return mixed
 *  True if the current page is a download category list page, otherwise false.
 */
function metatag_entity_download_category_list_detect()
{
	$plugin = defset('e_CURRENT_PLUGIN', '');

	if($plugin == 'download' && empty($_GET['action']))
	{
		return TRUE;
	}

	return false;
}

/**
 * Determines if the current page is a download category page.
 *
 * @return mixed
 *  Download category ID if the current page is a download category page, otherwise false.
 */
function metatag_entity_download_category_detect()
{
	$plugin = defset('e_CURRENT_PLUGIN', '');

	if($plugin == 'download' && !empty($_GET['action']) && $_GET['action'] == 'list' && !empty($_GET['id']))
	{
		return (int) $_GET['id'];
	}

	return false;
}

/**
 * Determines if the current page is a download page.
 *
 * @return mixed
 *  Download item ID if the current page is a download page, otherwise false.
 */
function metatag_entity_download_detect()
{
	$plugin = defset('e_CURRENT_PLUGIN', '');

	if($plugin == 'download' && !empty($_GET['action']) && $_GET['action'] == 'view' && !empty($_GET['id']))
	{
		return (int) $_GET['id'];
	}

	return false;
}

/**
 * Loads download category entity.
 *
 * @param $id
 *   Entity ID.
 *
 * @return array|bool
 */
function metatag_entity_download_category_load($id)
{
	$db = e107::getDb();
	$db->select('download_category', '*', 'download_category_id = ' . (int) $id);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Loads download item entity.
 *
 * @param $id
 *   Entity ID.
 *
 * @return array|bool
 */
function metatag_entity_download_load($id)
{
	$db = e107::getDb();
	$db->gen("SELECT * FROM #download AS d
	LEFT JOIN #download_category AS dc ON d.download_category = dc.download_category_id
	WHERE d.download_id = " . (int) $id, false);

	$entity = array();

	while($row = $db->fetch())
	{
		$entity = $row;
	}

	return $entity;
}

/**
 * Token handler for Download Category ID.
 *
 * @param $entity
 *   Download category record.
 *
 * @return mixed
 */
function metatag_entity_download_category_token_id($entity) {
	return varset($entity['download_category_id'], '');
}

/**
 * Token handler for Download Category Name.
 *
 * @param $entity
 *   Download category record.
 *
 * @return mixed
 */
function metatag_entity_download_category_token_name($entity) {
	return varset($entity['download_category_name'], '');
}

/**
 * Token handler for Download Category Description.
 *
 * @param $entity
 *   Download category record.
 *
 * @return mixed
 */
function metatag_entity_download_category_token_description($entity) {
	$tp = e107::getParser();
	$desc = varset($entity['download_category_description'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for Download Category Description (Truncated).
 *
 * @param $entity
 *   Download category record.
 *
 * @return mixed
 */
function metatag_entity_download_category_token_description_truncated($entity) {
	$tp = e107::getParser();
	$desc = varset($entity['download_category_description'], 'asd');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}

/**
 * Token handler for Download Item ID.
 *
 * @param $entity
 *   Download Item record.
 *
 * @return mixed
 */
function metatag_entity_download_item_token_id($entity) {
	return varset($entity['download_id'], '');
}

/**
 * Token handler for Download Item Name.
 *
 * @param $entity
 *   Download Item record.
 *
 * @return mixed
 */
function metatag_entity_download_item_token_name($entity) {
	return varset($entity['download_name'], '');
}

/**
 * Token handler for Download Item Description.
 *
 * @param $entity
 *   Download Item record.
 *
 * @return mixed
 */
function metatag_entity_download_item_token_description($entity) {
	$tp = e107::getParser();
	$desc = varset($entity['download_description'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	return $desc;
}

/**
 * Token handler for Download Item Description (Truncated).
 *
 * @param $entity
 *   Download Item record.
 *
 * @return mixed
 */
function metatag_entity_download_item_token_description_truncated($entity) {
	$tp = e107::getParser();
	$desc = varset($entity['download_description'], '');
	// Removes tags.
	$desc = $tp->toText($desc);
	// Removes line breaks.
	$desc = trim(preg_replace('/\s+/', ' ', $desc));
	// Truncates string to max 150 chars (includes ellipsis).
	$desc = $tp->text_truncate($desc,147); // + '...'
	return $desc;
}
