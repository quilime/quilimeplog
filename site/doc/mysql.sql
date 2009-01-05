# PLOG DB STRUCTURE

CREATE database plog_db;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(255) character set utf8 NOT NULL,
  `desc` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `consumers` (
  `id` varchar(32) NOT NULL,
  `secret` varchar(32) default NULL,
  `short` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL auto_increment,
  `project_title` text character set utf8 NOT NULL,
  `title` text character set utf8 NOT NULL,
  `content` longtext character set utf8 NOT NULL,
  `slug` varchar(128) character set utf8 NOT NULL,
  `date` datetime NOT NULL,
  `hidden` int(2) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL auto_increment,
  `title` text character set utf8 NOT NULL,
  `content` longtext character set utf8 NOT NULL,
  `slug` varchar(255) character set utf8 NOT NULL,
  `thumb_url` varchar(128) character set utf8 NOT NULL,
  `date` datetime NOT NULL,
  `hidden` int(2) NOT NULL default '1',
  `type_id` int(4) default '0',
  `order` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

CREATE TABLE IF NOT EXISTS `tokens` (
  `id` varchar(32) NOT NULL,
  `secret` varchar(32) default NULL,
  `type` tinyint(4) default NULL,
  `consumer_key` varchar(32) default NULL,
  `user_name` varchar(128) default NULL,
  `time` datetime default NULL,
  `deleted` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `active_token` (`id`,`deleted`),
  KEY `user` (`user_name`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) character set utf8 NOT NULL,
  `desc` text character set utf8 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `users` (
  `name` varchar(128) character set utf8 NOT NULL,
  `password` varchar(41) character set utf8 default NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
