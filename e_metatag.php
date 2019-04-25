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
	 *  An associative array whose keys are the event trigger names used by
	 *  Admin UIs.
	 *
	 *  $config[KEY]
	 *      Where KEY is the event trigger name used by Admin UI in that case
	 *      you want to add "Metatag" tab to your Create/Edit form.
	 * @See $eventName in class e_admin_ui.
	 *  $config[KEY]['name']
	 *      Human-readable name for this entity.
	 *  $config[KEY]['detect']
	 *      Callback function to implement logic for detecting entity path.
	 *      - If your callback function is a class::method, you have to
	 *        provide an array whose first element is the class name and the
	 *        second is the method.
	 *      - If your callback is a simple function, you have to provide a
	 *        string instead of an array.
	 *      - If your callback function returns with false, it means that
	 *        current path is not an entity path.
	 *      - If your callback function returns with true, it means that
	 *        current path is an entity path, and entity does not have custom
	 *        instances, so default meta tags will be loaded for the entity.
	 *      - If your callback function returns with a primary id (e.g. a News
	 *        ID), it means that current path is an entity path, and need to
	 *        load meta tags for a specific entity item.
	 *  $config[KEY]['load']
	 *      Callback function to load entity from database in case of
	 *      'detect' returns with ID, and $config[KEY]['token'] is provided.
	 *  $config[KEY]['file']
	 *      Path for the file, which contains $config[KEY]['detect'] function.
	 *  $config[KEY]['token']
	 *      An associative array with tokens can be used for this entity. The
	 *      key is the token name, and the value is an array with:
	 *      'help' - Contains a short description about the token.
	 *      'handler' - Callback function returns with the token's value. The
	 *          handler function's first parameter will be the return value of
	 *          $config[KEY]['load'].
	 *      'file' - Path to the file, which contains the handler function.
	 *  $config[KEY]['default']
	 *      Provides default meta tags for the entity. An associative array
	 *      whose keys are the meta tag's name, and the value is the value of
	 *      the meta tag. These default meta tags will override the top level,
	 *      global meta tags.
	 *  $config[KEY]['tab']
	 *      Set to false if admin_ui has no tabs.
	 */
	public function config()
	{
		$config = array();

		// Global (default) meta tags.
		$config['metatag_default'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_01,
			'token'   => array(
				'site:name'               => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_01,
					'handler' => 'metatag_global_token_site_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:description'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_02,
					'handler' => 'metatag_global_token_site_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:email'              => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_03,
					'handler' => 'metatag_global_token_site_email',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:url'                => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_04,
					'handler' => 'metatag_global_token_site_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:login-url'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_05,
					'handler' => 'metatag_global_token_site_login_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:signup-url'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_06,
					'handler' => 'metatag_global_token_site_signup_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:lan'                => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_07,
					'handler' => 'metatag_global_token_site_lan',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:lancode'            => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_08,
					'handler' => 'metatag_global_token_site_lancode',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:language'           => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_09,
					'handler' => 'metatag_global_token_site_language',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:fb-app-id'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_10,
					'handler' => 'metatag_global_token_site_fb_app_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:admin:name'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_11,
					'handler' => 'metatag_global_token_site_admin_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:admin:email'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_12,
					'handler' => 'metatag_global_token_site_admin_email',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:current-page:title' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_13,
					'handler' => 'metatag_global_token_site_current_page_title',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				'site:current-page:url'   => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_14,
					'handler' => 'metatag_global_token_site_current_page_url',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.global.php',
				),
				// TODO - more tokens.
			),
			'default' => array(
				'title'        => '{site:current-page:title} | {site:name}',
				'description'  => '{site:description}',
				'generator'    => 'e107 v2 (http://e107.org)',
				'canonical'    => '{site:current-page:url}',
				'fb:app_id'    => '{site:fb-app-id}',
				'og:site_name' => '{site:name}',
				'og:url'       => '{site:current-page:url}',
				'og:title'     => '{site:current-page:title}',
			),
			'tab'     => false,
		);

		// Front page.
		$config['front'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_02,
			'detect'  => 'metatag_entity_front_detect',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.front.php',
			'default' => array(
				'title' => '{site:name}',
			),
		);

		// News - List page.
		$config['news_list'] = array(
			'name'   => LAN_PLUGIN_METATAG_TYPE_05,
			'detect' => 'metatag_entity_news_list_detect',
			'file'   => '{e_PLUGIN}metatag/includes/metatag.news.php',
		);

		// News - Category page.
		$config['news-category'] = array(
			'name'   => LAN_PLUGIN_METATAG_TYPE_06,
			'detect' => 'metatag_entity_news_category_detect',
			'load'   => 'metatag_entity_news_category_load',
			'file'   => '{e_PLUGIN}metatag/includes/metatag.news.php',
			'token'  => array(
				'news:category:id'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_15,
					'handler' => 'metatag_entity_news_token_category_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:name'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_16,
					'handler' => 'metatag_entity_news_token_category_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:description' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_17,
					'handler' => 'metatag_entity_news_token_category_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:keywords'    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_18,
					'handler' => 'metatag_entity_news_token_category_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
			),
			'tab'    => false,
		);

		// News - Tag page.
		$config['news_tag'] = array(
			'name'   => LAN_PLUGIN_METATAG_TYPE_07,
			'detect' => 'metatag_entity_news_tag_detect',
			'load'   => 'metatag_entity_news_tag_load',
			'file'   => '{e_PLUGIN}metatag/includes/metatag.news.php',
			'token'  => array(
				'news:tag:name' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_19,
					'handler' => 'metatag_entity_news_token_tag_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
			),
		);

		// News - Extended page (News item).
		$config['news'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_03,
			'detect'  => 'metatag_entity_news_detect',
			'load'    => 'metatag_entity_news_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
			'token'   => array(
				'news:id'                   => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_20,
					'handler' => 'metatag_entity_news_token_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:title'                => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_21,
					'handler' => 'metatag_entity_news_token_title',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:summary'              => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_22,
					'handler' => 'metatag_entity_news_token_summary',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail'            => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_23,
					'handler' => 'metatag_entity_news_token_thumbnail',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail:first'      => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_24,
					'handler' => 'metatag_entity_news_token_thumbnail_first',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail:og'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_25,
					'handler' => 'metatag_entity_news_token_thumbnail_og',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:thumbnail:first:og'   => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_26,
					'handler' => 'metatag_entity_news_token_thumbnail_first_og',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:username'      => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_27,
					'handler' => 'metatag_entity_news_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:display'       => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_28,
					'handler' => 'metatag_entity_news_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:author:real'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_29,
					'handler' => 'metatag_entity_news_token_author_real',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:short'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_30,
					'handler' => 'metatag_entity_news_token_created_short',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:long'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_31,
					'handler' => 'metatag_entity_news_token_created_long',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:forum'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_32,
					'handler' => 'metatag_entity_news_token_created_forum',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:created:utc'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_33,
					'handler' => 'metatag_entity_news_token_created_utc',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:id'          => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_34,
					'handler' => 'metatag_entity_news_token_category_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:name'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_35,
					'handler' => 'metatag_entity_news_token_category_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:description' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_36,
					'handler' => 'metatag_entity_news_token_category_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'news:category:keywords'    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_37,
					'handler' => 'metatag_entity_news_token_category_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
			),
			'default' => array(
				'title'                  => '{news:title}',
				'description'            => '{news:summary}',
				'image_src'              => '{news:thumbnail:first}',
				'og:title'               => '{news:title}',
				'og:type'                => 'article',
				'og:description'         => '{news:summary}',
				'og:image'               => '{news:thumbnail:og}',
				'article:published_time' => '{news:created:utc}',
				'itemprop:name'          => '{news:title}',
				'itemprop:description'   => '{news:summary}',
				'itemprop:image'         => '{news:thumbnail:first}',
			),
		);

		// Page - List Books
		$config['page_list_books'] = array(
			'name'   => LAN_PLUGIN_METATAG_TYPE_08,
			'detect' => 'metatag_entity_page_list_books_detect',
			'file'   => '{e_PLUGIN}metatag/includes/metatag.page.php',
		);

		// Page - List Chapters within a specific Book
		$config['page_list_chapters'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_09,
			'detect'  => 'metatag_entity_page_list_chapters_detect',
			'load'    => 'metatag_entity_page_list_chapters_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
			'token'   => array(
				'page:book:name'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_38,
					'handler' => 'metatag_entity_page_token_book_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:book:description' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_39,
					'handler' => 'metatag_entity_page_token_book_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:book:keywords'    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_40,
					'handler' => 'metatag_entity_page_token_book_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
			),
			'default' => array(
				'title'                => '{page:book:name}',
				'description'          => '{page:book:description}',
				'og:title'             => '{page:book:name}',
				'og:description'       => '{page:book:description}',
				'itemprop:name'        => '{page:book:name}',
				'itemprop:description' => '{page:book:description}',
			),
		);

		// Page - List Pages within a specific Chapter
		$config['page_list_pages'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_10,
			'detect'  => 'metatag_entity_page_list_pages_detect',
			'load'    => 'metatag_entity_page_list_pages_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
			'token'   => array(
				'page:chapter:name'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_41,
					'handler' => 'metatag_entity_page_token_chapter_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:chapter:description' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_42,
					'handler' => 'metatag_entity_page_token_chapter_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:chapter:keywords'    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_43,
					'handler' => 'metatag_entity_page_token_chapter_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
			),
			'default' => array(
				'title'                => '{page:chapter:name}',
				'description'          => '{page:chapter:description}',
				'og:title'             => '{page:chapter:name}',
				'og:description'       => '{page:chapter:description}',
				'itemprop:name'        => '{page:chapter:name}',
				'itemprop:description' => '{page:chapter:description}',
			),
		);

		// Page - Page item
		$config['page'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_04,
			'detect'  => 'metatag_entity_page_detect',
			'load'    => 'metatag_entity_page_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
			'token'   => array(
				'page:id'                  => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_44,
					'handler' => 'metatag_entity_page_token_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:title'               => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_45,
					'handler' => 'metatag_entity_page_token_title',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:body'                => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_46,
					'handler' => 'metatag_entity_page_token_body',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:description'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_47,
					'handler' => 'metatag_entity_page_token_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:keywords'            => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_48,
					'handler' => 'metatag_entity_page_token_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:short'       => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_49,
					'handler' => 'metatag_entity_page_token_created_short',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:long'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_50,
					'handler' => 'metatag_entity_page_token_created_long',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:forum'       => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_51,
					'handler' => 'metatag_entity_page_token_created_forum',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:created:utc'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_52,
					'handler' => 'metatag_entity_page_token_created_utc',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:username'     => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_53,
					'handler' => 'metatag_entity_page_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:display'      => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_54,
					'handler' => 'metatag_entity_page_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:real'         => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_55,
					'handler' => 'metatag_entity_page_token_author_real',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:chapter:name'        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_56,
					'handler' => 'metatag_entity_page_token_chapter_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:chapter:description' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_57,
					'handler' => 'metatag_entity_page_token_chapter_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:chapter:keywords'    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_58,
					'handler' => 'metatag_entity_page_token_chapter_keywords',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
			),
			'default' => array(
				'title'                  => '{page:title}',
				'description'            => '{page:description}',
				'og:title'               => '{page:title}',
				'og:type'                => 'article',
				'og:description'         => '{page:description}',
				'article:published_time' => '{page:created:utc}',
				'itemprop:name'          => '{page:title}',
				'itemprop:description'   => '{page:description}',
			),
		);

		// Download - Category list page.
		$config['download-category-list'] = array(
			'name'   => LAN_PLUGIN_METATAG_TYPE_11,
			'detect' => 'metatag_entity_download_category_list_detect',
			'load'   => 'metatag_entity_download_category_list_load',
			'file'   => '{e_PLUGIN}metatag/includes/metatag.download.php',
			'tab'    => false,
		);

		// Download - Category page.
		$config['download_category'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_12,
			'detect'  => 'metatag_entity_download_category_detect',
			'load'    => 'metatag_entity_download_category_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
			'token'   => array(
				'download:category:id'                    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_59,
					'handler' => 'metatag_entity_download_category_token_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:name'                  => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_60,
					'handler' => 'metatag_entity_download_category_token_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:description'           => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_61,
					'handler' => 'metatag_entity_download_category_token_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:description:truncated' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_65,
					'handler' => 'metatag_entity_download_category_token_description_truncated',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
			),
			'default' => array(
				'title'                => '{download:category:name}',
				'description'          => '{download:category:description:truncated}',
				'og:title'             => '{download:category:name}',
				'og:description'       => '{download:category:description:truncated}',
				'itemprop:name'        => '{download:category:name}',
				'itemprop:description' => '{download:category:description:truncated}',
			),
			'tab'     => false,
		);

		// Download - Download item.
		$config['download'] = array(
			'name'    => LAN_PLUGIN_METATAG_TYPE_13,
			'detect'  => 'metatag_entity_download_detect',
			'load'    => 'metatag_entity_download_load',
			'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
			'token'   => array(
				'download:item:id'                        => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_62,
					'handler' => 'metatag_entity_download_item_token_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:item:name'                      => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_63,
					'handler' => 'metatag_entity_download_item_token_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:item:description'               => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_64,
					'handler' => 'metatag_entity_download_item_token_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:item:description:truncated'     => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_66,
					'handler' => 'metatag_entity_download_item_token_description_truncated',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:id'                    => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_59,
					'handler' => 'metatag_entity_download_category_token_id',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:name'                  => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_60,
					'handler' => 'metatag_entity_download_category_token_name',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:description'           => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_61,
					'handler' => 'metatag_entity_download_category_token_description',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
				'download:category:description:truncated' => array(
					'help'    => LAN_PLUGIN_METATAG_TOKEN_65,
					'handler' => 'metatag_entity_download_category_token_description_truncated',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.download.php',
				),
			),
			'default' => array(
				'title'                => '{download:item:name}',
				'description'          => '{download:item:description:truncated}',
				'og:title'             => '{download:item:name}',
				'og:description'       => '{download:item:description:truncated}',
				'itemprop:name'        => '{download:item:name}',
				'itemprop:description' => '{download:item:description:truncated}',
			),
			'tab'     => false,
		);

		return $config;
	}

	// TODO - method for altering config array.

}
