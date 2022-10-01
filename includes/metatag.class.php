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
	 * Database connection.
	 *
	 * @var e_db
	 */
	protected $pdo;

	/**
	 * Parser methods.
	 *
	 * @var e_parse
	 */
	protected $parser;

	/**
	 * Messenger.
	 *
	 * @var eMessage
	 */
	protected $messenger;

	/**
	 * Form handler.
	 *
	 * @var e_form
	 */
	protected $form;

	/**
	 * Plugin template.
	 *
	 * @var array
	 */
	protected $template;

	/**
	 * Plugin shortcodes.
	 *
	 * @var e_shortcode
	 */
	protected $shortcode;

	/**
	 * Plugin preferences.
	 *
	 * @var array
	 */
	private $prefs;

	/**
	 * Contains a list about plugins, which has e_metatag.php addon file.
	 *
	 * @var array
	 */
	private $addonList;

	/**
	 * @var bool
	 */
	private $disable_caching;

	/**
	 * Contains enabled meta tag groups.
	 *
	 * @var array
	 */
	private $enabled_groups;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		$this->pdo = e107::getDb();
		$this->parser = e107::getParser();
		$this->messenger = e107::getMessage();
		$this->form = e107::getForm();
		$this->template = e107::getTemplate('metatag');
		$this->shortcode = e107::getScBatch('metatag', true);

		$this->prefs = e107::getPlugConfig('metatag')->getPref();
		$this->addonList = varset($this->prefs['addon_list'], array());
		$this->disable_caching = (bool) varset($this->prefs['cache_disabled'], 0);
		$this->enabled_groups = array_keys(varset($this->prefs['groups'], array()));
	}

	/**
	 * Allow overriding default meta tag values on entity forms or not?
	 *
	 * @return bool
	 */
	public function isOverrideAllowed()
	{
		return !empty($this->prefs['override']);
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

			$addonFile = e_PLUGIN . $plugin_path . '/e_metatag.php';

			if(is_readable($addonFile))
			{
				$addonsList[] = $plugin_path;
			}
		}

		e107::getPlugConfig('metatag')->set('addon_list', $addonsList)->save(false);
		// FIXME - won't work...
		// e107::getCache()->clear('metatag_addon_config');
		e107::getCache()->clearAll('system');
	}

	/**
	 * Builds configuration array.
	 *
	 * @param bool $nocache
	 *   Set TRUE to disable cache.
	 *
	 * @return array
	 */
	public function getAddonConfig($nocache = false)
	{
		// Re-use the statically cached value to save memory.
		static $config;

		if(!empty($config))
		{
			return $config;
		}

		$cache = e107::getCache();
		$cacheID = 'metatag_addon_config';

		if(!$this->disable_caching && $nocache !== true)
		{
			$cached = $cache->retrieve($cacheID, false, true, true);

			if($cached)
			{
				$config = $this->unserialize($cached);
			}
		}

		if(empty($config))
		{
			$sql = e107::getDb();

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

					// We also add plugin name to each tokens are provided.
					if(isset($config[$type]['token']))
					{
						foreach($config[$type]['token'] as $token => $token_info)
						{
							$config[$type]['token'][$token]['plugin'] = $plugin;
						}
					}
				}
			}

			// Altering...

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

				if(!method_exists($class, 'config_alter'))
				{
					continue;
				}

				$class->config_alter($config);
			}

			$this->alterAddonConfig($config);

			$cacheData = $this->serialize($config);
			$cache->set($cacheID, $cacheData, true, false, true);
		}

		return $config;
	}

	/**
	 * Alters configuration array.
	 *
	 * @param array $config
	 */
	public function alterAddonConfig(&$config)
	{
		foreach($config as $event_name => $info)
		{
			if(isset($info['dependencies']) && isset($info['dependencies']['plugin']) && !e107::isInstalled($info['dependencies']['plugin']))
			{
				unset($config[$event_name]);
			}
		}
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
			'data'        => array(),
		);

		$types = $this->getAllowedTypes();
		if(!in_array($type, $types))
		{
			return $data;
		}

		if($type == 'metatag_default')
		{
			$this->pdo->select('metatag_default', '*', 'id = ' . (int) $id);

			while($row = $this->pdo->fetch())
			{
				$values = $this->unserialize($row['data']);

				// Global (default) meta tags - no need to set upper level's values.
				if($row['type'] == 'metatag_default')
				{
					foreach($values as $key => $value)
					{
						$data['data'][$key] = $value;
					}
				}
				// Lower level (overwrite global values) - need to set global values.
				else
				{
					$global = $this->getGlobalMetaTags();
					$data['data'] = $global;

					foreach($values as $key => $value)
					{
						if(!isset($data['data'][$key]))
						{
							$data['data'][$key] = $value;
							continue;
						}

						if(!empty($value) && $data['data'][$key] != $value)
						{
							$data['data'][$key] = $value;
						}
					}
				}
			}

			return $data;
		}

		$data['data'] = $this->getMetaTags($id, $type);

		// TODO - Replace data values with posted ones.

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

		$global = $this->getGlobalMetaTags();
		$default = $this->getDefaultMetaTagsByType($type);

		$temp = $global;

		// Override global tags with the default ones provided by entity type.
		if(!empty($default))
		{
			foreach($default as $key => $value)
			{
				if(!isset($temp[$key]))
				{
					$temp[$key] = $value;
					continue;
				}

				if(!empty($value) && $temp[$key] != $value)
				{
					$temp[$key] = $value;
				}
			}
		}

		// Filter only values, which differ from the default/global ones.
		foreach($values['data'] as $key => $value)
		{
			if(isset($temp[$key]) && $temp[$key] == $value)
			{
				// Unset value, so we will use top level value.
				unset($values['data'][$key]);
			}
		}

		// Unset empty values.
		foreach($values['data'] as $key => $value)
		{
			if(empty($value))
			{
				unset($values['data'][$key]);
			}
		}

		if($action == 'create' || $action == 'edit')
		{
			$eID = (int) $values['entity_id'];
			$eType = $this->parser->toDB($values['entity_type']);

			$this->pdo->select('metatag', '*', 'entity_id = "' . $eID . '" AND entity_type = "' . $eType . '"');
			$count = $this->pdo->rowCount();

			if($count > 0)
			{
				$update = array(
					'data'  => array(
						'data' => $this->serialize($values['data']),
					),
					'WHERE' => 'entity_id = "' . $eID . '" AND entity_type = "' . $eType . '"'
				);
				if($this->pdo->update('metatag', $update, false))
				{
					$this->messenger->addSuccess(LAN_PLUGIN_METATAG_MSG_01);
				}
			}
			else
			{
				$insert = array(
					'data' => array(
						'entity_id'   => $eID,
						'entity_type' => $eType,
						'data'        => $this->serialize($values['data']),
					),
				);
				if($this->pdo->insert('metatag', $insert, false))
				{
					$this->messenger->addSuccess(LAN_PLUGIN_METATAG_MSG_01);
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
			$this->pdo->delete('metatag', 'entity_id = "' . (int) $id . '" AND entity_type = "' . $this->parser->toDB($type) . '"');
		}
	}

	/**
	 * Add available tokens to e107 Javascript settings.
	 *
	 * @param string $entity_type
	 *  Entity type.
	 * @param int $entity_id
	 *  Entity ID.
	 */
	public function setWidgetTokens($entity_type, $entity_id)
	{
		$config = $this->getAddonConfig();

		$settings = array(
			'token' => array(
				'modal_title'   => LAN_PLUGIN_METATAG_HELP_03,
				'modal_help'    => '',
				'global_tokens' => array(
					'title'  => LAN_PLUGIN_METATAG_HELP_04,
					'tokens' => array(),
				),
				'entity_tokens' => array(
					'title'  => LAN_PLUGIN_METATAG_HELP_05,
					'tokens' => array(),
				),
			),
		);

		// Global tokens.
		foreach($config['metatag_default']['token'] as $token => $info)
		{
			$settings['token']['global_tokens']['tokens'][] = array(
				'token' => $token,
				'help'  => $info['help'],
			);
		}

		if($entity_type == 'metatag_default')
		{
			$entity_id = (int) $entity_id;

			if($entity_id > 1)
			{
				$this->pdo->select('metatag_default', '*', 'id = ' . (int) $entity_id);

				while($row = $this->pdo->fetch())
				{
					$type = $row['type'];
				}

				// Entity specific tokens.
				if(isset($type) && isset($config[$type]['token']))
				{
					foreach($config[$type]['token'] as $token => $info)
					{
						$settings['token']['entity_tokens']['tokens'][] = array(
							'token' => $token,
							'help'  => $info['help'],
						);
					}
				}
			}
		}
		else
		{
			// Entity specific tokens.
			if(isset($config[$entity_type]['token']))
			{
				foreach($config[$entity_type]['token'] as $token => $info)
				{
					$settings['token']['entity_tokens']['tokens'][] = array(
						'token' => $token,
						'help'  => $info['help'],
					);
				}
			}
		}

		e107::js('settings', array(
			'metatag' => $settings,
		));
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
		e107::css('metatag', 'css/metatag.css');
		e107::library('load', 'jquery.once');
		e107::js('metatag', 'js/metatag.js');

		$meta_info = $this->getMetatagInfo();

		// Output.
		$html = '';

		// Tokens.
		$this->setWidgetTokens($values['entity_type'], $values['entity_id']);
		// Render token info and button.
		$this->shortcode->setVars(array(
			'token_help'   => '<h4>' . LAN_PLUGIN_METATAG_HELP_06 . '</h4><p>' . LAN_PLUGIN_METATAG_HELP_01 . '</p>',
			'token_button' => $this->form->button('token-button', LAN_PLUGIN_METATAG_HELP_02, 'action', null, array(
				'class' => 'btn-sm',
			)),
		));
		$html .= $this->parser->parseTemplate($this->template['TOKEN'], true, $this->shortcode);

		// Basic meta tags.
		if(isset($meta_info['basic']))
		{
			$basic = array();

			foreach($meta_info['basic'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$basic[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_01, $basic);
		}

		// Advanced meta tags.
		if(isset($meta_info['advanced']))
		{
			$advanced = [];

			foreach($meta_info['advanced'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$advanced[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_02, $advanced);
		}

		// Open Graph meta tags.
		if(isset($meta_info['opengraph']))
		{
			$opengraph = [];

			foreach($meta_info['opengraph'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$opengraph[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$help = $this->parser->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_01, array(
				'x' => '<a href="http://ogp.me/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_01_X . '</a>',
			));
			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_03, $opengraph, $help);
		}

		// Facebook meta tags.
		if(isset($meta_info['facebook']))
		{
			$facebook = [];

			foreach($meta_info['facebook'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$facebook[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_04, $facebook, LAN_METATAG_ADMIN_PANEL_HELP_02);
		}

		// Twitter card meta tags.
		if(isset($meta_info['twitter']))
		{
			$twitter = [];

			foreach($meta_info['twitter'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$twitter[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$help = $this->parser->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_03, array(
				'x' => '<a href="https://twitter.com/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_03_X . '</a>',
			));
			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_05, $twitter, $help);
		}

		// Dublin Core Basic Tags.
		if(isset($meta_info['dublin']))
		{
			$dublin = [];

			foreach($meta_info['dublin'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$dublin[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			$help = $this->parser->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_04, array(
				'x' => '<a href="http://dublincore.org/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_04_X . '</a>',
			));
			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_06, $dublin, $help);
		}

		// Google Plus meta tags.
		if(isset($meta_info['google']))
		{
			$google = [];

			foreach($meta_info['google'] as $name => $info)
			{
				$method = 'getWidget' . ucfirst($info['type']);
				$google[$field . '[' . $name . ']'] = $this->{$method}($field, $name, $info['label'], $info['help'], $values, varset($info['options'], []));
			}

			// Finally, we render the panels.
			$help = $this->parser->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_05, [
				'x' => '<a href="https://plus.google.com/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_05_X . '</a>',
			]);
			$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_07, $google, $help);
		}

		return '<div class="metatag-widget-container">' . $html . '</div>';
	}

	/**
	 * Text field widget appears on meta tag configuration form.
	 *
	 * @param $field
	 *   Base field name.
	 * @param $name
	 *   Base field element name.
	 * @param $label
	 *   Field label.
	 * @param $help
	 *   Help text for the field.
	 * @param $values
	 *   Form values.
	 * @param $options
	 *   Field options if it has...
	 *
	 * @return array
	 */
	public function getWidgetText($field, $name, $label, $help, $values, $options = [])
	{
		return [
			'label' => $label,
			'help'  => $this->form->help($help),
			'field' => $this->form->text($field . '[' . $name . ']', varset($values['data'][$name], ''), 255, [
				'class' => 'input-block-level',
			]),
		];
	}

	/**
	 * Checkboxes field widget appears on meta tag configuration form.
	 *
	 * @param $field
	 *   Base field name.
	 * @param $name
	 *   Base field element name.
	 * @param $label
	 *   Field label.
	 * @param $help
	 *   Help text for the field.
	 * @param $values
	 *   Form values.
	 * @param $options
	 *   Field options if it has...
	 *
	 * @return array
	 */
	public function getWidgetCheckboxes($field, $name, $label, $help, $values, $options = [])
	{
		$checkboxes = '';

		foreach($options as $k => $v)
		{
			$checkboxes .= '<div class="checkbox">';
			$checkboxes .= '<label>';
			$default = in_array($k, varset($values['data'][$name], []));
			$checkboxes .= $this->form->checkbox($field . '[' . $name . '][]', $k, $default);
			$checkboxes .= $v;
			$checkboxes .= '</label>';
			$checkboxes .= '</div>';
		}

		return [
			'label' => $label,
			'help'  => $this->form->help($help),
			'field' => $checkboxes,
		];
	}

	/**
	 * Select field widget appears on meta tag configuration form.
	 *
	 * @param $field
	 *   Base field name.
	 * @param $name
	 *   Base field element name.
	 * @param $label
	 *   Field label.
	 * @param $help
	 *   Help text for the field.
	 * @param $values
	 *   Form values.
	 * @param $options
	 *   Field options if it has...
	 *
	 * @return array
	 */
	public function getWidgetSelect($field, $name, $label, $help, $values, $options = [])
	{
		return [
			'label' => $label,
			'help'  => $this->form->help($help),
			'field' => $this->form->select($field . '[' . $name . ']', $options, varset($values['data'][$name], false), [
				'class' => 'input-block-level',
			], true),
		];
	}

	/**
	 * Helper function to render Bootstrap Panel.
	 *
	 * @param string $title
	 *  Panel title.
	 * @param string|array $fields
	 *  Panel body contents.
	 * @param string $help
	 *  Help text, description.
	 *
	 * @return string
	 */
	function getWidgetPanel($title = '', $fields = '', $help = '')
	{
		$body = '';

		if(!empty($help))
		{
			$this->shortcode->setVars(array(
				'panel_help' => $help,
			));

			$body .= $this->parser->parseTemplate($this->template['PANEL']['HELP'], true, $this->shortcode);
		}

		if(is_array($fields))
		{
			foreach($fields as $key => $row)
			{
				$this->shortcode->setVars(array(
					'field_id'    => $this->form->name2id($key),
					'field_label' => $row['label'],
					'field_help'  => $row['help'],
					'field'       => $row['field'],
				));

				$body .= $this->parser->parseTemplate($this->template['PANEL']['FIELD'], true, $this->shortcode);
			}
		}

		$this->shortcode->setVars(array(
			'panel_id'        => md5($title),
			'panel_title'     => $title,
			'panel_collapsed' => ($title != LAN_METATAG_ADMIN_PANEL_01),
			'panel_body'      => $body,
			'panel_footer'    => '',
		));

		$html = $this->parser->parseTemplate($this->template['PANEL']['OPEN'], true, $this->shortcode);
		$html .= $this->parser->parseTemplate($this->template['PANEL']['HEADER'], true, $this->shortcode);
		$html .= $this->parser->parseTemplate($this->template['PANEL']['BODY'], true, $this->shortcode);
		$html .= $this->parser->parseTemplate($this->template['PANEL']['FOOTER'], true, $this->shortcode);
		$html .= $this->parser->parseTemplate($this->template['PANEL']['CLOSE'], true, $this->shortcode);

		return $html;
	}

	/**
	 * Helper function to set cron job, and activate it after installation.
	 *
	 * @deprecated and will be removed after this issue will be closed:
	 * @see https://github.com/e107inc/e107/issues/1962
	 */
	public function setCronJob()
	{
		$count = $this->pdo->count('cron', '(*)', 'cron_function LIKE "metatag::%"');

		if($count > 0)
		{
			$update = array(
				'data'  => array(
					'cron_function' => 'metatag::metatag_cron_purge_expired_cache',
					'cron_tab'      => '0 * * * *',
					'cron_active'   => 1,
				),
				'WHERE' => 'cron_function = LIKE "metatag::%"',
			);
			$this->pdo->update('cron', $update, false);
		}
		else
		{
			$insert = array(
				'data' => array(
					'cron_id'          => 0,
					'cron_name'        => 'Purge expired cache.',
					'cron_category'    => 'content',
					'cron_description' => 'Purge expired cache data from metatag_cache table.',
					'cron_function'    => 'metatag::metatag_cron_purge_expired_cache',
					'cron_tab'         => '0 * * * *',
					'cron_active'      => 1,
				),
			);

			$this->pdo->insert('cron', $insert, false);
		}
	}

	/**
	 * Creates a database record for each metatag types are provided
	 * by e_metatag addon files.
	 *
	 * @param bool $nocache
	 *   Set TRUE to disable cache.
	 */
	public function prepareDefaultTypes($nocache = false)
	{
		$this->pdo->select('metatag_default', '*', 'id > 0');

		$exists = array();
		while($row = $this->pdo->fetch())
		{
			$exists[] = $row['type'];
		}

		$config = $this->getAddonConfig($nocache);
		$types = $this->getAllowedTypes();

		// Delete non-allowed types.
		foreach($exists as $exist)
		{
			if(!in_array($exist, $types))
			{
				$this->pdo->delete('metatag_default', 'type = "' . $exist . '"');
			}
		}

		// First, we insert 'metatag_default'.
		if(!in_array('metatag_default', $exists))
		{
			$data = array();

			// Set initial, default values.
			if(!empty($config['metatag_default']['default']))
			{
				$data = $config['metatag_default']['default'];
			}

			$insert = array(
				'name'   => $config['metatag_default']['name'],
				'type'   => 'metatag_default',
				'parent' => 0,
				'data'   => $this->serialize($data),
			);
			$this->pdo->insert('metatag_default', array('data' => $insert), false);
		}

		foreach($types as $type)
		{
			if(!in_array($type, $exists) && $type != 'metatag_default')
			{
				$data = array();

				// Set initial, default values.
				if(!empty($config[$type]['default']))
				{
					$data = $config[$type]['default'];
				}

				$insert = array(
					'name'   => $config[$type]['name'],
					'type'   => $type,
					'parent' => 1,
					'data'   => $this->serialize($data),
				);
				$this->pdo->insert('metatag_default', array('data' => $insert), false);
			}
		}
	}

	/**
	 * Try to determine entity type and ID, and set meta tags.
	 */
	public function addMetaTags($data = array())
	{
		if(empty($data))
		{
			$data = $this->prepareMetaTags();
		}

		// Finally, we render meta tags.
		if(!empty($data))
		{
			$this->renderMetaTags($data);
		}
	}

	/**
	 * Prepares Meta tags.
	 *
	 * @return array
	 */
	public function prepareMetaTags()
	{
		// Try to load cached data by e_REQUEST_URI.
		$data = $this->getCacheByUri(e_REQUEST_URI);

		if(empty($data))
		{
			// Try to detect entity.
			$entity = $this->detectEntity();

			$entity_type = $entity['entity_type'];
			$entity_id = $entity['entity_id'];

			// Get meta tags by entity_id and/or entity_type.
			$metatags = $this->getMetaTags($entity_id, $entity_type);

			if(!empty($metatags))
			{
				// Replace constants and tokens.
				$data = $this->preProcessMetaTags($metatags, $entity_id, $entity_type);

				if(!empty($data))
				{
					$cacheData = array(
						'cid'         => e_REQUEST_URI,
						'entity_type' => $entity_type,
						'entity_id'   => $entity_id,
						'data'        => $this->serialize($data),
					);

					// Set cache.
					$this->setCache($cacheData);
				}
			}
		}

		return $data;
	}

	/**
	 * Try to load cached data by e_REQUEST_URI.
	 *
	 * @param string $uri
	 *  Request URI.
	 *
	 * @return array $data
	 */
	public function getCacheByUri($uri = '')
	{
		$data = array();

		if($this->disable_caching)
		{
			return $data;
		}

		if(!empty($uri))
		{
			$this->pdo->select('metatag_cache', '*', 'cid = "' . $this->parser->toDB($uri) . '"');

			while($row = $this->pdo->fetch())
			{
				$data = $this->unserialize($row['data']);
			}
		}

		return $data;
	}

	/**
	 * Set cache data.
	 *
	 * @param array $details
	 *  Cache details.
	 */
	public function setCache($details = array())
	{
		if(!empty($details['cid']))
		{
			$created = time();
			$expire = $created + ((int) varset($this->prefs['cache_expire'], 0));

			$details['expire'] = $expire;
			$details['created'] = $created;

			// If false...
			if(empty($details['entity_type']))
			{
				$details['entity_type'] = 'metatag_default';
			}

			if($details['entity_id'] === true)
			{
				$details['entity_id'] = 0;
			}

			$details['entity_id'] = (int) $details['entity_id'];

			$insert = array(
				'data' => $details,
			);

			if($details['entity_type'] != 'metatag_default' && $details['entity_id'] > 0)
			{
				$this->clearCacheByTypeAndId($details['entity_type'], $details['entity_id']);
			}

			$this->pdo->insert('metatag_cache', $insert, false);
		}
	}

	/**
	 * Clear cache table by entity type.
	 *
	 * @param string $type
	 *  Entity type.
	 */
	public function clearCacheByType($type = '')
	{
		if(!empty($type))
		{
			$this->pdo->delete('metatag_cache', 'entity_type = "' . $this->parser->toDB($type) . '"');
		}
	}

	/**
	 * Clear cache table by entity type.
	 *
	 * @param string $type
	 *  Entity type.
	 * @param int $id
	 *  Entity ID.
	 */
	public function clearCacheByTypeAndId($type = '', $id = 0)
	{
		if(!empty($type) && (int) $id > 0)
		{
			$this->pdo->delete('metatag_cache', 'entity_type = "' . $this->parser->toDB($type) . '" AND entity_id = ' . (int) $id);
		}
	}

	/**
	 * Clear all cached data.
	 */
	public function clearCacheAll()
	{
		$this->pdo->delete('metatag_cache');
	}

	/**
	 * Try to detect entity.
	 *
	 * @return array
	 */
	public function detectEntity()
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

			if(!isset($handler['detect']) || !isset($handler['file']) || !isset($handler['plugin']))
			{
				continue;
			}

			$file = e_PLUGIN . $handler['plugin'] . '/' . $handler['file'];
			if(!is_readable($file))
			{
				$file = $this->parser->replaceConstants($handler['file']);

				if(!is_readable($file))
				{
					continue;
				}
			}

			e107_require_once($file);

			if(is_array($handler['detect']))
			{
				$class = new $handler['detect'][0]();
				$entity_id = $class->$handler['detect'][1]();
			}
			else
			{
				$entity_id = $handler['detect']();
			}

			if($entity_id !== false)
			{
				$entity_type = $type;
			}
		}

		return array(
			'entity_type' => $entity_type,
			'entity_id'   => $entity_id,
		);
	}

	/**
	 * Pre-process meta tag values. Replace constants, tokens.
	 *
	 * @param $data
	 *  Contains meta tag values.
	 * @param $entity_id
	 *  Entity ID.
	 * @param $entity_type
	 *  Entity type.
	 *
	 * @return array $data
	 *  Contains processed meta tag values.
	 */
	public function preProcessMetaTags($data, $entity_id, $entity_type)
	{
		$config = $this->getAddonConfig();

		foreach($data as $key => $value)
		{
			// Replace constants. Use the full URLs, and replace {USERID} too.
			$value = $this->parser->replaceConstants($value, 'full', true);

			// Replace global tokens.
			$tokens = $config['metatag_default']['token'];
			$value = $this->replaceTokens($tokens, $value);

			// Replace entity type specific tokens.
			if(!empty($entity_id) && !empty($entity_type))
			{
				// If 'token' and 'load' is set.
				if(isset($config[$entity_type]['token']) && isset($config[$entity_type]['load']))
				{
					$entity = $this->loadEntity($config[$entity_type], $entity_id, $entity_type);
					$tokens = $config[$entity_type]['token'];
					$value = $this->replaceTokens($tokens, $value, $entity);
				}
			}

			$data[$key] = $value;
		}

		$data = $this->preProcessMetaTagsApplyRules($data);

		return $data;
	}

	/**
	 * Apply some rules! E.g. replacing deprecated meta tags with newer
	 * ones.
	 *
	 * @param array $data
	 *  Contains meta tag values.
	 *
	 * @return array $data
	 */
	public function preProcessMetaTagsApplyRules($data = array())
	{
		// [twitter:image] replaces [twitter:image:src]
		if(!empty($data['twitter:image']) && isset($data['twitter:image:src']))
		{
			unset($data['twitter:image:src']);
		}

		// [rights] replaces [copyright]
		if(!empty($data['rights']) && isset($data['copyright']))
		{
			unset($data['copyright']);
		}

		// [shortlink] replaces [shorturl]
		if(!empty($data['shortlink']) && isset($data['shorturl']))
		{
			unset($data['shorturl']);
		}

		// [og:street_address] replaces [og:street-address]
		if(!empty($data['og:street_address']) && isset($data['og:street-address']))
		{
			unset($data['og:street-address']);
		}

		// [og:postal_code] replaces [og:postal-code]
		if(!empty($data['og:postal_code']) && isset($data['og:postal-code']))
		{
			unset($data['og:postal-code']);
		}

		// [og:country_name] replaces [og:country-name]
		if(!empty($data['og:country_name']) && isset($data['og:country-name']))
		{
			unset($data['og:country-name']);
		}

		// [og:video:url] replaces [og:video]
		if(!empty($data['og:video:url']) && isset($data['og:video']))
		{
			unset($data['og:video']);
		}

		// If [twitter:image] is not set, unset its properties.
		if(!isset($data['twitter:image']) || empty($data['twitter:image']))
		{
			if(isset($data['twitter:image:width']))
			{
				unset($data['twitter:image:width']);
			}

			if(isset($data['twitter:image:height']))
			{
				unset($data['twitter:image:height']);
			}

			if(isset($data['twitter:image:alt']))
			{
				unset($data['twitter:image:alt']);
			}
		}

		// If [twitter:card] is not set, or its value is not 'gallery', we
		// unset gallery properties.
		if(!isset($data['twitter:card']) || $data['twitter:card'] != 'gallery')
		{
			if(isset($data['twitter:image0']))
			{
				unset($data['twitter:image0']);
			}

			if(isset($data['twitter:image1']))
			{
				unset($data['twitter:image1']);
			}

			if(isset($data['twitter:image2']))
			{
				unset($data['twitter:image2']);
			}

			if(isset($data['twitter:image3']))
			{
				unset($data['twitter:image3']);
			}
		}

		// If [twitter:card] is not set, or its value is not 'player', we
		// unset player properties.
		if(!isset($data['twitter:card']) || $data['twitter:card'] != 'player')
		{
			if(isset($data['twitter:player']))
			{
				unset($data['twitter:player']);
			}

			if(isset($data['twitter:player:width']))
			{
				unset($data['twitter:player:width']);
			}

			if(isset($data['twitter:player:height']))
			{
				unset($data['twitter:player:height']);
			}

			if(isset($data['twitter:player:stream']))
			{
				unset($data['twitter:player:stream']);
			}

			if(isset($data['twitter:player:stream:content_type']))
			{
				unset($data['twitter:player:stream:content_type']);
			}
		}

		// If [twitter:card] is not set, or its value is not 'product', we
		// unset product properties.
		if(!isset($data['twitter:card']) || $data['twitter:card'] != 'product')
		{
			if(isset($data['twitter:label1']))
			{
				unset($data['twitter:label1']);
			}

			if(isset($data['twitter:data1']))
			{
				unset($data['twitter:data1']);
			}

			if(isset($data['twitter:label2']))
			{
				unset($data['twitter:label2']);
			}

			if(isset($data['twitter:data2']))
			{
				unset($data['twitter:data2']);
			}
		}

		return $data;
	}

	/**
	 * Try to load entity using provided entityQuery.
	 *
	 * @param $entity_info
	 *  Information about entity provided by e_metatag addon file.
	 * @param $entity_id
	 *  Entity ID.
	 * @param $entity_type
	 *  Entity type.
	 *
	 * @return array|bool
	 *  An associative array contains entity record from DB, otherwise false.
	 */
	public function loadEntity($entity_info, $entity_id, $entity_type)
	{
		// Re-use the statically cached value to save memory.
		static $entities;

		// Unique key for entity.
		$entity_key = $entity_type . '_' . $entity_id;

		if(!isset($entities[$entity_key]))
		{
			$entities[$entity_key] = false;

			if(!isset($entity_info['load']) || !isset($entity_info['file']) || !isset($entity_info['plugin']))
			{
				return $entities[$entity_key];
			}

			$file = e_PLUGIN . $entity_info['plugin'] . '/' . $entity_info['file'];
			if(!is_readable($file))
			{
				$file = $this->parser->replaceConstants($entity_info['file']);

				if(!is_readable($file))
				{
					return $entities[$entity_key];
				}
			}

			e107_require_once($file);

			if(is_array($entity_info['load']))
			{
				$class = new $entity_info['load'][0]();
				$entities[$entity_key] = $class->$entity_info['load'][1]($entity_id);
			}
			else
			{
				$entities[$entity_key] = $entity_info['load']($entity_id);
			}
		}

		return is_array($entities[$entity_key]) ? $entities[$entity_key] : false;
	}

	/**
	 * Replace tokens.
	 *
	 * @param $tokens
	 *  Contains information about tokens.
	 * @param $data
	 *  Subject for replacing.
	 * @param array $entity
	 *  Contains the entity selected from DB.
	 *
	 * @return mixed $data
	 */
	public function replaceTokens($tokens, $data, $entity = array())
	{
		if(empty($tokens) || empty($data))
		{
			return $data;
		}

		foreach($tokens as $token => $info)
		{
			// Try to load handler file.
			$file = e_PLUGIN . $info['plugin'] . '/' . $info['file'];
			if(!is_readable($file))
			{
				$file = $this->parser->replaceConstants($info['file']);

				if(!is_readable($file))
				{
					continue;
				}
			}

			// Include handler file.
			e107_require_once($file);

			if(is_array($info['handler']))
			{
				$class = new $info['handler'][0]();
				$replaced = $class->$info['handler'][1]($entity);
			}
			else
			{
				$replaced = $info['handler']($entity);
			}

			// If no return value (e.g. null, false), we set default empty string.
			if(empty($replaced) || !is_string($replaced))
			{
				$replaced = '';
			}

			// Finally, we replace token with value.
			$data = str_replace('{' . $token . '}', $replaced, $data);
		}

		return $data;
	}

	/**
	 * Get meta tags.
	 *
	 * @param int $entity_id
	 *  Primary ID of the record being created/edited/deleted.
	 * @param string $entity_type
	 *  Event name, e.g: 'news', 'page' etc. (core or plugin).
	 *
	 * @return array $data
	 *  Available meta tags.
	 */
	public function getMetaTags($entity_id, $entity_type)
	{
		$global = $this->getGlobalMetaTags();
		$default = $this->getDefaultMetaTagsByType($entity_type);
		$custom = $this->isOverrideAllowed() ? $this->getCustomMetaTagsByEntity($entity_id, $entity_type) : [];

		$tags = $global;

		// Override meta tags with the default ones.
		if(!empty($default))
		{
			foreach($default as $key => $value)
			{
				if(!isset($tags[$key]))
				{
					$tags[$key] = $value;
					continue;
				}

				if(!empty($value) && $tags[$key] != $value)
				{
					$tags[$key] = $value;
				}
			}
		}

		// Override meta tags with the custom ones.
		if(!empty($custom))
		{
			foreach($custom as $key => $value)
			{
				if(!isset($tags[$key]))
				{
					$tags[$key] = $value;
					continue;
				}

				if(!empty($value) && $tags[$key] != $value)
				{
					$tags[$key] = $value;
				}
			}
		}

		// Get enabled meta tags.
		$elements = $this->getMetatagInfo(false, false);
		$element_keys = array_keys($elements);

		foreach($tags as $key => $value)
		{
			// Remove meta tag that is not enabled.
			if(!in_array($key, $element_keys))
			{
				unset($tags[$key]);
			}
		}

		return $tags;
	}

	/**
	 * Returns with available global meta tags.
	 *
	 * @return array $data
	 *  Contains global meta tags.
	 */
	public function getGlobalMetaTags()
	{
		$this->pdo->select('metatag_default', '*', 'type = "metatag_default"');

		$data = array();
		while($row = $this->pdo->fetch())
		{
			$values = $this->unserialize($row['data']);

			foreach($values as $key => $value)
			{
				if(!empty($value))
				{
					$data[$key] = $value;
				}
			}
		}

		return $data;
	}

	/**
	 * Returns with available default meta tags by entity type.
	 *
	 * @param string $entity_type
	 *  Entity type.
	 *
	 * @return array $data
	 *  Contains default meta tags.
	 */
	public function getDefaultMetaTagsByType($entity_type)
	{
		$data = array();

		if(!empty($entity_type) && $entity_type !== false)
		{
			$this->pdo->select('metatag_default', '*', 'type = "' . $entity_type . '"');

			while($row = $this->pdo->fetch())
			{
				$values = $this->unserialize($row['data']);

				foreach($values as $key => $value)
				{
					if(!empty($value))
					{
						$data[$key] = $value;
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Returns with available custom meta tags by entity.
	 *
	 * @param int $entity_id
	 *  Entity ID.
	 * @param string $entity_type
	 *  Entity type.
	 *
	 * @return array $data
	 *  Contains custom meta tags.
	 */
	public function getCustomMetaTagsByEntity($entity_id, $entity_type)
	{
		$data = array();

		if(empty($entity_id) || $entity_id === false)
		{
			return $data;
		}

		if(empty($entity_type) || $entity_type === false)
		{
			return $data;
		}

		$this->pdo->select('metatag', '*', 'entity_id = ' . (int) $entity_id . ' AND entity_type = "' . $entity_type . '"');

		while($row = $this->pdo->fetch())
		{
			$values = $this->unserialize($row['data']);

			foreach($values as $key => $value)
			{
				if(!empty($value))
				{
					$data[$key] = $value;
				}
			}
		}

		return $data;
	}

	/**
	 * Render meta tags.
	 *
	 * @param $data
	 *  Array contains available meta tags.
	 */
	public function renderMetaTags($data)
	{
		foreach($data as $key => $value)
		{
			switch($key)
			{
				case "title":
				case "description":
				case "abstract":
				case "keywords":
				case "news_keywords":
				case "standout":
				case "rating":
				case "referrer":
				case "generator":
				case "rights":
				case "shortlink":
				case "publisher":
				case "author":
				case "original-source":
				case "prev":
				case "next":
				case "geo.position":
				case "geo.placename":
				case "geo.regio":
				case "icbm":
				case "revisit-after":
				case "expires":
				case "twitter:card":
				case "twitter:site":
				case "twitter:site:id":
				case "twitter:creator":
				case "twitter:creator:id":
				case "twitter:url":
				case "twitter:title":
				case "twitter:description":
				case "twitter:image":
				case "twitter:image:width":
				case "twitter:image:height":
				case "twitter:image:alt":
				case "twitter:image0":
				case "twitter:image1":
				case "twitter:image2":
				case "twitter:image3":
				case "twitter:player":
				case "twitter:player:width":
				case "twitter:player:height":
				case "twitter:player:stream":
				case "twitter:player:stream:content_type":
				case "twitter:app:country":
				case "twitter:app:name:iphone":
				case "twitter:app:id:iphone":
				case "twitter:app:url:iphone":
				case "twitter:app:name:ipad":
				case "twitter:app:id:ipad":
				case "twitter:app:url:ipad":
				case "twitter:app:name:googleplay":
				case "twitter:app:id:googleplay":
				case "twitter:app:url:googleplay":
				case "twitter:label1":
				case "twitter:data1":
				case "twitter:label2":
				case "twitter:data2":
				case "dcterms.title":
				case "dcterms.creator":
				case "dcterms.subject":
				case "dcterms.description":
				case "dcterms.publisher":
				case "dcterms.contributor":
				case "dcterms.date":
				case "dcterms.type":
				case "dcterms.format":
				case "dcterms.identifier":
				case "dcterms.source":
				case "dcterms.language":
				case "dcterms.relation":
				case "dcterms.coverage":
				case "dcterms.rights":
					$this->renderMeta($key, $value);
					break;

				// Need to implode.
				case "robots":
					$this->renderMeta($key, implode(', ', $value));
					break;

				// Restrict to one item.
				case "image_src":
					$values = explode('|', $value);
					$this->renderMeta($key, $values[0]);
					break;

				case "itemprop:name":
				case "itemprop:description":
				case "itemprop:image":
					$this->renderItemprop($key, $value);
					break;

				case "content-language":
				case "refresh":
				case "cache-control":
				case "pragma":
					$this->renderHttpEquiv($key, $value);
					break;

				case "og:site_name":
				case "og:type":
				case "og:url":
				case "og:title":
				case "og:determiner":
				case "og:description":
				case "og:updated_time":
				case "og:see_also":
				case "og:image:url":
				case "og:image:secure_url":
				case "og:image:type":
				case "og:image:width":
				case "og:image:height":
				case "og:latitude":
				case "og:longitude":
				case "og:street_address":
				case "og:locality":
				case "og:region":
				case "og:postal_code":
				case "og:country_name":
				case "og:email":
				case "og:phone_number":
				case "og:fax_number":
				case "og:locale":
				case "og:locale:alternate":
				case "article:author":
				case "article:publisher":
				case "article:section":
				case "article:tag":
				case "article:published_time":
				case "article:modified_time":
				case "article:expiration_time":
				case "profile:first_name":
				case "profile:last_name":
				case "profile:username":
				case "profile:gender":
				case "og:audio":
				case "og:audio:secure_url":
				case "og:audio:type":
				case "book:author":
				case "book:isbn":
				case "book:release_date":
				case "book:tag":
				case "og:video:url":
				case "og:video:secure_url":
				case "og:video:width":
				case "og:video:height":
				case "og:video:type":
				case "video:actor":
				case "video:actor:role":
				case "video:director":
				case "video:writer":
				case "video:duration":
				case "video:release_date":
				case "video:tag":
				case "video:series":
				case "fb:admins":
				case "fb:app_id":
				case "fb:pages":
					$this->renderProperty($key, $value);
					break;

				// Allowed multiple tags.
				case "og:image":
					$values = explode('|', $value);
					foreach($values as $item)
					{
						$this->renderProperty($key, $item);
					}
					break;

				case "canonical":
					// "canonical" is handled by e107 core.
					// $this->renderLinkRel($key, $value);
					break;
			}
		}
	}

	/**
	 * Callback for a normal meta tag.
	 *
	 * The format is:
	 * <meta name="[name]" content="[value]" />
	 *
	 * @param string $name
	 *  Name attribute for meta tag.
	 * @param string $content
	 *  Content attribute for meta tag.
	 */
	public function renderMeta($name, $content)
	{
		if(!empty($name) && !empty($content))
		{
			e107::meta(null, $content, array(
				'name' => $name,
			));
		}
	}

	/**
	 * Callback for a http-equiv meta tag.
	 *
	 * The format is:
	 * <meta http-equiv="[name]" content="[value]" />
	 *
	 * @param string $httpequiv
	 *  Http-equiv attribute for meta tag.
	 * @param string $content
	 *  Content attribute for meta tag.
	 */
	public function renderHttpEquiv($httpequiv, $content)
	{
		if(!empty($httpequiv) && !empty($content))
		{
			e107::meta(null, $content, array(
				'http-equiv' => $httpequiv,
			));
		}
	}

	/**
	 * Callback for a property meta tag.
	 *
	 * The format is:
	 * <meta property="[name]" content="[value]" />
	 *
	 * @param string $property
	 *  Property attribute for meta tag.
	 * @param string $content
	 *  Content attribute for meta tag.
	 */
	public function renderProperty($property, $content)
	{
		if(!empty($property) && !empty($content))
		{
			e107::meta(null, $content, array(
				'property' => $property,
			));
		}
	}

	/**
	 * Callback for a itemprop meta tag.
	 *
	 * The format is:
	 * <meta itemprop="[name]" content="[value]" />
	 *
	 * @param string $itemprop
	 *  Itemprop attribute for meta tag.
	 * @param string $content
	 *  Content attribute for meta tag.
	 */
	public function renderItemprop($itemprop, $content)
	{
		if(!empty($itemprop) && !empty($content))
		{
			e107::meta(null, $content, array(
				'itemprop' => str_replace('itemprop:', '', $itemprop),
			));
		}
	}

	/**
	 * Callback for a rel link tag.
	 *
	 * The format is:
	 * <link rel="[name]" href="[value]" />
	 *
	 * @param string $rel
	 *  Rel attribute for link tag.
	 * @param string $href
	 *  Href attribute for link tag.
	 */
	public function renderLinkRel($rel, $href)
	{
		if(!empty($rel) && !empty($href))
		{
			e107::link(array(
				'rel'  => $rel,
				'href' => $href,
			));
		}
	}

	/**
	 * Callback for a rev link tag.
	 *
	 * The format is:
	 * <link rev="[name]" href="[value]" />
	 *
	 * @param string $rev
	 *  Rev attribute for link tag.
	 * @param string $href
	 *  Href attribute for link tag.
	 */
	public function renderLinkRev($rev, $href)
	{
		if(!empty($rev) && !empty($href))
		{
			e107::link(array(
				'rev'  => $rev,
				'href' => $href,
			));
		}
	}

	/**
	 * Generates a storable representation of a value.
	 *
	 * @param mixed $data
	 *   The value to be serialized.
	 *
	 * @return string
	 */
	public function serialize($data)
	{
		// $data = base64_encode(serialize($data));
		// $data = e107::serialize($data);
		$data = json_encode($data);
		return $data;
	}

	/**
	 * Creates a PHP value from a stored representation.
	 *
	 * @param string $data
	 *   The serialized string.
	 *
	 * @return mixed
	 */
	public function unserialize($data)
	{
		// $data = unserialize(base64_decode($data));
		// $data = e107::unserialize($data);
		$data = html_entity_decode($data);
		$data = json_decode($data, true);
		return $data;
	}

	/**
	 * Meta tag groups with meta tags.
	 *
	 * @param $all
	 *   If TRUE, return with all meta tag groups, not only with those that
	 *   are enabled.
	 *
	 * @return array
	 */
	public function getMetatagInfo($all = false, $grouped = true)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.tags.info.php');

		$info = metatag_get_widget_elements_info();

		if(!$all)
		{
			foreach($info as $group => $items)
			{
				if(!in_array($group, $this->enabled_groups))
				{
					unset($info[$group]);
				}
			}
		}

		if(!$grouped)
		{
			$elements = array();
			foreach($info as $items)
			{
				foreach($items as $name => $item)
				{
					$elements[$name] = $item;
				}
			}
			$info = $elements;
		}

		return $info;
	}

}
