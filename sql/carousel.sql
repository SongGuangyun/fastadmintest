/*
 Navicat Premium Data Transfer

 Source Server         : homestead
 Source Server Type    : MySQL
 Source Server Version : 80021
 Source Host           : 127.0.0.1:33060
 Source Schema         : homestead

 Target Server Type    : MySQL
 Target Server Version : 80021
 File Encoding         : 65001

 Date: 06/08/2021 11:40:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for carousel
-- ----------------------------
DROP TABLE IF EXISTS `carousel`;
CREATE TABLE `carousel`  (
  `id` int unsigned NOT NULL,
  `position_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '位置',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `img_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '图片路径',
  `link` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '跳转连接',
  `sort` int(0) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(0) NOT NULL DEFAULT 1 COMMENT '显示状态：1=启用，0=禁用',
  `display_time` int(0) NOT NULL DEFAULT 0 COMMENT '展示时间',
  `create_time` int(0) NOT NULL DEFAULT 0,
  `update_time` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '轮播图' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for carousel_position
-- ----------------------------
DROP TABLE IF EXISTS `carousel_position`;
CREATE TABLE `carousel_position`  (
  `id` int unsigned NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称，唯一标识',
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `type` tinyint(0) NOT NULL DEFAULT 1 COMMENT '形式：1：轮播图，2：banner',
  `size` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '尺寸（长*宽px）',
  `platform` tinyint(0) NOT NULL DEFAULT 0 COMMENT '展示平台：1：个人端，2：车商端，3：个人端小程序',
  `status` tinyint(0) NULL DEFAULT 1 COMMENT '显示状态：0：隐藏，1：展示',
  `create_time` int(0) NOT NULL DEFAULT 0,
  `update_time` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '轮播图位置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for carousel_stats
-- ----------------------------
DROP TABLE IF EXISTS `carousel_stats`;
CREATE TABLE `carousel_stats`  (
  `id` int unsigned NOT NULL,
  `carousel_id` int(0) NOT NULL DEFAULT 0,
  `pv` int(0) NOT NULL DEFAULT 0,
  `uv` int(0) NOT NULL DEFAULT 0,
  `create_time` int(0) NOT NULL DEFAULT 0,
  `update_time` int(0) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '轮播图统计' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods`  (
  `id` int unsigned NOT NULL,
  `cateogry_id` int(0) NOT NULL DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `price` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cateogry_id`(`cateogry_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for goods_categories
-- ----------------------------
DROP TABLE IF EXISTS `goods_categories`;
CREATE TABLE `goods_categories`  (
  `id` int unsigned NOT NULL COMMENT '商品分类id',
  `name` varchar(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '商品分类名称',
  `parent_id` int unsigned NOT NULL COMMENT '父id',
  `parent_id_path` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '家族图谱',
  `level` tinyint(0) NOT NULL DEFAULT 0 COMMENT '等级',
  `status` tinyint(0) NOT NULL DEFAULT 1 COMMENT '状态1正常2冻结',
  `created_at` int(0) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(0) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(0) NOT NULL DEFAULT 0 COMMENT '删除时间',
  `sort` int unsigned NOT NULL COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for goods_skus
-- ----------------------------
DROP TABLE IF EXISTS `goods_skus`;
CREATE TABLE `goods_skus`  (
  `id` int unsigned NOT NULL,
  `goods_id` int(0) NOT NULL DEFAULT 0,
  `combine_ids` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `combine_values` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `spec_items` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `stock` int(0) NOT NULL DEFAULT 0,
  `price` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `goods_id`(`goods_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 23 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of goods_skus
-- ----------------------------
INSERT INTO `goods_skus` VALUES (21, 23, '1-12_2-22_3-33', '颜色-黑色_内存-256G_ff-gg', '黑色;256G', 121, 19902.10, '2021-07-19 16:12:20', '2021-07-19 16:12:55');
INSERT INTO `goods_skus` VALUES (22, 23, '1-12_2-21', '颜色-黑色_内存-128G', '黑色;128G', 2, 45.10, '2021-07-19 16:12:20', '2021-07-19 16:12:59');

-- ----------------------------
-- Table structure for goods_spec_items
-- ----------------------------
DROP TABLE IF EXISTS `goods_spec_items`;
CREATE TABLE `goods_spec_items`  (
  `id` int unsigned NOT NULL,
  `spec_id` int(0) NOT NULL DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `spec_id`(`spec_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of goods_spec_items
-- ----------------------------
INSERT INTO `goods_spec_items` VALUES (1, 1, '白色', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (2, 1, '黑色', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (3, 1, '黄色', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (4, 2, '小', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (5, 2, '中', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (6, 2, '大', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (7, 2, '特大', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (8, 4, 'L', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (9, 4, 'XL', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (10, 4, 'XXL', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (11, 3, '棉花', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (12, 3, '纱布', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (13, 3, '麻布', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (14, 3, '蚕丝', NULL, NULL);
INSERT INTO `goods_spec_items` VALUES (15, 1, '红色', NULL, NULL);

-- ----------------------------
-- Table structure for goods_specs
-- ----------------------------
DROP TABLE IF EXISTS `goods_specs`;
CREATE TABLE `goods_specs`  (
  `id` int unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of goods_specs
-- ----------------------------
INSERT INTO `goods_specs` VALUES (1, '颜色', '2021-08-06 11:32:05', '2021-08-06 11:32:07');
INSERT INTO `goods_specs` VALUES (2, '尺寸', NULL, NULL);
INSERT INTO `goods_specs` VALUES (3, '材料', NULL, NULL);
INSERT INTO `goods_specs` VALUES (4, '尺码', NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
