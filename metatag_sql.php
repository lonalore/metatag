CREATE TABLE `metatag` (
`entity_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The entity id this data is attached to.',
`entity_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The entity type this data is attached to.',
`data` longblob DEFAULT NULL,
KEY `entity_id` (`entity_id`),
KEY `entity_type` (`entity_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `metatag_default` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary ID.',
`name` varchar(255) NOT NULL DEFAULT '' COMMENT 'The entity name this data is attached to.',
`type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The entity type this data is attached to.',
`parent` int(11) UNSIGNED NOT NULL DEFAULT '0',
`data` longblob DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `metatag_cache` (
`cid` varchar(255) NOT NULL DEFAULT '' COMMENT 'Unique cache ID.',
`expire` int(11) NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire or 0 for never.',
`created` int(11) NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry was created.',
`entity_type` varchar(50) NOT NULL DEFAULT '' COMMENT 'The type of the entity this cache data belongs to.',
`entity_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The ID of the entity this cache data belongs to.',
`data` longblob DEFAULT NULL COMMENT 'A collection of data to cache.',
PRIMARY KEY (`cid`),
KEY `created` (`created`),
KEY `expire` (`expire`),
KEY `entity_type` (`entity_type`),
KEY `entity_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
