<?php

/**
 * @file
 *
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class metatag_event.
 */
class metatag_event
{

	/**
	 * Configure functions/methods to run when specific e107 events are triggered.
	 *
	 * @return array
	 */
	function config()
	{
		$event = array();

		// After updating plugins table.
		$event[] = array(
			'name'     => "system_plugins_table_updated",
			'function' => "metatag_update_addon_list",
		);

		// After a plugin is installed.
		$event[] = array(
			'name'     => "admin_plugin_install",
			'function' => "metatag_update_addon_list",
		);

		// After a plugin is uninstalled.
		$event[] = array(
			'name'     => "admin_plugin_uninstall",
			'function' => "metatag_update_addon_list",
		);

		// After a plugin is upgraded.
		$event[] = array(
			'name'     => "admin_plugin_upgrade",
			'function' => "metatag_update_addon_list",
		);

		// Plugin information is updated.
		$event[] = array(
			'name'     => "admin_plugin_refresh",
			'function' => "metatag_update_addon_list",
		);

		// Admin deletes a news item.
		$event[] = array(
			'name'     => "admin_news_delete",
			'function' => "metatag_deleted_news",
		);

		// Admin deletes a news category.
		$event[] = array(
			'name'     => "admin_news_category_delete",
			'function' => "metatag_deleted_news_category",
		);

		// Admin deletes a page/menu item.
		$event[] = array(
			'name'     => "admin_page_delete",
			'function' => "metatag_deleted_page",
		);

		// Admin updates a news item.
		$event[] = array(
			'name'     => "admin_news_update",
			'function' => "metatag_updated_news",
		);

		// Admin updates a news category.
		$event[] = array(
			'name'     => "admin_news_category_update",
			'function' => "metatag_updated_news_category",
		);

		// 	Admin updates a page/menu item.
		$event[] = array(
			'name'     => "admin_page_update",
			'function' => "metatag_updated_page",
		);

		// Admin updates a download category.
		$event[] = array(
			'name'     => "admin_download_category_update",
			'function' => "metatag_updated_download_category",
		);

		$event[] = array(
			'name'     => "system_meta_pre",
			'function' => "metatag_alter",
		);

		return $event;
	}

	/**
	 * Callback function to update metatag addon list.
	 */
	function metatag_update_addon_list()
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->updateAddonList();
		$meta->prepareDefaultTypes(true);
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
		$meta->clearCacheByTypeAndId('news', $data['id']);
	}

	/**
	 * Callback function to delete custom meta tags are set for news category.
	 *
	 * @param array $data
	 *  Array of news category data.
	 */
	function metatag_deleted_news_category($data)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->deleteMetaTagData($data['id'], 'news-category');
		$meta->clearCacheByTypeAndId('news-category', $data['id']);
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
		$meta->clearCacheByTypeAndId('page', $data['id']);
	}

	/**
	 * Callback function to delete cached meta tags after updating a news item.
	 *
	 * @param array $data
	 *  Array of news data.
	 */
	function metatag_updated_news($data)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->clearCacheByTypeAndId('news', $data['id']);
	}

	/**
	 * Callback function to delete cached meta tags after updating a news category.
	 *
	 * @param array $data
	 *  Array of news category data.
	 */
	function metatag_updated_news_category($data)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->clearCacheByTypeAndId('news-category', $data['id']);
	}

	/**
	 * Callback function to delete cached meta tags after updating a page.
	 *
	 * @param array $data
	 *  Array of page data.
	 */
	function metatag_updated_page($data)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->clearCacheByTypeAndId('page', $data['id']);
	}

	/**
	 * Callback function to delete cached meta tags after updating a download category.
	 *
	 * @param array $data
	 *  Array of download category data.
	 */
	function metatag_updated_download_category($data)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();
		$meta->clearCacheByTypeAndId('download-category', $data['id']);
	}

	/**
	 * Callback function to alter meta tags.
	 *
	 * @param array $meta_tags
	 *   Previously added meta tags.
	 */
	function metatag_alter($meta_tags = array())
	{
		if(defset('e_ADMIN_AREA', false) !== true)
		{
			e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
			$meta = new metatag();
			$new_tags = $meta->prepareMetaTags();
			$remove_tags = array_keys($meta->getMetatagInfo(true, false));

			/** @var \eResponse $response */
			$response = e107::getSingleton('eResponse');
			$data = $response->getMeta();

			// Remove all previously added meta tags we want to override.
			foreach($data as $m)
			{
				if(isset($m['name']) && in_array($m['name'], $remove_tags))
				{
					$response->removeMeta($m['name']);
					continue;
				}

				if(isset($m['property']) && in_array($m['property'], $remove_tags))
				{
					$response->removeMeta($m['property']);
					continue;
				}
			}

			// Finally, apply new meta tags.
			$meta->addMetaTags($new_tags);
		}
	}

}
