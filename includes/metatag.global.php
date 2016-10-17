<?php

/**
 * @file
 * Contains callback functions for global tokens.
 */


/**
 * Returns with the current site title.
 */
function metatag_global_token_site_title()
{
	return defset('e_PAGETITLE', '');
}

/**
 * Returns with Site Name.
 */
function metatag_global_token_site_name()
{
	return defset('SITENAME', '');
}

/**
 * Returns with Site Description.
 */
function metatag_global_token_site_description()
{
	return defset('SITEDESCRIPTION', '');
}

/**
 * Returns with Site URL.
 */
function metatag_global_token_site_url()
{
	return defset('SITEURL', '');
}

/**
 * Returns with login URL.
 */
function metatag_global_token_site_login_url()
{
	return defset('SITEURL', '') . 'login.php';
}
