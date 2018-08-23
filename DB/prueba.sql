/*
Navicat MySQL Data Transfer

Source Server         : LocalHost
Source Server Version : 50635
Source Host           : localhost:3306
Source Database       : prueba

Target Server Type    : MYSQL
Target Server Version : 50635
File Encoding         : 65001

Date: 2018-08-22 23:51:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `detalle`
-- ----------------------------
DROP TABLE IF EXISTS `detalle`;
CREATE TABLE `detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `direccion` varchar(255) DEFAULT NULL,
  `idPersona` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of detalle
-- ----------------------------
INSERT INTO `detalle` VALUES ('1', 'calle falsa123', '2');
INSERT INTO `detalle` VALUES ('2', 'calle real 234', '1');
INSERT INTO `detalle` VALUES ('3', 'calle batman 23', '3');

-- ----------------------------
-- Table structure for `personas`
-- ----------------------------
DROP TABLE IF EXISTS `personas`;
CREATE TABLE `personas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `imagenurl` varchar(255) DEFAULT NULL,
  `tipo` int(11) DEFAULT NULL COMMENT '1=Mago,2=Normal',
  `urlgo` varchar(255) DEFAULT NULL,
  `trabajo` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of personas
-- ----------------------------
INSERT INTO `personas` VALUES ('1', 'paco', '10', 'http://www.laondaverde.org/laondaverde/energy/images/desktop-pc-250x188.jpg', '1', 'www.google.com', '1');
INSERT INTO `personas` VALUES ('2', 'alberto', '20', null, '2', 'www.youtube.com', '2');
INSERT INTO `personas` VALUES ('3', 'pedro', '50', 'http://images1.fanpop.com/images/image_uploads/Metallicar-metallicar-853610_400_225.jpg', '1', 'www.facebook.com', '3');
INSERT INTO `personas` VALUES ('4', 'abel', '30', null, null, 'www.twd.com', '3');

-- ----------------------------
-- Table structure for `trabajos`
-- ----------------------------
DROP TABLE IF EXISTS `trabajos`;
CREATE TABLE `trabajos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of trabajos
-- ----------------------------
INSERT INTO `trabajos` VALUES ('1', 'Obrero');
INSERT INTO `trabajos` VALUES ('2', 'Ingeniero');
INSERT INTO `trabajos` VALUES ('3', 'Heroe');
