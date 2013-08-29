
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `blog`
-- ----------------------------
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(64) NOT NULL,
  `title` varchar(256) NOT NULL,
  `content` mediumtext,
  `ctime` datetime DEFAULT NULL,
  `src` text NOT NULL,
  `status` char(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=675 DEFAULT CHARSET=utf8;
