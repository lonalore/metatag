<?php

/**
 * @file
 * Shortcodes for "metatag" plugin.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/metatag/languages/[LANGUAGE]_front.php
e107::lan('metatag');


/**
 * Class metatag_shortcodes.
 */
class metatag_shortcodes extends e_shortcode
{

	/**
	 * Constructor.
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * @return string
	 */
	function sc_panel_heading()
	{
		$html = '<a data-toggle="collapse" href="#' . $this->var['panel_id'] . '">';
		$html .= $this->var['panel_title'];
		$html .= '</a>';

		return $html;
	}

	/**
	 * @return string
	 */
	function sc_panel_body()
	{
		return $this->var['panel_body'];
	}

	/**
	 * @return string
	 */
	function sc_panel_id()
	{
		return $this->var['panel_id'];
	}

	/**
	 * @return string
	 */
	function sc_panel_class()
	{
		$class = 'panel-collapse collapse';
		if((bool) $this->var['panel_collapsed'] === false)
		{
			$class .= ' in';
		}
		return $class;
	}

	/**
	 * @return string
	 */
	function sc_panel_help()
	{
		return $this->var['panel_help'];
	}

	/**
	 * @return string
	 */
	function sc_panel_field_id()
	{
		return $this->var['field_id'];
	}

	/**
	 * @return string
	 */
	function sc_panel_field_label()
	{
		return $this->var['field_label'];
	}

	/**
	 * @return string
	 */
	function sc_panel_field_help()
	{
		return $this->var['field_help'];
	}

	/**
	 * @return string
	 */
	function sc_panel_field()
	{
		return $this->var['field'];
	}

	/**
	 * @return string
	 */
	function sc_token_help()
	{
		return $this->var['token_help'];
	}

	/**
	 * @return string
	 */
	function sc_token_button()
	{
		return $this->var['token_button'];
	}

}
