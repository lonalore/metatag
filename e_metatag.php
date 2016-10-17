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

		$config['front'] = array(
			// Human-readable name for this entity.
			'entityName'   => LAN_PLUGIN_METATAG_TYPE_02,
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
			'entityDetect' => 'metatag_entity_front_detect',
			// Path for the file, which contains entityDetect function.
			'entityFile'   => '{e_PLUGIN}metatag/includes/metatag.front.php',
		);

		$config['news'] = array(
			// Human-readable name for this entity.
			'entityName'   => LAN_PLUGIN_METATAG_TYPE_03,
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
			'entityDetect' => 'metatag_entity_news_detect',
			// Callback function to load entity from database in case of entityDetect
			// returns with ID, and entityTokens are provided.
			'entityQuery'  => 'metatag_entity_news_load',
			// Path for the file, which contains the entityDetect and entityQuery functions.
			'entityFile'   => '{e_PLUGIN}metatag/includes/metatag.news.php',
			// Tokens can be used for this entity.
			// FIXME - use LANs.
			'entityTokens' => array(
				'news:author:username' => array(
					'help'    => 'The username of author of the news item.',
					'handler' => 'metatag_entity_news_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'page:author:display'  => array(
					'help'    => 'The display name of author of the news item.',
					'handler' => 'metatag_entity_news_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.news.php',
				),
				'page:author:real'     => array(
					'help'    => 'The real name of author of the news item.',
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
		);

		// Page entity.
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
					'help'    => 'The username of author of the page.',
					'handler' => 'metatag_entity_page_token_author_username',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:display'  => array(
					'help'    => 'The display name of author of the page.',
					'handler' => 'metatag_entity_page_token_author_display',
					'file'    => '{e_PLUGIN}metatag/includes/metatag.page.php',
				),
				'page:author:real'     => array(
					'help'    => 'The real name of author of the page.',
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
