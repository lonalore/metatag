<?php

/**
 * @file
 * Metatag class for common use.
 *
 * TODO - implement some cache logic.
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

		$data = $this->getMetaTags($entity_id, $entity_type);

		if(!empty($data))
		{
			// TODO - ability to use tokens
			// TODO - replace constants

			$this->renderMetaTags($data);
		}
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

				if($tags[$key] != $value)
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

				if($tags[$key] != $value)
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
				// Basic meta tags.
				case "title":
				case "description":
				case "abstract":
				case "keywords":
					$this->renderMeta($key, $value);
					break;

				// Advanced meta tags.
				case "robots":
				case "news_keywords":
				case "standout":
				case "rating":
				case "referrer":
				case "generator":
				case "rights":
				case "image_src":
				case "canonical":
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
					$this->renderMeta($key, $value);
					break;

				// Open Graph meta tags.
				case "og:site_name":
				case "og:type":
				case "og:url":
				case "og:title":
				case "og:determiner":
				case "og:description":
				case "og:updated_time":
				case "og:see_also":
				case "og:image":
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
					$this->renderMeta($key, $value);
					break;

				// Twitter card meta tags.
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
					$this->renderMeta($key, $value);
					break;

				// Dublin Core Basic Tags.
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

				// Google+ meta tags.
				case "itemprop:name":
				case "itemprop:description":
				case "itemprop:image":
					$this->renderItemprop($key, $value);
					break;

				// Advanced meta tags.
				case "content-language":
				case "refresh":
				case "cache-control":
				case "pragma":
					$this->renderHttpEquiv($key, $value);
					break;

				// Facebook meta tags.
				case "fb:admins":
				case "fb:app_id":
				case "fb:pages":
					$this->renderProperty($key, $value);
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
			e107::meta($name, $content);
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
			e107::meta(null, null, array(
				'http-equiv' => $httpequiv,
				'content'    => $content,
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
			e107::meta(null, null, array(
				'property' => $property,
				'content'  => $content,
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
			e107::meta(null, null, array(
				'itemprop' => str_replace('itemprop:', '', $itemprop),
				'content'  => $content,
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
	 * TODO - better method to detect news pages.
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
