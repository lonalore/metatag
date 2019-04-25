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

		$config = $this->getWidgetConfig($type, $action, $id);
		$addonConfig = $this->getAddonConfig();

		if(varset($addonConfig[$type]['tab'], true) === false)
		{
			$config['tabs'] = array();

			foreach($config['fields'] as $key => $value)
			{
				if(isset($value['tab']))
				{
					$config['fields'][$key]['tab'] = 0;
				}
			}
		}

		// TODO check if Admin UI has tabs and create "General" tabs if not, then append "Metatag" tab.

//		if(varset($addonConfig[$type]['tab'], true) === false)
//		{
//			$config['tabs'] = array(
//				0         => LAN_GENERAL,
//				'metatag' => LAN_PLUGIN_METATAG_TAB,
//			);
//
//			foreach($config['fields'] as $key => $value)
//			{
//				if(isset($value['tab']))
//				{
//					// $config['fields'][$key]['tab'] = 0;
//				}
//			}
//		}

		return $config;
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

		$this->processWidgetData($id, $type, $action, $data);
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
