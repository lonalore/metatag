<?php

/**
 * @file
 * Admin UI.
 */

require_once("../../class2.php");

if(!e107::isInstalled('metatag') || !getperms("P"))
{
	e107::redirect(e_BASE . 'index.php');
}

e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);


/**
 * Class metatag_admin.
 */
class metatag_admin_config extends e_admin_dispatcher
{

	/**
	 * Required (set by child class).
	 *
	 * Controller map array in format.
	 * @code
	 *  'MODE' => array(
	 *      'controller' =>'CONTROLLER_CLASS_NAME',
	 *      'path' => 'CONTROLLER SCRIPT PATH',
	 *      'ui' => 'UI_CLASS', // extend of 'comments_admin_form_ui'
	 *      'uipath' => 'path/to/ui/',
	 *  );
	 * @endcode
	 *
	 * @var array
	 */
	protected $modes = array(
		'ajax' => array(
			'controller' => 'metatag_admin_ajax_ui',
		),
		'main' => array(
			'controller' => 'metatag_admin_ui',
			'path'       => null,
			'ui'         => 'metatag_admin_form_ui',
			'uipath'     => null
		),
	);

	/**
	 * Optional (set by child class).
	 *
	 * Required for admin menu render. Format:
	 * @code
	 *  'mode/action' => array(
	 *      'caption' => 'Link title',
	 *      'perm' => '0',
	 *      'url' => '{e_PLUGIN}plugname/admin_config.php',
	 *      ...
	 *  );
	 * @endcode
	 *
	 * Note that 'perm' and 'userclass' restrictions are inherited from the $modes, $access and $perm, so you don't
	 * have to set that vars if you don't need any additional 'visual' control.
	 *
	 * All valid key-value pair (see e107::getNav()->admin function) are accepted.
	 *
	 * @var array
	 */
	protected $adminMenu = array(
		'main/list'  => array(
			'caption' => LAN_METATAG_ADMIN_UI_01,
			'perm'    => 'P',
		),
		'opt1'       => array(
			'divider' => true,
		),
		'main/prefs' => array(
			'caption' => LAN_METATAG_ADMIN_UI_09,
			'perm'    => 'P',
		),
	);

	/**
	 * Optional (set by child class).
	 *
	 * @var string
	 */
	protected $menuTitle = LAN_PLUGIN_METATAG_NAME;

	/**
	 * User defined constructor - called before _initController() method.
	 */
	public function init()
	{
		$meta = new metatag();
		$meta->prepareDefaultTypes(true);
	}

}


/**
 * Class metatag_admin_ajax_ui.
 */
class metatag_admin_ajax_ui extends e_admin_ui
{

	/**
	 * Initial function.
	 */
	public function init()
	{
		// Construct action string.
		$action = varset($_GET['mode']) . '/' . varset($_GET['action']);

		switch($action)
		{
			case 'ajax/revert':
				$this->ajaxRevert();
				break;

			case 'ajax/cache':
				$this->ajaxCache();
				break;
		}
	}

	/**
	 * Ajax Request handler.
	 */
	public function ajaxRevert()
	{
		$id = (int) varset($_GET['id'], 0);

		if($id > 0)
		{
			$db = e107::getDb();

			$type = $db->retrieve('metatag_default', 'type', 'id = ' . $id);

			if(is_string($type))
			{
				$meta = new metatag();
				$config = $meta->getAddonConfig();
				$entityDefaults = varset($config[$type]['default'], array());

				$update = array(
					'data'  => array(
						'name' => varset($config[$type]['name'], ''),
						'data' => e107::serialize($entityDefaults),
					),
					'WHERE' => 'id = "' . (int) $id . '"',
				);
				$db->update('metatag_default', $update, false);

				if($type == 'metatag_default')
				{
					$meta->clearCacheAll();
				}
				else
				{
					$meta->clearCacheByType($type);
				}
			}
		}

		$ajax = e107::getAjax();
		$commands = array();
		$commands[] = $ajax->commandInvoke('#uiModal', 'modal', array('hide'));
		$ajax->response($commands);
		exit;
	}

	/**
	 * Ajax Request handler.
	 */
	public function ajaxCache()
	{
		$id = (int) varset($_GET['id'], 0);

		if($id > 0)
		{
			$db = e107::getDb();
			$type = $db->retrieve('metatag_default', 'type', 'id = ' . $id);

			if(is_string($type))
			{
				$meta = new metatag();

				if($type == 'metatag_default')
				{
					$meta->clearCacheAll();
				}
				else
				{
					$meta->clearCacheByType($type);
				}
			}
		}

		$ajax = e107::getAjax();
		$commands = array();
		$commands[] = $ajax->commandInvoke('#uiModal', 'modal', array('hide'));
		$ajax->response($commands);
		exit;
	}

}


/**
 * Class metatag_admin_ui.
 */
class metatag_admin_ui extends e_admin_ui
{

	/**
	 * Could be LAN constant (multi-language support).
	 *
	 * @var string plugin name
	 */
	protected $pluginTitle = LAN_PLUGIN_METATAG_NAME;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $pluginName = "metatag";


	/**
	 * Base event trigger name to be used. Leave blank for no trigger.
	 *
	 * @var string event name
	 */
	protected $eventName = 'metatag_default';

	protected $table = "metatag_default";

	protected $pid = "id";

	/**
	 * Default (db) limit value.
	 *
	 * @var integer
	 */
	protected $perPage = 0;

	/**
	 * @var boolean
	 */
	protected $batchDelete = false;

	/**
	 * @var string SQL order, false to disable order, null is default order
	 */
	protected $listOrder = 'name';

	protected $sortField = 'name';

	protected $sortParent = 'parent';

	protected $tabs = array(
		LAN_METATAG_ADMIN_TAB_01,
	);

	/**
	 * @var array UI field data
	 */
	protected $fields = array(
		'name'    => array(
			'title'    => LAN_METATAG_ADMIN_UI_03,
			'type'     => 'method',
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => true,
			'inline'   => false,
		),
		'type'    => array(
			'title'    => LAN_METATAG_ADMIN_UI_02,
			'type'     => 'hidden',
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => true,
			'inline'   => false,
		),
		'parent'  => array(
			'type'     => 'hidden',
			'readonly' => true,
			'inline'   => false,
		),
		'data'    => array(
			'type'     => 'hidden',
			'readonly' => true,
			'inline'   => false,
		),
		'options' => array(
			'type'    => 'method',
			'width'   => '10%',
			'forced'  => true,
			'thclass' => 'center last',
			'class'   => 'center',
			'sort'    => false,
		),
	);

	/**
	 * @var array default fields activated on List view
	 */
	protected $fieldpref = array(
		'name',
		'options',
	);

	/**
	 * @var array
	 */
	protected $afterSubmitOptions = array(
		'list' => LAN_METATAG_ADMIN_UI_06,
		'edit' => LAN_METATAG_ADMIN_UI_07,
	);

	protected $preftabs = array(
		LAN_METATAG_ADMIN_UI_09,
	);

	protected $prefs = array(
		'cache_expire'   => array(
			'title'      => LAN_METATAG_ADMIN_UI_10,
			'type'       => 'dropdown',
			'data'       => 'int',
			'writeParms' => array(
				0        => LAN_METATAG_ADMIN_UI_11,
				86400    => LAN_METATAG_ADMIN_UI_12,
				604800   => LAN_METATAG_ADMIN_UI_13,
				2629000  => LAN_METATAG_ADMIN_UI_14,
				31536000 => LAN_METATAG_ADMIN_UI_15,
			),
			'tab'        => 0,
		),
		'cache_disabled' => array(
			'title'      => LAN_METATAG_ADMIN_UI_23,
			'type'       => 'dropdown',
			'data'       => 'int',
			'writeParms' => array(
				0 => LAN_NO,
				1 => LAN_YES,
			),
			'tab'        => 0,
		),
	);

	/**
	 * User defined init.
	 */
	public function init()
	{
		e107::css('metatag', 'css/metatag.css');
		e107::js('metatag', 'js/metatag.js');
	}

	/**
	 * User defined pre-create logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 *
	 * @return boolean
	 */
	public function beforeCreate($new_data, $old_data)
	{

	}

	/**
	 * User defined after-create logic.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 * @param $id
	 */
	public function afterCreate($new_data, $old_data, $id)
	{

	}

	/**
	 * User defined pre-update logic, return false to prevent DB query execution.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 * @return mixed
	 */
	public function beforeUpdate($new_data, $old_data)
	{
		$data = array();

		foreach($new_data['x_metatag_metatags'] as $key => $value)
		{
			$data[$key] = $value;
		}

		if($new_data['type'] != 'metatag_default')
		{
			$meta = new metatag();
			$global = $meta->getGlobalMetaTags();

			// Filter only values, which differ from the default/global ones.
			foreach($data as $key => $value)
			{
				if(isset($global[$key]) && $global[$key] == $value)
				{
					// Unset value, so we will use top level (global) value.
					unset($data[$key]);
				}
			}
		}

		// Unset empty values.
		foreach($new_data['data'] as $key => $value)
		{
			if(empty($value))
			{
				unset($new_data['data'][$key]);
			}
		}

		$new_data['data'] = e107::serialize($data);

		return $new_data;
	}

	/**
	 * User defined after-update logic.
	 *
	 * @param $new_data
	 *  Posted data.
	 * @param $old_data
	 */
	public function afterUpdate($new_data, $old_data, $id)
	{
		$meta = new metatag();

		if($new_data['type'] == 'metatag_default')
		{
			$meta->clearCacheAll();
		}
		else
		{
			$meta->clearCacheByType($new_data['type']);
		}
	}

	/**
	 * User defined pre-delete logic.
	 */
	public function beforeDelete($data, $id)
	{
		// Cancel deletion.
		return false;
	}

	/**
	 * User defined after-delete logic.
	 */
	public function afterDelete($deleted_data, $id, $deleted_check)
	{
		// If this doesn't return with TRUE, "admin_metatag_default_deleted" event won't be fired.
		return false;
	}

}


/**
 * Class metatag_admin_form_ui.
 */
class metatag_admin_form_ui extends e_admin_form_ui
{

	/**
	 * Get item's name.
	 *
	 * @param $curVal
	 * @param $mode
	 * @param $parm
	 *
	 * @return string
	 */
	function name($curVal, $mode, $parm)
	{
		if($mode == 'read')
		{
			$listModel = $this->getController()->getListModel();

			if($listModel)
			{
				$parent = $listModel->get('parent');

				if($parent > 0)
				{
					$prefix = '<img src="' . e_IMAGE_ABS . 'generic/branchbottom.gif" class="icon" alt="" style="margin-left: 20px" />&nbsp;';
					return $prefix . $curVal;
				}
			}
		}

		return $curVal;
	}

	/**
	 * Override the default Options field.
	 *
	 * @param $parms
	 * @param $value
	 * @param $id
	 * @param $attributes
	 *
	 * @return string
	 */
	function options($parms, $value, $id, $attributes)
	{
		$html = '';

		if($attributes['mode'] == 'read')
		{
			$tp = e107::getParser();

			// Edit button.
			parse_str(str_replace('&amp;', '&', e_QUERY), $query);
			$query['action'] = 'edit';
			$query['id'] = $id;
			$query = http_build_query($query);
			$link = array(
				'href'           => e_SELF . '?' . $query,
				'class'          => 'btn btn-default action edit',
				'title'          => LAN_EDIT,
				'data-toggle'    => 'tooltip',
				'data-placement' => 'top',
				'data-animation' => 'false',
			);
			$link_attributes = '';
			foreach($link as $name => $val)
			{
				$link_attributes .= ' ' . $name . '="' . $val . '"';
			}
			$html .= '<a' . $link_attributes . '>' . $tp->toGlyph('fa-edit') . '</a>';


			// Revert button.
			parse_str(str_replace('&amp;', '&', e_QUERY), $query);
			$query['mode'] = 'ajax';
			$query['action'] = 'revert';
			$query['id'] = $id;
			$query = http_build_query($query);
			$link = array(
				'href'                 => '#',
				'class'                => 'btn btn-default action revert',
				'title'                => LAN_METATAG_ADMIN_UI_04,
				'data-toggle'          => 'tooltip',
				'data-placement'       => 'top',
				'data-animation'       => 'false',
				'data-confirm-title'   => LAN_METATAG_ADMIN_UI_16,
				'data-confirm-message' => LAN_METATAG_ADMIN_UI_05,
				'data-confirm-yes'     => LAN_METATAG_ADMIN_UI_17,
				'data-confirm-no'      => LAN_METATAG_ADMIN_UI_18,
				'data-confirm-url'     => e_SELF . '?' . $query,
			);
			$link_attributes = '';
			foreach($link as $name => $val)
			{
				$link_attributes .= ' ' . $name . '="' . $val . '"';
			}
			$html .= '<a' . $link_attributes . '>' . $tp->toGlyph('fa-undo') . '</a>';


			// Cache button.
			parse_str(str_replace('&amp;', '&', e_QUERY), $query);
			$query['mode'] = 'ajax';
			$query['action'] = 'cache';
			$query['id'] = $id;
			$query = http_build_query($query);
			$link = array(
				'href'                 => '#',
				'class'                => 'btn btn-default action cache',
				'title'                => LAN_METATAG_ADMIN_UI_08,
				'data-toggle'          => 'tooltip',
				'data-placement'       => 'top',
				'data-animation'       => 'false',
				'data-confirm-title'   => LAN_METATAG_ADMIN_UI_08,
				'data-confirm-message' => LAN_METATAG_ADMIN_UI_21,
				'data-confirm-yes'     => LAN_METATAG_ADMIN_UI_19,
				'data-confirm-no'      => LAN_METATAG_ADMIN_UI_18,
				'data-confirm-url'     => e_SELF . '?' . $query,
			);
			if($id == 1)
			{
				$link['data-confirm-message'] = LAN_METATAG_ADMIN_UI_20;
				$link['data-confirm-yes'] = LAN_METATAG_ADMIN_UI_22;
			}
			$link_attributes = '';
			foreach($link as $name => $val)
			{
				$link_attributes .= ' ' . $name . '="' . $val . '"';
			}
			$html .= '<a' . $link_attributes . '>' . $tp->toGlyph('fa-database') . '</a>';

			$html = '<div class="btn-group">' . $html . '</div>';
		}

		return $html;
	}

}


new metatag_admin_config();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
