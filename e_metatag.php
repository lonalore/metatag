<?php

/**
 * @file
 * Metatag addon file.
 */


/**
 * Class metatag_metatag.
 *
 * Usage: PLUGIN_metatg
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
			'name'     => LAN_PLUGIN_METATAG_TYPE_02,
			/**
			 * Callback function to determine current path is the front page, or not. In
			 * this case, ENTITY is the front page.
			 *
			 * Callback function to implement logic for detecting ENTITY path. ENTITY can
			 * be news, page, etc.
			 * If your callback function is a class::method, you have to provide an array
			 * whose first element is the class name and the second is the method.
			 * If your callback is a simple function, you have to provide a string instead
			 * of an array.
			 * If your callback function returns with false, it means that current path is
			 * not an ENTITY path.
			 * If your callback function returns with true, it means that current path is
			 * an ENTITY path, and ENTITY does not have custom instances, so default meta
			 * tags will be loaded for the ENTITY.
			 * If your callback function returns with a primary id (e.g. a News ID), it
			 * means that current path is an ENTITY path, and need to load metatags for a
			 * specific ENTITY item.
			 */
			'callback' => array('metatag', 'currentPathIsFrontPage'),
			// Path for the file, which contains the callback function.
			'file'     => 'includes/metatag.class.php',
		);

		$config['news'] = array(
			'name'     => LAN_PLUGIN_METATAG_TYPE_03,
			/**
			 * Callback function to determine current path is a news page, or not. In
			 * this case, ENTITY is a news item.
			 *
			 * Callback function to implement logic for detecting ENTITY path. ENTITY can
			 * be news, page, etc.
			 * If your callback function is a class::method, you have to provide an array
			 * whose first element is the class name and the second is the method.
			 * If your callback is a simple function, you have to provide a string instead
			 * of an array.
			 * If your callback function returns with false, it means that current path is
			 * not an ENTITY path.
			 * If your callback function returns with true, it means that current path is
			 * an ENTITY path, and ENTITY does not have custom instances, so default meta
			 * tags will be loaded for the ENTITY.
			 * If your callback function returns with a primary id (e.g. a News ID), it
			 * means that current path is an ENTITY path, and need to load metatags for a
			 * specific ENTITY item.
			 */
			'callback' => array('metatag', 'currentPathIsNewsItem'),
			// Path for the file, which contains the callback function.
			'file'     => 'includes/metatag.class.php',
		);

		return $config;
	}

}
