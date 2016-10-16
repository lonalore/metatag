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
			'name'         => LAN_PLUGIN_METATAG_TYPE_02,
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
			'entityDetect' => array('metatag', 'currentPathIsFrontPage'),
			// Path for the file, which contains the callback functions.
			'file'         => 'includes/metatag.class.php',
		);

		$config['news'] = array(
			// Human-readable name for this entity.
			'name'         => LAN_PLUGIN_METATAG_TYPE_03,
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
			'entityDetect' => array('metatag', 'currentPathIsNewsItem'),
			// Callback function to load entity from database in case of entityDetect
			// returns with ID, and entityTokens are provided.
			'entityQuery'  => array('metatag', 'loadNewsItem'),
			// Tokens can be used for this entity.
			// FIXME - use LANs.
			'entityTokens' => array(
				'news:author'  => array(
					'help'    => 'The author of the news item.',
					'handler' => array('metatag', 'tokensNewsAuthor'),
				),
				'news:created' => array(
					'help'    => 'The date the news item was created.',
					'handler' => array('metatag', 'tokensNewsCreated'),
				),
				// TODO - more tokens.
			),
			// Path for the file, which contains the callback functions.
			'file'         => 'includes/metatag.class.php',
		);

		// Page entity.
		$config['page'] = array(
			// Human-readable name for this entity.
			'name'         => LAN_PLUGIN_METATAG_TYPE_04,
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
			'entityDetect' => array('metatag', 'currentPathIsPage'),
			// Callback function to load entity from database in case of entityDetect
			// returns with ID, and entityTokens are provided.
			'entityQuery'  => array('metatag', 'loadPageItem'),
			// Tokens can be used for this entity.
			// FIXME - use LANs.
			'entityTokens' => array(
				'page:author'  => array(
					'help'    => 'The author of the page.',
					'handler' => array('metatag', 'tokensPageAuthor'),
				),
				'page:created' => array(
					'help'    => 'The date the page was created.',
					'handler' => array('metatag', 'tokensPageCreated'),
				),
				// TODO - more tokens.
			),
			// Path for the file, which contains the callback functions.
			'file'         => 'includes/metatag.class.php',
		);

		return $config;
	}

	// TODO - method for altering config array.

}
