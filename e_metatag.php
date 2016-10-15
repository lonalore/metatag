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

		// Not a real type, only for rendering widget.
		$config['metatag_default'] = array(
			'callback' => false,
		);

		$config['news'] = array(
			'callback' => array('metatag', 'currentPathIsNewsItem'),
			'file' => 'includes/metatag.class.php',
		);

		return $config;
	}

}
