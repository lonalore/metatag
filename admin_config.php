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
		'main/list' => array(
			'caption' => LAN_METATAG_ADMIN_UI_01,
			'perm'    => 'P',
		),
		/*
		'main/create' => array(
			'caption' => LAN_METATAG_ADMIN_UI_01,
			'perm'    => 'P',
		),
		*/
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
		$meta->prepareDefaultTypes();
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
	protected $listOrder = 'id, name';

	protected $sortField = 'id';

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

	/**
	 * User defined init.
	 */
	public function init()
	{

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

		$new_data['data'] = base64_encode(serialize($data));

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

	}

	/**
	 * User defined pre-delete logic.
	 */
	public function beforeDelete($data, $id)
	{
		if((int) $id > 0)
		{
			$meta = new metatag();
			$config = $meta->getAddonConfig();

			$type = $data['type'];
			$entityDefaults = varset($config[$type]['default'], array());

			$update = array(
				'data'  => array(
					'name' => varset($config[$type]['name'], ''),
					'data' => base64_encode(serialize($entityDefaults)),
				),
				'WHERE' => 'id = "' . (int) $id . '"',
			);
			e107::getDb()->update('metatag_default', $update, false);
		}

		// Cancel deletion.
		return false;
	}

	/**
	 * User defined after-delete logic.
	 */
	public function afterDelete($deleted_data, $id, $deleted_check)
	{
		// If this doesn't return with TRUE, "admin_metatag_default_deleted" event won't be fired.
		return true;
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

			$editClass = false;
			$deleteClass = false;

			if(varset($parms['editClass']))
			{
				$editClass = (deftrue($parms['editClass'])) ? constant($parms['editClass']) : $parms['editClass'];
			}

			if(($editClass === false || check_class($editClass)) && varset($parms['edit'], 1) == 1)
			{
				parse_str(str_replace('&amp;', '&', e_QUERY), $query);

				$query['action'] = 'edit';
				$query['id'] = $id;

				$query = http_build_query($query);

				$link = array(
					'href'           => e_SELF . '?' . $query,
					'class'          => 'btn btn-default',
					'title'          => LAN_EDIT,
					'data-toggle'    => 'tooltip',
					'data-placement' => 'left',
				);

				$link_attributes = '';
				foreach($link as $name => $val)
				{
					$link_attributes .= ' ' . $name . '="' . $val . '"';
				}

				$html .= '<a' . $link_attributes . '>' . $tp->toGlyph('fa-edit') . '</a>';
			}

			if(varset($parms['deleteClass']))
			{
				$deleteClass = (deftrue($parms['deleteClass'])) ? constant($parms['deleteClass']) : $parms['deleteClass'];
			}

			if(($deleteClass === false || check_class($deleteClass)) && varset($parms['delete'], 1) == 1)
			{
				$name = 'etrigger_delete[' . $id . ']';

				$options = $this->format_options('submit_image', $name, array(
					'class' => 'action delete btn btn-default',
				));
				$options['title'] = LAN_METATAG_ADMIN_UI_04;
				$options['data-toggle'] = 'tooltip';
				$options['data-placement'] = 'left';
				$options['data-confirm'] = LAN_METATAG_ADMIN_UI_05;

				$delete_attributes = $this->get_attributes($options, $name, $value);

				$html .= '<button type="submit" name="' . $name . '" value="' . $id . '"' . $delete_attributes . '>' . $tp->toIcon('fa-undo') . '</button>';
			}

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
