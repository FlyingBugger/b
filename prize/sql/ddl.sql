CREATE TABLE `order` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `prize_id` int(11) NOT NULL DEFAULT '0' COMMENT '奖品ID',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `openid` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='中奖名单';

CREATE TABLE `prize` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '奖品名称',
  `num` smallint(6) NOT NULL DEFAULT '0' COMMENT '数量',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `img_url` varchar(255) DEFAULT NULL,
  `top` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='奖品';

CREATE TABLE `sys_config` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `top` smallint(6) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

CREATE TABLE `tp_newuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(1000) DEFAULT NULL COMMENT '昵称',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别',
  `city` varchar(1000) DEFAULT NULL COMMENT '城市',
  `country` varchar(1000) DEFAULT NULL COMMENT '国家',
  `province` varchar(1000) DEFAULT NULL,
  `headimgurl` varchar(1000) DEFAULT NULL,
  `is_gz` tinyint(1) NOT NULL DEFAULT '1',
  `openid` varchar(1000) NOT NULL,
  `telphone` varchar(100) DEFAULT NULL,
  `gztime` int(11) NOT NULL,
  `jfnum` int(8) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`(255)),
  KEY `openids` (`openid`(255))
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;
