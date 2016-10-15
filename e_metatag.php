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
class metatag_metatag {

	/**
	 * Provides information about metatag handlers.
	 *
	 * @return array $config
	 *  An associative array whose keys are the event trigger names used by Admin UIs.
	 *
	 *  @See $eventName in class e_admin_ui.
	 */
	public function config() {
		$config = array();

		// Not a real type, only for rendering widget on Admin UI of metatag plugin.
		$config['metatag_default'] = array(
			'callback' => false,
		);

		$config['news'] = array(
			// Callback function to implement logic for detecting news pages.
			// Callback function must return the primary ID of news item in that case if
			// a news page is detected successfully. Otherwise it must return false.
			// If your callback function is a class::method, you have to provide an array
			// whose first element is the class name and the second is the method.
			// If your callback is a simple function, you have to provide a string instead
			// of an array.
			'callback' => array('metatag', 'currentPathIsNewsItem'),
			// Path for the file, which contains the callback function.
			'file' => 'includes/metatag.class.php',
		);

		return $config;
	}

}
