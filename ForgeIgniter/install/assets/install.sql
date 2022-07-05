/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table FI_blog_catmap
# ------------------------------------------------------------

CREATE TABLE FI_blog_catmap (
  `catID` int(11) NOT NULL default '0',
  `postID` int(11) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`catID`,`postID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_blog_cats
# ------------------------------------------------------------

CREATE TABLE `FI_blog_cats` (
  `catID` int(11) NOT NULL auto_increment,
  `catName` varchar(100) collate utf8mb4_bin default NULL,
  `catSafe` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `catOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_blog_comments
# ------------------------------------------------------------

CREATE TABLE `FI_blog_comments` (
  `commentID` int(11) NOT NULL auto_increment,
  `postID` int(11) NOT NULL default '0',
  `dateCreated` timestamp NULL default '0000-00-00 00:00:00',
  `comment` text collate utf8mb4_bin,
  `fullName` varchar(100) collate utf8mb4_bin default NULL,
  `email` varchar(100) collate utf8mb4_bin default NULL,
  `website` varchar(100) collate utf8mb4_bin default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`commentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_blog_posts
# ------------------------------------------------------------

CREATE TABLE `FI_blog_posts` (
  `postID` int(11) NOT NULL auto_increment,
  `postTitle` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `uri` varchar(100) collate utf8mb4_bin default NULL,
  `body` text collate utf8mb4_bin,
  `excerpt` text collate utf8mb4_bin,
  `userID` int(11) default NULL,
  `tags` varchar(250) collate utf8mb4_bin default NULL,
  `published` tinyint(1) NOT NULL default '1',
  `allowComments` tinyint(1) NOT NULL default '1',
  `allowPings` tinyint(1) NOT NULL default '1',
  `views` int(11) unsigned NOT NULL default '0',
  `deleted` tinyint(1) default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`postID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_captcha
# ------------------------------------------------------------

CREATE TABLE `FI_captcha` (
  `captcha_id` bigint(13) unsigned NOT NULL auto_increment,
  `captcha_time` int(10) unsigned NOT NULL default '0',
  `ip_address` varchar(16) collate utf8mb4_bin NOT NULL default '0',
  `word` varchar(20) collate utf8mb4_bin NOT NULL default '',
  PRIMARY KEY  (`captcha_id`),
  KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_community_messagemap
# ------------------------------------------------------------

CREATE TABLE `FI_community_messagemap` (
  `messageID` int(11) NOT NULL default '0',
  `toUserID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `siteID` int(11) NOT NULL default '0',
  `parentID` int(11) NOT NULL default '0',
  `unread` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`messageID`,`toUserID`,`userID`,`siteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_community_messages
# ------------------------------------------------------------

CREATE TABLE `FI_community_messages` (
  `messageID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userID` int(11) default NULL,
  `subject` varchar(100) collate utf8mb4_bin default NULL,
  `message` text collate utf8mb4_bin,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`messageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_system_sessions
# ------------------------------------------------------------

CREATE TABLE `FI_system_sessions` (
  `id` varchar(128) collate utf8mb4_bin NOT NULL default '0',
  `ip_address` varchar(45) collate utf8mb4_bin NOT NULL default '0',
  `timestamp` int(10) unsigned NOT NULL default '0',
  `data` text collate utf8mb4_bin NOT NULL,
  PRIMARY KEY  (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_blocks
# ------------------------------------------------------------

CREATE TABLE `FI_email_blocks` (
  `blockID` int(11) NOT NULL auto_increment,
  `emailID` int(11) default NULL,
  `blockRef` varchar(50) collate utf8mb4_bin default NULL,
  `body` text collate utf8mb4_bin,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`blockID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_campaigns
# ------------------------------------------------------------

CREATE TABLE `FI_email_campaigns` (
  `campaignID` int(11) NOT NULL auto_increment,
  `campaignName` varchar(100) default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`campaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_deploy
# ------------------------------------------------------------

CREATE TABLE `FI_email_deploy` (
  `deployID` int(11) NOT NULL auto_increment,
  `emailID` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '0',
  `sent` tinyint(1) NOT NULL default '0',
  `failed` tinyint(1) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`deployID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_includes
# ------------------------------------------------------------

CREATE TABLE `FI_email_includes` (
  `includeID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `includeRef` varchar(100) default NULL,
  `body` text,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`includeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_list_subscribers
# ------------------------------------------------------------

CREATE TABLE `FI_email_list_subscribers` (
  `listID` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL default '0',
  `name` varchar(100) default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`listID`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_lists
# ------------------------------------------------------------

CREATE TABLE `FI_email_lists` (
  `listID` int(11) NOT NULL auto_increment,
  `listName` varchar(100) default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`listID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_email_templates
# ------------------------------------------------------------

CREATE TABLE `FI_email_templates` (
  `templateID` int(11) NOT NULL auto_increment,
  `templateName` varchar(100) default NULL,
  `body` text,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `linkStyle` varchar(200) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`templateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_emails
# ------------------------------------------------------------

CREATE TABLE `FI_emails` (
  `emailID` int(11) NOT NULL auto_increment,
  `emailName` varchar(100) default NULL,
  `emailSubject` varchar(100) default NULL,
  `bodyHTML` text,
  `bodyText` text,
  `campaignID` int(11) NOT NULL default '0',
  `templateID` int(11) default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `dateSent` timestamp NOT NULL default '0000-00-00 00:00:00',
  `listID` int(11) NOT NULL default '0',
  `deploy` tinyint(1) NOT NULL default '0',
  `deployDate` timestamp NULL default '0000-00-00 00:00:00',
  `status` enum('D','S') NOT NULL default 'D',
  `sent` int(11) unsigned NOT NULL default '0',
  `views` int(11) unsigned NOT NULL default '0',
  `clicks` int(11) unsigned NOT NULL default '0',
  `unsubscribed` int(11) unsigned NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`emailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_events
# ------------------------------------------------------------

CREATE TABLE `FI_events` (
  `eventID` int(11) NOT NULL auto_increment,
  `eventTitle` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `eventDate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `eventEnd` timestamp NOT NULL default '0000-00-00 00:00:00',
  `time` varchar(100) collate utf8mb4_bin default NULL,
  `location` varchar(200) collate utf8mb4_bin default NULL,
  `description` text collate utf8mb4_bin,
  `excerpt` text collate utf8mb4_bin,
  `userID` int(11) default NULL,
  `groupID` int(11) NOT NULL default '0',
  `tags` varchar(250) collate utf8mb4_bin default NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `featured` tinyint(1) unsigned default '0',
  `deleted` tinyint(1) unsigned default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`eventID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_file_folders
# ------------------------------------------------------------

CREATE TABLE `FI_file_folders` (
  `folderID` int(11) unsigned NOT NULL auto_increment,
  `parentID` int(11) unsigned NOT NULL default '0',
  `folderName` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `folderOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`folderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_files
# ------------------------------------------------------------

CREATE TABLE `FI_files` (
  `fileID` int(11) NOT NULL auto_increment,
  `fileRef` varchar(100) collate utf8mb4_bin default NULL,
  `filename` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `folderID` int(11) NOT NULL default '0',
  `userID` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `filesize` int(11) NOT NULL default '0',
  `downloads` int(11) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`fileID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_forums
# ------------------------------------------------------------

CREATE TABLE `FI_forums` (
  `forumID` int(11) unsigned NOT NULL auto_increment,
  `forumName` varchar(100) collate utf8mb4_bin default NULL,
  `catID` int(11) default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `description` text collate utf8mb4_bin,
  `topics` int(10) unsigned NOT NULL default '0',
  `replies` int(10) unsigned NOT NULL default '0',
  `lastPostID` int(11) default NULL,
  `private` tinyint(1) unsigned NOT NULL default '0',
  `groupID` int(11) default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `FI_forums` (`forumID`, `forumName`, `catID`, `dateCreated`, `dateModified`, `description`, `topics`, `replies`, `lastPostID`, `private`, `groupID`, `active`, `deleted`, `siteID`) VALUES
(1, 'Test Forum', NULL, '2015-01-05 06:32:24', '2015-01-05 06:32:24', 'Just A Test Forum', 0, 0, NULL, 0, 0, 1, 0, 1);



# Dump of table FI_forums_cats
# ------------------------------------------------------------

CREATE TABLE `FI_forums_cats` (
  `catID` int(11) unsigned NOT NULL auto_increment,
  `parentID` int(11) unsigned NOT NULL default '0',
  `catName` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `catOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_forums_posts
# ------------------------------------------------------------

CREATE TABLE `FI_forums_posts` (
  `postID` int(11) unsigned NOT NULL auto_increment,
  `topicID` int(11) unsigned NOT NULL default '0',
  `body` text collate utf8mb4_bin,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userID` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`postID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_forums_subs
# ------------------------------------------------------------

CREATE TABLE `FI_forums_subs` (
  `topicID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`topicID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_forums_topics
# ------------------------------------------------------------

CREATE TABLE `FI_forums_topics` (
  `topicID` int(11) unsigned NOT NULL auto_increment,
  `forumID` int(11) unsigned NOT NULL default '0',
  `topicTitle` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `replies` int(11) unsigned NOT NULL default '0',
  `views` int(11) unsigned NOT NULL default '0',
  `userID` int(11) default NULL,
  `lastPostID` int(11) default NULL,
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `locked` tinyint(1) NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`topicID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_image_folders
# ------------------------------------------------------------

CREATE TABLE `FI_image_folders` (
  `folderID` int(11) unsigned NOT NULL auto_increment,
  `parentID` int(11) unsigned NOT NULL default '0',
  `folderName` varchar(100) collate utf8mb4_bin default NULL,
  `folderSafe` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `folderOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`folderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_images
# ------------------------------------------------------------

CREATE TABLE `FI_images` (
  `imageID` int(11) NOT NULL auto_increment,
  `imageRef` varchar(100) collate utf8mb4_bin default NULL,
  `filename` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `imageName` varchar(100) collate utf8mb4_bin default NULL,
  `folderID` int(11) NOT NULL default '0',
  `groupID` int(11) NOT NULL default '0',
  `userID` int(11) default NULL,
  `class` varchar(100) collate utf8mb4_bin default NULL,
  `filesize` int(11) NOT NULL default '0',
  `maxsize` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`imageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_include_versions
# ------------------------------------------------------------

CREATE TABLE `FI_include_versions` (
  `versionID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `objectID` int(11) default NULL,
  `userID` int(11) default NULL,
  `body` longtext collate utf8mb4_bin,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`versionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_includes
# ------------------------------------------------------------

CREATE TABLE `FI_includes` (
  `includeID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `versionID` int(11) NOT NULL default '0',
  `includeRef` varchar(100) collate utf8mb4_bin default NULL,
  `type` enum('H','C','J') collate utf8mb4_bin NOT NULL default 'H',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`includeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_navigation
# ------------------------------------------------------------

CREATE TABLE `FI_navigation` (
  `navID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `navName` varchar(100) collate utf8mb4_bin default NULL,
  `uri` varchar(100) collate utf8mb4_bin default '',
  `parentID` int(11) NOT NULL default '0',
  `navOrder` int(11) default NULL,
  `active` tinyint(1) NOT NULL default '1',
  `siteID` int(11) NOT NULL default '0',
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`navID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_page_blocks
# ------------------------------------------------------------

CREATE TABLE `FI_page_blocks` (
  `blockID` int(11) NOT NULL auto_increment,
  `pageID` int(11) default NULL,
  `versionID` int(11) NOT NULL default '0',
  `blockRef` varchar(50) collate utf8mb4_bin default NULL,
  `body` text collate utf8mb4_bin,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `siteID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`blockID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_page_versions
# ------------------------------------------------------------

CREATE TABLE `FI_page_versions` (
  `versionID` int(11) NOT NULL auto_increment,
  `pageID` int(11) default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `userID` int(11) default NULL,
  `published` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`versionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_pages
# ------------------------------------------------------------

CREATE TABLE `FI_pages` (
  `pageID` int(11) NOT NULL auto_increment,
  `versionID` int(11) NOT NULL default '0',
  `pageName` varchar(100) character set utf8mb4 default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `datePublished` timestamp NOT NULL default '0000-00-00 00:00:00',
  `title` varchar(100) character set utf8mb4 NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0',
  `uri` varchar(100) character set utf8mb4 NOT NULL default '',
  `draftID` int(11) default NULL,
  `templateID` int(11) default NULL,
  `parentID` int(11) NOT NULL default '0',
  `pageOrder` int(11) NOT NULL default '0',
  `keywords` varchar(255) character set utf8mb4 default NULL,
  `description` varchar(255) character set utf8mb4 default NULL,
  `redirect` varchar(255) collate utf8mb4_bin default NULL,
  `userID` int(11) default NULL,
  `groupID` int(11) default NULL,
  `navigation` tinyint(1) NOT NULL default '1',
  `views` int(11) unsigned NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pageID`),
  KEY `uri` (`uri`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_permission_groups
# ------------------------------------------------------------

CREATE TABLE `FI_permission_groups` (
  `groupID` int(11) NOT NULL auto_increment,
  `groupName` varchar(200) collate utf8mb4_bin default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`groupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

LOCK TABLES `FI_permission_groups` WRITE;
/*!40000 ALTER TABLE `FI_permission_groups` DISABLE KEYS */;
INSERT INTO `FI_permission_groups` (`groupID`,`groupName`,`siteID`)
VALUES
	(-1,'Superuser',0);

/*!40000 ALTER TABLE `FI_permission_groups` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table FI_permission_map
# ------------------------------------------------------------

CREATE TABLE `FI_permission_map` (
  `groupID` int(11) NOT NULL default '0',
  `permissionID` int(11) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`groupID`,`permissionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_permissions
# ------------------------------------------------------------

CREATE TABLE `FI_permissions` (
  `permissionID` int(11) NOT NULL auto_increment,
  `permission` varchar(200) collate utf8mb4_bin default NULL,
  `key` varchar(100) collate utf8mb4_bin default NULL,
  `category` varchar(100) collate utf8mb4_bin default NULL,
  `special` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`permissionID`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

LOCK TABLES `FI_permissions` WRITE;
/*!40000 ALTER TABLE `FI_permissions` DISABLE KEYS */;
INSERT INTO `FI_permissions` (`permission`,`key`,`category`,`special`)
VALUES
	('Allow Pages','pages','Pages',0),
	('Add / edit pages','pages_edit','Pages',0),
	('Delete pages','pages_delete','Pages',0),
	('Access to all pages','pages_all','Pages',0),
	('Navigation','pages_navigation','Pages',0),
	('Allow Templates','pages_templates','Templates',0),
	('Allow Web Forms','webforms','Web Forms',0),
	('Delete tickets','webforms_tickets','Web Forms',0),
	('Add / edit web forms','webforms_edit','Web Forms',0),
	('Delete web forms','webforms_delete','Web Forms',0),
	('Allow image uploads','images','Uploads',0),
	('Allow file uploads','files','Uploads',0),
	('Access to all images','images_all','Uploads',0),
	('Access to all files','files_all','Uploads',0),
	('Allow Users','users','Users',0),
	('Add / edit users','users_edit','Users',0),
	('Delete users','users_delete','Users',0),
	('Import / export users','users_import','Users',0),
	('Edit permission groups','users_groups','Users',0),
	('Allow Blog','blog','Blog',0),
	('Add / edit posts','blog_edit','Blog',0),
	('Add / edit categories','blog_cats','Blog',0),
	('Access to all posts','blog_all','Blog',0),
	('Delete posts','blog_delete','Blog',0),
	('Allow Shop','shop','Shop',0),
	('Add / edit products','shop_edit','Shop',0),
	('Delete products','shop_delete','Shop',0),
	('Add / edit categories','shop_cats','Shop',0),
	('Add / edit orders','shop_orders','Shop',0),
	('Access to all products','shop_all','Shop',0),
	('View subscriptions','shop_subscriptions','Shop',0),
	('Add / edit shipping','shop_shipping','Shop',0),
	('Add / edit reviews','shop_reviews','Shop',0),
	('Add / edit discounts','shop_discounts','Shop',0),
	('Add / edit upsells', 'shop_upsells', 'Shop', 0),
	('Access Events','events','Events',0),
	('Add / edit events','events_edit','Events',0),
	('Delete events','events_delete','Events',0),
	('Access Forums','forums','Forums',0),
	('Add / edit boards','forums_edit','Forums',0),
	('Delete boards','forums_delete','Forums',0),
	('Add / edit categories','forums_cats','Forums',0),
	('Allow Community','community','Community',0),
	('Allow Wiki','wiki','Wiki',0),
	('Add / edit pages','wiki_edit','Wiki',0),
	('Add / edit categories','wiki_cats','Wiki',0),
	('Emailer','emailer','Emailer',0),
	('Add / edit campaigns','emailer_campaigns_edit','Emailer',0),
	('Delete campaigns','emailer_campaigns_delete','Emailer',0),
	('Add /edit emails','emailer_edit','Emailer',0),
	('Delete emails','emailer_delete','Emailer',0),
	('Add / edit templates','emailer_templates','Emailer',0),
	('Add / edit lists','emailer_lists','Emailer',0);


/*!40000 ALTER TABLE `FI_permissions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table FI_ratings
# ------------------------------------------------------------

CREATE TABLE `FI_ratings` (
  `ratingID` int(11) NOT NULL auto_increment,
  `objectID` int(11) default NULL,
  `table` varchar(50) collate utf8mb4_bin default NULL,
  `rating` int(11) default NULL,
  `userID` int(11) default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`ratingID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_bands
# ------------------------------------------------------------

CREATE TABLE `FI_shop_bands` (
  `bandID` int(11) NOT NULL auto_increment,
  `bandName` varchar(100) collate utf8mb4_bin default NULL,
  `multiplier` double default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`bandID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_catmap
# ------------------------------------------------------------

CREATE TABLE `FI_shop_catmap` (
	`catID` int(11) NOT NULL DEFAULT '0',
	`productID` int(11) NOT NULL DEFAULT '0',
	`siteID` int(11) DEFAULT NULL,
	PRIMARY KEY (`catID`, `productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_cats
# ------------------------------------------------------------

CREATE TABLE `FI_shop_cats` (
  `catID` int(11) unsigned NOT NULL auto_increment,
  `parentID` int(11) unsigned NOT NULL default '0',
  `catName` varchar(100) collate utf8mb4_bin default NULL,
  `catSafe` varchar(100) collate utf8mb4_bin default NULL,
  `description` text collate utf8mb4_bin,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `catOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_discounts
# ------------------------------------------------------------

CREATE TABLE `FI_shop_discounts` (
  `discountID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `expiryDate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `code` varchar(50) collate utf8mb4_bin default NULL,
  `discount` double default NULL,
  `type` enum('T','P','C') collate utf8mb4_bin NOT NULL default 'T',
  `objectID` text collate utf8mb4_bin,
  `modifier` enum('A','P') collate utf8mb4_bin NOT NULL default 'A',
  `base` enum('T','P') collate utf8mb4_bin NOT NULL default 'T',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`discountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_modifiers
# ------------------------------------------------------------

CREATE TABLE `FI_shop_modifiers` (
  `modifierID` int(11) NOT NULL auto_increment,
  `modifierName` varchar(100) collate utf8mb4_bin default NULL,
  `bandID` int(11) default NULL,
  `multiplier` double default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`modifierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_orders
# ------------------------------------------------------------

CREATE TABLE `FI_shop_orders` (
  `orderID` int(11) unsigned NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `transactionID` int(11) NOT NULL default '0',
  `productID` int(11) NOT NULL default '0',
  `quantity` tinyint(4) NOT NULL default '0',
  `key` text collate utf8mb4_bin,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`orderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_postages
# ------------------------------------------------------------

CREATE TABLE `FI_shop_postages` (
  `postageID` int(11) NOT NULL auto_increment,
  `total` double NOT NULL default '0',
  `cost` double NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`postageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_products
# ------------------------------------------------------------

CREATE TABLE `FI_shop_products` (
  `productID` int(11) NOT NULL auto_increment,
  `catalogueID` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `productOrder` int(11) NOT NULL default '0',
  `productName` varchar(100) collate utf8mb4_bin default NULL,
  `subtitle` varchar(100) collate utf8mb4_bin default NULL,
  `description` text collate utf8mb4_bin,
  `excerpt` text collate utf8mb4_bin,
  `tags` varchar(250) collate utf8mb4_bin default NULL,
  `price` double(10,2) NOT NULL default '0.00',
  `imageName` varchar(200) collate utf8mb4_bin NOT NULL default 'noimage.gif',
  `status` enum('S','O','P','D') collate utf8mb4_bin NOT NULL default 'S',
  `stock` int(11) unsigned NOT NULL default '1',
  `fileID` int(11) default NULL,
  `views` int(11) NOT NULL default '0',
  `featured` enum('Y','T','N') collate utf8mb4_bin NOT NULL default 'N',
  `bandID` int(11) default NULL,
  `freePostage` tinyint(1) unsigned NOT NULL default '0',
  `userID` int(11) default NULL,
  `published` tinyint(1) unsigned NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`productID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_reviews
# ------------------------------------------------------------

CREATE TABLE `FI_shop_reviews` (
  `reviewID` int(11) NOT NULL auto_increment,
  `productID` int(11) NOT NULL default '0',
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `rating` int(5) NOT NULL default '0',
  `review` text collate utf8mb4_bin,
  `fullName` varchar(100) collate utf8mb4_bin default NULL,
  `email` varchar(100) collate utf8mb4_bin default NULL,
  `active` tinyint(1) NOT NULL default '1',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`reviewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_transactions
# ------------------------------------------------------------

CREATE TABLE `FI_shop_transactions` (
  `transactionID` int(11) unsigned NOT NULL auto_increment,
  `transactionCode` varchar(50) collate utf8mb4_bin NOT NULL default '',
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `userID` int(11) NOT NULL default '0',
  `amount` double(10,2) NOT NULL default '0.00',
  `postage` double(10,2) default NULL,
  `paid` tinyint(1) unsigned NOT NULL default '0',
  `trackingStatus` enum('U','L','A','O','D') collate utf8mb4_bin NOT NULL default 'U',
  `discounts` double(10,2) NOT NULL default '0.00',
  `donation` double(10,2) NOT NULL default '0.00',
  `tax` double(10,2) NOT NULL default '0.00',
  `discountCode` varchar(50) collate utf8mb4_bin default NULL,
  `notes` text collate utf8mb4_bin,
  `expiryDate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `viewed` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`transactionID`),
  KEY `transactionCode` (`transactionCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_upsells
# ------------------------------------------------------------

CREATE TABLE `FI_shop_upsells` (
  `upsellID` int(11) unsigned NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NULL default CURRENT_TIMESTAMP,
  `type` enum('V','N','P') collate utf8mb4_bin NOT NULL default 'V',
  `value` double(10,2) default NULL,
  `numProducts` int(11) default NULL,
  `productIDs` varchar(200) collate utf8mb4_bin default NULL,
  `productID` int(11) default NULL,
  `upsellOrder` int(11) default NULL,
  `remove` tinyint(1) unsigned NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`upsellID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_shop_variations
# ------------------------------------------------------------

CREATE TABLE `FI_shop_variations` (
  `variationID` int(11) unsigned NOT NULL auto_increment,
  `variation` varchar(50) collate utf8mb4_bin default NULL,
  `price` double(10,2) NOT NULL default '0.00',
  `type` int(11) default NULL,
  `productID` int(11) default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`variationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_sites
# ------------------------------------------------------------

CREATE TABLE `FI_sites` (
  `siteID` int(11) NOT NULL auto_increment,
  `siteDomain` varchar(100) collate utf8mb4_bin default NULL,
  `altDomain` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `siteName` varchar(100) collate utf8mb4_bin default NULL,
  `siteEmail` varchar(100) collate utf8mb4_bin default NULL,
  `siteURL` varchar(100) collate utf8mb4_bin default NULL,
  `siteTel` varchar(100) collate utf8mb4_bin default NULL,
  `siteAddress` text collate utf8mb4_bin,
  `siteCountry` varchar(100) collate utf8mb4_bin default NULL,
  `groupID` int(11) default NULL,
  `plan` int(11) NOT NULL default '0',
  `quota` int(11) unsigned NOT NULL default '0',
  `paging` int(11) NOT NULL default '20',
  `theme` varchar(50) collate utf8mb4_bin default NULL,
  `shopEmail` varchar(100) collate utf8mb4_bin default NULL,
  `shopItemsPerPage` int(11) NOT NULL default '6',
  `shopItemsPerRow` int(11) NOT NULL default '3',
  `shopFreePostage` tinyint(1) NOT NULL default '0',
  `shopShippingTable` varchar(250) collate utf8mb4_bin default NULL,
  `shopFreePostageRate` int(11) default NULL,
  `shopGateway` varchar(50) collate utf8mb4_bin NOT NULL default 'paypal',
  `shopVariation1` varchar(50) collate utf8mb4_bin default NULL,
  `shopVariation2` varchar(50) collate utf8mb4_bin default NULL,
  `shopVariation3` varchar(50) collate utf8mb4_bin default NULL,
  `shopStockControl` tinyint(1) NOT NULL default '0',
  `shopTax` tinyint(2) NOT NULL default '0',
  `shopTaxRate` double NOT NULL default '0',
  `shopTaxState` varchar(3) collate utf8mb4_bin default NULL,
  `shopAPIKey` varchar(100) collate utf8mb4_bin default NULL,
  `shopAPIUser` varchar(50) collate utf8mb4_bin default NULL,
  `shopAPIPass` varchar(50) collate utf8mb4_bin default NULL,
  `shopVendor` varchar(50) collate utf8mb4_bin default NULL,
  `emailerEmail` varchar(100) collate utf8mb4_bin default NULL,
  `emailerName` varchar(100) collate utf8mb4_bin default NULL,
  `currency` varchar(4) collate utf8mb4_bin NOT NULL default 'USD',
  `dateFmt` varchar(50) collate utf8mb4_bin default NULL,
  `dateOrder` enum('DM','MD') collate utf8mb4_bin NOT NULL default 'DM',
  `headlines` int(11) NOT NULL default '3',
  `clientID` int(11) default NULL,
  `emailHeader` text collate utf8mb4_bin,
  `emailFooter` text collate utf8mb4_bin,
  `emailTicket` text collate utf8mb4_bin,
  `emailOrder` text collate utf8mb4_bin,
  `emailAccount` text collate utf8mb4_bin,
  `emailDispatch` text collate utf8mb4_bin,
  `emailDonation` text collate utf8mb4_bin,
  `emailSubscription` text collate utf8mb4_bin,
  `timezone` varchar(5) collate utf8mb4_bin NOT NULL default 'UTC',
  `subscriptionAction` int(11) default NULL,
  `activation` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`siteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_sub_payments
# ------------------------------------------------------------

CREATE TABLE `FI_sub_payments` (
  `paymentID` int(11) NOT NULL auto_increment,
  `referenceID` char(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `amount` double default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`paymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=FIXED;



# Dump of table FI_subscribers
# ------------------------------------------------------------

CREATE TABLE `FI_subscribers` (
  `subscriberID` int(11) NOT NULL auto_increment,
  `subscriptionID` int(11) default NULL,
  `referenceID` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NULL default '0000-00-00 00:00:00',
  `lastPayment` timestamp NULL default '0000-00-00 00:00:00',
  `fullName` varchar(50) collate utf8mb4_bin default NULL,
  `email` varchar(100) collate utf8mb4_bin default NULL,
  `address` text collate utf8mb4_bin,
  `postcode` varchar(10) collate utf8mb4_bin default NULL,
  `country` varchar(100) collate utf8mb4_bin default NULL,
  `userID` int(11) default NULL,
  `active` tinyint(1) NOT NULL default '1',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`subscriberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_subscriptions
# ------------------------------------------------------------

CREATE TABLE `FI_subscriptions` (
  `subscriptionID` int(11) NOT NULL auto_increment,
  `subscriptionRef` varchar(100) collate utf8mb4_bin default NULL,
  `cgCode` varchar(100) collate utf8mb4_bin default NULL,
  `cgProduct` varchar(100) collate utf8mb4_bin default NULL,
  `subscriptionName` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default '0000-00-00 00:00:00',
  `description` text collate utf8mb4_bin,
  `price` double default NULL,
  `currency` varchar(3) collate utf8mb4_bin default NULL,
  `term` enum('M','Y') collate utf8mb4_bin NOT NULL default 'M',
  `active` tinyint(1) NOT NULL default '1',
  `deleted` tinyint(1) NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`subscriptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_tags
# ------------------------------------------------------------

CREATE TABLE `FI_tags` (
  `id` int(11) NOT NULL auto_increment,
  `safe_tag` varchar(30) collate utf8mb4_bin NOT NULL default '',
  `tag` varchar(50) collate utf8mb4_bin NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `safe_tag` (`safe_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_tags_ref
# ------------------------------------------------------------

CREATE TABLE `FI_tags_ref` (
  `tag_id` int(10) unsigned NOT NULL default '0',
  `row_id` int(10) unsigned NOT NULL default '0',
  `date` timestamp NOT NULL default '0000-00-00 00:00:00',
  `table` varchar(20) collate utf8mb4_bin NOT NULL default '',
  `siteID` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_template_versions
# ------------------------------------------------------------

CREATE TABLE `FI_template_versions` (
  `versionID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `objectID` int(11) default NULL,
  `userID` int(11) default NULL,
  `body` text collate utf8mb4_bin,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`versionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_templates
# ------------------------------------------------------------

CREATE TABLE `FI_templates` (
  `templateID` int(11) NOT NULL auto_increment,
  `templateName` varchar(100) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `versionID` int(11) NOT NULL default '0',
  `modulePath` varchar(100) collate utf8mb4_bin NOT NULL default '',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`templateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_tickets
# ------------------------------------------------------------

CREATE TABLE `FI_tickets` (
  `ticketID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `formName` varchar(200) collate utf8mb4_bin default NULL,
  `subject` varchar(100) collate utf8mb4_bin default NULL,
  `fullName` varchar(100) collate utf8mb4_bin default NULL,
  `email` varchar(100) collate utf8mb4_bin default NULL,
  `body` text collate utf8mb4_bin,
  `closed` tinyint(1) NOT NULL default '0',
  `notes` text collate utf8mb4_bin,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  `viewed` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ticketID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_tracking
# ------------------------------------------------------------

CREATE TABLE `FI_tracking` (
  `trackingID` int(11) NOT NULL auto_increment,
  `date` timestamp NULL default '0000-00-00 00:00:00',
  `userKey` varchar(32) collate utf8mb4_bin default NULL,
  `ipAddress` varchar(16) collate utf8mb4_bin default NULL,
  `userAgent` varchar(100) collate utf8mb4_bin default NULL,
  `referer` varchar(200) collate utf8mb4_bin default NULL,
  `views` int(11) NOT NULL default '0',
  `lastPage` varchar(250) collate utf8mb4_bin default NULL,
  `userdata` varchar(250) collate utf8mb4_bin NOT NULL default '',
  `siteID` int(11) default '0',
  PRIMARY KEY  (`trackingID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_users
# ------------------------------------------------------------

CREATE TABLE `FI_users` (
  `userID` int(11) NOT NULL auto_increment,
  `username` varchar(100) collate utf8mb4_bin NOT NULL default '',
  `password` varchar(255) collate utf8mb4_bin default NULL,
  `groupID` int(11) NOT NULL default '0',
  `email` varchar(100) collate utf8mb4_bin default NULL,
  `subscription` enum('Y','E','P','N') collate utf8mb4_bin NOT NULL default 'Y',
  `subscribed` tinyint(1) unsigned NOT NULL default '0',
  `plan` int(11) NOT NULL default '0',
  `bounced` tinyint(1) default '0',
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `displayName` varchar(100) collate utf8mb4_bin default NULL,
  `firstName` varchar(50) collate utf8mb4_bin default NULL,
  `lastName` varchar(50) collate utf8mb4_bin default NULL,
  `address1` varchar(100) collate utf8mb4_bin default NULL,
  `address2` varchar(100) collate utf8mb4_bin default NULL,
  `address3` varchar(100) collate utf8mb4_bin default NULL,
  `city` varchar(100) collate utf8mb4_bin default NULL,
  `state` varchar(50) collate utf8mb4_bin default NULL,
  `postcode` varchar(8) collate utf8mb4_bin default NULL,
  `country` varchar(100) collate utf8mb4_bin default NULL,
  `currency` varchar(4) collate utf8mb4_bin NOT NULL default 'USD',
  `billingAddress1` varchar(100) collate utf8mb4_bin default NULL,
  `billingAddress2` varchar(100) collate utf8mb4_bin default NULL,
  `billingAddress3` varchar(100) collate utf8mb4_bin default NULL,
  `billingCity` varchar(100) collate utf8mb4_bin default NULL,
  `billingState` varchar(50) collate utf8mb4_bin default NULL,
  `billingPostcode` varchar(8) collate utf8mb4_bin default NULL,
  `billingCountry` varchar(100) collate utf8mb4_bin default NULL,
  `phone` varchar(20) collate utf8mb4_bin default NULL,
  `avatar` varchar(50) collate utf8mb4_bin default NULL,
  `signature` text collate utf8mb4_bin,
  `bio` text collate utf8mb4_bin NOT NULL,
  `website` varchar(100) collate utf8mb4_bin default NULL,
  `companyName` varchar(100) collate utf8mb4_bin default NULL,
  `companyEmail` varchar(100) collate utf8mb4_bin default NULL,
  `companyWebsite` varchar(100) collate utf8mb4_bin default NULL,
  `companyDescription` text collate utf8mb4_bin,
  `companyLogo` varchar(50) collate utf8mb4_bin default NULL,
  `language` varchar(50) collate utf8mb4_bin NOT NULL default 'english',
  `posts` int(11) unsigned NOT NULL default '0',
  `kudos` int(11) NOT NULL default '0',
  `notifications` tinyint(1) unsigned NOT NULL default '1',
  `privacy` enum('V','F','H') collate utf8mb4_bin NOT NULL default 'V',
  `resetkey` varchar(32) collate utf8mb4_bin default NULL,
  `lastLogin` timestamp NOT NULL default '0000-00-00 00:00:00',
  `custom1` varchar(250) collate utf8mb4_bin default NULL,
  `custom2` varchar(250) collate utf8mb4_bin default NULL,
  `custom3` varchar(250) collate utf8mb4_bin default NULL,
  `custom4` text collate utf8mb4_bin,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`userID`),
  KEY `emailindex` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

LOCK TABLES `FI_users` WRITE;
/*!40000 ALTER TABLE `FI_users` DISABLE KEYS */;
INSERT INTO `FI_users` (`userID`,`username`,`password`,`groupID`,`email`,`subscription`,`subscribed`,`bounced`,`dateCreated`,`dateModified`,`displayName`,`firstName`,`lastName`,`address1`,`address2`,`address3`,`city`,`state`,`postcode`,`country`,`currency`,`billingAddress1`,`billingAddress2`,`billingAddress3`,`billingCity`,`billingState`,`billingPostcode`,`billingCountry`,`phone`,`avatar`,`signature`,`bio`,`website`,`companyName`,`companyEmail`,`companyWebsite`,`companyDescription`,`companyLogo`,`language`,`posts`,`kudos`,`notifications`,`privacy`,`resetkey`,`lastLogin`,`custom1`,`custom2`,`custom3`,`custom4`,`active`,`siteID`)
VALUES
	(1,'superuser','f35364bc808b079853de5a1e343e7159',-1,'','Y',0,0,NOW(),NOW(),NULL,'Admin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'USD',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,'english',0,0,1,'V',NULL,'0000-00-00 00:00:00',NULL,NULL,NULL,NULL,1,NULL);

/*!40000 ALTER TABLE `FI_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table FI_web_forms
# ------------------------------------------------------------

CREATE TABLE `FI_web_forms` (
  `formID` int(11) NOT NULL auto_increment,
  `dateCreated` timestamp NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `formName` varchar(200) collate utf8mb4_bin default NULL,
  `formRef` varchar(200) collate utf8mb4_bin default NULL,
  `fieldSet` tinyint(4) unsigned NOT NULL default '0',
  `captcha` tinyint(1) unsigned NOT NULL default '0',
  `account` tinyint(1) NOT NULL default '0',
  `groupID` int(11) default NULL,
  `outcomeMessage` text collate utf8mb4_bin,
  `outcomeEmails` text collate utf8mb4_bin,
  `outcomeRedirect` varchar(200) collate utf8mb4_bin default NULL,
  `fileTypes` varchar(100) collate utf8mb4_bin default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`formID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_wiki
# ------------------------------------------------------------

CREATE TABLE `FI_wiki` (
  `pageID` int(11) NOT NULL auto_increment,
  `pageName` varchar(100) collate utf8mb4_bin default NULL,
  `versionID` int(11) default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userID` int(11) default NULL,
  `catID` int(11) default NULL,
  `uri` varchar(100) character set utf8mb4 default NULL,
  `active` tinyint(1) unsigned NOT NULL default '1',
  `groupID` int(11) NOT NULL default '0',
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`pageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_wiki_cats
# ------------------------------------------------------------

CREATE TABLE `FI_wiki_cats` (
  `catID` int(11) unsigned NOT NULL auto_increment,
  `parentID` int(11) unsigned NOT NULL default '0',
  `catName` varchar(50) collate utf8mb4_bin default NULL,
  `dateCreated` timestamp NOT NULL default '0000-00-00 00:00:00',
  `dateModified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `description` text collate utf8mb4_bin,
  `catOrder` int(11) default NULL,
  `deleted` tinyint(1) unsigned NOT NULL default '0',
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`catID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;



# Dump of table FI_wiki_versions
# ------------------------------------------------------------

CREATE TABLE `FI_wiki_versions` (
  `versionID` int(11) NOT NULL auto_increment,
  `pageID` int(11) NOT NULL default '0',
  `dateCreated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `userID` int(11) default NULL,
  `body` text collate utf8mb4_bin,
  `notes` varchar(250) collate utf8mb4_bin default NULL,
  `siteID` int(11) default NULL,
  PRIMARY KEY  (`versionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;








/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
