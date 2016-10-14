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
	 * Get allowed metatag entity types.
	 *
	 * @return array
	 */
	public function getAllowedTypes()
	{
		$types = array(
			'metatag_default' => array(), // Because we need the widget.
			'news'            => array(),
			'page'            => array(),
		);

		// TODO - Implements logic for "types" defined by 3rd party plugins.

		return $types;
	}

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

		$types = $this->getAllowedTypes();
		$allowed = array_keys($types);
		if(in_array($type, $allowed))
		{
			$config = $this->getWidgetConfigData($type, $id);
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
		// Default values.
		$data = array(
			'entity_type' => $type,
			'entity_id'   => $id,
			'data'        => array(
				'title'       => '',
				'description' => '',
				'abstract'    => '',
				'keywords'    => '',
			),
		);

		$types = $this->getAllowedTypes();
		$allowed = array_keys($types);
		if(!in_array($type, $allowed))
		{
			return $data;
		}

		// TODO:
		// Step 1, Get default metatag values for a specific type.
		// Step 2, Get custom metatag values for a specific item.
		// Step 3, Replace default values with custom values.

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
			'field' => $form->text($field . '[title]', varset($values['data']['title'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_02,
				'class' => 'input-block-level',
			)),
		);

		$basic[$field . '[description]'] = array(
			'label' => LAN_METATAG_ADMIN_04,
			'help'  => $form->help(LAN_METATAG_ADMIN_05),
			'field' => $form->text($field . '[description]', varset($values['data']['description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_04,
				'class' => 'input-block-level',
			)),
		);

		$basic[$field . '[abstract]'] = array(
			'label' => LAN_METATAG_ADMIN_06,
			'help'  => $form->help(LAN_METATAG_ADMIN_07),
			'field' => $form->text($field . '[abstract]', varset($values['data']['abstract'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_06,
				'class' => 'input-block-level',
			)),
		);

		// TODO - use selectize instead.
		$basic[$field . '[keywords]'] = array(
			'label' => LAN_METATAG_ADMIN_08,
			'help'  => $form->help(LAN_METATAG_ADMIN_09),
			'field' => $form->text($field . '[keywords]', varset($values['data']['keywords'], ''), 255, array(
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
			$panelID = md5($title);
			$in = '';

			if($title == LAN_METATAG_ADMIN_PANEL_01)
			{
				$in = ' in';
			}

			$html .= '<div class="panel-heading">';
			$html .= '<h4 class="panel-title">';
			$html .= '<a data-toggle="collapse" href="#' . $panelID . '">';
			$html .= $title;
			$html .= '</a>';
			$html .= '</h4>';
			$html .= '</div>';
			$html .= '<div id="' . $panelID . '" class="panel-collapse collapse' . $in . '">';
		}

		$html .= '<div class="panel-body form-horizontal">';

		if(is_array($body))
		{
			$form = e107::getForm();

			foreach($body as $key => $row)
			{
				$html .= '<div class="form-group">';
				$html .= '<label for="' . $form->name2id($key) . '" class="control-label col-sm-3">';
				$html .= $row['label'];
				$html .= '</label>';
				$html .= '<div class="col-sm-9">';
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

		if(!empty($title))
		{
			$html .= '</div>';
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

		$types = $this->getAllowedTypes();
		$allowed = array_keys($types);
		if(!in_array($type, $allowed))
		{
			return;
		}

		$values = array(
			'entity_id'   => (int) $id,
			'entity_type' => $type,
			'data'        => array(),
		);

		foreach($data['x_metatag_metatags'] as $key => $value)
		{
			$values['data'][$key] = $value;
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
