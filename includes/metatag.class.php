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
	 * Plugin preferences.
	 *
	 * @var array|mixed
	 */
	private $plugPrefs = array();

	/**
	 * Contains a list about plugins, which has e_metatag.php addon file.
	 *
	 * @var array
	 */
	private $addonList = array();

	/**
	 * @var bool
	 */
	private $disable_caching = false;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		$prefs = e107::getPlugConfig('metatag')->getPref();
		$this->plugPrefs = $prefs;
		$this->addonList = varset($prefs['addon_list'], array());
		$this->disable_caching = (bool) varset($prefs['cache_disabled'], 0);
	}

	/**
	 * Builds configuration array with information is provided by addon files.
	 */
	public function getAddonConfig()
	{
		// Re-use the statically cached value to save memory.
		static $config;

		if(!empty($config))
		{
			return $config;
		}

		$cache = e107::getCache();
		$cacheID = 'metatag_addon_config';

		if(!$this->disable_caching)
		{
			$cached = $cache->retrieve($cacheID, false, true, true);

			if($cached)
			{
				$config = unserialize(base64_decode($cached));
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

			// If Gallery plugin is not installed.
			if(e107::isInstalled('gallery'))
			{
				// Unset Gallery related configs.
				// TODO
			}

			// If Forum plugin is not installed.
			if(e107::isInstalled('forum'))
			{
				// Unset Forum related configs.
				// TODO
			}

			// TODO - altering $config

			$cacheData = base64_encode(serialize($config));
			$cache->set($cacheID, $cacheData, true, false, true);
		}

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
			'data'        => array(),
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
			$tp = e107::getParser();
			$db = e107::getDb();
			$msg = e107::getMessage();

			$eID = (int) $values['entity_id'];
			$eType = $tp->toDB($values['entity_type']);

			$db->select('metatag', '*', 'entity_id = "' . $eID . '" AND entity_type = "' . $eType . '"');
			$count = $db->rowCount();

			if($count > 0)
			{
				$update = array(
					'data'  => array(
						'data' => base64_encode(serialize($values['data'])),
					),
					'WHERE' => 'entity_id = "' . $eID . '" AND entity_type = "' . $eType . '"'
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
						'entity_id'   => $eID,
						'entity_type' => $eType,
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
			$tp = e107::getParser();
			$db = e107::getDb();
			$db->delete('metatag', 'entity_id = "' . (int) $id . '" AND entity_type = "' . $tp->toDB($type) . '"');
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
				$db = e107::getDb();
				$db->select('metatag_default', '*', 'id = ' . (int) $entity_id);

				while($row = $db->fetch())
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
		$form = e107::getForm();
		$tpl = e107::getTemplate('metatag');
		$sc = e107::getScBatch('metatag', true);
		$tp = e107::getParser();

		e107::css('metatag', 'css/metatag.css');
		e107::js('metatag', 'js/metatag.js');

		// Output.
		$html = '';

		// Tokens.
		$this->setWidgetTokens($values['entity_type'], $values['entity_id']);
		// Render token info and button.
		$sc->setVars(array(
			'token_help'   => '<h4>' . LAN_PLUGIN_METATAG_HELP_06 . '</h4><p>' . LAN_PLUGIN_METATAG_HELP_01 . '</p>',
			'token_button' => $form->button('token-button', LAN_PLUGIN_METATAG_HELP_02, 'action', null, array(
				'class' => 'btn-sm',
			)),
		));
		$html .= $tp->parseTemplate($tpl['TOKEN'], true, $sc);


		// Basic meta tags.
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

		// Advanced meta tags.
		$advanced = array();

		$robots = varset($values['data']['robots'], array());

		$checkboxes = '';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('follow', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'follow', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_01;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('index', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'index', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_02;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('noarchive', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'noarchive', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_03;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('nofollow', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'nofollow', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_04;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('noimageindex', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'noimageindex', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_05;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('noindex', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'noindex', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_06;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('noodp', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'noodp', $default);
		$checkboxes .= $tp->lanVars(LAN_METATAG_ADMIN_10_07, array(
			'x' => '<a href="http://www.dmoz.org/" target="_blank">' . LAN_METATAG_ADMIN_10_07_X . '</a>',
		));
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('nosnippet', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'nosnippet', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_08;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('notranslate', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'notranslate', $default);
		$checkboxes .= LAN_METATAG_ADMIN_10_09;
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$checkboxes .= '<div class="checkbox">';
		$checkboxes .= '<label>';
		$default = in_array('noydir', $robots);
		$checkboxes .= $form->checkbox($field . '[robots][]', 'noydir', $default);
		$checkboxes .= $tp->lanVars(LAN_METATAG_ADMIN_10_10, array(
			'x' => '<a href="http://dir.yahoo.com/" target="_blank">' . LAN_METATAG_ADMIN_10_10_X . '</a>',
		));
		$checkboxes .= '</label>';
		$checkboxes .= '</div>';

		$advanced[$field . '[robots]'] = array(
			'label' => LAN_METATAG_ADMIN_10,
			'help'  => $form->help(LAN_METATAG_ADMIN_11),
			'field' => $checkboxes,
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_13, array(
			'x' => '<a href="https://support.google.com/news/publisher/answer/68297?hl=en" target="_blank">' . LAN_METATAG_ADMIN_13_X . '</a>',
		));

		$advanced[$field . '[news_keywords]'] = array(
			'label' => LAN_METATAG_ADMIN_12,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[news_keywords]', varset($values['data']['news_keywords'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_12,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_15, array(
			'x' => '<a href="https://support.google.com/news/publisher/answer/68297?hl=en" target="_blank">' . LAN_METATAG_ADMIN_15_X . '</a>',
		));

		$advanced[$field . '[standout]'] = array(
			'label' => LAN_METATAG_ADMIN_14,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[standout]', varset($values['data']['standout'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_14,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[rating]'] = array(
			'label' => LAN_METATAG_ADMIN_16,
			'help'  => $form->help(LAN_METATAG_ADMIN_17),
			'field' => $form->select($field . '[rating]', array(
				'general'       => LAN_METATAG_ADMIN_16_01,
				'mature'        => LAN_METATAG_ADMIN_16_02,
				'restricted'    => LAN_METATAG_ADMIN_16_03,
				'14 years'      => LAN_METATAG_ADMIN_16_04,
				'safe for kids' => LAN_METATAG_ADMIN_16_05,
			), varset($values['data']['rating'], false), array(
				'label' => LAN_METATAG_ADMIN_16,
				'class' => 'input-block-level',
			), true),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_19, array(
			'x' => '<a href="https://w3c.github.io/webappsec-referrer-policy/" target="_blank">' . LAN_METATAG_ADMIN_19_X . '</a>',
		));

		$advanced[$field . '[referrer]'] = array(
			'label' => LAN_METATAG_ADMIN_18,
			'help'  => $form->help($help),
			'field' => $form->select($field . '[referrer]', array(
				'no-referrer'                => LAN_METATAG_ADMIN_18_01,
				'origin'                     => LAN_METATAG_ADMIN_18_02,
				'no-referrer-when-downgrade' => LAN_METATAG_ADMIN_18_03,
				'origin-when-cross-origin'   => LAN_METATAG_ADMIN_18_04,
				'unsafe-url'                 => LAN_METATAG_ADMIN_18_05,
			), varset($values['data']['referrer'], false), array(
				'label' => LAN_METATAG_ADMIN_18,
				'class' => 'input-block-level',
			), true),
		);

		$advanced[$field . '[generator]'] = array(
			'label' => LAN_METATAG_ADMIN_20,
			'help'  => $form->help(LAN_METATAG_ADMIN_21),
			'field' => $form->text($field . '[generator]', varset($values['data']['generator'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_20,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[rights]'] = array(
			'label' => LAN_METATAG_ADMIN_22,
			'help'  => $form->help(LAN_METATAG_ADMIN_23),
			'field' => $form->text($field . '[rights]', varset($values['data']['rights'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_22,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[image_src]'] = array(
			'label' => LAN_METATAG_ADMIN_24,
			'help'  => $form->help(LAN_METATAG_ADMIN_25),
			'field' => $form->text($field . '[image_src]', varset($values['data']['image_src'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_24,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[canonical]'] = array(
			'label' => LAN_METATAG_ADMIN_26,
			'help'  => $form->help(LAN_METATAG_ADMIN_27),
			'field' => $form->text($field . '[canonical]', varset($values['data']['canonical'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_26,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[shortlink]'] = array(
			'label' => LAN_METATAG_ADMIN_28,
			'help'  => $form->help(LAN_METATAG_ADMIN_29),
			'field' => $form->text($field . '[shortlink]', varset($values['data']['shortlink'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_28,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[publisher]'] = array(
			'label' => LAN_METATAG_ADMIN_30,
			'help'  => $form->help(LAN_METATAG_ADMIN_31),
			'field' => $form->text($field . '[publisher]', varset($values['data']['publisher'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_30,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[author]'] = array(
			'label' => LAN_METATAG_ADMIN_32,
			'help'  => $form->help(LAN_METATAG_ADMIN_33),
			'field' => $form->text($field . '[author]', varset($values['data']['author'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_32,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[original-source]'] = array(
			'label' => LAN_METATAG_ADMIN_34,
			'help'  => $form->help(LAN_METATAG_ADMIN_35),
			'field' => $form->text($field . '[original-source]', varset($values['data']['original-source'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_34,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_37, array(
			'x' => '<a href="https://support.google.com/webmasters/answer/1663744" target="_blank">' . LAN_METATAG_ADMIN_37_X . '</a>',
		));

		$advanced[$field . '[prev]'] = array(
			'label' => LAN_METATAG_ADMIN_36,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[prev]', varset($values['data']['prev'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_36,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_39, array(
			'x' => '<a href="https://support.google.com/webmasters/answer/1663744" target="_blank">' . LAN_METATAG_ADMIN_39_X . '</a>',
		));

		$advanced[$field . '[next]'] = array(
			'label' => LAN_METATAG_ADMIN_38,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[next]', varset($values['data']['next'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_38,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_41, array(
			'x' => '<a href="https://en.wikipedia.org/wiki/Geotagging#HTML_pages" target="_blank">' . LAN_METATAG_ADMIN_41_X . '</a>',
		));

		$advanced[$field . '[geo.position]'] = array(
			'label' => LAN_METATAG_ADMIN_40,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[geo.position]', varset($values['data']['geo.position'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_40,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[geo.placename]'] = array(
			'label' => LAN_METATAG_ADMIN_42,
			'help'  => $form->help(LAN_METATAG_ADMIN_43),
			'field' => $form->text($field . '[geo.placename]', varset($values['data']['geo.placename'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_42,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[geo.region]'] = array(
			'label' => LAN_METATAG_ADMIN_44,
			'help'  => $form->help(LAN_METATAG_ADMIN_45),
			'field' => $form->text($field . '[geo.region]', varset($values['data']['geo.region'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_44,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_47, array(
			'x' => '<a href="https://en.wikipedia.org/wiki/Geotagging#HTML_pages" target="_blank">' . LAN_METATAG_ADMIN_47_X . '</a>',
		));

		$advanced[$field . '[icbm]'] = array(
			'label' => LAN_METATAG_ADMIN_46,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[icbm]', varset($values['data']['icbm'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_46,
				'class' => 'input-block-level',
			)),
		);

		$advanced[$field . '[refresh]'] = array(
			'label' => LAN_METATAG_ADMIN_48,
			'help'  => $form->help(LAN_METATAG_ADMIN_49),
			'field' => $form->text($field . '[refresh]', varset($values['data']['refresh'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_48,
				'class' => 'input-block-level',
			)),
		);

		// Open Graph meta tags.
		$opengraph = array();

		$opengraph[$field . '[og:site_name]'] = array(
			'label' => LAN_METATAG_ADMIN_160,
			'help'  => $form->help(LAN_METATAG_ADMIN_161),
			'field' => $form->text($field . '[og:site_name]', varset($values['data']['og:site_name'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_160,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:type]'] = array(
			'label' => LAN_METATAG_ADMIN_162,
			'help'  => $form->help(LAN_METATAG_ADMIN_163),
			'field' => $form->select($field . '[og:type]', array(
				LAN_METATAG_ADMIN_162_01 => array(
					'activity' => LAN_METATAG_ADMIN_162_02,
					'sport'    => LAN_METATAG_ADMIN_162_03,
				),
				LAN_METATAG_ADMIN_162_04 => array(
					'bar'        => LAN_METATAG_ADMIN_162_05,
					'company'    => LAN_METATAG_ADMIN_162_06,
					'cafe'       => LAN_METATAG_ADMIN_162_07,
					'hotel'      => LAN_METATAG_ADMIN_162_08,
					'restaurant' => LAN_METATAG_ADMIN_162_09,
				),
				LAN_METATAG_ADMIN_162_10 => array(
					'cause'         => LAN_METATAG_ADMIN_162_11,
					'sports_league' => LAN_METATAG_ADMIN_162_12,
					'sports_team'   => LAN_METATAG_ADMIN_162_13,
				),
				LAN_METATAG_ADMIN_162_14 => array(
					'band'       => LAN_METATAG_ADMIN_162_15,
					'government' => LAN_METATAG_ADMIN_162_16,
					'non_profit' => LAN_METATAG_ADMIN_162_17,
					'school'     => LAN_METATAG_ADMIN_162_18,
					'university' => LAN_METATAG_ADMIN_162_19,
				),
				LAN_METATAG_ADMIN_162_20 => array(
					'actor'         => LAN_METATAG_ADMIN_162_21,
					'athlete'       => LAN_METATAG_ADMIN_162_22,
					'author'        => LAN_METATAG_ADMIN_162_23,
					'director'      => LAN_METATAG_ADMIN_162_24,
					'musician'      => LAN_METATAG_ADMIN_162_25,
					'politician'    => LAN_METATAG_ADMIN_162_26,
					'profile'       => LAN_METATAG_ADMIN_162_27,
					'public_figure' => LAN_METATAG_ADMIN_162_28,
				),
				LAN_METATAG_ADMIN_162_29 => array(
					'city'           => LAN_METATAG_ADMIN_162_30,
					'country'        => LAN_METATAG_ADMIN_162_31,
					'landmark'       => LAN_METATAG_ADMIN_162_32,
					'state_province' => LAN_METATAG_ADMIN_162_33,
				),
				LAN_METATAG_ADMIN_162_34 => array(
					'album'         => LAN_METATAG_ADMIN_162_35,
					'book'          => LAN_METATAG_ADMIN_162_36,
					'drink'         => LAN_METATAG_ADMIN_162_37,
					'food'          => LAN_METATAG_ADMIN_162_38,
					'game'          => LAN_METATAG_ADMIN_162_39,
					'product'       => LAN_METATAG_ADMIN_162_40,
					'song'          => LAN_METATAG_ADMIN_162_41,
					'video.movie'   => LAN_METATAG_ADMIN_162_42,
					'video.tv_show' => LAN_METATAG_ADMIN_162_43,
					'video.episode' => LAN_METATAG_ADMIN_162_44,
					'video.other'   => LAN_METATAG_ADMIN_162_45,
				),
				LAN_METATAG_ADMIN_162_46 => array(
					'website' => LAN_METATAG_ADMIN_162_47,
					'article' => LAN_METATAG_ADMIN_162_48,
				),
			), varset($values['data']['og:type'], false), array(
				'label' => LAN_METATAG_ADMIN_162,
				'class' => 'input-block-level',
			), true),
		);

		$opengraph[$field . '[og:url]'] = array(
			'label' => LAN_METATAG_ADMIN_164,
			'help'  => $form->help(LAN_METATAG_ADMIN_165),
			'field' => $form->text($field . '[og:url]', varset($values['data']['og:url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_164,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:title]'] = array(
			'label' => LAN_METATAG_ADMIN_166,
			'help'  => $form->help(LAN_METATAG_ADMIN_167),
			'field' => $form->text($field . '[og:title]', varset($values['data']['og:title'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_166,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:determiner]'] = array(
			'label' => LAN_METATAG_ADMIN_168,
			'help'  => $form->help(LAN_METATAG_ADMIN_169),
			'field' => $form->select($field . '[og:determiner]', array(
				'auto' => LAN_METATAG_ADMIN_168_01,
				'a'    => LAN_METATAG_ADMIN_168_02,
				'an'   => LAN_METATAG_ADMIN_168_03,
				'the'  => LAN_METATAG_ADMIN_168_04,
			), varset($values['data']['og:determiner'], false), array(
				'label' => LAN_METATAG_ADMIN_168,
				'class' => 'input-block-level',
			), true),
		);

		$opengraph[$field . '[og:description]'] = array(
			'label' => LAN_METATAG_ADMIN_170,
			'help'  => $form->help(LAN_METATAG_ADMIN_171),
			'field' => $form->text($field . '[og:description]', varset($values['data']['og:description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_170,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_173, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_173_X . '</a>',
		));

		$opengraph[$field . '[og:updated_time]'] = array(
			'label' => LAN_METATAG_ADMIN_172,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[og:updated_time]', varset($values['data']['og:updated_time'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_172,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:see_also]'] = array(
			'label' => LAN_METATAG_ADMIN_174,
			'help'  => $form->help(LAN_METATAG_ADMIN_175),
			'field' => $form->text($field . '[og:see_also]', varset($values['data']['og:see_also'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_174,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image]'] = array(
			'label' => LAN_METATAG_ADMIN_176,
			'help'  => $form->help(LAN_METATAG_ADMIN_177),
			'field' => $form->text($field . '[og:image]', varset($values['data']['og:image'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_176,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image:url]'] = array(
			'label' => LAN_METATAG_ADMIN_178,
			'help'  => $form->help(LAN_METATAG_ADMIN_179),
			'field' => $form->text($field . '[og:image:url]', varset($values['data']['og:image:url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_178,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image:secure_url]'] = array(
			'label' => LAN_METATAG_ADMIN_180,
			'help'  => $form->help(LAN_METATAG_ADMIN_181),
			'field' => $form->text($field . '[og:image:secure_url]', varset($values['data']['og:image:secure_url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_180,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image:type]'] = array(
			'label' => LAN_METATAG_ADMIN_182,
			'help'  => $form->help(LAN_METATAG_ADMIN_183),
			'field' => $form->text($field . '[og:image:type]', varset($values['data']['og:image:type'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_182,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image:width]'] = array(
			'label' => LAN_METATAG_ADMIN_184,
			'help'  => $form->help(LAN_METATAG_ADMIN_185),
			'field' => $form->text($field . '[og:image:width]', varset($values['data']['og:image:width'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_184,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:image:height]'] = array(
			'label' => LAN_METATAG_ADMIN_186,
			'help'  => $form->help(LAN_METATAG_ADMIN_187),
			'field' => $form->text($field . '[og:image:height]', varset($values['data']['og:image:height'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_186,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:latitude]'] = array(
			'label' => LAN_METATAG_ADMIN_188,
			'help'  => $form->help(LAN_METATAG_ADMIN_189),
			'field' => $form->text($field . '[og:latitude]', varset($values['data']['og:latitude'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_188,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:longitude]'] = array(
			'label' => LAN_METATAG_ADMIN_190,
			'help'  => $form->help(LAN_METATAG_ADMIN_191),
			'field' => $form->text($field . '[og:longitude]', varset($values['data']['og:longitude'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_190,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:street_address]'] = array(
			'label' => LAN_METATAG_ADMIN_192,
			'help'  => $form->help(LAN_METATAG_ADMIN_193),
			'field' => $form->text($field . '[og:street_address]', varset($values['data']['og:street_address'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_192,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:locality]'] = array(
			'label' => LAN_METATAG_ADMIN_194,
			'help'  => $form->help(LAN_METATAG_ADMIN_195),
			'field' => $form->text($field . '[og:locality]', varset($values['data']['og:locality'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_194,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:region]'] = array(
			'label' => LAN_METATAG_ADMIN_196,
			'help'  => $form->help(LAN_METATAG_ADMIN_197),
			'field' => $form->text($field . '[og:region]', varset($values['data']['og:region'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_196,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:postal_code]'] = array(
			'label' => LAN_METATAG_ADMIN_198,
			'help'  => $form->help(LAN_METATAG_ADMIN_199),
			'field' => $form->text($field . '[og:postal_code]', varset($values['data']['og:postal_code'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_198,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:country_name]'] = array(
			'label' => LAN_METATAG_ADMIN_200,
			'help'  => $form->help(LAN_METATAG_ADMIN_201),
			'field' => $form->text($field . '[og:country_name]', varset($values['data']['og:country_name'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_200,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:email]'] = array(
			'label' => LAN_METATAG_ADMIN_202,
			'help'  => $form->help(LAN_METATAG_ADMIN_203),
			'field' => $form->text($field . '[og:email]', varset($values['data']['og:email'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_202,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:phone_number]'] = array(
			'label' => LAN_METATAG_ADMIN_204,
			'help'  => $form->help(LAN_METATAG_ADMIN_205),
			'field' => $form->text($field . '[og:phone_number]', varset($values['data']['og:phone_number'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_204,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:fax_number]'] = array(
			'label' => LAN_METATAG_ADMIN_206,
			'help'  => $form->help(LAN_METATAG_ADMIN_207),
			'field' => $form->text($field . '[og:fax_number]', varset($values['data']['og:fax_number'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_206,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:locale]'] = array(
			'label' => LAN_METATAG_ADMIN_208,
			'help'  => $form->help(LAN_METATAG_ADMIN_209),
			'field' => $form->text($field . '[og:locale]', varset($values['data']['og:locale'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_208,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:locale:alternate]'] = array(
			'label' => LAN_METATAG_ADMIN_210,
			'help'  => $form->help(LAN_METATAG_ADMIN_211),
			'field' => $form->text($field . '[og:locale:alternate]', varset($values['data']['og:locale:alternate'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_210,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[article:author]'] = array(
			'label' => LAN_METATAG_ADMIN_212,
			'help'  => $form->help(LAN_METATAG_ADMIN_213),
			'field' => $form->text($field . '[article:author]', varset($values['data']['article:author'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_212,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[article:publisher]'] = array(
			'label' => LAN_METATAG_ADMIN_214,
			'help'  => $form->help(LAN_METATAG_ADMIN_215),
			'field' => $form->text($field . '[article:publisher]', varset($values['data']['article:publisher'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_214,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[article:section]'] = array(
			'label' => LAN_METATAG_ADMIN_216,
			'help'  => $form->help(LAN_METATAG_ADMIN_217),
			'field' => $form->text($field . '[article:section]', varset($values['data']['article:section'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_216,
				'class' => 'input-block-level',
			)),
		);

		// TODO - use selectize instead.
		$opengraph[$field . '[article:tag]'] = array(
			'label' => LAN_METATAG_ADMIN_218,
			'help'  => $form->help(LAN_METATAG_ADMIN_219),
			'field' => $form->text($field . '[article:tag]', varset($values['data']['article:tag'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_218,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_221, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_221_X . '</a>',
		));

		$opengraph[$field . '[article:published_time]'] = array(
			'label' => LAN_METATAG_ADMIN_220,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[article:published_time]', varset($values['data']['article:published_time'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_220,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_223, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_223_X . '</a>',
		));

		$opengraph[$field . '[article:modified_time]'] = array(
			'label' => LAN_METATAG_ADMIN_222,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[article:modified_time]', varset($values['data']['article:modified_time'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_222,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_225, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_225_X . '</a>',
		));

		$opengraph[$field . '[article:expiration_time]'] = array(
			'label' => LAN_METATAG_ADMIN_224,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[article:expiration_time]', varset($values['data']['article:expiration_time'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_224,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[profile:first_name]'] = array(
			'label' => LAN_METATAG_ADMIN_226,
			'help'  => $form->help(LAN_METATAG_ADMIN_227),
			'field' => $form->text($field . '[profile:first_name]', varset($values['data']['profile:first_name'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_226,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[profile:last_name]'] = array(
			'label' => LAN_METATAG_ADMIN_228,
			'help'  => $form->help(LAN_METATAG_ADMIN_229),
			'field' => $form->text($field . '[profile:last_name]', varset($values['data']['profile:last_name'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_228,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[profile:username]'] = array(
			'label' => LAN_METATAG_ADMIN_230,
			'help'  => $form->help(LAN_METATAG_ADMIN_231),
			'field' => $form->text($field . '[profile:username]', varset($values['data']['profile:username'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_230,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[profile:gender]'] = array(
			'label' => LAN_METATAG_ADMIN_232,
			'help'  => $form->help(LAN_METATAG_ADMIN_233),
			'field' => $form->text($field . '[profile:gender]', varset($values['data']['profile:gender'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_232,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:audio]'] = array(
			'label' => LAN_METATAG_ADMIN_234,
			'help'  => $form->help(LAN_METATAG_ADMIN_235),
			'field' => $form->text($field . '[og:audio]', varset($values['data']['og:audio'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_234,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:audio:secure_url]'] = array(
			'label' => LAN_METATAG_ADMIN_236,
			'help'  => $form->help(LAN_METATAG_ADMIN_237),
			'field' => $form->text($field . '[og:audio:secure_url]', varset($values['data']['og:audio:secure_url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_236,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:audio:type]'] = array(
			'label' => LAN_METATAG_ADMIN_238,
			'help'  => $form->help(LAN_METATAG_ADMIN_239),
			'field' => $form->text($field . '[og:audio:type]', varset($values['data']['og:audio:type'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_238,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[book:author]'] = array(
			'label' => LAN_METATAG_ADMIN_240,
			'help'  => $form->help(LAN_METATAG_ADMIN_241),
			'field' => $form->text($field . '[book:author]', varset($values['data']['book:author'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_240,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_243, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/International_Standard_Book_Number" target="_blank">' . LAN_METATAG_ADMIN_243_X . '</a>',
		));

		$opengraph[$field . '[book:isbn]'] = array(
			'label' => LAN_METATAG_ADMIN_242,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[book:isbn]', varset($values['data']['book:isbn'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_242,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_245, array(
			'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_245_X . '</a>',
		));

		$opengraph[$field . '[book:release_date]'] = array(
			'label' => LAN_METATAG_ADMIN_244,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[book:release_date]', varset($values['data']['book:release_date'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_244,
				'class' => 'input-block-level',
			)),
		);

		// TODO - use selectize instead.
		$opengraph[$field . '[book:tag]'] = array(
			'label' => LAN_METATAG_ADMIN_246,
			'help'  => $form->help(LAN_METATAG_ADMIN_247),
			'field' => $form->text($field . '[book:tag]', varset($values['data']['book:tag'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_246,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:video:url]'] = array(
			'label' => LAN_METATAG_ADMIN_248,
			'help'  => $form->help(LAN_METATAG_ADMIN_249),
			'field' => $form->text($field . '[og:video:url]', varset($values['data']['og:video:url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_248,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:video:secure_url]'] = array(
			'label' => LAN_METATAG_ADMIN_250,
			'help'  => $form->help(LAN_METATAG_ADMIN_251),
			'field' => $form->text($field . '[og:video:secure_url]', varset($values['data']['og:video:secure_url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_250,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:video:width]'] = array(
			'label' => LAN_METATAG_ADMIN_252,
			'help'  => $form->help(LAN_METATAG_ADMIN_253),
			'field' => $form->text($field . '[og:video:width]', varset($values['data']['og:video:width'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_252,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:video:height]'] = array(
			'label' => LAN_METATAG_ADMIN_254,
			'help'  => $form->help(LAN_METATAG_ADMIN_255),
			'field' => $form->text($field . '[og:video:height]', varset($values['data']['og:video:height'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_254,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[og:video:type]'] = array(
			'label' => LAN_METATAG_ADMIN_256,
			'help'  => $form->help(LAN_METATAG_ADMIN_257),
			'field' => $form->text($field . '[og:video:type]', varset($values['data']['og:video:type'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_256,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:actor]'] = array(
			'label' => LAN_METATAG_ADMIN_258,
			'help'  => $form->help(LAN_METATAG_ADMIN_259),
			'field' => $form->text($field . '[video:actor]', varset($values['data']['video:actor'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_258,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:actor:role]'] = array(
			'label' => LAN_METATAG_ADMIN_260,
			'help'  => $form->help(LAN_METATAG_ADMIN_261),
			'field' => $form->text($field . '[video:actor:role]', varset($values['data']['video:actor:role'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_260,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:director]'] = array(
			'label' => LAN_METATAG_ADMIN_262,
			'help'  => $form->help(LAN_METATAG_ADMIN_263),
			'field' => $form->text($field . '[video:director]', varset($values['data']['video:director'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_262,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:writer]'] = array(
			'label' => LAN_METATAG_ADMIN_264,
			'help'  => $form->help(LAN_METATAG_ADMIN_265),
			'field' => $form->text($field . '[video:writer]', varset($values['data']['video:writer'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_264,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:duration]'] = array(
			'label' => LAN_METATAG_ADMIN_266,
			'help'  => $form->help(LAN_METATAG_ADMIN_267),
			'field' => $form->text($field . '[video:duration]', varset($values['data']['video:duration'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_266,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:release_date]'] = array(
			'label' => LAN_METATAG_ADMIN_268,
			'help'  => $form->help(LAN_METATAG_ADMIN_269),
			'field' => $form->text($field . '[video:release_date]', varset($values['data']['video:release_date'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_268,
				'class' => 'input-block-level',
			)),
		);

		// TODO - use selectize instead.
		$opengraph[$field . '[video:tag]'] = array(
			'label' => LAN_METATAG_ADMIN_270,
			'help'  => $form->help(LAN_METATAG_ADMIN_271),
			'field' => $form->text($field . '[video:tag]', varset($values['data']['video:tag'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_270,
				'class' => 'input-block-level',
			)),
		);

		$opengraph[$field . '[video:series]'] = array(
			'label' => LAN_METATAG_ADMIN_272,
			'help'  => $form->help(LAN_METATAG_ADMIN_273),
			'field' => $form->text($field . '[video:series]', varset($values['data']['video:series'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_272,
				'class' => 'input-block-level',
			)),
		);

		// Facebook meta tags.
		$facebook = array();

		$facebook[$field . '[fb:admins]'] = array(
			'label' => LAN_METATAG_ADMIN_50,
			'help'  => $form->help(LAN_METATAG_ADMIN_51),
			'field' => $form->text($field . '[fb:admins]', varset($values['data']['fb:admins'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_50,
				'class' => 'input-block-level',
			)),
		);

		$facebook[$field . '[fb:app_id]'] = array(
			'label' => LAN_METATAG_ADMIN_52,
			'help'  => $form->help(LAN_METATAG_ADMIN_53),
			'field' => $form->text($field . '[fb:app_id]', varset($values['data']['fb:app_id'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_52,
				'class' => 'input-block-level',
			)),
		);

		// Twitter card meta tags.
		$twitter = array();

		$twitter[$field . '[twitter:card]'] = array(
			'label' => LAN_METATAG_ADMIN_90,
			'help'  => $form->help(LAN_METATAG_ADMIN_91),
			'field' => $form->select($field . '[twitter:card]', array(
				'summary'             => LAN_METATAG_ADMIN_90_01,
				'summary_large_image' => LAN_METATAG_ADMIN_90_02,
				'photo'               => LAN_METATAG_ADMIN_90_03,
				'player'              => LAN_METATAG_ADMIN_90_04,
				'gallery'             => LAN_METATAG_ADMIN_90_05,
				'app'                 => LAN_METATAG_ADMIN_90_06,
				'product'             => LAN_METATAG_ADMIN_90_07,
			), varset($values['data']['twitter:card'], false), array(
				'label' => LAN_METATAG_ADMIN_90,
				'class' => 'input-block-level',
			), true),
		);

		$twitter[$field . '[twitter:site:id]'] = array(
			'label' => LAN_METATAG_ADMIN_94,
			'help'  => $form->help(LAN_METATAG_ADMIN_95),
			'field' => $form->text($field . '[twitter:site:id]', varset($values['data']['twitter:site:id'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_94,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:creator]'] = array(
			'label' => LAN_METATAG_ADMIN_96,
			'help'  => $form->help(LAN_METATAG_ADMIN_97),
			'field' => $form->text($field . '[twitter:creator]', varset($values['data']['twitter:creator'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_96,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:creator:id]'] = array(
			'label' => LAN_METATAG_ADMIN_98,
			'help'  => $form->help(LAN_METATAG_ADMIN_99),
			'field' => $form->text($field . '[twitter:creator:id]', varset($values['data']['twitter:creator:id'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_98,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:url]'] = array(
			'label' => LAN_METATAG_ADMIN_100,
			'help'  => $form->help(LAN_METATAG_ADMIN_101),
			'field' => $form->text($field . '[twitter:url]', varset($values['data']['twitter:url'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_100,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:title]'] = array(
			'label' => LAN_METATAG_ADMIN_102,
			'help'  => $form->help(LAN_METATAG_ADMIN_103),
			'field' => $form->text($field . '[twitter:title]', varset($values['data']['twitter:title'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_102,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:description]'] = array(
			'label' => LAN_METATAG_ADMIN_104,
			'help'  => $form->help(LAN_METATAG_ADMIN_105),
			'field' => $form->text($field . '[twitter:description]', varset($values['data']['twitter:description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_104,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image]'] = array(
			'label' => LAN_METATAG_ADMIN_106,
			'help'  => $form->help(LAN_METATAG_ADMIN_107),
			'field' => $form->text($field . '[twitter:image]', varset($values['data']['twitter:image'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_106,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image:width]'] = array(
			'label' => LAN_METATAG_ADMIN_108,
			'help'  => $form->help(LAN_METATAG_ADMIN_109),
			'field' => $form->text($field . '[twitter:image:width]', varset($values['data']['twitter:image:width'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_108,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image:height]'] = array(
			'label' => LAN_METATAG_ADMIN_110,
			'help'  => $form->help(LAN_METATAG_ADMIN_111),
			'field' => $form->text($field . '[twitter:image:height]', varset($values['data']['twitter:image:height'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_110,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image:alt]'] = array(
			'label' => LAN_METATAG_ADMIN_112,
			'help'  => $form->help(LAN_METATAG_ADMIN_113),
			'field' => $form->text($field . '[twitter:image:alt]', varset($values['data']['twitter:image:alt'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_112,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image0]'] = array(
			'label' => LAN_METATAG_ADMIN_114,
			'help'  => $form->help(LAN_METATAG_ADMIN_115),
			'field' => $form->text($field . '[twitter:image0]', varset($values['data']['twitter:image0'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_114,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image1]'] = array(
			'label' => LAN_METATAG_ADMIN_116,
			'help'  => $form->help(LAN_METATAG_ADMIN_117),
			'field' => $form->text($field . '[twitter:image1]', varset($values['data']['twitter:image1'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_116,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image2]'] = array(
			'label' => LAN_METATAG_ADMIN_118,
			'help'  => $form->help(LAN_METATAG_ADMIN_119),
			'field' => $form->text($field . '[twitter:image2]', varset($values['data']['twitter:image2'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_118,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:image3]'] = array(
			'label' => LAN_METATAG_ADMIN_120,
			'help'  => $form->help(LAN_METATAG_ADMIN_121),
			'field' => $form->text($field . '[twitter:image3]', varset($values['data']['twitter:image3'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_120,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:player]'] = array(
			'label' => LAN_METATAG_ADMIN_122,
			'help'  => $form->help(LAN_METATAG_ADMIN_123),
			'field' => $form->text($field . '[twitter:player]', varset($values['data']['twitter:player'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_122,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:player:width]'] = array(
			'label' => LAN_METATAG_ADMIN_124,
			'help'  => $form->help(LAN_METATAG_ADMIN_125),
			'field' => $form->text($field . '[twitter:player:width]', varset($values['data']['twitter:player:width'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_124,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:player:height]'] = array(
			'label' => LAN_METATAG_ADMIN_126,
			'help'  => $form->help(LAN_METATAG_ADMIN_127),
			'field' => $form->text($field . '[twitter:player:height]', varset($values['data']['twitter:player:height'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_126,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:player:stream]'] = array(
			'label' => LAN_METATAG_ADMIN_128,
			'help'  => $form->help(LAN_METATAG_ADMIN_129),
			'field' => $form->text($field . '[twitter:player:stream]', varset($values['data']['twitter:player:stream'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_128,
				'class' => 'input-block-level',
			)),
		);

		$help = $tp->lanVars(LAN_METATAG_ADMIN_131, array(
			'x' => '<a href="http://tools.ietf.org/rfc/rfc4337.txt" target="_blank">' . LAN_METATAG_ADMIN_131_X . '</a>',
		));

		$twitter[$field . '[twitter:player:stream:content_type]'] = array(
			'label' => LAN_METATAG_ADMIN_130,
			'help'  => $form->help($help),
			'field' => $form->text($field . '[twitter:player:stream:content_type]', varset($values['data']['twitter:player:stream:content_type'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_130,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:country]'] = array(
			'label' => LAN_METATAG_ADMIN_132,
			'help'  => $form->help(LAN_METATAG_ADMIN_133),
			'field' => $form->text($field . '[twitter:app:country]', varset($values['data']['twitter:app:country'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_132,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:name:iphone]'] = array(
			'label' => LAN_METATAG_ADMIN_134,
			'help'  => $form->help(LAN_METATAG_ADMIN_135),
			'field' => $form->text($field . '[twitter:app:name:iphone]', varset($values['data']['twitter:app:name:iphone'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_134,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:id:iphone]'] = array(
			'label' => LAN_METATAG_ADMIN_136,
			'help'  => $form->help(LAN_METATAG_ADMIN_137),
			'field' => $form->text($field . '[twitter:app:id:iphone]', varset($values['data']['twitter:app:id:iphone'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_136,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:url:iphone]'] = array(
			'label' => LAN_METATAG_ADMIN_138,
			'help'  => $form->help(LAN_METATAG_ADMIN_139),
			'field' => $form->text($field . '[twitter:app:url:iphone]', varset($values['data']['twitter:app:url:iphone'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_138,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:name:ipad]'] = array(
			'label' => LAN_METATAG_ADMIN_140,
			'help'  => $form->help(LAN_METATAG_ADMIN_141),
			'field' => $form->text($field . '[twitter:app:name:ipad]', varset($values['data']['twitter:app:name:ipad'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_140,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:id:ipad]'] = array(
			'label' => LAN_METATAG_ADMIN_142,
			'help'  => $form->help(LAN_METATAG_ADMIN_143),
			'field' => $form->text($field . '[twitter:app:id:ipad]', varset($values['data']['twitter:app:id:ipad'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_142,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:url:ipad]'] = array(
			'label' => LAN_METATAG_ADMIN_144,
			'help'  => $form->help(LAN_METATAG_ADMIN_145),
			'field' => $form->text($field . '[twitter:app:url:ipad]', varset($values['data']['twitter:app:url:ipad'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_144,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:name:googleplay]'] = array(
			'label' => LAN_METATAG_ADMIN_146,
			'help'  => $form->help(LAN_METATAG_ADMIN_147),
			'field' => $form->text($field . '[twitter:app:name:googleplay]', varset($values['data']['twitter:app:name:googleplay'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_146,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:id:googleplay]'] = array(
			'label' => LAN_METATAG_ADMIN_148,
			'help'  => $form->help(LAN_METATAG_ADMIN_149),
			'field' => $form->text($field . '[twitter:app:id:googleplay]', varset($values['data']['twitter:app:id:googleplay'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_148,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:app:url:googleplay]'] = array(
			'label' => LAN_METATAG_ADMIN_150,
			'help'  => $form->help(LAN_METATAG_ADMIN_151),
			'field' => $form->text($field . '[twitter:app:url:googleplay]', varset($values['data']['twitter:app:url:googleplay'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_150,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:label1]'] = array(
			'label' => LAN_METATAG_ADMIN_152,
			'help'  => $form->help(LAN_METATAG_ADMIN_153),
			'field' => $form->text($field . '[twitter:label1]', varset($values['data']['twitter:label1'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_152,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:data1]'] = array(
			'label' => LAN_METATAG_ADMIN_154,
			'help'  => $form->help(LAN_METATAG_ADMIN_155),
			'field' => $form->text($field . '[twitter:data1]', varset($values['data']['twitter:data1'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_154,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:label2]'] = array(
			'label' => LAN_METATAG_ADMIN_156,
			'help'  => $form->help(LAN_METATAG_ADMIN_157),
			'field' => $form->text($field . '[twitter:label2]', varset($values['data']['twitter:label2'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_156,
				'class' => 'input-block-level',
			)),
		);

		$twitter[$field . '[twitter:data2]'] = array(
			'label' => LAN_METATAG_ADMIN_158,
			'help'  => $form->help(LAN_METATAG_ADMIN_159),
			'field' => $form->text($field . '[twitter:data2]', varset($values['data']['twitter:data2'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_158,
				'class' => 'input-block-level',
			)),
		);

		// Dublin Core Basic Tags.
		$dublin = array();

		$dublin[$field . '[dcterms.title]'] = array(
			'label' => LAN_METATAG_ADMIN_60,
			'help'  => $form->help(LAN_METATAG_ADMIN_61),
			'field' => $form->text($field . '[dcterms.title]', varset($values['data']['dcterms.title'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_60,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.creator]'] = array(
			'label' => LAN_METATAG_ADMIN_62,
			'help'  => $form->help(LAN_METATAG_ADMIN_63),
			'field' => $form->text($field . '[dcterms.creator]', varset($values['data']['dcterms.creator'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_62,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.subject]'] = array(
			'label' => LAN_METATAG_ADMIN_64,
			'help'  => $form->help(LAN_METATAG_ADMIN_65),
			'field' => $form->text($field . '[dcterms.subject]', varset($values['data']['dcterms.subject'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_64,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.description]'] = array(
			'label' => LAN_METATAG_ADMIN_66,
			'help'  => $form->help(LAN_METATAG_ADMIN_67),
			'field' => $form->text($field . '[dcterms.description]', varset($values['data']['dcterms.description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_66,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.publisher]'] = array(
			'label' => LAN_METATAG_ADMIN_68,
			'help'  => $form->help(LAN_METATAG_ADMIN_69),
			'field' => $form->text($field . '[dcterms.publisher]', varset($values['data']['dcterms.publisher'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_68,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.contributor]'] = array(
			'label' => LAN_METATAG_ADMIN_70,
			'help'  => $form->help(LAN_METATAG_ADMIN_71),
			'field' => $form->text($field . '[dcterms.contributor]', varset($values['data']['dcterms.contributor'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_70,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.date]'] = array(
			'label' => LAN_METATAG_ADMIN_72,
			'help'  => $form->help(LAN_METATAG_ADMIN_73),
			'field' => $form->text($field . '[dcterms.date]', varset($values['data']['dcterms.date'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_72,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.type]'] = array(
			'label' => LAN_METATAG_ADMIN_74,
			'help'  => $form->help(LAN_METATAG_ADMIN_75),
			'field' => $form->select($field . '[dcterms.type]', array(
				'Collection'          => LAN_METATAG_ADMIN_74_01,
				'Dataset'             => LAN_METATAG_ADMIN_74_02,
				'Event'               => LAN_METATAG_ADMIN_74_03,
				'Image'               => LAN_METATAG_ADMIN_74_04,
				'InteractiveResource' => LAN_METATAG_ADMIN_74_05,
				'MovingImage'         => LAN_METATAG_ADMIN_74_06,
				'PhysicalObject'      => LAN_METATAG_ADMIN_74_07,
				'Service'             => LAN_METATAG_ADMIN_74_08,
				'Software'            => LAN_METATAG_ADMIN_74_09,
				'Sound'               => LAN_METATAG_ADMIN_74_10,
				'StillImage'          => LAN_METATAG_ADMIN_74_11,
				'Text'                => LAN_METATAG_ADMIN_74_12,
			), varset($values['data']['dcterms.type'], false), array(
				'label' => LAN_METATAG_ADMIN_74,
				'class' => 'input-block-level',
			), true),
		);

		$dublin[$field . '[dcterms.format]'] = array(
			'label' => LAN_METATAG_ADMIN_76,
			'help'  => $form->help(LAN_METATAG_ADMIN_77),
			'field' => $form->text($field . '[dcterms.format]', varset($values['data']['dcterms.format'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_76,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.identifier]'] = array(
			'label' => LAN_METATAG_ADMIN_78,
			'help'  => $form->help(LAN_METATAG_ADMIN_79),
			'field' => $form->text($field . '[dcterms.identifier]', varset($values['data']['dcterms.identifier'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_78,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.source]'] = array(
			'label' => LAN_METATAG_ADMIN_80,
			'help'  => $form->help(LAN_METATAG_ADMIN_81),
			'field' => $form->text($field . '[dcterms.source]', varset($values['data']['dcterms.source'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_80,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.language]'] = array(
			'label' => LAN_METATAG_ADMIN_76,
			'help'  => $form->help(LAN_METATAG_ADMIN_77),
			'field' => $form->text($field . '[dcterms.language]', varset($values['data']['dcterms.language'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_76,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.format]'] = array(
			'label' => LAN_METATAG_ADMIN_82,
			'help'  => $form->help(LAN_METATAG_ADMIN_83),
			'field' => $form->text($field . '[dcterms.format]', varset($values['data']['dcterms.format'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_82,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.relation]'] = array(
			'label' => LAN_METATAG_ADMIN_84,
			'help'  => $form->help(LAN_METATAG_ADMIN_85),
			'field' => $form->text($field . '[dcterms.relation]', varset($values['data']['dcterms.relation'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_84,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.coverage]'] = array(
			'label' => LAN_METATAG_ADMIN_86,
			'help'  => $form->help(LAN_METATAG_ADMIN_87),
			'field' => $form->text($field . '[dcterms.coverage]', varset($values['data']['dcterms.coverage'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_86,
				'class' => 'input-block-level',
			)),
		);

		$dublin[$field . '[dcterms.rights]'] = array(
			'label' => LAN_METATAG_ADMIN_88,
			'help'  => $form->help(LAN_METATAG_ADMIN_89),
			'field' => $form->text($field . '[dcterms.rights]', varset($values['data']['dcterms.rights'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_88,
				'class' => 'input-block-level',
			)),
		);

		// Google+ meta tags.
		$google = array();

		$google[$field . '[itemprop:name]'] = array(
			'label' => LAN_METATAG_ADMIN_54,
			'help'  => $form->help(LAN_METATAG_ADMIN_55),
			'field' => $form->text($field . '[itemprop:name]', varset($values['data']['itemprop:name'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_54,
				'class' => 'input-block-level',
			)),
		);

		$google[$field . '[itemprop:description]'] = array(
			'label' => LAN_METATAG_ADMIN_56,
			'help'  => $form->help(LAN_METATAG_ADMIN_57),
			'field' => $form->text($field . '[itemprop:description]', varset($values['data']['itemprop:description'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_56,
				'class' => 'input-block-level',
			)),
		);

		$google[$field . '[itemprop:image]'] = array(
			'label' => LAN_METATAG_ADMIN_58,
			'help'  => $form->help(LAN_METATAG_ADMIN_59),
			'field' => $form->text($field . '[itemprop:image]', varset($values['data']['itemprop:image'], ''), 255, array(
				'label' => LAN_METATAG_ADMIN_58,
				'class' => 'input-block-level',
			)),
		);

		// Finally, we render the panels.
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_01, $basic);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_02, $advanced);
		$help = $tp->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_01, array(
			'x' => '<a href="http://ogp.me/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_01_X . '</a>',
		));
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_03, $opengraph, $help);
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_04, $facebook, LAN_METATAG_ADMIN_PANEL_HELP_02);
		$help = $tp->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_03, array(
			'x' => '<a href="https://twitter.com/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_03_X . '</a>',
		));
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_05, $twitter, $help);
		$help = $tp->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_04, array(
			'x' => '<a href="http://dublincore.org/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_04_X . '</a>',
		));
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_06, $dublin, $help);
		$help = $tp->lanVars(LAN_METATAG_ADMIN_PANEL_HELP_05, array(
			'x' => '<a href="https://plus.google.com/" target="_blank">' . LAN_METATAG_ADMIN_PANEL_HELP_05_X . '</a>',
		));
		$html .= $this->getWidgetPanel(LAN_METATAG_ADMIN_PANEL_07, $google, $help);

		return '<div class="metatag-widget-container">' . $html . '</div>';
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
		$tpl = e107::getTemplate('metatag');
		$sc = e107::getScBatch('metatag', true);
		$tp = e107::getParser();

		$body = '';

		if(!empty($help))
		{
			$sc->setVars(array(
				'panel_help' => $help,
			));

			$body .= $tp->parseTemplate($tpl['PANEL']['HELP'], true, $sc);
		}

		if(is_array($fields))
		{
			$form = e107::getForm();

			foreach($fields as $key => $row)
			{
				$sc->setVars(array(
					'field_id'    => $form->name2id($key),
					'field_label' => $row['label'],
					'field_help'  => $row['help'],
					'field'       => $row['field'],
				));

				$body .= $tp->parseTemplate($tpl['PANEL']['FIELD'], true, $sc);
			}
		}

		$sc->setVars(array(
			'panel_id'        => md5($title),
			'panel_title'     => $title,
			'panel_collapsed' => ($title != LAN_METATAG_ADMIN_PANEL_01),
			'panel_body'      => $body,
			'panel_footer'    => '',
		));

		$html = $tp->parseTemplate($tpl['PANEL']['OPEN'], true, $sc);
		$html .= $tp->parseTemplate($tpl['PANEL']['HEADER'], true, $sc);
		$html .= $tp->parseTemplate($tpl['PANEL']['BODY'], true, $sc);
		$html .= $tp->parseTemplate($tpl['PANEL']['FOOTER'], true, $sc);
		$html .= $tp->parseTemplate($tpl['PANEL']['CLOSE'], true, $sc);

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
	 * Helper function to set cron job, and activate it after installation.
	 *
	 * @deprecated and will be removed after this issue will be closed:
	 * @see https://github.com/e107inc/e107/issues/1962
	 */
	public function setCronJob()
	{
		$db = e107::getDb();
		$count = $db->count('cron', '(*)', 'cron_function LIKE "metatag::%"');

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
			$db->update('cron', $update, false);
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

			$db->insert('cron', $insert, false);
		}
	}

	/**
	 * Creates a database record for each metatag types are provided
	 * by e_metatag addon files.
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

		// Delete non-allowed types.
		foreach($exists as $exist)
		{
			if(!in_array($exist, $types))
			{
				$db->delete('metatag_default', 'type = "' . $exist . '"');
			}
		}

		// First of all, we insert 'metatag_default'.
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
				'data'   => base64_encode(serialize($data)),
			);
			$db->insert('metatag_default', array('data' => $insert), false);
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
					'data'   => base64_encode(serialize($data)),
				);
				$db->insert('metatag_default', array('data' => $insert), false);
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

		// Finally we render meta tags.
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
						'data'        => base64_encode(serialize($data)),
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
			$tp = e107::getParser();
			$db = e107::getDb();
			$db->select('metatag_cache', '*', 'cid = "' . $tp->toDB($uri) . '"');

			while($row = $db->fetch())
			{
				$data = unserialize(base64_decode($row['data']));
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
			$expire = $created + ((int) varset($this->plugPrefs['cache_expire'], 0));

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

			$db = e107::getDb();
			$db->insert('metatag_cache', $insert, false);
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
			$tp = e107::getParser();
			$db = e107::getDb();
			$db->delete('metatag_cache', 'entity_type = "' . $tp->toDB($type) . '"');
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
			$tp = e107::getParser();
			$db = e107::getDb();
			$db->delete('metatag_cache', 'entity_type = "' . $tp->toDB($type) . '" AND entity_id = ' . (int) $id);
		}
	}

	/**
	 * Clear all cached data.
	 */
	public function clearCacheAll()
	{
		$db = e107::getDb();
		$db->delete('metatag_cache');
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
				$tp = e107::getParser();
				$file = $tp->replaceConstants($handler['file']);

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
		$tp = e107::getParser();

		$config = $this->getAddonConfig();

		foreach($data as $key => $value)
		{
			// Replace constants. Use full URLs, and replace {USERID} too.
			$value = $tp->replaceConstants($value, 'full', true);

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
				$tp = e107::getParser();
				$file = $tp->replaceConstants($entity_info['file']);

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
				$tp = e107::getParser();
				$file = $tp->replaceConstants($info['file']);

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
		$custom = $this->getCustomMetaTagsByEntity($entity_id, $entity_type);

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
		$db = e107::getDb();
		$db->select('metatag_default', '*', 'type = "metatag_default"');

		$data = array();
		while($row = $db->fetch())
		{
			$values = unserialize(base64_decode($row['data']));

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
			$db = e107::getDb();
			$db->select('metatag_default', '*', 'type = "' . $entity_type . '"');

			while($row = $db->fetch())
			{
				$values = unserialize(base64_decode($row['data']));

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

		$db = e107::getDb();
		$db->select('metatag', '*', 'entity_id = ' . (int) $entity_id . ' AND entity_type = "' . $entity_type . '"');

		while($row = $db->fetch())
		{
			$values = unserialize(base64_decode($row['data']));

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
					$this->renderLinkRel($key, $value);
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
			e107::meta(null, $href, array(
				'tag'  => 'link',
				'rel'  => $rel,
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
			e107::meta(null, $href, array(
				'tag'  => 'link',
				'rev'  => $rev,
			));
		}
	}

}
