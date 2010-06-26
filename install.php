<?php
require_once 'settings.php';
mysql_connect($host,$user,$password);
@mysql_select_db($database) or die("Unable to select database");

$file="CREATE TABLE IF NOT EXISTS `files` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(128) NOT NULL,
`short_name` varchar(12) NOT NULL,
`short_code` varchar(5) NOT NULL,
`short_url` varchar(9999),
`text` varchar(999999),
`size` varchar(12) NOT NULL,
`date` varchar(10) NOT NULL,
`view` varchar(10) NOT NULL default '0',
`type` int(1) NOT NULL,
`user` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$user="CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL auto_increment,
`username` varchar(32) NOT NULL,
`password` varchar(32) NOT NULL,
`online` int(20) NOT NULL default'0',
`email` varchar(100) NOT NULL,
`active` int(1) NOT NULL default '0',
`rtime` int(20) NOT NULL default '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$code="CREATE TABLE IF NOT EXISTS `codes` (
`id` int(11) NOT NULL auto_increment,
`code` varchar(128) NOT NULL,
`used` int(1) NOT NULL default '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


mysql_query($file);
mysql_query($user);
mysql_query($code);
mysql_close();
?>