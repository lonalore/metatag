<?php

/**
 * @file
 * Cron handler.
 */

if(!defined('e107_INIT'))
{
	exit;
}


/**
 * Class metatag_cron.
 */
class metatag_cron
{

	function config()
	{
		$cron = array();

		$cron[] = array(
			// Displayed in admin area.
			'name'        => 'Purge expired cache.',
			// Name of the function which is defined below.
			'function'    => 'metatag_cron_purge_expired_cache',
			// Choose between: mail, user, content, notify, or backup.
			'category'    => 'content',
			// Displayed in admin area.
			'description' => 'Purge expired cache data from metatag_cache table.',
			'tab'         => '0 * * * *',
			'active'      => 1,
		);

		return $cron;
	}

	/**
	 * Purge expired cache data from metatag_cache table.
	 */
	public function metatag_cron_purge_expired_cache()
	{
		$sql = e107::getDb();
		$sql->delete("metatag_cache", "expire > 0 AND expire <= UNIX_TIMESTAMP()");
	}

}
