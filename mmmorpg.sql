DROP TABLE IF EXISTS `battle`;
CREATE TABLE `battle` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `enemy_id` int(10) unsigned NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`,`enemy_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=415 ;

DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` char(1) collate utf8_general_ci default 'b',
  `user_id` int(10) unsigned NOT NULL default '0',
  `date` int(10) unsigned default '0',
  `message` text collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1994 ;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(1) collate utf8_general_ci NOT NULL default 'p',
  `login` varchar(32) collate utf8_general_ci NOT NULL default '',
  `password` varchar(32) collate utf8_general_ci NOT NULL default '',
  `email` varchar(32) collate utf8_general_ci NOT NULL default '',
  `name` varchar(32) collate utf8_general_ci NOT NULL default '',
  `class` char(1) collate utf8_general_ci NOT NULL default '',
  `hp` int(10) unsigned NOT NULL default '0',
  `level` int(10) unsigned NOT NULL default '0',
  `exp` int(10) unsigned NOT NULL default '0',
  `last_update` int(11) NOT NULL default '0',
  `weapon` varchar(32) collate utf8_general_ci NOT NULL,
  `quest` int(10) unsigned NOT NULL default '0',
  `battle_count` int(10) unsigned default '0',
  `victory_count` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `login` (`login`,`password`),
  KEY `class` (`class`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=15 ;

INSERT INTO `user` (`type`, `login`, `password`, `email`, `name`, `class`, `hp`, `level`, `exp`, `last_update`, `weapon`, `quest`, `battle_count`, `victory_count`) VALUES ('m', '-', '-', '-', '�����', 'b', 190, 3, 6217, 1162384701, '�������', 0, 0, 0),
('m', '-', '-', '-', '������', 'b', 190, 3, 6379, 1162380903, '�������', 0, 0, 0),
('m', '-', '-', '-', '������', 'b', 23, 7, 32561, 1162386938, '�����', 0, 5, 5),
('m', '-', '-', '-', '������ ������', 'a', 280, 6, 23980, 1162386182, '�������', 0, 0, 0),
('m', '-', '-', '-', '��������', 'a', 130, 1, 1024, 1162384938, '�������������', 0, 0, 0);
        