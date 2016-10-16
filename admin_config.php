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
	protected $listOrder = 'id';

	protected $sortField = 'id';

	protected $sortParent = 'parent';

	protected $tabs = array(
		LAN_METATAG_ADMIN_TAB_01,
	);

	/**
	 * @var array UI field data
	 */
	protected $fields = array(
		'checkboxes' => array(
			'title'   => '',
			'type'    => null,
			'width'   => '5%',
			'forced'  => true,
			'thclass' => 'center',
			'class'   => 'center',
		),
		'name'       => array(
			'title'    => LAN_METATAG_ADMIN_UI_03,
			'type'     => 'method',
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => true,
			'inline'   => false,
		),
		'type'       => array(
			'title'    => LAN_METATAG_ADMIN_UI_02,
			'type'     => 'text',
			'width'    => 'auto',
			'thclass'  => 'left',
			'readonly' => true,
			'inline'   => false,
		),
		'parent'     => array(
			'type'     => 'hidden',
			'readonly' => true,
			'inline'   => false,
		),
		'data'       => array(
			'type'     => 'hidden',
			'readonly' => true,
			'inline'   => false,
		),
		'options'    => array(
			'type'    => null,
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
		'checkboxes',
		'name',
		'type',
		'options',
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
		// TODO - Revert config back to default.

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

}


new metatag_admin_config();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();
require_once(e_ADMIN . "footer.php");
exit;
