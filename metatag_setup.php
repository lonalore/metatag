<?php

/**
 * @file
 * Installation hooks and callbacks of metatag plugin.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class metatag_setup.
 */
class metatag_setup
{

	/**
	 * This function is called before plugin table has been created
	 * by the metatag_sql.php file.
	 *
	 * @param array $var
	 */
	function install_pre($var)
	{

	}

	/**
	 * This function is called after plugin table has been created
	 * by the metatag_sql.php file.
	 *
	 * @param array $var
	 */
	function install_post($var)
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');

		$meta = new metatag();
		$meta->updateAddonList();
		$meta->setCronJob();
	}

	function uninstall_options()
	{

	}

	function uninstall_post($var)
	{

	}

	/**
	 * Call During Upgrade Check. May be used to check for existance of tables etc
	 * and if not found return TRUE to call for an upgrade.
	 *
	 * @param array $var
	 *
	 * @return bool
	 *  True to trigger an upgrade alert, and false to not.
	 */
	function upgrade_required($var)
	{
		$xml = e107::getXml();
		$sql = e107::getDb();

		// Current version.
		$version = $sql->retrieve('plugin', 'plugin_version', 'plugin_path = "metatag"');

		if(empty($version))
		{
			return false;
		}

		$plugInfo = $xml->loadXMLfile(e_PLUGIN . 'metatag/plugin.xml', 'advanced');

		$version_new = isset($plugInfo['@attributes']['version']) ? $plugInfo['@attributes']['version'] : null;

		if(empty($version_new))
		{
			return false;
		}

		if(version_compare($version, $version_new, '<'))
		{
			return true;
		}

		return false;
	}

	/**
	 * Before Automatic Upgrade Routine has completed.. run this.
	 *
	 * @param $var
	 */
	function upgrade_pre($var)
	{
		$sql = e107::getDb();

		$version = $sql->retrieve('plugin', 'plugin_version', 'plugin_path = "metatag"');

		if(!empty($version) && version_compare($version, '1.6', '<'))
		{
			$this->upgrade_n_to_16();
		}
	}

	/**
	 * After Automatic Upgrade Routine has completed.. run this.
	 *
	 * @param $var
	 */
	function upgrade_post($var)
	{
		$sql = e107::getDb();

		// Clear the cache.

		$sql->truncate('metatag_cache');
	}

	/**
	 * Upgrades Metatag plugin from version N to 1.6.
	 */
	function upgrade_n_to_16()
	{
		e107_require_once(e_PLUGIN . 'metatag/includes/metatag.class.php');
		$meta = new metatag();

		$sql1 = e107::getDb('select');
		$sql2 = e107::getDb('update');

		// Update metatag_default table data.

		$sql1->select('metatag_default', '*', '', true);

		while($row = $sql1->fetch())
		{
			if(empty($row['data']) || base64_encode(base64_decode($row['data'])) != $row['data'])
			{
				continue;
			}

			$data = unserialize(base64_decode($row['data']));

			$sql2->update('metatag_default', [
				'data'  => [
					'data' => $meta->serialize($data),
				],
				'WHERE' => 'id = "' . $row['id'] . '"',
			]);
		}

		// Update metatag table data.

		$sql1->select('metatag', '*', '', true);

		while($row = $sql1->fetch())
		{
			if(empty($row['data']) || base64_encode(base64_decode($row['data'])) != $row['data'])
			{
				continue;
			}

			$data = unserialize(base64_decode($row['data']));

			$sql2->update('metatag', [
				'data'  => [
					'data' => $meta->serialize($data),
				],
				'WHERE' => 'entity_id = "' . $row['entity_id'] . '" AND entity_type = "' . $row['entity_type'] . '"',
			]);
		}
	}

}
