-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 02 月 14 日 17:38
-- 服务器版本: 5.5.24-log
-- PHP 版本: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `wavecloud`
--

-- --------------------------------------------------------

--
-- 表的结构 `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `group`
--

INSERT INTO `group` (`id`, `group_name`, `create_date`) VALUES
(1, '管理员', '2013-06-14 00:00:00'),
(11, '用户', '2013-11-20 19:01:22');

-- --------------------------------------------------------

--
-- 表的结构 `iso`
--

CREATE TABLE IF NOT EXISTS `iso` (
  `iso_id` int(11) NOT NULL AUTO_INCREMENT,
  `iso_name` varchar(255) DEFAULT NULL COMMENT '镜像名称',
  `iso_path` varchar(255) DEFAULT NULL COMMENT '镜像路径',
  `add_user` int(11) DEFAULT '0' COMMENT '创建人',
  `last_modify_user` int(11) DEFAULT '0',
  `add_date` datetime DEFAULT NULL COMMENT '创建日期',
  `last_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`iso_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `iso`
--

INSERT INTO `iso` (`iso_id`, `iso_name`, `iso_path`, `add_user`, `last_modify_user`, `add_date`, `last_modify_date`) VALUES
(6, 'Centos6.3', '/data/iso/centos6.3.qcow2', 1, 1, '2013-10-22 18:11:53', '2014-01-03 16:36:27'),
(8, 'Centos5.3', '/data/iso/centos5.3.qcow2', 1, 1, '2014-01-03 16:36:17', '2014-01-03 16:36:17');

-- --------------------------------------------------------

--
-- 表的结构 `net`
--

CREATE TABLE IF NOT EXISTS `net` (
  `net_id` int(11) NOT NULL AUTO_INCREMENT,
  `net_name` varchar(255) DEFAULT NULL COMMENT '网络名称',
  `net_bridge` varchar(255) DEFAULT NULL COMMENT '网桥',
  `net_private` varchar(20) DEFAULT NULL,
  `net_gateway` varchar(20) DEFAULT NULL COMMENT '网关',
  `net_mask` varchar(20) DEFAULT NULL COMMENT '掩码',
  `net_dns` varchar(20) DEFAULT NULL,
  `check_radio` enum('2','1') DEFAULT '1',
  `ip_begin` varchar(20) DEFAULT NULL,
  `ip_end` varchar(20) DEFAULT NULL,
  `add_user` int(11) DEFAULT NULL,
  `last_modify_user` int(11) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `last_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`net_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `net`
--

INSERT INTO `net` (`net_id`, `net_name`, `net_bridge`, `net_private`, `net_gateway`, `net_mask`, `net_dns`, `check_radio`, `ip_begin`, `ip_end`, `add_user`, `last_modify_user`, `add_date`, `last_modify_date`) VALUES
(3, 'test', 'public', '192.168.10', '42.51.14.1', '255.255.255.0', '8.8.8.8', '2', '42.51.14.160', '42.51.14.170', 1, 1, '2013-12-04 16:28:23', '2013-12-26 17:59:40'),
(6, 'test2', 'public', '192.168.10', '42.51.14.1', '255.255.255.0', '8.8.8.8', '2', '42.51.14.180', '42.51.14.190', 1, 1, '2013-12-26 17:56:54', '2013-12-26 17:59:46');

-- --------------------------------------------------------

--
-- 表的结构 `net_ips`
--

CREATE TABLE IF NOT EXISTS `net_ips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `net_id` int(11) NOT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `use_status` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=89 ;

--
-- 转存表中的数据 `net_ips`
--

INSERT INTO `net_ips` (`id`, `net_id`, `ip`, `use_status`) VALUES
(53, 3, '42.51.14.160', '1'),
(54, 3, '42.51.14.161', '0'),
(55, 3, '42.51.14.162', '0'),
(56, 3, '42.51.14.163', '0'),
(57, 3, '42.51.14.164', '0'),
(58, 3, '42.51.14.165', '0'),
(59, 3, '42.51.14.166', '0'),
(60, 3, '42.51.14.167', '0'),
(61, 3, '42.51.14.168', '0'),
(62, 3, '42.51.14.169', '0'),
(63, 3, '42.51.14.170', '0'),
(78, 6, '42.51.14.180', '0'),
(79, 6, '42.51.14.181', '0'),
(80, 6, '42.51.14.182', '0'),
(81, 6, '42.51.14.183', '0'),
(82, 6, '42.51.14.184', '0'),
(83, 6, '42.51.14.185', '0'),
(84, 6, '42.51.14.186', '0'),
(85, 6, '42.51.14.187', '0'),
(86, 6, '42.51.14.188', '0'),
(87, 6, '42.51.14.189', '0'),
(88, 6, '42.51.14.190', '0');

-- --------------------------------------------------------

--
-- 表的结构 `pm`
--

CREATE TABLE IF NOT EXISTS `pm` (
  `pm_id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_name` varchar(50) DEFAULT NULL,
  `pm_ip` varchar(20) DEFAULT NULL,
  `cpu_use` int(5) DEFAULT NULL,
  `mem_use` int(8) DEFAULT NULL,
  `public_mask` varchar(20) DEFAULT NULL,
  `public_gateway` varchar(20) DEFAULT NULL,
  `dns` varchar(20) DEFAULT NULL,
  `private_ip` varchar(20) DEFAULT NULL,
  `private_mask` varchar(20) DEFAULT NULL,
  `cpu_cores` tinyint(4) DEFAULT NULL,
  `cs_hd` int(11) DEFAULT NULL,
  `actual_free_hd` int(11) DEFAULT NULL,
  `plan_free_hd` int(11) DEFAULT NULL,
  `cs_mem` int(11) DEFAULT NULL,
  `actual_free_mem` int(11) DEFAULT NULL,
  `plan_free_mem` int(11) DEFAULT NULL,
  `add_user` int(11) DEFAULT NULL,
  `last_modify_user` int(11) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `last_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`pm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `pm`
--

INSERT INTO `pm` (`pm_id`, `pm_name`, `pm_ip`, `cpu_use`, `mem_use`, `public_mask`, `public_gateway`, `dns`, `private_ip`, `private_mask`, `cpu_cores`, `cs_hd`, `actual_free_hd`, `plan_free_hd`, `cs_mem`, `actual_free_mem`, `plan_free_mem`, `add_user`, `last_modify_user`, `add_date`, `last_modify_date`) VALUES
(2, 'test222', '42.51.14.2', 52, 47, '255.255.255.0', '42.51.14.1', '8.8.8.8', '192.168.17.11', '255.255.0.0', 32, 7811, 6727, 4505, 198, 101, 102, 1, 1, '2013-12-10 10:58:35', '2013-12-10 10:58:35'),
(3, 'test223', '42.51.14.3', 42, 47, '255.255.255.0', '42.51.14.1', '8.8.8.8', '192.168.17.11', '255.255.0.0', 32, 7811, 6727, 4505, 198, 101, 102, 1, 1, '2013-12-10 10:58:35', '2013-12-10 10:58:35'),
(4, 'test224', '42.51.14.4', 53, 47, '255.255.255.0', '42.51.14.1', '8.8.8.8', '192.168.17.11', '255.255.0.0', 32, 7811, 6727, 4505, 198, 101, 102, 1, 1, '2013-12-10 10:58:35', '2013-12-10 10:58:35'),
(5, 'test225', '42.51.14.5', 56, 47, '255.255.255.0', '42.51.14.1', '8.8.8.8', '192.168.17.11', '255.255.0.0', 32, 7811, 6727, 4505, 198, 101, 102, 1, 1, '2013-12-10 10:58:35', '2013-12-10 10:58:35'),
(6, 'test226', '42.51.14.6', 52, 47, '255.255.255.0', '42.51.14.1', '8.8.8.8', '192.168.17.11', '255.255.0.0', 32, 7811, 6727, 4505, 198, 101, 102, 1, 1, '2013-12-10 10:58:35', '2013-12-26 15:15:46');

-- --------------------------------------------------------

--
-- 表的结构 `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `net_id` int(11) DEFAULT NULL,
  `template_name` varchar(255) DEFAULT NULL COMMENT '模版名称',
  `iso_id` int(11) DEFAULT NULL COMMENT '镜像id',
  `iso_node_id` int(11) DEFAULT NULL COMMENT '物理机镜像节点ID',
  `cpu` tinyint(4) DEFAULT NULL COMMENT 'CPU核心数',
  `mem` tinyint(4) DEFAULT NULL COMMENT '内存',
  `hd` int(4) DEFAULT NULL COMMENT '硬盘',
  `partition` enum('1','0') DEFAULT '0' COMMENT '是否分区 0-是，1-否',
  `percentage` tinyint(4) DEFAULT '20' COMMENT 'home分区 百分比',
  `add_user` int(11) DEFAULT NULL,
  `last_modify_user` int(11) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL COMMENT '添加日期',
  `last_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `template`
--

INSERT INTO `template` (`template_id`, `net_id`, `template_name`, `iso_id`, `iso_node_id`, `cpu`, `mem`, `hd`, `partition`, `percentage`, `add_user`, `last_modify_user`, `add_date`, `last_modify_date`) VALUES
(4, 3, 'testttt', 6, 3, 2, 4, 320, '0', 20, 1, 1, '2013-12-04 17:54:31', '2014-01-03 15:58:35');

-- --------------------------------------------------------

--
-- 表的结构 `template_pm_mapping`
--

CREATE TABLE IF NOT EXISTS `template_pm_mapping` (
  `mapping_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `pm_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`mapping_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `template_pm_mapping`
--

INSERT INTO `template_pm_mapping` (`mapping_id`, `template_id`, `pm_id`) VALUES
(4, 4, 2),
(6, 4, 4),
(7, 4, 3);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `group` int(11) unsigned NOT NULL,
  `create_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `group`, `create_date`) VALUES
(1, 'xuping', 'e10adc3949ba59abbe56e057f20f883e', 'xuping@gamewave.net', 1, '2013-06-14 00:00:00'),
(27, 'xpmozong', 'e10adc3949ba59abbe56e057f20f883e', '361131953@qq.com', 11, '2013-11-20 19:01:41');

-- --------------------------------------------------------

--
-- 表的结构 `vm`
--

CREATE TABLE IF NOT EXISTS `vm` (
  `vm_id` int(11) NOT NULL AUTO_INCREMENT,
  `vm_status` varchar(10) DEFAULT NULL,
  `vm_name` varchar(255) DEFAULT NULL,
  `vm_instanse` varchar(255) DEFAULT NULL,
  `pm_ip` varchar(20) DEFAULT NULL,
  `vm_ip` varchar(20) DEFAULT NULL,
  `vm_internel_ip` varchar(20) DEFAULT NULL,
  `pm_id` int(11) DEFAULT NULL,
  `template_id` int(11) NOT NULL,
  `add_user` int(11) DEFAULT NULL,
  `last_modify_user` int(11) DEFAULT NULL,
  `add_date` datetime DEFAULT NULL,
  `last_modify_date` datetime DEFAULT NULL,
  PRIMARY KEY (`vm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `vm`
--

INSERT INTO `vm` (`vm_id`, `vm_status`, `vm_name`, `vm_instanse`, `pm_ip`, `vm_ip`, `vm_internel_ip`, `pm_id`, `template_id`, `add_user`, `last_modify_user`, `add_date`, `last_modify_date`) VALUES
(6, 'running', 'test1111', 'instanse-6', '42.51.14.3', '42.51.14.160', '192.168.10.160', 2, 4, 1, 1, '2013-12-25 17:55:32', '2013-12-25 17:55:32');

-- --------------------------------------------------------

--
-- 表的结构 `yiisession`
--

CREATE TABLE IF NOT EXISTS `yiisession` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `YiiSession` (
  `id` char(32) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  `data` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
