<?php

/**
 * @file
 * Addon file to display help block on Admin UI.
 */

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);


/**
 * Class metatag_help.
 */
class metatag_help
{

	private $action;

	public function __construct()
	{
		$this->action = varset($_GET['action'], '');
		$this->renderHelpBlock();
	}

	public function renderHelpBlock()
	{
		switch($this->action)
		{
			default:
				$block = $this->getHelpBlockListPage();
				break;
		}

		if(!empty($block))
		{
			e107::getRender()->tablerender($block['title'], $block['body']);
		}
	}

	public function getHelpBlockListPage()
	{
		e107::js('footer', 'https://buttons.github.io/buttons.js');

		$content = '';

		$issue = array(
			'href="https://github.com/lonalore/metatag/issues"',
			'class="github-button"',
			'data-icon="octicon-issue-opened"',
			'data-style="mega"',
			'data-count-api="/repos/lonalore/metatag#open_issues_count"',
			'data-count-aria-label="# issues on GitHub"',
			'aria-label="Issue lonalore/metatag on GitHub"',
		);

		$star = array(
			'href="https://github.com/lonalore/metatag"',
			'class="github-button"',
			'data-icon="octicon-star"',
			'data-style="mega"',
			'data-count-href="/lonalore/metatag/stargazers"',
			'data-count-api="/repos/lonalore/metatag#stargazers_count"',
			'data-count-aria-label="# stargazers on GitHub"',
			'aria-label="Star lonalore/metatag on GitHub"',
		);

		$content .= '<p class="text-center">' . LAN_METATAG_ADMIN_HELP_03 . '</p>';
		$content .= '<p class="text-center">';
		$content .= '<a ' . implode(" ", $issue) . '>' . LAN_METATAG_ADMIN_HELP_04 . '</a>';
		$content .= '</p>';

		$content .= '<p class="text-center">' . LAN_METATAG_ADMIN_HELP_02 . '</p>';
		$content .= '<p class="text-center">';
		$content .= '<a ' . implode(" ", $star) . '>' . LAN_METATAG_ADMIN_HELP_05 . '</a>';
		$content .= '</p>';

		$beerImage = '<img src="https://beerpay.io/lonalore/metatag/badge.svg" />';
		$beerWishImage = '<img src="https://beerpay.io/lonalore/metatag/make-wish.svg" />';

		$content .= '<p class="text-center">' . LAN_METATAG_ADMIN_HELP_06 . '</p>';
		$content .= '<p class="text-center">';
		$content .= '<a href="https://beerpay.io/lonalore/metatag">' . $beerImage . '</a>';
		$content .= '</p>';
		$content .= '<p class="text-center">';
		$content .= '<a href="https://beerpay.io/lonalore/metatag">' . $beerWishImage . '</a>';
		$content .= '</p>';

		$block = array(
			'title' => LAN_METATAG_ADMIN_HELP_01,
			'body'  => $content,
		);

		return $block;
	}

}


new metatag_help();
