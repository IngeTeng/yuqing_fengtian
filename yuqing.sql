-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2018 年 10 月 25 日 16:08
-- 服务器版本: 5.00.15
-- PHP 版本: 5.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `yuqing`
--

-- --------------------------------------------------------

--
-- 表的结构 `app_article`
--

DROP TABLE IF EXISTS `app_article`;
CREATE TABLE IF NOT EXISTS `app_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_content` text,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `article_comment` int(11) default '0',
  `article_source` varchar(255) default '未知',
  `article_channel` varchar(255) default '未知',
  `media` varchar(255) default '',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `article_title` (`article_title`),
  KEY `article_title_2` (`article_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=870614 ;

-- --------------------------------------------------------

--
-- 表的结构 `app_filter_ids`
--

DROP TABLE IF EXISTS `app_filter_ids`;
CREATE TABLE IF NOT EXISTS `app_filter_ids` (
  `ids` varchar(64) NOT NULL default '',
  `media` varchar(64) NOT NULL default '',
  UNIQUE KEY `ids` (`ids`,`media`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app_key`
--

DROP TABLE IF EXISTS `app_key`;
CREATE TABLE IF NOT EXISTS `app_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `uk_id` (`uk_id`),
  KEY `audit_status` (`audit_status`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=978900 ;

-- --------------------------------------------------------

--
-- 表的结构 `auto_work`
--

DROP TABLE IF EXISTS `auto_work`;
CREATE TABLE IF NOT EXISTS `auto_work` (
  `aw_id` int(11) NOT NULL auto_increment,
  `aw_type` int(11) NOT NULL default '0',
  `aw_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`aw_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1289 ;

-- --------------------------------------------------------

--
-- 表的结构 `bbs_article`
--

DROP TABLE IF EXISTS `bbs_article`;
CREATE TABLE IF NOT EXISTS `bbs_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_content` text,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `article_reply` int(11) default '0',
  `article_click` int(11) default '0',
  `author` varchar(255) default '',
  `media` varchar(255) default '未知',
  `forum` varchar(255) default '未知',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `article_title` (`article_title`),
  KEY `media` (`media`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=7270683 ;

-- --------------------------------------------------------

--
-- 表的结构 `bbs_article_copy`
--

DROP TABLE IF EXISTS `bbs_article_copy`;
CREATE TABLE IF NOT EXISTS `bbs_article_copy` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_title` text NOT NULL,
  `article_content` text NOT NULL,
  PRIMARY KEY  (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1817 ;

-- --------------------------------------------------------

--
-- 表的结构 `bbs_article_copy1`
--

DROP TABLE IF EXISTS `bbs_article_copy1`;
CREATE TABLE IF NOT EXISTS `bbs_article_copy1` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_title` text NOT NULL,
  `article_content` text NOT NULL,
  PRIMARY KEY  (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=8073 ;

-- --------------------------------------------------------

--
-- 表的结构 `bbs_article_rule`
--

DROP TABLE IF EXISTS `bbs_article_rule`;
CREATE TABLE IF NOT EXISTS `bbs_article_rule` (
  `r_id` int(11) NOT NULL auto_increment,
  `rule_name` varchar(255) default '',
  `site_url` varchar(255) default '',
  `title_b` varchar(255) default '',
  `title_e` varchar(255) default '',
  `time_b` varchar(255) default '',
  `time_e` varchar(255) default '',
  `time_format` varchar(255) default '',
  `content_b` varchar(255) default '',
  `content_e` varchar(255) default '',
  `reply_b` varchar(255) default '',
  `reply_e` varchar(255) default '',
  `click_b` varchar(255) default '',
  `click_e` varchar(255) default '',
  `author_b` varchar(255) default '',
  `author_e` varchar(255) default '',
  `media_b` varchar(255) default '',
  `media_e` varchar(255) default '',
  `forum_b` varchar(255) default '',
  `forum_e` varchar(255) default '',
  PRIMARY KEY  (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;

-- --------------------------------------------------------

--
-- 表的结构 `bbs_key`
--

DROP TABLE IF EXISTS `bbs_key`;
CREATE TABLE IF NOT EXISTS `bbs_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `old_property` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pk` (`uk_id`,`article_id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `uk_id` (`uk_id`),
  KEY `audit_status` (`audit_status`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `article_id` (`article_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=7962962 ;

-- --------------------------------------------------------

--
-- 表的结构 `blog_article`
--

DROP TABLE IF EXISTS `blog_article`;
CREATE TABLE IF NOT EXISTS `blog_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_content` text,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `article_comment` int(11) default '0',
  `article_click` int(11) default '0',
  `author` varchar(255) default '',
  `media` varchar(255) default '未知',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1312234 ;

-- --------------------------------------------------------

--
-- 表的结构 `blog_article_rule`
--

DROP TABLE IF EXISTS `blog_article_rule`;
CREATE TABLE IF NOT EXISTS `blog_article_rule` (
  `r_id` int(11) NOT NULL auto_increment,
  `rule_name` varchar(255) default '',
  `site_url` varchar(255) default '',
  `title_b` varchar(255) default '',
  `title_e` varchar(255) default '',
  `time_b` varchar(255) default '',
  `time_e` varchar(255) default '',
  `time_format` varchar(255) default '',
  `content_b` varchar(255) default '',
  `content_e` varchar(255) default '',
  `comment_b` varchar(255) default '',
  `comment_e` varchar(255) default '',
  `read_b` varchar(255) default '',
  `read_e` varchar(255) default '',
  `author_b` varchar(255) default '',
  `author_e` varchar(255) default '',
  `media_b` varchar(255) default '',
  `media_e` varchar(255) default '',
  PRIMARY KEY  (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `blog_key`
--

DROP TABLE IF EXISTS `blog_key`;
CREATE TABLE IF NOT EXISTS `blog_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `uk_id` (`uk_id`),
  KEY `audit_status` (`audit_status`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1500188 ;

-- --------------------------------------------------------

--
-- 表的结构 `channel_list`
--

DROP TABLE IF EXISTS `channel_list`;
CREATE TABLE IF NOT EXISTS `channel_list` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_name` varchar(255) default '',
  `full_domain` varchar(255) default '',
  PRIMARY KEY  (`c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- 表的结构 `file_list`
--

DROP TABLE IF EXISTS `file_list`;
CREATE TABLE IF NOT EXISTS `file_list` (
  `file_id` int(11) NOT NULL auto_increment,
  `file_name` varchar(255) default '',
  `export_time` int(11) default '0',
  `start_time` int(11) default '0',
  `end_time` int(11) default '0',
  `user_id` int(11) default '0',
  `uk_id` varchar(255) default '',
  PRIMARY KEY  (`file_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=33028 ;

-- --------------------------------------------------------

--
-- 表的结构 `info_admin`
--

DROP TABLE IF EXISTS `info_admin`;
CREATE TABLE IF NOT EXISTS `info_admin` (
  `admin_id` int(4) NOT NULL auto_increment,
  `admin_account` varchar(50) default NULL,
  `admin_password` varchar(50) default NULL,
  `admin_logintime` datetime default NULL,
  `admin_loginip` varchar(100) default NULL,
  `admin_logincount` int(10) default '0',
  PRIMARY KEY  (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `info_stats`
--

DROP TABLE IF EXISTS `info_stats`;
CREATE TABLE IF NOT EXISTS `info_stats` (
  `is_id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) default '0',
  `positive_num` int(11) default '0',
  `negative_num` int(11) default '0',
  `neutral_num` int(11) default '0',
  `total_num` int(11) default '0',
  `article_class` int(11) default '0',
  `stats_date` varchar(255) default '',
  `stats_time` int(11) default '0',
  PRIMARY KEY  (`is_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=37720 ;

-- --------------------------------------------------------

--
-- 表的结构 `keyword`
--

DROP TABLE IF EXISTS `keyword`;
CREATE TABLE IF NOT EXISTS `keyword` (
  `k_id` int(11) NOT NULL auto_increment,
  `keyword` varchar(255) NOT NULL default '',
  `weight` int(11) NOT NULL default '1' COMMENT '权重',
  PRIMARY KEY  (`k_id`),
  UNIQUE KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=253 ;

-- --------------------------------------------------------

--
-- 表的结构 `keyword_filter`
--

DROP TABLE IF EXISTS `keyword_filter`;
CREATE TABLE IF NOT EXISTS `keyword_filter` (
  `id` int(11) NOT NULL auto_increment,
  `k_id` int(11) NOT NULL,
  `filter_word` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- 表的结构 `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `media_id` int(11) NOT NULL,
  `media_name` varchar(255) default '',
  `media_url` varchar(255) default '',
  PRIMARY KEY  (`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `media_list`
--

DROP TABLE IF EXISTS `media_list`;
CREATE TABLE IF NOT EXISTS `media_list` (
  `m_id` int(11) NOT NULL auto_increment,
  `media_name` varchar(255) default '',
  `domain` varchar(255) default '',
  `grade` int(11) default '1',
  PRIMARY KEY  (`m_id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1136 ;

-- --------------------------------------------------------

--
-- 表的结构 `media_list_bak`
--

DROP TABLE IF EXISTS `media_list_bak`;
CREATE TABLE IF NOT EXISTS `media_list_bak` (
  `m_id` int(11) NOT NULL auto_increment,
  `media_name` varchar(255) default '',
  `domain` varchar(255) default '',
  `grade` int(11) default '1',
  PRIMARY KEY  (`m_id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1098 ;

-- --------------------------------------------------------

--
-- 表的结构 `media_stats`
--

DROP TABLE IF EXISTS `media_stats`;
CREATE TABLE IF NOT EXISTS `media_stats` (
  `ms_id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) default '0',
  `media` varchar(255) default '',
  `positive_num` int(11) default '0',
  `negative_num` int(11) default '0',
  `neutral_num` int(11) default '0',
  `total_num` int(11) default '0',
  `article_class` int(11) default '0',
  `stats_date` varchar(255) default '',
  `stats_time` int(11) default '0',
  PRIMARY KEY  (`ms_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=87442 ;

-- --------------------------------------------------------

--
-- 表的结构 `news_article`
--

DROP TABLE IF EXISTS `news_article`;
CREATE TABLE IF NOT EXISTS `news_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_content` text,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `article_comment` int(11) default '0',
  `article_source` varchar(255) default '未知',
  `article_channel` varchar(255) default '未知',
  `media` varchar(255) default '',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `article_title` (`article_title`),
  KEY `article_title_2` (`article_title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=15725319 ;

-- --------------------------------------------------------

--
-- 表的结构 `news_article_index`
--

DROP TABLE IF EXISTS `news_article_index`;
CREATE TABLE IF NOT EXISTS `news_article_index` (
  `id` int(11) NOT NULL auto_increment,
  `article_title` varchar(255) NOT NULL default ' ',
  `article_content` varchar(255) NOT NULL default ' ',
  `user_id` int(11) NOT NULL default '0',
  `add_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uk_id` (`user_id`),
  KEY `add_time` (`add_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `news_article_rule`
--

DROP TABLE IF EXISTS `news_article_rule`;
CREATE TABLE IF NOT EXISTS `news_article_rule` (
  `r_id` int(11) NOT NULL auto_increment,
  `rule_name` varchar(255) default '',
  `site_url` varchar(255) default '',
  `title_b` varchar(255) default '',
  `title_e` varchar(255) default '',
  `time_b` varchar(255) default '',
  `time_e` varchar(255) default '',
  `time_format` varchar(255) default '',
  `content_b` varchar(255) default '',
  `content_e` varchar(255) default '',
  `comment_b` varchar(255) default '',
  `comment_e` varchar(255) default '',
  `media_b` varchar(255) default '',
  `media_e` varchar(255) default '',
  `source_b` varchar(255) default '',
  `source_e` varchar(255) default '',
  `channel_b` varchar(255) default '',
  `channel_e` varchar(255) default '',
  PRIMARY KEY  (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- 表的结构 `news_key`
--

DROP TABLE IF EXISTS `news_key`;
CREATE TABLE IF NOT EXISTS `news_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `uk_id` (`uk_id`),
  KEY `audit_status` (`audit_status`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=17590905 ;

-- --------------------------------------------------------

--
-- 表的结构 `news_search`
--

DROP TABLE IF EXISTS `news_search`;
CREATE TABLE IF NOT EXISTS `news_search` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_content` text,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `article_comment` int(11) default '0',
  `article_source` varchar(255) default '未知',
  `article_channel` varchar(255) default '未知',
  `media` varchar(255) default '',
  `user_id` int(11) NOT NULL default '0',
  `keyword` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `a_type` int(1) NOT NULL default '1',
  `search_engine` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `article_title` (`article_title`),
  KEY `user_id` (`user_id`),
  KEY `keyword` (`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=74833 ;

-- --------------------------------------------------------

--
-- 表的结构 `order_list`
--

DROP TABLE IF EXISTS `order_list`;
CREATE TABLE IF NOT EXISTS `order_list` (
  `o_id` int(11) NOT NULL auto_increment,
  `url` varchar(255) default '',
  `r_id` int(11) NOT NULL default '0',
  `r_type` int(11) default '0',
  `r_order` int(11) default '99',
  PRIMARY KEY  (`o_id`),
  KEY `r_order` (`r_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

-- --------------------------------------------------------

--
-- 表的结构 `positive_words`
--

DROP TABLE IF EXISTS `positive_words`;
CREATE TABLE IF NOT EXISTS `positive_words` (
  `id` int(11) NOT NULL auto_increment,
  `words_content` varchar(64) NOT NULL,
  `words_score` double NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `property`
--

DROP TABLE IF EXISTS `property`;
CREATE TABLE IF NOT EXISTS `property` (
  `w_id` int(11) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  `w_type` int(11) default '0',
  PRIMARY KEY  (`w_id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=393 ;

-- --------------------------------------------------------

--
-- 表的结构 `qq_cookies`
--

DROP TABLE IF EXISTS `qq_cookies`;
CREATE TABLE IF NOT EXISTS `qq_cookies` (
  `qc_id` int(11) NOT NULL auto_increment,
  `qq_number` varchar(32) NOT NULL default '',
  `qq_cookie` text NOT NULL,
  `expries` int(11) NOT NULL default '0',
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`qc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- 表的结构 `spider_site`
--

DROP TABLE IF EXISTS `spider_site`;
CREATE TABLE IF NOT EXISTS `spider_site` (
  `site_id` int(11) NOT NULL auto_increment,
  `site_name` varchar(200) character set utf8 default '',
  `site_url` varchar(200) character set utf8 default '',
  `site_crawldepth` int(4) default '1',
  `site_updatedepth` int(4) default '1',
  `site_interval` int(11) default '3600',
  `site_urlhold1` varchar(200) character set utf8 default '',
  `site_urlhold2` varchar(200) character set utf8 default '',
  `site_urlhold3` varchar(200) character set utf8 default '',
  `site_urlhold4` varchar(200) character set utf8 default '',
  `site_urlthrow1` varchar(200) character set utf8 default '',
  `site_urlthrow2` varchar(200) character set utf8 default '',
  `site_urlthrow3` varchar(200) character set utf8 default '',
  `site_urlthrow4` varchar(200) character set utf8 default '',
  `site_addtime` int(11) default '0',
  `site_type` int(11) NOT NULL default '0',
  `stress` int(11) NOT NULL default '1',
  PRIMARY KEY  (`site_id`),
  UNIQUE KEY `site_url` (`site_url`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=115 ;

-- --------------------------------------------------------

--
-- 表的结构 `static_words`
--

DROP TABLE IF EXISTS `static_words`;
CREATE TABLE IF NOT EXISTS `static_words` (
  `id` int(11) NOT NULL auto_increment,
  `words_content` varchar(64) NOT NULL,
  `num` int(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=3668 ;

-- --------------------------------------------------------

--
-- 表的结构 `statsFile_list`
--

DROP TABLE IF EXISTS `statsFile_list`;
CREATE TABLE IF NOT EXISTS `statsFile_list` (
  `file_id` int(11) NOT NULL auto_increment,
  `file_name` varchar(255) default '',
  `export_time` int(11) default '0',
  `start_time` int(11) default '0',
  `end_time` int(11) default '0',
  `user_id` int(11) default '0',
  `uk_id` varchar(255) default '',
  PRIMARY KEY  (`file_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- 表的结构 `throw_url_list`
--

DROP TABLE IF EXISTS `throw_url_list`;
CREATE TABLE IF NOT EXISTS `throw_url_list` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `key_num` int(11) default '0',
  `status` int(11) default '0',
  `reg_time` int(11) NOT NULL default '0',
  `user_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_category`
--

DROP TABLE IF EXISTS `user_category`;
CREATE TABLE IF NOT EXISTS `user_category` (
  `c_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `category_name` varchar(64) NOT NULL default '',
  `add_time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_keywords`
--

DROP TABLE IF EXISTS `user_keywords`;
CREATE TABLE IF NOT EXISTS `user_keywords` (
  `uk_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `k_id` int(11) NOT NULL,
  `add_time` int(11) default '0',
  `c_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uk_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=256 ;

-- --------------------------------------------------------

--
-- 表的结构 `video_article`
--

DROP TABLE IF EXISTS `video_article`;
CREATE TABLE IF NOT EXISTS `video_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_site` int(11) default '0',
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default '',
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_summary` varchar(1000) default '',
  `media` varchar(255) default '未知',
  `article_channel` varchar(255) default '未知',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=727644 ;

-- --------------------------------------------------------

--
-- 表的结构 `video_article_rule`
--

DROP TABLE IF EXISTS `video_article_rule`;
CREATE TABLE IF NOT EXISTS `video_article_rule` (
  `r_id` int(11) NOT NULL auto_increment,
  `rule_name` varchar(255) default '',
  `site_url` varchar(255) default '',
  `title_b` varchar(255) default '',
  `title_e` varchar(255) default '',
  `time_b` varchar(255) default '',
  `time_e` varchar(255) default '',
  `time_format` varchar(255) default '',
  `summary_b` varchar(255) default '',
  `summary_e` varchar(255) default '',
  `media_b` varchar(255) default '',
  `media_e` varchar(255) default '',
  `channel_b` varchar(255) default '',
  `channel_e` varchar(255) default '',
  PRIMARY KEY  (`r_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `video_key`
--

DROP TABLE IF EXISTS `video_key`;
CREATE TABLE IF NOT EXISTS `video_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `uk_id` (`uk_id`),
  KEY `audit_status` (`audit_status`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=780776 ;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_article`
--

DROP TABLE IF EXISTS `weibo_article`;
CREATE TABLE IF NOT EXISTS `weibo_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default NULL,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_comment` int(11) default '0',
  `article_repost` int(11) default '0',
  `author` varchar(255) default '',
  `isV` int(11) default '0',
  `rz_info` varchar(255) default NULL,
  `fans` int(11) default '0',
  `media` varchar(255) default '未知',
  `mid` varchar(128) NOT NULL,
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `media` (`media`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=8020762 ;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_key`
--

DROP TABLE IF EXISTS `weibo_key`;
CREATE TABLE IF NOT EXISTS `weibo_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `audit_status` (`audit_status`),
  KEY `uk_id` (`uk_id`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=9172997 ;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_stats`
--

DROP TABLE IF EXISTS `weibo_stats`;
CREATE TABLE IF NOT EXISTS `weibo_stats` (
  `ws_id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) default '0',
  `media` varchar(255) default '',
  `positive_num` int(11) default '0',
  `negative_num` int(11) default '0',
  `neutral_num` int(11) default '0',
  `total_num` int(11) default '0',
  `isV` int(11) default '0',
  `stats_date` varchar(255) default '',
  `stats_time` int(11) default '0',
  PRIMARY KEY  (`ws_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=7391 ;

-- --------------------------------------------------------

--
-- 表的结构 `weibo_task`
--

DROP TABLE IF EXISTS `weibo_task`;
CREATE TABLE IF NOT EXISTS `weibo_task` (
  `id` int(11) NOT NULL auto_increment,
  `k_id` int(11) default '0',
  `last_scan_time1` int(11) default '0',
  `last_scan_time2` int(11) default '0',
  `last_scan_time3` int(11) default '0',
  `last_scan_time4` int(11) default '0',
  `last_scan_time5` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=253 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_article`
--

DROP TABLE IF EXISTS `weixin_article`;
CREATE TABLE IF NOT EXISTS `weixin_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default NULL,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_comment` int(11) default '0',
  `article_repost` int(11) default '0',
  `author` varchar(255) default '',
  `isV` int(11) default '0',
  `rz_info` varchar(255) default NULL,
  `fans` int(11) default '0',
  `media` varchar(255) default '未知',
  `mid` varchar(128) NOT NULL default '''''',
  `article_summary` text NOT NULL,
  `read_num` int(11) NOT NULL default '0',
  `like_num` int(11) NOT NULL default '0',
  `sign` varchar(255) NOT NULL,
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `media` (`media`),
  KEY `sign` (`sign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2028513 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_cookies`
--

DROP TABLE IF EXISTS `weixin_cookies`;
CREATE TABLE IF NOT EXISTS `weixin_cookies` (
  `id` int(11) NOT NULL auto_increment,
  `cookie` text NOT NULL,
  `status` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=4927 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_key`
--

DROP TABLE IF EXISTS `weixin_key`;
CREATE TABLE IF NOT EXISTS `weixin_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `audit_status` (`audit_status`),
  KEY `uk_id` (`uk_id`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2191917 ;

-- --------------------------------------------------------

--
-- 表的结构 `weixin_url`
--

DROP TABLE IF EXISTS `weixin_url`;
CREATE TABLE IF NOT EXISTS `weixin_url` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1852308 ;

-- --------------------------------------------------------

--
-- 表的结构 `words_feeling`
--

DROP TABLE IF EXISTS `words_feeling`;
CREATE TABLE IF NOT EXISTS `words_feeling` (
  `id` int(11) NOT NULL auto_increment,
  `words_content` varchar(64) NOT NULL,
  `words_property` varchar(32) NOT NULL,
  `words_type` varchar(32) NOT NULL,
  `words_level` int(11) NOT NULL,
  `words_polar` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=27469 ;

-- --------------------------------------------------------

--
-- 表的结构 `words_group`
--

DROP TABLE IF EXISTS `words_group`;
CREATE TABLE IF NOT EXISTS `words_group` (
  `id` int(11) NOT NULL auto_increment,
  `first_word` varchar(32) NOT NULL,
  `second_word` varchar(32) NOT NULL,
  `score` double NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `words_negative`
--

DROP TABLE IF EXISTS `words_negative`;
CREATE TABLE IF NOT EXISTS `words_negative` (
  `id` int(11) NOT NULL auto_increment,
  `words_content` varchar(64) NOT NULL,
  `words_score` double NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `words_content` (`words_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=19166 ;

-- --------------------------------------------------------

--
-- 表的结构 `words_positive`
--

DROP TABLE IF EXISTS `words_positive`;
CREATE TABLE IF NOT EXISTS `words_positive` (
  `id` int(11) NOT NULL auto_increment,
  `words_content` varchar(64) NOT NULL,
  `words_score` double NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `words_content` (`words_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=14023 ;

-- --------------------------------------------------------

--
-- 表的结构 `yq_search`
--

DROP TABLE IF EXISTS `yq_search`;
CREATE TABLE IF NOT EXISTS `yq_search` (
  `search_id` int(11) NOT NULL auto_increment,
  `search_name` varchar(32) NOT NULL,
  `search_type` tinyint(4) NOT NULL,
  `search_character` tinyint(4) NOT NULL default '0',
  `search_url` varchar(255) NOT NULL COMMENT '360新闻和 bing需要转码',
  `search_urlhold` varchar(200) NOT NULL default 'http://*',
  `search_urlthrow` varchar(200) NOT NULL,
  `page_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- 表的结构 `zhidao_article`
--

DROP TABLE IF EXISTS `zhidao_article`;
CREATE TABLE IF NOT EXISTS `zhidao_article` (
  `article_id` int(11) NOT NULL auto_increment,
  `article_url` varchar(255) default '',
  `article_title` varchar(255) default NULL,
  `article_pubtime` int(11) default '0',
  `article_addtime` int(11) default '0',
  `article_comment` int(11) default '0',
  `article_repost` int(11) default '0',
  `author` varchar(255) default '',
  `isV` int(11) default '0',
  `rz_info` varchar(255) default NULL,
  `fans` int(11) default '0',
  `media` varchar(255) default '未知',
  `mid` varchar(128) default NULL,
  `article_summary` text NOT NULL,
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_url` (`article_url`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `media` (`media`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=810812 ;

-- --------------------------------------------------------

--
-- 表的结构 `zhidao_key`
--

DROP TABLE IF EXISTS `zhidao_key`;
CREATE TABLE IF NOT EXISTS `zhidao_key` (
  `id` int(11) NOT NULL auto_increment,
  `uk_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `article_property` int(11) default '0',
  `article_pubtime` int(11) default '0',
  `audit_status` int(11) NOT NULL default '0',
  `audit_time` int(11) NOT NULL default '0',
  `a_type` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '1',
  `article_addtime` int(11) NOT NULL default '0',
  `c_id` int(11) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `article_pubtime` (`article_pubtime`),
  KEY `audit_status` (`audit_status`),
  KEY `uk_id` (`uk_id`),
  KEY `article_id` (`article_id`),
  KEY `article_property` (`article_property`),
  KEY `user_id` (`user_id`),
  KEY `article_addtime` (`article_addtime`),
  KEY `c_id` (`c_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=868397 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
