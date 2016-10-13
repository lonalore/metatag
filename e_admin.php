<?php

/**
 * @file
 * Contains class metatag_admin for extending admin areas.
 */


/**
 * Class metatag_admin.
 *
 * v2.x Standard for extending admin areas.
 */
class metatag_admin
{

	/**
	 * Extend Admin-ui Parameters.
	 *
	 * @param object $ui
	 *  Admin UI object.
	 *
	 * @return array
	 */
	public function config($ui)
	{
		// Event name, e.g: 'wmessage', 'news' etc. (core or plugin).
		$type = $ui->getEventName();
		// Current mode, e.g: 'create', 'edit', 'list'.
		$action = $ui->getAction();
		// Primary ID of the record being created/edited/deleted.
		$id = $ui->getId();

		$config = array();

		switch($type)
		{
			// Hook into the news admin form.
			case "news":
				$default = array();

				$config['tabs'] = array('metatag' => LAN_PLUGIN_METATAG_TAB);

				$config['fields'] = array(
					// $_POST['x_metatag_metatags']
					'metatags' => array(
						'type'       => 'method', // metatag_admin_form::x_metatag_metatags()
						'title'      => '',
						'help'       => '',
						'tab'        => 'metatag',
						'writeParms' => array(
							'default'     => $default,
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
				break;
		}

		return $config;

	}


	/**
	 * Process Posted Data.
	 *
	 * @param object $ui
	 *  Admin UI object.
	 * @param int $id
	 *  Primary ID of the record being created/edited/deleted.
	 */
	public function process($ui, $id = 0)
	{
		// Contains posted data.
		$data = $ui->getPosted();
		// Event name, e.g: 'wmessage', 'news' etc. (core or plugin).
		$type = $ui->getEventName();
		// Current mode, e.g: 'create', 'edit', 'list'.
		$action = $ui->getAction();

		if(empty($id))
		{
			return;
		}

		// TODO.
	}

}


/**
 * Class metatag_admin_form.
 */
class metatag_admin_form extends e_form
{

	/**
	 * Metatags field widget.
	 *
	 * @param $curval
	 * @param $mode
	 * @param $att
	 *
	 * @return string
	 */
	function x_metatag_metatags($curval, $mode, $att)
	{
		return '';
	}

}
