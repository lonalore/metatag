CREATE TABLE `metatag` (
`entity_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The entity id this data is attached to.',
`entity_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The entity type this data is attached to.',
`data` longblob NOT NULL,
KEY `entity_id` (`entity_id`),
KEY `entity_type` (`entity_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `metatag_default` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary ID.',
`name` varchar(50) NOT NULL DEFAULT '' COMMENT 'The entity name this data is attached to.',
`type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The entity type this data is attached to.',
`parent` int(11) UNSIGNED NOT NULL DEFAULT '0',
`data` longblob NOT NULL,
PRIMARY KEY (`id`),
KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `metatag_cache` (
`id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Cache ID.',
`entity_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The type of the entity this cache data belongs to.',
`entity_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The ID of the entity this cache data belongs to.',
`data` longblob NOT NULL,
KEY `cache_id` (`id`),
KEY `entity_id` (`entity_id`),
KEY `entity_type` (`entity_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
