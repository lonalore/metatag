<?php

if(!defined('e107_INIT'))
{
	exit;
}

// [PLUGINS]/metatag/languages/[LANGUAGE]/[LANGUAGE]_admin.php
e107::lan('metatag', true, true);

/**
 * @return \array[][]
 */
function metatag_get_widget_elements_info()
{
	$tp = e107::getParser();

	return array(
		'basic'     => array(
			'title'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_02,
				'help'  => LAN_METATAG_ADMIN_03,
			),
			'description' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_04,
				'help'  => LAN_METATAG_ADMIN_05,
			),
			'abstract'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_06,
				'help'  => LAN_METATAG_ADMIN_07,
			),
			'keywords'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_08,
				'help'  => LAN_METATAG_ADMIN_09,
			),
		),
		'advanced'  => array(
			'robots'          => array(
				'type'    => 'checkboxes',
				'label'   => LAN_METATAG_ADMIN_10,
				'help'    => LAN_METATAG_ADMIN_11,
				'options' => array(
					'follow'       => LAN_METATAG_ADMIN_10_01,
					'index'        => LAN_METATAG_ADMIN_10_02,
					'noarchive'    => LAN_METATAG_ADMIN_10_03,
					'nofollow'     => LAN_METATAG_ADMIN_10_04,
					'noimageindex' => LAN_METATAG_ADMIN_10_05,
					'noindex'      => LAN_METATAG_ADMIN_10_06,
					'noodp'        => $tp->lanVars(LAN_METATAG_ADMIN_10_07, [
						'x' => '<a href="http://www.dmoz.org/" target="_blank">' . LAN_METATAG_ADMIN_10_07_X . '</a>',
					]),
					'nosnippet'    => LAN_METATAG_ADMIN_10_08,
					'notranslate'  => LAN_METATAG_ADMIN_10_09,
					'noydir'       => $tp->lanVars(LAN_METATAG_ADMIN_10_10, [
						'x' => '<a href="http://dir.yahoo.com/" target="_blank">' . LAN_METATAG_ADMIN_10_10_X . '</a>',
					]),
				),
			),
			'news_keywords'   => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_12,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_13, [
					'x' => '<a href="https://support.google.com/news/publisher/answer/68297?hl=en" target="_blank">' . LAN_METATAG_ADMIN_13_X . '</a>',
				]),
			),
			'standout'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_14,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_15, [
					'x' => '<a href="https://support.google.com/news/publisher/answer/68297?hl=en" target="_blank">' . LAN_METATAG_ADMIN_15_X . '</a>',
				]),
			),
			'rating'          => array(
				'type'    => 'select',
				'label'   => LAN_METATAG_ADMIN_16,
				'help'    => LAN_METATAG_ADMIN_17,
				'options' => array(
					'general'       => LAN_METATAG_ADMIN_16_01,
					'mature'        => LAN_METATAG_ADMIN_16_02,
					'restricted'    => LAN_METATAG_ADMIN_16_03,
					'14 years'      => LAN_METATAG_ADMIN_16_04,
					'safe for kids' => LAN_METATAG_ADMIN_16_05,
				),
			),
			'referrer'        => array(
				'type'    => 'select',
				'label'   => LAN_METATAG_ADMIN_18,
				'help'    => $tp->lanVars(LAN_METATAG_ADMIN_19, [
					'x' => '<a href="https://w3c.github.io/webappsec-referrer-policy/" target="_blank">' . LAN_METATAG_ADMIN_19_X . '</a>',
				]),
				'options' => array(
					'no-referrer'                => LAN_METATAG_ADMIN_18_01,
					'origin'                     => LAN_METATAG_ADMIN_18_02,
					'no-referrer-when-downgrade' => LAN_METATAG_ADMIN_18_03,
					'origin-when-cross-origin'   => LAN_METATAG_ADMIN_18_04,
					'unsafe-url'                 => LAN_METATAG_ADMIN_18_05,
				),
			),
			'generator'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_20,
				'help'  => LAN_METATAG_ADMIN_21,
			),
			'rights'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_22,
				'help'  => LAN_METATAG_ADMIN_23,
			),
			'image_src'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_24,
				'help'  => LAN_METATAG_ADMIN_25,
			),
			'shortlink'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_28,
				'help'  => LAN_METATAG_ADMIN_29,
			),
			'publisher'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_30,
				'help'  => LAN_METATAG_ADMIN_31,
			),
			'author'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_32,
				'help'  => LAN_METATAG_ADMIN_33,
			),
			'original-source' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_34,
				'help'  => LAN_METATAG_ADMIN_35,
			),
			'prev'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_36,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_37, [
					'x' => '<a href="https://support.google.com/webmasters/answer/1663744" target="_blank">' . LAN_METATAG_ADMIN_37_X . '</a>',
				]),
			),
			'next'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_38,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_39, [
					'x' => '<a href="https://support.google.com/webmasters/answer/1663744" target="_blank">' . LAN_METATAG_ADMIN_39_X . '</a>',
				]),
			),
			'geo.position'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_40,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_41, [
					'x' => '<a href="https://en.wikipedia.org/wiki/Geotagging#HTML_pages" target="_blank">' . LAN_METATAG_ADMIN_41_X . '</a>',
				]),
			),
			'geo.placename'   => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_42,
				'help'  => LAN_METATAG_ADMIN_43,
			),
			'geo.region'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_44,
				'help'  => LAN_METATAG_ADMIN_45,
			),
			'icbm'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_46,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_47, [
					'x' => '<a href="https://en.wikipedia.org/wiki/Geotagging#HTML_pages" target="_blank">' . LAN_METATAG_ADMIN_47_X . '</a>',
				]),
			),
			'refresh'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_48,
				'help'  => LAN_METATAG_ADMIN_49,
			),
		),
		'opengraph' => array(
			'og:site_name'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_160,
				'help'  => LAN_METATAG_ADMIN_161,
			),
			'og:type'                 => array(
				'type'    => 'select',
				'label'   => LAN_METATAG_ADMIN_162,
				'help'    => LAN_METATAG_ADMIN_163,
				'options' => array(
					LAN_METATAG_ADMIN_162_01 => [
						'activity' => LAN_METATAG_ADMIN_162_02,
						'sport'    => LAN_METATAG_ADMIN_162_03,
					],
					LAN_METATAG_ADMIN_162_04 => [
						'bar'        => LAN_METATAG_ADMIN_162_05,
						'company'    => LAN_METATAG_ADMIN_162_06,
						'cafe'       => LAN_METATAG_ADMIN_162_07,
						'hotel'      => LAN_METATAG_ADMIN_162_08,
						'restaurant' => LAN_METATAG_ADMIN_162_09,
					],
					LAN_METATAG_ADMIN_162_10 => [
						'cause'         => LAN_METATAG_ADMIN_162_11,
						'sports_league' => LAN_METATAG_ADMIN_162_12,
						'sports_team'   => LAN_METATAG_ADMIN_162_13,
					],
					LAN_METATAG_ADMIN_162_14 => [
						'band'       => LAN_METATAG_ADMIN_162_15,
						'government' => LAN_METATAG_ADMIN_162_16,
						'non_profit' => LAN_METATAG_ADMIN_162_17,
						'school'     => LAN_METATAG_ADMIN_162_18,
						'university' => LAN_METATAG_ADMIN_162_19,
					],
					LAN_METATAG_ADMIN_162_20 => [
						'actor'         => LAN_METATAG_ADMIN_162_21,
						'athlete'       => LAN_METATAG_ADMIN_162_22,
						'author'        => LAN_METATAG_ADMIN_162_23,
						'director'      => LAN_METATAG_ADMIN_162_24,
						'musician'      => LAN_METATAG_ADMIN_162_25,
						'politician'    => LAN_METATAG_ADMIN_162_26,
						'profile'       => LAN_METATAG_ADMIN_162_27,
						'public_figure' => LAN_METATAG_ADMIN_162_28,
					],
					LAN_METATAG_ADMIN_162_29 => [
						'city'           => LAN_METATAG_ADMIN_162_30,
						'country'        => LAN_METATAG_ADMIN_162_31,
						'landmark'       => LAN_METATAG_ADMIN_162_32,
						'state_province' => LAN_METATAG_ADMIN_162_33,
					],
					LAN_METATAG_ADMIN_162_34 => [
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
					],
					LAN_METATAG_ADMIN_162_46 => [
						'website' => LAN_METATAG_ADMIN_162_47,
						'article' => LAN_METATAG_ADMIN_162_48,
					],
				),
			),
			'og:url'                  => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_164,
				'help'  => LAN_METATAG_ADMIN_165,
			),
			'og:title'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_166,
				'help'  => LAN_METATAG_ADMIN_167,
			),
			'og:determiner'           => array(
				'type'    => 'select',
				'label'   => LAN_METATAG_ADMIN_168,
				'help'    => LAN_METATAG_ADMIN_169,
				'options' => array(
					'auto' => LAN_METATAG_ADMIN_168_01,
					'a'    => LAN_METATAG_ADMIN_168_02,
					'an'   => LAN_METATAG_ADMIN_168_03,
					'the'  => LAN_METATAG_ADMIN_168_04,
				),
			),
			'og:description'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_170,
				'help'  => LAN_METATAG_ADMIN_171,
			),
			'og:updated_time'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_172,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_173, [
					'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_173_X . '</a>',
				]),
			),
			'og:see_also'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_174,
				'help'  => LAN_METATAG_ADMIN_175,
			),
			'og:image'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_176,
				'help'  => LAN_METATAG_ADMIN_177,
			),
			'og:image:url'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_178,
				'help'  => LAN_METATAG_ADMIN_179,
			),
			'og:image:secure_url'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_180,
				'help'  => LAN_METATAG_ADMIN_181,
			),
			'og:image:type'           => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_182,
				'help'  => LAN_METATAG_ADMIN_183,
			),
			'og:image:width'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_184,
				'help'  => LAN_METATAG_ADMIN_185,
			),
			'og:image:height'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_186,
				'help'  => LAN_METATAG_ADMIN_187,
			),
			'og:latitude'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_188,
				'help'  => LAN_METATAG_ADMIN_189,
			),
			'og:longitude'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_190,
				'help'  => LAN_METATAG_ADMIN_191,
			),
			'og:street_address'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_192,
				'help'  => LAN_METATAG_ADMIN_193,
			),
			'og:locality'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_194,
				'help'  => LAN_METATAG_ADMIN_195,
			),
			'og:region'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_196,
				'help'  => LAN_METATAG_ADMIN_197,
			),
			'og:postal_code'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_198,
				'help'  => LAN_METATAG_ADMIN_199,
			),
			'og:country_name'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_200,
				'help'  => LAN_METATAG_ADMIN_201,
			),
			'og:email'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_202,
				'help'  => LAN_METATAG_ADMIN_203,
			),
			'og:phone_number'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_204,
				'help'  => LAN_METATAG_ADMIN_205,
			),
			'og:fax_number'           => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_206,
				'help'  => LAN_METATAG_ADMIN_207,
			),
			'og:locale'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_208,
				'help'  => LAN_METATAG_ADMIN_209,
			),
			'og:locale:alternate'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_210,
				'help'  => LAN_METATAG_ADMIN_211,
			),
			'og:author'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_212,
				'help'  => LAN_METATAG_ADMIN_213,
			),
			'og:publisher'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_214,
				'help'  => LAN_METATAG_ADMIN_215,
			),
			'og:section'              => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_216,
				'help'  => LAN_METATAG_ADMIN_217,
			),
			// TODO - use selectize instead.
			'article:tag'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_218,
				'help'  => LAN_METATAG_ADMIN_219,
			),
			'article:published_time'  => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_220,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_221, [
					'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_221_X . '</a>',
				]),
			),
			'article:modified_time'   => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_222,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_223, [
					'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_223_X . '</a>',
				]),
			),
			'article:expiration_time' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_224,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_225, [
					'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_225_X . '</a>',
				]),
			),
			'profile:first_name'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_226,
				'help'  => LAN_METATAG_ADMIN_227,
			),
			'profile:last_name'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_228,
				'help'  => LAN_METATAG_ADMIN_229,
			),
			'profile:username'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_230,
				'help'  => LAN_METATAG_ADMIN_231,
			),
			'profile:gender'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_232,
				'help'  => LAN_METATAG_ADMIN_233,
			),
			'og:audio'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_234,
				'help'  => LAN_METATAG_ADMIN_235,
			),
			'og:audio:secure_url'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_236,
				'help'  => LAN_METATAG_ADMIN_237,
			),
			'og:audio:type'           => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_238,
				'help'  => LAN_METATAG_ADMIN_239,
			),
			'book:author'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_240,
				'help'  => LAN_METATAG_ADMIN_241,
			),
			'book:isbn'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_242,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_243, [
					'x' => '<a href="http://en.wikipedia.org/wiki/International_Standard_Book_Number" target="_blank">' . LAN_METATAG_ADMIN_243_X . '</a>',
				]),
			),
			'book:release_date'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_244,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_245, [
					'x' => '<a href="http://en.wikipedia.org/wiki/ISO_8601" target="_blank">' . LAN_METATAG_ADMIN_245_X . '</a>',
				]),
			),
			// TODO - use selectize instead.
			'book:tag'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_246,
				'help'  => LAN_METATAG_ADMIN_247,
			),
			'og:video:url'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_248,
				'help'  => LAN_METATAG_ADMIN_249,
			),
			'og:video:secure_url'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_250,
				'help'  => LAN_METATAG_ADMIN_251,
			),
			'og:video:width'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_252,
				'help'  => LAN_METATAG_ADMIN_253,
			),
			'og:video:height'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_254,
				'help'  => LAN_METATAG_ADMIN_255,
			),
			'og:video:type'           => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_256,
				'help'  => LAN_METATAG_ADMIN_257,
			),
			'video:actor'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_258,
				'help'  => LAN_METATAG_ADMIN_259,
			),
			'video:actor:role'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_260,
				'help'  => LAN_METATAG_ADMIN_261,
			),
			'video:director'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_262,
				'help'  => LAN_METATAG_ADMIN_263,
			),
			'video:writer'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_264,
				'help'  => LAN_METATAG_ADMIN_265,
			),
			'video:duration'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_266,
				'help'  => LAN_METATAG_ADMIN_267,
			),
			'video:release_date'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_268,
				'help'  => LAN_METATAG_ADMIN_269,
			),
			// TODO - use selectize instead.
			'video:tag'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_270,
				'help'  => LAN_METATAG_ADMIN_271,
			),
			'video:series'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_272,
				'help'  => LAN_METATAG_ADMIN_273,
			),
		),
		'facebook'  => array(
			'fb:admins' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_50,
				'help'  => LAN_METATAG_ADMIN_51,
			),
			'fb:app_id' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_52,
				'help'  => LAN_METATAG_ADMIN_53,
			),
		),
		'twitter'   => array(
			'twitter:card'                       => array(
				'type'    => 'select',
				'label'   => LAN_METATAG_ADMIN_90,
				'help'    => LAN_METATAG_ADMIN_91,
				'options' => array(
					'summary'             => LAN_METATAG_ADMIN_90_01,
					'summary_large_image' => LAN_METATAG_ADMIN_90_02,
					'photo'               => LAN_METATAG_ADMIN_90_03,
					'player'              => LAN_METATAG_ADMIN_90_04,
					'gallery'             => LAN_METATAG_ADMIN_90_05,
					'app'                 => LAN_METATAG_ADMIN_90_06,
					'product'             => LAN_METATAG_ADMIN_90_07,
				),
			),
			'twitter:site:id'                    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_94,
				'help'  => LAN_METATAG_ADMIN_95,
			),
			'twitter:creator'                    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_96,
				'help'  => LAN_METATAG_ADMIN_97,
			),
			'twitter:creator:id'                 => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_98,
				'help'  => LAN_METATAG_ADMIN_99,
			),
			'twitter:url'                        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_100,
				'help'  => LAN_METATAG_ADMIN_101,
			),
			'twitter:title'                      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_102,
				'help'  => LAN_METATAG_ADMIN_103,
			),
			'twitter:description'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_104,
				'help'  => LAN_METATAG_ADMIN_105,
			),
			'twitter:image'                      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_106,
				'help'  => LAN_METATAG_ADMIN_107,
			),
			'twitter:image:width'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_108,
				'help'  => LAN_METATAG_ADMIN_109,
			),
			'twitter:image:height'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_110,
				'help'  => LAN_METATAG_ADMIN_111,
			),
			'twitter:image:alt'                  => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_112,
				'help'  => LAN_METATAG_ADMIN_113,
			),
			'twitter:image0'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_114,
				'help'  => LAN_METATAG_ADMIN_115,
			),
			'twitter:image1'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_116,
				'help'  => LAN_METATAG_ADMIN_117,
			),
			'twitter:image2'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_118,
				'help'  => LAN_METATAG_ADMIN_119,
			),
			'twitter:image3'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_120,
				'help'  => LAN_METATAG_ADMIN_121,
			),
			'twitter:player'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_122,
				'help'  => LAN_METATAG_ADMIN_123,
			),
			'twitter:player:width'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_124,
				'help'  => LAN_METATAG_ADMIN_125,
			),
			'twitter:player:height'              => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_126,
				'help'  => LAN_METATAG_ADMIN_127,
			),
			'twitter:player:stream'              => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_128,
				'help'  => LAN_METATAG_ADMIN_129,
			),
			'twitter:player:stream:content_type' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_130,
				'help'  => $tp->lanVars(LAN_METATAG_ADMIN_131, [
					'x' => '<a href="http://tools.ietf.org/rfc/rfc4337.txt" target="_blank">' . LAN_METATAG_ADMIN_131_X . '</a>',
				]),
			),
			'twitter:app:country'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_132,
				'help'  => LAN_METATAG_ADMIN_133,
			),
			'twitter:app:name:iphone'            => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_134,
				'help'  => LAN_METATAG_ADMIN_135,
			),
			'twitter:app:id:iphone'              => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_136,
				'help'  => LAN_METATAG_ADMIN_137,
			),
			'twitter:app:url:iphone'             => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_138,
				'help'  => LAN_METATAG_ADMIN_139,
			),
			'twitter:app:name:ipad'              => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_140,
				'help'  => LAN_METATAG_ADMIN_141,
			),
			'twitter:app:id:ipad'                => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_142,
				'help'  => LAN_METATAG_ADMIN_143,
			),
			'twitter:app:url:ipad'               => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_144,
				'help'  => LAN_METATAG_ADMIN_145,
			),
			'twitter:app:name:googleplay'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_146,
				'help'  => LAN_METATAG_ADMIN_147,
			),
			'twitter:app:id:googleplay'          => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_148,
				'help'  => LAN_METATAG_ADMIN_149,
			),
			'twitter:app:url:googleplay'         => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_150,
				'help'  => LAN_METATAG_ADMIN_151,
			),
			'twitter:label1'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_152,
				'help'  => LAN_METATAG_ADMIN_153,
			),
			'twitter:data1'                      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_154,
				'help'  => LAN_METATAG_ADMIN_155,
			),
			'twitter:label2'                     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_156,
				'help'  => LAN_METATAG_ADMIN_157,
			),
			'twitter:data2'                      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_158,
				'help'  => LAN_METATAG_ADMIN_159,
			),
		),
		'dublin'    => array(
			'dcterms.title'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_60,
				'help'  => LAN_METATAG_ADMIN_61,
			),
			'dcterms.creator'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_62,
				'help'  => LAN_METATAG_ADMIN_63,
			),
			'dcterms.subject'     => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_64,
				'help'  => LAN_METATAG_ADMIN_65,
			),
			'dcterms.description' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_66,
				'help'  => LAN_METATAG_ADMIN_67,
			),
			'dcterms.publisher'   => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_68,
				'help'  => LAN_METATAG_ADMIN_69,
			),
			'dcterms.contributor' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_70,
				'help'  => LAN_METATAG_ADMIN_71,
			),
			'dcterms.date'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_72,
				'help'  => LAN_METATAG_ADMIN_73,
			),
			'dcterms.type'        => array(
				'type'    => 'text',
				'label'   => LAN_METATAG_ADMIN_74,
				'help'    => LAN_METATAG_ADMIN_75,
				'options' => array(
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
				),
			),
			'dcterms.format'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_76,
				'help'  => LAN_METATAG_ADMIN_77,
			),
			'dcterms.identifier'  => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_78,
				'help'  => LAN_METATAG_ADMIN_79,
			),
			'dcterms.source'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_80,
				'help'  => LAN_METATAG_ADMIN_81,
			),
			'dcterms.language'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_82,
				'help'  => LAN_METATAG_ADMIN_83,
			),
			'dcterms.relation'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_84,
				'help'  => LAN_METATAG_ADMIN_85,
			),
			'dcterms.coverage'    => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_86,
				'help'  => LAN_METATAG_ADMIN_87,
			),
			'dcterms.rights'      => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_88,
				'help'  => LAN_METATAG_ADMIN_89,
			),
		),
		'google'    => array(
			'itemprop:name'        => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_54,
				'help'  => LAN_METATAG_ADMIN_55,
			),
			'itemprop:description' => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_56,
				'help'  => LAN_METATAG_ADMIN_57,
			),
			'itemprop:image'       => array(
				'type'  => 'text',
				'label' => LAN_METATAG_ADMIN_58,
				'help'  => LAN_METATAG_ADMIN_59,
			),
		),
	);
}
