<?php

/**
 * @file
 * Metatag class for common use.
 */

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);


/**
 * Class metatag.
 */
class metatag
{

	/**
	 * Get Widget config array for e_admin plugin files.
	 *
	 * @param string $type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 * @param string $action
	 *  Current mode, e.g: 'create', 'edit', 'list'.
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 *
	 * @return array $config
	 */
	public function getWidgetConfig($type, $action, $id)
	{
		$config = array();

		switch($type)
		{
			case "news":
			case "page":
				$config = $this->getWidgetConfigData($type, $id);
				break;

			default:
				// TODO - Implements logic for "types" defined by 3rd party plugins.
				break;
		}

		return $config;
	}

	/**
	 * Returns with Widget configuration array for extending Admin UI.
	 *
	 * @param string $type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 *
	 * @return array
	 */
	public function getWidgetConfigData($type, $id)
	{
		$config = array();
		$config['tabs'] = array('metatag' => LAN_PLUGIN_METATAG_TAB);
		$config['fields'] = array(
			// $_POST['x_metatag_metatags']
			'metatags' => array(
				'type'       => 'method', // metatag_admin_form::x_metatag_metatags()
				'title'      => '',
				'help'       => '',
				'tab'        => 'metatag',
				'writeParms' => array(
					'default'     => $this->getWidgetDefaultValues($type, $id),
					'nolabel'     => true,
					'size'        => 'xxlarge',
					'placeholder' => '',
				),
				'readParms'  => '',
				'width'      => 'auto',
				'class'      => 'left',
				'thclass'    => 'left',
			),
		);
		return $config;
	}

	/**
	 * Get default values for Widget.
	 *
	 * @param string $type
	 *  Event name, e.g: 'wmessage', 'news' etc. (core or plugin).
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 *
	 * @return array $data
	 */
	public function getWidgetDefaultValues($type, $id)
	{
		$data = array(
			'entity_type' => $type,
			'entity_id'   => $id,
			'title'       => 'asd',
			'description' => 'asd',
			'abstract'    => 'asd',
			'keywords'    => 'asd',
		);

		// TODO:
		// Step 1, Get default metatag values for a specific type.
		// Step 2, Get custom metatag values for a specific item.
		// Step 3, Replace default values with custom values.

		switch($type)
		{
			case "news":
				// TODO
				break;

			case "page":
				// TODO
				break;

			default:
				// TODO - Implements logic for "types" defined by 3rd party plugins.
				break;
		}

		return $data;
	}

	/**
	 * Get rendered Widget.
	 *
	 * @param array $values
	 *  Array contains default values.
	 * @param string $field
	 *  Field name.
	 *
	 * @return string
	 */
	public function getWidget($values = array(), $field = 'x_metatag_metatags')
	{
		$form = e107::getForm();
		$tp = e107::getParser();

		// Output.
		$html = '';

		$basic = array();

		$basic[$field . '[title]'] = array(
			'label' => LAN_METATAG_ADMIN_02,
			'help'  => $form->help(LAN_METATAG_ADMIN_03),
			'field' => $form->text($field . '[title]', varset($values['title'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_02,
				'class' => 'input-block-level',
			)),
		);

		$basic[$field . '[description]'] = array(
			'label' => LAN_METATAG_ADMIN_04,
			'help'  => $form->help(LAN_METATAG_ADMIN_05),
			'field' => $form->text($field . '[description]', varset($values['description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_04,
				'class' => 'input-block-level',
			)),
		);

		$basic[$field . '[abstract]'] = array(
			'label' => LAN_METATAG_ADMIN_06,
			'help'  => $form->help(LAN_METATAG_ADMIN_07),
			'field' => $form->text($field . '[abstract]', varset($values['abstract'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_06,
				'class' => 'input-block-level',
			)),
		);

		// TODO - use selectize instead.
		$basic[$field . '[keywords]'] = array(
			'label' => LAN_METATAG_ADMIN_08,
			'help'  => $form->help(LAN_METATAG_ADMIN_09),
			'field' => $form->text($field . '[keywords]', varset($values['keywords'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_08,
				'class' => 'input-block-level',
			)),
		);

		$advanced = array();
		$opengraph = array();
		$facebook = array();
		$twitter = array();
		$dublin = array();
		$google = array();

		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_01, $basic);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_02, $advanced);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_03, $opengraph);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_04, $facebook);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_05, $twitter);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_06, $dublin);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_07, $google);

		return $html;
	}

	/**
	 * Helper function to render Bootstrap Panel.
	 *
	 * @param string $title
	 *  Panel title.
	 * @param string|array $body
	 *  Panel body contents.
	 *
	 * @return string
	 */
	function getWidgetPanel($title = '', $body = '')
	{
		$html = '<div class="panel panel-default">';

		if(!empty($title))
		{
			$html .= '<div class="panel-heading">';
			$html .= $title;
			$html .= '</div>';
		}

		$html .= '<div class="panel-body form-horizontal">';

		if(is_array($body))
		{
			$form = e107::getForm();

			foreach($body as $key => $row)
			{
				$html .= '<div class="form-group">';
				$html .= '<label for="' . $form->name2id($key) . '" class="control-label col-sm-2">';
				$html .= $row['label'];
				$html .= '</label>';
				$html .= '<div class="col-sm-10">';
				$html .= $row['field'];
				$html .= $row['help'];
				$html .= '</div>';
				$html .= '</div>';
			}
		}
		else
		{
			$html .= $body;
		}

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Process posted widget data.
	 *
	 * @param string $type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 * @param string $action
	 *  Current mode, e.g: 'create', 'edit', 'list'.
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 * @param array $data
	 *  Posted data.
	 */
	public function processWidgetData($id, $type, $action, $data)
	{
		if(empty($id) || empty($data['x_metatag_metatags']))
		{
			return;
		}

		$values = array(
			'entity_id'   => (int) $id,
			'entity_type' => $type,
		);

		foreach($data['x_metatag_metatags'] as $key => $value)
		{
			$values[$key] = $value;
		}

		if($action == 'create')
		{
			// TODO - db insert.
		}

		if($action == 'edit')
		{
			// TODO - db update.
		}
	}
}
