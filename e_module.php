<?php

/**
 * @file
 * This file is loaded every time the core of e107 is included. ie. Wherever
 * you see require_once("class2.php") in a script. It allows a developer to
 * modify or define constants, parameters etc. which should be loaded prior to
 * the header or anything that is sent to the browser as output. It may also be
 * included in Ajax calls.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// Register events.
$event = e107::getEvent();

$event->register('admin_plugin_install', 'metatag_update_addon_list');
$event->register('admin_plugin_uninstall', 'metatag_update_addon_list');
$event->register('admin_plugin_upgrade', 'metatag_update_addon_list');
$event->register('admin_plugin_refresh', 'metatag_update_addon_list');

$event->register('admin_news_delete', 'metatag_deleted_news');
$event->register('admin_page_delete', 'metatag_deleted_page');


/**
 * Callback function to update metatag addon list.
 */
function metatag_update_addon_list()
{
	e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
	$meta = new metatag();
	$meta->updateAddonList();
}

/**
 * Callback function to delete custom meta tags are set for news item.
 *
 * @param array $data
 *  Array of news data.
 */
function metatag_deleted_news($data)
{
	e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
	$meta = new metatag();
	$meta->deleteMetaTagData($data['id'], 'news');
}

/**
 * Callback function to delete custom meta tags are set for page item.
 *
 * @param array $data
 *  Array of page data.
 */
function metatag_deleted_page($data)
{
	e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
	$meta = new metatag();
	$meta->deleteMetaTagData($data['id'], 'page');
}
