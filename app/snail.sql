-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2018 �?11 �?01 �?12:47
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `snail`
--

-- --------------------------------------------------------

--
-- 表的结构 `snail_ad`
--

CREATE TABLE IF NOT EXISTS `snail_ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ad_img` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ad_remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `ad_type` varchar(10) NOT NULL,
  `ad_show` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `snail_ad`
--

INSERT INTO `snail_ad` (`id`, `ad_name`, `ad_img`, `ad_remark`, `ad_type`, `ad_show`) VALUES
(1, '首页轮播图01', '/upload/20181031/33d2360b6fb024e170425f9ce57a14c1.jpg', '', 'INDEX', 0),
(2, '首页广告02', '/upload/20181031/33d2360b6fb024e170425f9ce57a14c1.jpg', 'test', 'INDEX', 1),
(3, '首页广告02', '/upload/20181031/33d2360b6fb024e170425f9ce57a14c1.jpg', 'test', 'INDEX', 1);

-- --------------------------------------------------------

--
-- 表的结构 `snail_ad_category_bak`
--

CREATE TABLE IF NOT EXISTS `snail_ad_category_bak` (
  `category_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  `parent_id` int(3) NOT NULL COMMENT '上级id',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `snail_ad_category_bak`
--

INSERT INTO `snail_ad_category_bak` (`category_id`, `category_name`, `parent_id`) VALUES
(1, '招聘求职', 0),
(2, '房屋租赁', 0),
(3, '商铺租售', 0),
(4, '二手车', 0),
(5, '闲置二手', 0),
(6, '广告墙', 0),
(7, '全职招聘', 1),
(8, '兼职招聘', 1),
(9, '我要求职', 1),
(10, '整租', 2),
(11, '合租', 2),
(12, '商铺出售', 3),
(13, '商铺出租', 3);

-- --------------------------------------------------------

--
-- 表的结构 `snail_job_find`
--

CREATE TABLE IF NOT EXISTS `snail_job_find` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `real_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `sex` enum('男','女','保密','') NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `birthday` varchar(10) NOT NULL,
  `city` varchar(50) NOT NULL,
  `now_state` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '求职状态',
  `now_ident` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '当前身份',
  `highest_degree` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '最高学历',
  `job_experience` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '工作经验',
  `job_desc` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '求职描述',
  `show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否展示',
  `start_date` int(10) NOT NULL,
  `end_date` int(10) NOT NULL,
  `order_id` int(20) NOT NULL COMMENT '订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='求职招聘' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `snail_job_find`
--

INSERT INTO `snail_job_find` (`id`, `uid`, `real_name`, `sex`, `mobile`, `birthday`, `city`, `now_state`, `now_ident`, `highest_degree`, `job_experience`, `job_desc`, `show`, `start_date`, `end_date`, `order_id`) VALUES
(1, 1, '王某某', '男', '17621090121', '1990-01-21', '上海', '在职', '学生', '本科', '三年', '自我介绍。。。。', 1, 1540994349, 1550994349, 0),
(3, 1, '老xxxx', '男', '17621090121', '', '', '', '', 'edqwdqd', '', 'dqwdq', 0, 0, 0, 0),
(4, 1, '老xxxx', '男', '17621090121', '', '', '', '', 'edqwdqd', '', 'dqwdq', 0, 0, 0, 0),
(5, 1, '老xxxx', '男', '17621090121', '', '', '', '', 'edqwdqd', '', 'dqwdq', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `snail_job_release`
--

CREATE TABLE IF NOT EXISTS `snail_job_release` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('FULLTIME','PARTTIME','','') NOT NULL,
  `uid` int(10) NOT NULL,
  `job_title` varchar(20) NOT NULL COMMENT '职称',
  `company_industry` varchar(50) NOT NULL COMMENT '公司行业',
  `pay` varchar(50) NOT NULL COMMENT '月薪',
  `welfare` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '福利',
  `job_demand` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '职位要求',
  `ed_demand` varchar(20) NOT NULL COMMENT '学历要求',
  `year_demand` varchar(20) NOT NULL COMMENT '年限要求',
  `contacts_man` varchar(20) NOT NULL COMMENT '联系人',
  `contacts_mobile` varchar(20) NOT NULL COMMENT '联系电话',
  `part_term` enum('长期','短期','','') NOT NULL,
  `part_interval_1` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '兼职时段1',
  `part_interval_2` varchar(20) NOT NULL COMMENT '兼职时段2',
  `part_payment` varchar(20) NOT NULL COMMENT '结账方式',
  `part_address` varchar(100) NOT NULL COMMENT '工作地址',
  `part_content` varchar(200) NOT NULL COMMENT '工作内容',
  `show` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` int(10) NOT NULL,
  `end_date` int(10) NOT NULL,
  `order_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_title` (`job_title`,`company_industry`,`pay`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `snail_job_release`
--

INSERT INTO `snail_job_release` (`id`, `type`, `uid`, `job_title`, `company_industry`, `pay`, `welfare`, `job_demand`, `ed_demand`, `year_demand`, `contacts_man`, `contacts_mobile`, `part_term`, `part_interval_1`, `part_interval_2`, `part_payment`, `part_address`, `part_content`, `show`, `start_date`, `end_date`, `order_id`) VALUES
(1, 'FULLTIME', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `snail_order_log`
--

CREATE TABLE IF NOT EXISTS `snail_order_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(10) NOT NULL,
  `uid` int(11) NOT NULL,
  `mobile` int(11) NOT NULL,
  `amount` int(10) NOT NULL DEFAULT '0',
  `chargeTime` int(10) NOT NULL,
  `createTime` int(10) NOT NULL,
  `payMethod` varchar(20) NOT NULL,
  `platformId` varchar(200) NOT NULL,
  `status` enum('CREATE','PAIDED','CANCEL','') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `snail_post_log`
--

CREATE TABLE IF NOT EXISTS `snail_post_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(10) NOT NULL,
  `post_type` varchar(20) NOT NULL,
  `uid` int(10) NOT NULL,
  `dateline` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `snail_post_log`
--

INSERT INTO `snail_post_log` (`id`, `post_id`, `post_type`, `uid`, `dateline`) VALUES
(1, 4, 'FIND', 1, 1541076285),
(2, 5, 'FIND', 1, 1541076287);

-- --------------------------------------------------------

--
-- 表的结构 `snail_token`
--

CREATE TABLE IF NOT EXISTS `snail_token` (
  `uid` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `expire_time` int(10) NOT NULL,
  UNIQUE KEY `uid_2` (`uid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `snail_token`
--

INSERT INTO `snail_token` (`uid`, `token`, `expire_time`) VALUES
(1, 'e10adc3949ba59abbe56e057f20f883e', 1543634157);

-- --------------------------------------------------------

--
-- 表的结构 `snail_user`
--

CREATE TABLE IF NOT EXISTS `snail_user` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `mobile` int(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `snail_user`
--

INSERT INTO `snail_user` (`uid`, `username`, `mobile`, `password`) VALUES
(1, 'tao', 2147483647, '');

-- --------------------------------------------------------

--
-- 表的结构 `snail_verify`
--

CREATE TABLE IF NOT EXISTS `snail_verify` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mobile` int(11) NOT NULL,
  `code` int(6) NOT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
