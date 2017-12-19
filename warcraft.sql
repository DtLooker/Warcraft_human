
DROP DATABASE IF EXISTS `warcraft`;

CREATE DATABASE `warcraft`;

-- ----------------------------
-- Table structure for banner
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT 'Banner名称，通常作为标识',
  `description` varchar(255) DEFAULT NULL COMMENT 'banner描述',
  `delete_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT="banner管理表";
-- ----------------------------
-- Records of banner
-- ----------------------------
INSERT INTO `banner` VALUES('1', '首页置顶', '首页轮播图', null, null);

/**
    banner 与 banner_item之间一对多的关系
    一个banner可以有多个banner_item; 一个banner_item只能属于一个banner
 */

-- ----------------------------
-- Table structure for banner_item
-- ----------------------------
DROP TABLE IF EXISTS `banner_item`;
CREATE TABLE `banner_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img_id` int(11) NOT NULL COMMENT '外键，关联image表',
  `key_word` varchar(100) NOT NULL COMMENT '执行关键字，根据不同type含义不同',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '跳转类型，可能导向商品，可能导向专题或其他。 0，无导向； 1，导向商品； 2.导向专题',
  `delete_time` int(11) DEFAULT NULL,
  `banner_id` int(11) NOT NULL COMMENT '外键，关联banner表',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='banner子项表';
-- ----------------------------
-- Records of banner_item
-- ----------------------------
INSERT INTO `banner_item` VALUES ('1', '65', '6', '1', null, '1', null);
INSERT INTO `banner_item` VALUES ('2', '2', '25', '1', null, '1', null);
INSERT INTO `banner_item` VALUES ('3', '3', '11', '1', null, '1', null);
INSERT INTO `banner_item` VALUES ('5', '1', '10', '1', null, '1', null);