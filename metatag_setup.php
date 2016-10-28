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
	 * Trigger an upgrade alert or not.
	 *
	 * @param array $var
	 *
	 * @return bool
	 *  True to trigger an upgrade alert, and false to not.
	 */
	function upgrade_required($var)
	{
		return false;
	}


	function upgrade_pre($var)
	{

	}


	function upgrade_post($var)
	{

	}

}
