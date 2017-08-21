CREATE TABLE `car` (
  `car_sn` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '車子序號',
  `car_com` varchar(255) NOT NULL COMMENT '車廠',
  `car_style` varchar(255) NOT NULL COMMENT '車型',
  `car_owner` varchar(255) NOT NULL COMMENT '顧客',
  `car_tel` varchar(255) NOT NULL COMMENT '電話',
  `car_phone` varchar(255) NOT NULL COMMENT '手機',
  `car_email` varchar(255) NOT NULL COMMENT '電子郵件',
  `car_address` varchar(255) NOT NULL COMMENT '地址',
  `car_id` varchar(255) NOT NULL COMMENT '車牌號碼',
  PRIMARY KEY (`car_sn`),
  UNIQUE KEY `car_id` (`car_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `content` (
  `content_sn` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `log_sn` mediumint(9) unsigned NOT NULL COMMENT '保修序號',
  `item` varchar(255) NOT NULL COMMENT '保修項目',
  `item_price` smallint(6) unsigned NOT NULL COMMENT '細項價格',
  PRIMARY KEY (`content_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `item` (
  `item_price` smallint(6) unsigned NOT NULL COMMENT '保修價格',
  `item_title` varchar(255) NOT NULL COMMENT '維修項目',
  `item_sn` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '保修序號',
  PRIMARY KEY (`item_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `log` (
  `log_sn` mediumint(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '保修序號',
  `car_sn` smallint(6) unsigned NOT NULL COMMENT '車子序號',
  `mainten_date` date NOT NULL COMMENT '保養日期',
  `mainten_kilometer` mediumint(6) unsigned NOT NULL COMMENT '進場公里數',
  `suggest` text NOT NULL COMMENT '建議事項',
  `uid` varchar(255) NOT NULL COMMENT '使用者序號',
  PRIMARY KEY (`log_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `user` (
  `uid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '使用者序號',
  `id` varchar(255) NOT NULL COMMENT '使用者帳號',
  `name` varchar(255) NOT NULL COMMENT '使用者姓名',
  `mail` varchar(255) NOT NULL COMMENT '使用者郵件',
  `phone` varchar(255) NOT NULL COMMENT '使用者手機',
  `status` enum('ok','no') NOT NULL COMMENT '使用者狀態',
  `password` varchar(255) NOT NULL COMMENT '使用者密碼',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;