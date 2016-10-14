<?php

/**
 * @file
 * Contains class metatag_admin for extending admin areas.
 */

e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);


/**
 * Class metatag_admin.
 *
 * v2.x Standard for extending admin areas.
 */
class metatag_admin extends metatag
{

	/**
	 * Extend Admin-ui Parameters.
	 *
	 * @param object $ui
	 *  Admin UI object.
	 *
	 * @return array
	 */
	public function config($ui)
	{
		// Event name, e.g: 'wmessage', 'news' etc. (core or plugin).
		$type = $ui->getEventName();
		// Current mode, e.g: 'create', 'edit', 'list'.
		$action = $ui->getAction();
		// Primary ID of the record being created/edited/deleted.
		$id = $ui->getId();

		return $this->getWidgetConfig($type, $action, $id);
	}


	/**
	 * Process Posted Data.
	 *
	 * @param object $ui
	 *  Admin UI object.
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 */
	public function process($ui, $id = 0)
	{
		// Contains posted data.
		$data = $ui->getPosted();
		// Event name, e.g: 'wmessage', 'news' etc. (core or plugin).
		$type = $ui->getEventName();
		// Current mode, e.g: 'create', 'edit', 'list'.
		$action = $ui->getAction();

		$this->processWidgetData($type, $action, $id, $data);
	}

}


/**
 * Class metatag_admin_form.
 */
class metatag_admin_form extends e_form
{

	/**
	 * Metatags field widget.
	 *
	 * @param $curval
	 * @param $mode
	 * @param $att
	 *
	 * @return string
	 */
	function x_metatag_metatags($curval, $mode, $att)
	{
		$meta = new metatag();
		return $meta->getWidget($curval);
	}

}
