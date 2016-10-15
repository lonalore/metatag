<?php

/**
 * @file
 * Metatag class for common use.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);


/**
 * Class metatag.
 */
class metatag
{

	/**
	 * Contains a list about plugins, which has e_metatag.php addon file.
	 *
	 * @var array
	 */
	private $addonList = array();

	/**
	 * Constructor.
	 */
	function __construct()
	{
		$prefs = e107::getPlugConfig('metatag')->getPref();
		$this->addonList = varset($prefs['addon_list'], array());
	}

	/**
	 * Builds configuration array with information is provided by addon files.
	 */
	public function getAddonConfig()
	{
		$sql = e107::getDb();

		$config = array();

		// Not a real type, only for rendering widget on Admin UI of metatag plugin.
		$config['metatag_default'] = array(
			'name' => LAN_PLUGIN_METATAG_TYPE_01,
		);

		$enabledPlugins = array();

		// Get list of enabled plugins.
		$sql->select("plugin", "*", "plugin_id !='' order by plugin_path ASC");
		while($row = $sql->fetch())
		{
			if($row['plugin_installflag'] == 1)
			{
				$enabledPlugins[] = $row['plugin_path'];
			}
		}

		foreach($this->addonList as $plugin)
		{
			if(!in_array($plugin, $enabledPlugins))
			{
				continue;
			}

			$file = e_PLUGIN . $plugin . '/e_metatag.php';

			if(!is_readable($file))
			{
				continue;
			}

			e107_require_once($file);
			$addonClass = $plugin . '_metatag';

			if(!class_exists($addonClass))
			{
				continue;
			}

			$class = new $addonClass();

			if(!method_exists($class, 'config'))
			{
				continue;
			}

			$addonConfig = $class->config();

			if(!is_array($addonConfig))
			{
				continue;
			}

			foreach($addonConfig as $type => $info)
			{
				$config[$type] = $info;
				$config[$type]['plugin'] = $plugin;
			}
		}

		// TODO - altering $config

		return $config;
	}

	/**
	 * Get allowed metatag entity types.
	 *
	 * @return array
	 */
	public function getAllowedTypes()
	{
		$types = $this->getAddonConfig();
		return array_keys($types);
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
		if(in_array($type, $types))
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
		if(!in_array($type, $types))
		{
			return $data;
		}

		if($type == 'metatag_default')
		{
			$db = e107::getDb();
			$db->select('metatag_default', '*', 'id = ' . (int) $id);

			while($row = $db->fetch())
			{
				$values = unserialize(base64_decode($row['data']));

				foreach($values as $key => $value)
				{
					$data['data'][$key] = $value;
				}
			}

			return $data;
		}

		$db = e107::getDb();
		$db->select('metatag', '*', 'entity_type = "' . $type . '" AND entity_id = "' . (int) $id . '"');

		while($row = $db->fetch())
		{
			$values = unserialize(base64_decode($row['data']));

			foreach($values as $key => $value)
			{
				$data['data'][$key] = $value;
			}
		}

		return $data;
	}

	/**
	 * Process posted widget data.
	 *
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 * @param string $type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 * @param string $action
	 *  Current mode, e.g: 'create', 'edit', 'list'.
	 * @param array $data
	 *  Posted data.
	 */
	public function processWidgetData($id, $type, $action, $data)
	{
		if(empty($id) || empty($data['x_metatag_metatags']) || $type == 'metatag_deafult')
		{
			return;
		}

		$types = $this->getAllowedTypes();
		if(!in_array($type, $types))
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

		if($action == 'create' || $action == 'edit')
		{
			$db = e107::getDb();
			$msg = e107::getMessage();

			$db->select('metatag', '*', 'entity_id = "' . (int) $values['entity_id'] . '" AND entity_type = "' . $values['entity_type'] . '"');
			$count = $db->rowCount();

			if($count > 0)
			{
				$update = array(
					'data' => array(
						'data' => base64_encode(serialize($values['data'])),
					),
					'WHERE entity_id = "' . (int) $values['entity_id'] . '" AND entity_type = "' . $values['entity_type'] . '"'
				);
				if($db->update('metatag', $update, false))
				{
					$msg->addSuccess(LAN_PLUGIN_METATAG_MSG_01);
				}
			}
			else
			{
				$insert = array(
					'data' => array(
						'entity_id'   => $values['entity_id'],
						'entity_type' => $values['entity_type'],
						'data'        => base64_encode(serialize($values['data'])),
					),
				);
				if($db->insert('metatag', $insert, false))
				{
					$msg->addSuccess(LAN_PLUGIN_METATAG_MSG_01);
				}
			}
		}
	}

	/**
	 * Delete custom meta tags from database.
	 *
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 * @param string $type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 */
	public function deleteMetaTagData($id, $type)
	{
		if(!empty($id) && !empty($type))
		{
			$db = e107::getDb();
			$db->delete('metatag', 'entity_id = "' . (int) $id . '" AND entity_type = "' . $type . '"');
		}
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
	 * Update addon list.
	 */
	public function updateAddonList()
	{
		$fl = e107::getFile();

		$plugList = $fl->get_files(e_PLUGIN, "^plugin\.(php|xml)$", "standard", 1);
		$pluginList = array();
		$addonsList = array();

		// Remove Duplicates caused by having both plugin.php AND plugin.xml.
		foreach($plugList as $num => $val)
		{
			$key = basename($val['path']);
			$pluginList[$key] = $val;
		}

		foreach($pluginList as $p)
		{
			$p['path'] = substr(str_replace(e_PLUGIN, '', $p['path']), 0, -1);
			$plugin_path = $p['path'];

			if(is_readable(e_PLUGIN . $plugin_path . '/e_metatag.php'))
			{
				$addonsList[] = $plugin_path;
			}
		}

		e107::getPlugConfig('metatag')->set('addon_list', $addonsList)->save(false);
	}

	/**
	 * Creates a database record for each metatag types.
	 */
	public function prepareDefaultTypes()
	{
		$db = e107::getDb();
		$db->select('metatag_default', '*', 'id > 0');

		$exists = array();
		while($row = $db->fetch())
		{
			$exists[] = $row['type'];
		}

		$config = $this->getAddonConfig();
		$types = $this->getAllowedTypes();
		foreach($types as $type)
		{
			if(!in_array($type, $exists))
			{
				$data = array();

				$insert = array(
					'name' => $config[$type]['name'],
					'type' => $type,
					'data' => base64_encode(serialize($data)),
				);

				$db->insert('metatag_default', array('data' => $insert), false);
			}
		}
	}

	/**
	 * Try to determine entity type and ID, and set meta tags.
	 */
	public function addMetaTags()
	{
		$config = $this->getAddonConfig();

		$entity_id = false;
		$entity_type = false;

		foreach($config as $type => $handler)
		{
			if($entity_id !== false)
			{
				continue;
			}

			if(!isset($handler['callback']) || !isset($handler['file']) || !isset($handler['plugin']))
			{
				continue;
			}

			$file = e_PLUGIN . $handler['plugin'] . '/' . $handler['file'];

			if(!is_readable($file))
			{
				continue;
			}

			e107_require_once($file);

			if(is_array($handler['callback']))
			{
				$class = new $handler['callback'][0]();
				$entity_id = $class->$handler['callback'][1]();
			}
			else
			{
				$entity_id = $handler['callback']();
			}

			if($entity_id !== false)
			{
				$entity_type = $type;
			}
		}

		// TODO
		// 1, Try to load custom (overridden) meta tags for a specific entity item with entity_id and entity_type.
		// 2, Try to load default meta tags for entity type with entity_type. (if the first step failed)
		// 3, Try to load global meta tags. (if the first two steps failed)

		if($entity_id !== false)
		{
			var_dump(array(
				'entity_type' => $entity_type,
				'entity_id'   => $entity_id,
			));
		}
	}

	/**
	 * Determines if the current page is the front page.
	 *
	 * @return mixed
	 *  True if the current page is the front page, otherwise false.
	 */
	public function currentPathIsFrontPage()
	{
		if(deftrue('e_FRONTPAGE', false) == true)
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current page is a news item.
	 *
	 * @return mixed
	 *  News item ID if the current page is a news page, otherwise false.
	 */
	public function currentPathIsNewsItem()
	{
		$query = e_QUERY;

		if(substr($query, 0, 7) == 'extend.')
		{
			$id = (int) str_replace('extend.', '', $query);

			if($id > 0)
			{
				return $id;
			}
		}

		return false;
	}

}
