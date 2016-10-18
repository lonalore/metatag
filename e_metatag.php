<?php

/**
 * @file
 * Metatag addon file.
 */


/**
 * Class metatag_metatag.
 *
 * Usage: PLUGIN_metatag
 */
class metatag_metatag
{

	/**
	 * Provides information about metatag handlers.
	 *
	 * @return array $config
	 *  An associative array whose keys are the event trigger names used by Admin UIs.
	 *
	 * @See $eventName in class e_admin_ui.
	 */
	public function config()
	{
		$config = array();

		// Global meta tags.
		$config['metatag_default'] = array(
			'entityName'     => LAN_PLUGIN_METATAG_TYPE_01,
			'entityFile'     => '{e_PLUGIN}metatag/includes/metatag.global.php',
			// FIXME - use LANs.
			'entityTokens'   => array(
				'site:name'               => array(
					'help'    => 'The name of the site.',
					'handler' => 'metatag_global_token_site_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:description'        => array(
					'help'    => 'The description of the site.',
					'handler' => 'metatag_global_token_site_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:email'              => array(
					'help'    => 'The email address of the site.',
					'handler' => 'metatag_global_token_site_email',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:url'                => array(
					'help'    => 'The URL of the site\'s front page.',
					'handler' => 'metatag_global_token_site_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:login-url'          => array(
					'help'    => 'The URL of the site\'s login page.',
					'handler' => 'metatag_global_token_site_login_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:signup-url'         => array(
					'help'    => 'The URL of the signup page.',
					'handler' => 'metatag_global_token_site_signup_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:lan'                => array(
					'help'    => 'ISO 2 Letter Language Code for the current language.',
					'handler' => 'metatag_global_token_site_lan',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:lancode'            => array(
					'help'    => 'Language Code for the current language.',
					'handler' => 'metatag_global_token_site_lancode',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:language'           => array(
					'help'    => 'Human-readable name for the current language E.g. English.',
					'handler' => 'metatag_global_token_site_language',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:fb-app-id'          => array(
					'help'    => 'The facebook App ID belongs to the site.',
					'handler' => 'metatag_global_token_site_fb_app_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:admin:name'         => array(
					'help'    => 'The name of the site Admin.',
					'handler' => 'metatag_global_token_site_admin_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:admin:email'        => array(
					'help'    => 'The email address of the site Admin.',
					'handler' => 'metatag_global_token_site_admin_email',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:current-page:title' => array(
					'help'    => 'The title of the current page.',
					'handler' => 'metatag_global_token_site_current_page_title',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:current-page:url'   => array(
					'help'    => 'The URL of the current page.',
					'handler' => 'metatag_global_token_site_current_page_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				// TODO - more tokens.
			),
			// Initial, default meta tags.
			'entityDefaults' => array(
				'title'        => '{site:current-page:title} | {site:name}',
				'description'  => '{site:description}',
				'generator'    => 'e107 v2 (http://e107.org)',
				'canonical'    => '{site:current-page:url}',
				'fb:app_id'    => '{site:fb-app-id}',
				'og:site_name' => '{site:name}',
				'og:url'       => '{site:current-page:url}',
				'og:title'     => '{site:current-page:title}',
			),
		);

		// Front page meta tags.
		$config['front'] = array(
			// Human-readable name for this entity.
			'entityName'     => LAN_PLUGIN_METATAG_TYPE_02,
			// Callback function to implement logic for detecting entity path.
			// - If your callback function is a class::method, you have to provide an array
			//   whose first element is the class name and the second is the method.
			// - If your callback is a simple function, you have to provide a string instead
			//   of an array.
			// - If your callback function returns with false, it means that current path is
			//   not an entity path.
			// - If your callback function returns with true, it means that current path is
			//   an entity path, and entity does not have custom instances, so default meta
			//   tags will be loaded for the entity.
			// - If your callback function returns with a primary id (e.g. a News ID), it
			//   means that current path is an entity path, and need to load meta tags for
			//   a specific entity item.
			'entityDetect'   => 'metatag_entity_front_detect',
			// Path for the file, which contains entityDetect function.
			'entityFile'     => '{e_PLUGIN}metatag/includes/metatag.front.php',
			// Initial, default meta tags.
			'entityDefaults' => array(
				'title' => '{site:name}',
			),
		);

		// News list page meta tags.
		$config['news_list'] = array(
			// Human-readable name for this entity.
			'entityName'   => LAN_PLUGIN_METATAG_TYPE_05,
			// Callback function to implement logic for detecting entity path.
			// - If your callback function is a class::method, you have to provide an array
			//   whose first element is the class name and the second is the method.
			// - If your callback is a simple function, you have to provide a string instead
			//   of an array.
			// - If your callback function returns with false, it means that current path is
			//   not an entity path.
			// - If your callback function returns with true, it means that current path is
			//   an entity path, and entity does not have custom instances, so default meta
			//   tags will be loaded for the entity.
			// - If your callback function returns with a primary id (e.g. a News ID), it
			//   means that current path is an entity path, and need to load meta tags for
			//   a specific entity item.
			'entityDetect' => 'metatag_entity_news_list_detect',
			// Path for the file, which contains entityDetect function.
			'entityFile'   => '{e_PLUGIN}metatag/includes/metatag.news.php',
		);

		// News entity meta tags.
		$config['news'] = array(
			// Human-readable name for this entity.
			'entityName'     => LAN_PLUGIN_METATAG_TYPE_03,
			// Callback function to implement logic for detecting entity path.
			// - If your callback function is a class::method, you have to provide an array
			//   whose first element is the class name and the second is the method.
			// - If your callback is a simple function, you have to provide a string instead
			//   of an array.
			// - If your callback function returns with false, it means that current path is
			//   not an entity path.
			// - If your callback function returns with true, it means that current path is
			//   an entity path, and entity does not have custom instances, so default meta
			//   tags will be loaded for the entity.
			// - If your callback function returns with a primary id (e.g. a News ID), it
			//   means that current path is an entity path, and need to load meta tags for
			//   a specific entity item.
			'entityDetect'   => 'metatag_entity_news_detect',
			// Callback function to load entity from database in case of entityDetect
			// returns with ID, and entityTokens are provided.
			'entityQuery'    => 'metatag_entity_news_load',
			// Path for the file, which contains the entityDetect and entityQuery functions.
			'entityFile'     => '{e_PLUGIN}metatag/includes/metatag.news.php',
			// Tokens can be used for this entity.
			// FIXME - use LANs.
			'entityTokens'   => array(
				'news:title'           => array(
					'help'    => 'The title of the news item.',
					'handler' => 'metatag_entity_news_token_title',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:summary'         => array(
					'help'    => 'The summary of the news item.',
					'handler' => 'metatag_entity_news_token_summary',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail'       => array(
					'help'    => 'Thumbnail image(s) of the news item.',
					'handler' => 'metatag_entity_news_token_thumbnail',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail:first' => array(
					'help'    => 'First thumbnail image of the news item.',
					'handler' => 'metatag_entity_news_token_thumbnail_first',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:username' => array(
					'help'    => 'The username of the author.',
					'handler' => 'metatag_entity_news_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:display'  => array(
					'help'    => 'The display name of the author.',
					'handler' => 'metatag_entity_news_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:real'     => array(
					'help'    => 'The real name of the author.',
					'handler' => 'metatag_entity_news_token_author_real',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:short'   => array(
					'help'    => 'The date the news item was created. (short date format)',
					'handler' => 'metatag_entity_news_token_created_short',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:long'    => array(
					'help'    => 'The date the news item was created. (long date format)',
					'handler' => 'metatag_entity_news_token_created_long',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:forum'   => array(
					'help'    => 'The date the news item was created. (forum date format)',
					'handler' => 'metatag_entity_news_token_created_forum',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				// TODO - more tokens.
			),
			// Initial, default meta tags.
			'entityDefaults' => array(
				'title'                => '{news:title}',
				'description'          => '{news:summary}',
				'image_src'            => '{news:thumbnail:first}',
				'og:title'             => '{news:title}',
				'og:description'       => '{news:summary}',
				'og:image'             => '{news:thumbnail}',
				'itemprop:name'        => '{news:title}',
				'itemprop:description' => '{news:summary}',
				'itemprop:image'       => '{news:thumbnail:first}',
			),
		);

		// Page entity meta tags.
		$config['page'] = array(
			// Human-readable name for this entity.
			'entityName'   => LAN_PLUGIN_METATAG_TYPE_04,
			// Callback function to implement logic for detecting entity path.
			// - If your callback function is a class::method, you have to provide an array
			//   whose first element is the class name and the second is the method.
			// - If your callback is a simple function, you have to provide a string instead
			//   of an array.
			// - If your callback function returns with false, it means that current path is
			//   not an entity path.
			// - If your callback function returns with true, it means that current path is
			//   an entity path, and entity does not have custom instances, so default meta
			//   tags will be loaded for the entity.
			// - If your callback function returns with a primary id (e.g. a News ID), it
			//   means that current path is an entity path, and need to load meta tags for
			//   a specific entity item.
			'entityDetect' => 'metatag_entity_page_detect',
			// Callback function to load entity from database in case of entityDetect
			// returns with ID, and entityTokens are provided.
			'entityQuery'  => 'metatag_entity_page_load',
			// Path for the file, which contains the entityDetect and entityQuery functions.
			'entityFile'   => '{e_PLUGIN}metatag/includes/metatag.page.php',
			// Tokens can be used for this entity.
			// FIXME - use LANs.
			'entityTokens' => array(
				'page:author:username' => array(
					'help'    => 'The username of the author.',
					'handler' => 'metatag_entity_page_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:display'  => array(
					'help'    => 'The display name of the author.',
					'handler' => 'metatag_entity_page_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:real'     => array(
					'help'    => 'The real name of the author.',
					'handler' => 'metatag_entity_page_token_author_real',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:short'   => array(
					'help'    => 'The date the page was created. (short date format)',
					'handler' => 'metatag_entity_page_token_created_short',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:long'    => array(
					'help'    => 'The date the page was created. (long date format)',
					'handler' => 'metatag_entity_page_token_created_long',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:forum'   => array(
					'help'    => 'The date the page was created. (forum date format)',
					'handler' => 'metatag_entity_page_token_created_forum',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				// TODO - more tokens.
			),
		);

		return $config;
	}

	// TODO - method for altering config array.

}
