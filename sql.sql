
CREATE TABLE IF NOT EXISTS `eventInvites` (
  `inviteID` int(10) unsigned NOT NULL auto_increment,
  `eventID` int(10) unsigned default NULL,
  `userID` bigint(10) unsigned default NULL,
  PRIMARY KEY  (`inviteID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `eventResponses` (
  `responseID` int(10) unsigned NOT NULL auto_increment,
  `eventID` int(10) unsigned default NULL,
  `timeID` int(10) unsigned default NULL,
  `userID` bigint(10) unsigned default NULL,
  `response` enum('Y','N') default NULL,
  PRIMARY KEY  (`responseID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `eventTimes` (
  `timeID` int(10) unsigned NOT NULL auto_increment,
  `eventID` int(10) unsigned default NULL,
  `startTimestamp` int(10) unsigned default NULL,
  `endTimestamp` int(10) unsigned default NULL,
  PRIMARY KEY  (`timeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `events` (
  `eventID` int(10) unsigned NOT NULL auto_increment,
  `userID` bigint(10) unsigned default '0',
  `name` varchar(50) default '0',
  `description` text,
  `timestamp` int(10) unsigned default '0',
  PRIMARY KEY  (`eventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(10) unsigned NOT NULL,
  `name` varchar(200) default NULL,
  `first_name` varchar(200) default NULL,
  `last_name` varchar(200) default NULL,
  `email` text,
  `link` varchar(200) default NULL,
  `updated_time` int(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


