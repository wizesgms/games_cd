/*
 Navicat Premium Data Transfer

 Source Server         : MySQL
 Source Server Type    : MySQL
 Source Server Version : 80031
 Source Host           : localhost:3307
 Source Schema         : pragmatic

 Target Server Type    : MySQL
 Target Server Version : 80031
 File Encoding         : 65001

 Date: 05/12/2022 22:43:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for agents
-- ----------------------------
DROP TABLE IF EXISTS `agents`;
CREATE TABLE `agents`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `balance` double(50, 2) NULL DEFAULT NULL,
  `aasEndpoint` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `siteEndpoint` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `totalDebit` double(50, 2) NOT NULL DEFAULT 0.00,
  `totalCredit` double(50, 2) NOT NULL DEFAULT 0.00,
  `realRtp` double(5, 2) NOT NULL DEFAULT 0.00,
  `targetRtp` double(5, 2) NOT NULL DEFAULT 80.00,
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of agents
-- ----------------------------
INSERT INTO `agents` VALUES (1, 'justslot', 999998368060.00, 'http://pragmatic.kro.kr:8940', 'http://justslot.kro.kr:2422/api', 3019580.00, 1650840.00, 54.67, 80.00, '2022-06-08 11:53:06', '2022-11-30 11:23:59');
INSERT INTO `agents` VALUES (2, 'lobby', 996300.00, 'http://pragmatic.kro.kr:8940', 'http://lobby.com/slotapi', 16000.00, 12300.00, 76.88, 80.00, '2022-05-23 05:45:17', '2022-12-05 11:46:44');

-- ----------------------------
-- Table structure for calls
-- ----------------------------
DROP TABLE IF EXISTS `calls`;
CREATE TABLE `calls`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `userCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `gameCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `agentCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `betMoney` bigint(0) NOT NULL,
  `calledMoney` bigint(0) NOT NULL DEFAULT 0 COMMENT '콜준 금액',
  `summedMoney` bigint(0) NOT NULL DEFAULT 0 COMMENT '콜로 배당한 금액',
  `type` int(0) NOT NULL COMMENT '1: 일반콜, 2: 구입콜',
  `callStatus` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '\"NOCALL\",일반,콜끝난상태 \r\n\"CALL_START\"  콜시작,\r\n \"CALLING\" 콜 실행중,',
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of calls
-- ----------------------------

-- ----------------------------
-- Table structure for events
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `agentCode` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `userCode` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `gameCode` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `txnID` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `txnType` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'debit_credit',
  `bet` double(50, 2) NULL DEFAULT NULL,
  `win` double(50, 2) NULL DEFAULT NULL,
  `type` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `checked` tinyint(1) NULL DEFAULT 0,
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of events
-- ----------------------------

-- ----------------------------
-- Table structure for games
-- ----------------------------
DROP TABLE IF EXISTS `games`;
CREATE TABLE `games`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `gameCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT ' 게임식별코드(예:vs20doghouse)',
  `banner` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `gameName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '게임명_한글',
  `enName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '게이명_영어',
  `memo` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `status` int(0) NOT NULL DEFAULT 1 COMMENT '1: 허용 0: 금지',
  `checked` tinyint(0) NOT NULL DEFAULT 0 COMMENT '0: 테스트, 1: 출시',
  `createdAt` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 302 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of games
-- ----------------------------
INSERT INTO `games` VALUES (1, 'vs243mwarrior', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243mwarrior.png', '몽키 워리어', 'Monkey Warrior', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (2, 'vs20doghouse', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20doghouse.png', '도그 하우스™', 'The Dog House', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (3, 'vs40pirate', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40pirate.png', '파이럿 골드', 'Pirate Gold', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (4, 'vs20rhino', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20rhino.png', '그레이트 라이노™', 'Great Rhino', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (5, 'vs25pandagold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25pandagold.png', '판다의 행운™', 'Panda Fortune', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (6, 'vs4096bufking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs4096bufking.png', '버팔로 킹', 'Buffalo King', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (7, 'vs25pyramid', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25pyramid.png', '피라미드 킹™', 'Pyramid King', NULL, 1, 1, '2021-02-02 01:33:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (8, 'vs5ultrab', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5ultrab.png', '울트라 번™', 'Ultra Burn', NULL, 1, 1, '2021-08-23 20:51:41', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (9, 'vs5ultra', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5ultra.png', '울트라 홀드 앤 스핀', 'Ultra Hold and Spin', NULL, 1, 1, '2021-08-23 20:52:49', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (10, 'vs25jokerking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25jokerking.png', '조커 킹', 'Joker King', NULL, 1, 1, '2021-08-23 20:51:41', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (11, 'vs10returndead', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10returndead.png', '죽은 자의 귀환', 'Return of the Dead', NULL, 1, 1, '2021-08-23 20:51:41', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (13, 'vs10madame', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10madame.png', '마담 데스티니', 'Madame Destiny', NULL, 1, 1, '2021-08-25 09:29:36', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (14, 'vs15diamond', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs15diamond.png', '다이아몬드 스트라이크™', 'Diamond Strike', NULL, 1, 1, '2021-08-25 09:30:38', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (15, 'vs25aztecking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25aztecking.png', '아즈텍 왕', 'Aztec King', NULL, 1, 1, '2021-08-23 20:51:41', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (16, 'vs25wildspells', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25wildspells.png', '와일드 스펠즈', 'Wild Spells', NULL, 1, 1, '2021-08-25 09:36:23', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (17, 'vs10bbbonanza', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bbbonanza.png', '빅 베이스 보난자', 'Big Bass Bonanza', NULL, 1, 1, '2021-09-03 22:01:25', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (18, 'vs10cowgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10cowgold.png', '카우보이 골드', 'Cowboys Gold', NULL, 1, 1, '2021-09-03 22:02:16', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (19, 'vs25tigerwar', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25tigerwar.png', '호랑이 전사™', 'The Tiger Warrior', NULL, 1, 1, '2021-09-03 22:04:14', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (20, 'vs25mustang', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25mustang.png', '머스탱 골드', 'Mustang Gold', NULL, 1, 1, '2021-09-03 22:06:12', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (21, 'vs25hotfiesta', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25hotfiesta.png', '핫 피에스타', 'Hotfiesta', NULL, 1, 1, '2021-09-14 16:32:50', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (22, 'vs243dancingpar', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243dancingpar.png', '댄스 파티 ', 'Dance Party', NULL, 1, 1, '2021-09-15 13:26:52', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (23, 'vs576treasures', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs576treasures.png', '와일드 와일드 리치즈', 'Wild Wild Riches', NULL, 1, 1, '2021-09-15 19:29:14', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (24, 'vs20hburnhs', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20hburnhs.png', '핫 투 번 홀드 앤 스핀', 'Hot to Burn Hold and Spin', NULL, 1, 1, '2021-09-15 19:47:26', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (25, 'vs20emptybank', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20emptybank.png', '은행을 털어라', 'Empty the Bank', NULL, 1, 1, '2021-09-20 11:13:32', '2022-08-31 08:29:50');
INSERT INTO `games` VALUES (26, 'vs20midas', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20midas.png', '마이다스의 손', 'The Hand of Midas', NULL, 1, 1, '2021-09-27 19:01:13', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (27, 'vs20olympgate', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20olympgate.png', '올림푸스의 문', 'Gates of Olympus', NULL, 1, 1, '2021-08-24 14:52:37', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (28, 'vswayslight', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayslight.png', '럭키 라이트닝', 'Lucky Lightning', NULL, 1, 1, '2021-08-24 14:55:30', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (29, 'vs20vegasmagic', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20vegasmagic.png', '베가스 매직™', 'Vegas Magic', NULL, 1, 1, '2021-09-03 22:08:21', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (30, 'vs20fruitparty', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20fruitparty.png', '후르츠 파티™', 'Fruit Party', NULL, 1, 1, '2021-10-09 20:32:25', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (31, 'vs20fparty2', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20fparty2.png', '후르츠 파티 2', 'Fruit Party 2', NULL, 1, 1, '2021-10-21 23:30:32', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (32, 'vswaysdogs', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysdogs.png', '더 도그 하우스 메가웨이즈™', 'The Dog House Megaways', NULL, 1, 1, '2021-10-21 23:31:26', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (33, 'vs50juicyfr', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50juicyfr.png', '쥬시 후루츠', 'Juicy Fruits', NULL, 1, 1, '2021-10-21 23:31:26', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (34, 'vs25pandatemple', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25pandatemple.png', '판다의 행운 2', 'Panda Fortune 2', NULL, 1, 1, '2021-10-21 23:31:26', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (35, 'vswaysbufking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysbufking.png', '버팔로 킹 메가웨이즈', 'Buffalo King Megaways', NULL, 1, 1, '2021-10-21 23:31:26', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (36, 'vs40wildwest', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40wildwest.png', '와일드 웨스트 골드', 'Wild West Gold', NULL, 1, 1, '2021-10-29 10:47:00', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (37, 'vs20chickdrop', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20chickdrop.png', '치킨 드롭', 'Chicken Drop', NULL, 1, 1, '2021-10-29 10:53:25', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (38, 'vs40spartaking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40spartaking.png', '스파르타의 왕', 'Spartan King', NULL, 1, 1, '2021-10-29 10:53:42', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (39, 'vswaysrhino', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysrhino.png', '그레이트 라이노 메가웨이즈™', 'Great Rhino Megaways', NULL, 1, 1, '2021-10-29 10:54:04', '2022-07-29 17:20:41');
INSERT INTO `games` VALUES (40, 'vs20sbxmas', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20sbxmas.png', '스위트 보난자 크리스마스', 'Sweet Bonanza Xmas', NULL, 1, 1, '2021-10-29 10:54:30', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (41, 'vs10fruity2', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10fruity2.png', '과즙이 듬뿍™', 'Extra Juicy', NULL, 1, 1, '2021-11-15 18:01:02', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (42, 'vs10egypt', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10egypt.png', '에인션트 이집트™', 'Ancient Egypt', NULL, 1, 1, '2021-11-15 20:03:29', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (43, 'vs5drhs', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5drhs.png', '드래곤 핫 홀드 앤 스핀', 'Dragon Hot Hold and Spin', NULL, 1, 1, '2021-11-30 14:50:02', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (44, 'vs12bbb', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs12bbb.png', '더 큰 베스 보난자', 'Bigger Bass Bonanza', NULL, 1, 1, '2021-11-30 14:50:38', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (45, 'vs20tweethouse', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20tweethouse.png', '트위티 하우스', 'The Tweety House', NULL, 1, 1, '2021-11-30 14:51:10', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (46, 'vswayslions', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayslions.png', '5 라이언즈 메가웨이즈', '5 Lions Megaways', NULL, 1, 1, '2021-11-30 14:52:00', '2022-08-26 12:57:58');
INSERT INTO `games` VALUES (47, 'vswayssamurai', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayssamurai.png', '사무라이 메가웨이즈의 등장', 'Rise of Samurai Megaways', NULL, 1, 1, '2021-11-30 14:52:32', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (48, 'vs50pixie', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50pixie.png', '픽시 윙', 'Pixie Wings', NULL, 1, 1, '2021-12-05 14:53:56', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (49, 'vs10floatdrg', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10floatdrg.png', '플로팅 드래곤 홀 앤 스핀', 'Floating Dragon', NULL, 1, 1, '2021-12-05 18:03:08', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (50, 'vs20fruitsw', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20fruitsw.png', '스위트 보난자', 'Sweet Bonanza', NULL, 1, 1, '2021-12-06 12:08:41', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (51, 'vs20rhinoluxe', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20rhinoluxe.png', '그레이트 라이노 디럭스™', 'Great Rhino Deluxe', NULL, 1, 1, '2021-12-06 12:08:53', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (52, 'vs432congocash', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs432congocash.png', '콩고 캐시', 'Congo Cash', NULL, 1, 1, '2021-12-06 12:09:04', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (53, 'vswaysmadame', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysmadame.png', '마담 데스티니 메가웨이즈', 'Madame Destiny Megaways', NULL, 1, 1, '2021-12-06 12:09:13', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (54, 'vs1024temuj', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1024temuj.png', '테무진의 보물', 'Temujin Treasures', NULL, 1, 1, '2021-12-06 12:09:22', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (55, 'vs40pirgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40pirgold.png', '파이럿 골드 디럭스', 'Pirate Gold Deluxe', NULL, 1, 1, '2021-12-06 12:09:31', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (56, 'vs25mmouse', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25mmouse.png', '머니 마우스', 'Money Mouse', NULL, 1, 1, '2021-12-09 10:48:05', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (57, 'vs10threestar', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10threestar.png', '쓰리 스타 포춘', 'Three Star Fortune', NULL, 1, 1, '2021-12-09 10:47:54', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (58, 'vs1ball', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1ball.png', '럭키 드래곤볼', 'Lucky Dragon Ball', NULL, 1, 1, '2021-12-09 10:47:39', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (59, 'vs243lionsgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243lionsgold.png', '5라이온스 골드', '5 Lions', NULL, 1, 1, '2021-12-09 10:48:19', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (60, 'vs10egyptcls', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10egyptcls.png', '에인션트 이집트 클래식', 'Ancient Egypt Classic', NULL, 1, 1, '2021-12-09 10:48:38', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (61, 'vs25davinci', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25davinci.png', '다빈치의 보물', 'Da Vinci Treasure', NULL, 1, 1, '2021-12-09 10:49:03', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (62, 'vs7776secrets', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs7776secrets.png', '존 헌터와 아즈텍의 보물', 'Aztec Treasure', NULL, 1, 1, '2021-12-09 10:49:18', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (63, 'vs25wolfgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25wolfgold.png', '울프 골드', 'Wolf Gold', NULL, 1, 1, '2021-12-09 10:49:33', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (64, 'vs50safariking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50safariking.png', '사파리 킹™', 'Safari King', NULL, 1, 1, '2021-12-09 10:49:45', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (65, 'vs25peking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25peking.png', '북경의 행운', 'Peking Luck', NULL, 1, 1, '2021-12-09 10:50:01', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (66, 'vs25asgard', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25asgard.png', '아스가르드', 'Asgard', NULL, 1, 1, '2021-12-09 10:50:16', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (67, 'vs25vegas', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25vegas.png', '베가스의 밤™', 'Vegas Nights', NULL, 1, 1, '2021-12-09 10:50:32', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (68, 'vs75empress', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs75empress.png', '골든 뷰티', 'Golden Beauty', NULL, 1, 1, '2021-12-09 10:47:10', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (69, 'vs25scarabqueen', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25scarabqueen.png', '존 헌터와 스카라브 퀸의 무덤', 'John Hunter and the Tomb of the Scarab Queen', NULL, 1, 1, '2021-12-09 10:41:23', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (70, 'vs20starlight', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20starlight.png', '별빛공주', 'Starlight Princess', NULL, 1, 1, '2021-12-12 18:38:10', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (71, 'vs10bookoftut', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bookoftut.png', '존 헌터와 투탕카멘의 저서', 'John Hunter and the Book of Tut', NULL, 1, 1, '2021-12-12 18:37:13', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (72, 'vs9piggybank', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs9piggybank.png', '피기 뱅크 빌즈', 'Piggy Bank Bills', NULL, 1, 1, '2021-12-12 18:36:50', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (73, 'vs5drmystery', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5drmystery.png', '드래곤 왕국 불의 눈', 'Dragon Kingdom Mystery', NULL, 1, 1, '2021-12-22 22:08:29', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (74, 'vs20eightdragons', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20eightdragons.png', '에잇 드래곤', 'Eight Dragons', NULL, 1, 1, '2021-12-22 22:08:53', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (75, 'vs1024lionsd', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1024lionsd.png', '라이온스 댄스', '5 Lions Dance', NULL, 1, 1, '2021-12-22 22:09:21', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (76, 'vs25rio', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25rio.png', '하트 오브 리오', 'Heart of Rio', NULL, 1, 1, '2021-12-22 22:10:18', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (77, 'vs10nudgeit', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10nudgeit.png', '라이즈 오브 기자 파워넛지', 'Rise of Giza PowerNudge', NULL, 1, 1, '2021-12-12 18:37:29', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (78, 'vs10bxmasbnza', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bxmasbnza.png', '크리스마스 빅 베이스 보난자', 'Christmas Big Bass Bonanza', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (79, 'vs20santawonder', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20santawonder.png', '산타의 원더랜드', 'Santa\'s Wonderland', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:42');
INSERT INTO `games` VALUES (80, 'vs20terrorv', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20terrorv.png', '캐시 엘리베이터', 'Cash Elevator', NULL, 1, 1, '2022-01-27 11:49:12', '2022-08-31 08:29:50');
INSERT INTO `games` VALUES (81, 'vs10bblpop', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bblpop.png', '버블팝', 'Bubble Pop', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (82, 'vs25btygold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25btygold.png', '바운티 골드', 'Bounty Gold', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (83, 'vs88hockattack', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs88hockattack.png', '하키 공격', 'Hockey Attack™', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (84, 'vswaysbbb', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysbbb.png', '빅 베스 보난자 메가웨이', 'Big Bass Bonanza Megaways™', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (85, 'vs10bookfallen', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bookfallen.png', '타락의 책', 'Book of the Fallen', NULL, 0, 0, '2022-01-27 11:49:12', '2022-11-10 09:34:40');
INSERT INTO `games` VALUES (86, 'vs40bigjuan', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40bigjuan.png', '빅 후안', 'Big Juan', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (87, 'vs20bermuda', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20bermuda.png', '존 헌터와 버뮤다 재물 탐방', 'John Hunter and the Quest for Bermuda Riches', NULL, 1, 1, '2022-01-27 11:49:12', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (88, 'vs10starpirate', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10starpirate.png', '스타 파이러츠 코드', 'Star Pirates Code', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (89, 'vswayswest', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayswest.png', '미스틱 치프', 'Mystic Chief', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (90, 'vs20daydead', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20daydead.png', '망자의 날', 'Day of Dead', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (91, 'vs20candvil', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20candvil.png', '캔디 빌리지', 'Candy Village', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (92, 'vs20wildboost', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20wildboost.png', '와일드 부스터', 'Wild Booster', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (93, 'vswayshammthor', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayshammthor.png', '토르의 힘 메가웨이즈', 'Power of Thor Megaways', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (94, 'vs243lions', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243lions.png', '5라이온스', '5 Lions', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (95, 'vs5super7', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5super7.png', '슈퍼 7', 'Super 7s', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (96, 'vs1masterjoker', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1masterjoker.png', '마스터 조커', 'Master Joker', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (97, 'vs20kraken', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20kraken.png', '릴리스 더 크라켄', 'Release the Kraken', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (98, 'vs10firestrike', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10firestrike.png', '파이어 스트라이크', 'Fire Strike', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (99, 'vs243fortune', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243fortune.png', '재물신의 금', 'Caishen\'s Gold', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (100, 'vs4096mystery', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs4096mystery.png', '미스테리어스', 'Mysterious', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (101, 'vs20aladdinsorc', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20aladdinsorc.png', '알라딘 앤 더 소서러', 'Aladdin and the Sorcerrer', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (102, 'vs243fortseren', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243fortseren.png', '그리스 신들', 'Greek Gods', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (103, 'vs25chilli', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25chilli.png', '칠리 히트™', 'Chilli Heat', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (104, 'vs8magicjourn', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs8magicjourn.png', '매직 져니', 'Magic Journey', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (105, 'vs25pantherqueen', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25pantherqueen.png', '팬더 퀸', 'Panther Queen', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (107, 'vs20leprexmas', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20leprexmas.png', '머스탱 골드', 'Leprechaun Carol', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (109, 'vs7pigs', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs7pigs.png', '7 피기', '7 Piggies', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (110, 'vs243caishien', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243caishien.png', '카이셴의 캐쉬™', 'Caishen\'s Cash', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (111, 'vs5joker', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5joker.png', '조커스 쥬얼리', 'Joker\'s Jewels', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (112, 'vs25gladiator', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25gladiator.png', '상남자 검투사™', 'Wild Gladiator', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (113, 'vs25goldpig', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25goldpig.png', '골든 피그', 'Golden Pig', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (114, 'vs25goldrush', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25goldrush.png', '골드 러쉬™', 'Gold Rush', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (115, 'vs25dragonkingdom', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25dragonkingdom.png', '드래곤 킹덤', 'Dragon Kingdom', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:43');
INSERT INTO `games` VALUES (116, 'vs25kingdoms', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25kingdoms.png', '오촉위 삼국시대', '3 Kingdoms - Battle of Red Cliffs', NULL, 1, 1, '2022-01-27 11:49:13', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (117, 'vs1dragon8', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1dragon8.png', '888 드래곤', '888 Dragons', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (118, 'vs5aztecgems', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5aztecgems.png', '아즈텍 젬™', 'Aztec Gems', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (119, 'vs20hercpeg', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20hercpeg.png', '헤라클레스와 페가수스', 'Hercules and Pegasus', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (120, 'vs7fire88', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs7fire88.png', '파이어 88™', 'Fire 88', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (121, 'vs20honey', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20honey.png', '허니 허니 허니', 'Honey Honey Honey', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (123, 'vs25safari', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25safari.png', '핫 사파리', 'Hot Safari', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (124, 'vs25journey', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25journey.png', '서유기', 'Journey to the West', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (125, 'vs20chicken', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20chicken.png', '더 그레이트 치킨 이스케이프', 'The Great Chicken Escape', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (126, 'vs1fortunetree', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1fortunetree.png', '트리 오브 리치스™', 'Tree of Riches', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (129, 'vs20wildpix', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20wildpix.png', '신나는 요정들', 'Wild Pixies', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (130, 'vs15fairytale', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs15fairytale.png', '페어리테일 포춘™', 'Fairytale Fortune', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (131, 'vs20santa', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20santa.png', '산타', 'Santa', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (132, 'vs10vampwolf', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10vampwolf.png', '뱀파이어 vs 울프', 'Vampires vs Wolves', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (133, 'vs50aladdin', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50aladdin.png', '지니의 소원 3가지', '3 Genie Wishes', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (135, 'vs50hercules', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50hercules.png', '헤라클레스', 'Hercules Son of Zeus', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (136, 'vs7776aztec', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs7776aztec.png', '아즈텍 보난자', 'Aztec Bonanza', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (137, 'vs5trdragons', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5trdragons.png', '트리플 드래곤즈', 'Triple Dragons', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (138, 'vs40madwheel', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40madwheel.png', '더 와일드 머신', 'The Wild Machine', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (139, 'vs25newyear', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25newyear.png', '럭키 뉴 이어™', 'Lucky New Year', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (140, 'vs40frrainbow', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40frrainbow.png', '후르츠 레인보우', 'Fruit Rainbow', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (141, 'vs50kingkong', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50kingkong.png', '마이티 콩', 'Mighty Kong', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (143, 'vs20godiva', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20godiva.png', '레이디 고디바', 'Lady Godiva', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (144, 'vs9madmonkey', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs9madmonkey.png', '원숭이의 광란™', 'Monkey Madness', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (145, 'vs1tigers', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1tigers.png', '트리플 타이거스™', 'Triple Tigers', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (146, 'vs9chen', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs9chen.png', '재물신의 포춘', 'Master Chen\'s Fortune', NULL, 1, 1, '2022-01-27 11:49:14', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (147, 'vs5hotburn', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5hotburn.png', '핫 투 번', 'Hot to burn', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (148, 'vs25dwarves_new', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25dwarves_new.png', '드워프 골드 디럭스', 'Dwarven Gold Deluxe', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:44');
INSERT INTO `games` VALUES (149, 'vs1024butterfly', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1024butterfly.png', '비취 나비', 'Jade Butterfly', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:40');
INSERT INTO `games` VALUES (151, 'vs25sea', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25sea.png', '위대한 산호초', 'Great Reef', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:40');
INSERT INTO `games` VALUES (153, 'vs20leprechaun', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20leprechaun.png', '레프리콘의 노래', 'Leprechaun Song', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (154, 'vs7monkeys', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs7monkeys.png', '7 멍키', '7 Monkeys', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (155, 'vs50chinesecharms', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50chinesecharms.png', '럭키 드래곤즈', 'Lucky Dragons', NULL, 1, 0, '2022-01-27 11:49:15', '2022-11-10 09:35:06');
INSERT INTO `games` VALUES (156, 'vs18mashang', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs18mashang.png', '대박 기원™', 'Treasure Horse', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (157, 'vs5spjoker', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5spjoker.png', '슈퍼 조커', 'Super Joker', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (158, 'vs20egypttrs', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20egypttrs.png', '파라오의 비밀™', 'Egyptian Fortunes', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (160, 'vs25queenofgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25queenofgold.png', '퀸 오브 골드', 'Queen of Gold', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:19');
INSERT INTO `games` VALUES (161, 'vs9hotroll', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs9hotroll.png', '핫 칠리', 'Hot Chilli', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (162, 'vs4096jurassic', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs4096jurassic.png', '쥬라기 자이언츠', 'Jurassic Giants', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (163, 'vs3train', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs3train.png', '골드 트레인', 'Gold Train', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (164, 'vs40beowulf', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40beowulf.png', '베오울프', 'Beowulf', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (165, 'vs1024atlantis', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1024atlantis.png', '퀸 오브 아틀란티스', 'Queen of Atlantis', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (166, 'vs20bl', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20bl.png', '바쁜 벌들', 'Busy Bees', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:16');
INSERT INTO `games` VALUES (167, 'vs25champ', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25champ.png', '더 챔피온스', 'The Champions', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:16');
INSERT INTO `games` VALUES (168, 'vs13g', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs13g.png', '데블스 13', 'Devil\'s 13', NULL, 0, 0, '2022-01-27 11:49:15', '2022-11-10 09:34:16');
INSERT INTO `games` VALUES (173, 'vs243crystalcave', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243crystalcave.png', '매직 크리스탈', 'Magic Crystals', NULL, 1, 1, '2022-01-27 11:49:15', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (176, 'vs5trjokers', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5trjokers.png', '트리블 조커스™', 'Triple Jokers', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (179, 'vs1money', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1money.png', '머니 머니 머니™', 'Money Money Money', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (180, 'vs75bronco', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs75bronco.png', '브롱코 스피릿', 'Bronco Spirit', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (181, 'vs1600drago', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1600drago.png', '드라고 쥬얼스 오브 포춘', 'Drago - Jewels of Fortune', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (182, 'vs1fufufu', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1fufufu.png', '후 후 후', 'Fu Fu Fu', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (183, 'vs40streetracer', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40streetracer.png', '스트리트 레이서', 'Street Racer', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (184, 'vs9aztecgemsdx', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs9aztecgemsdx.png', '아즈텍 보석 디럭스™', 'Aztec Gems Deluxe', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (185, 'vs20gorilla', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20gorilla.png', '정글 고릴라', 'Jungle Gorilla', NULL, 0, 0, '2022-01-27 11:49:16', '2022-11-15 17:39:25');
INSERT INTO `games` VALUES (186, 'vswayswerewolf', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayswerewolf.png', '늑대인간의 저주Megaways', 'Curse of the Werewolf Megaways', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (187, 'vswayshive', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayshive.png', '스타 바운티', 'Star Bounty', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (188, 'vs25samurai', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25samurai.png', '라이즈 오브 사무라이', 'Rise of Samurai', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (189, 'vs25walker', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25walker.png', '와일드 워커', 'Wild Walker', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (190, 'vs20goldfever', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20goldfever.png', '보석 보난자', 'Gems Bonanza', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (191, 'vs25bkofkngdm', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25bkofkngdm.png', '왕국의 저서', 'Book of Kingdoms', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:45');
INSERT INTO `games` VALUES (193, 'vs10goldfish', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10goldfish.png', '낚시 릴', 'Fishin Reels', NULL, 1, 1, '2022-01-27 11:49:16', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (195, 'vs1024dtiger', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs1024dtiger.png', '그레이건 타이거', 'The Dragon Tiger', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (197, 'vs20eking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20eking.png', '에메랄드 킹', 'Emerald King', NULL, 1, 1, '2022-01-27 11:49:17', '2022-10-02 10:04:43');
INSERT INTO `games` VALUES (198, 'vs20xmascarol', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20xmascarol.png', '크리스마스 캐롤 Megaways', 'Christmas Carol Megaways', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (199, 'vs10mayangods', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10mayangods.png', '존 헌터와 마야의 신', 'John Hunter and the Mayan Gods', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (200, 'vs20bonzgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20bonzgold.png', '보난자 골드', 'Bonanza Gold', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (201, 'vs40voodoo', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40voodoo.png', '부두 주술', 'Voodoo Magic', NULL, 0, 0, '2022-01-27 11:49:17', '2022-11-10 09:33:59');
INSERT INTO `games` VALUES (202, 'vs25gldox', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25gldox.png', '골든 황소', 'Golden Ox', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (203, 'vs10wildtut', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10wildtut.png', '신비의 이집트', 'Mysterious Egypt', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (204, 'vs20ekingrr', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20ekingrr.png', '에메랄드 킹 레인보우 로드', 'Emerald King Rainbow Road', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (205, 'vs10eyestorm', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10eyestorm.png', '폭풍의 눈', 'Eye of the Storm', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (212, 'vs117649starz', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs117649starz.png', '스타즈 메가웨이즈™', 'Starz Megaways', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (213, 'vs10amm', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10amm.png', '어메이징 머니 머신', 'The Amazing Money Machine', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (214, 'vs20magicpot', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20magicpot.png', '더 매직 콜드런 - 인챈티드 브루', 'The Magic Cauldron - Enchanted Brew', NULL, 0, 0, '2022-01-27 11:49:17', '2022-11-10 09:33:57');
INSERT INTO `games` VALUES (215, 'vswaysyumyum', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysyumyum.png', '냠냠 파워웨이즈', 'Yum Yum Powerways', NULL, 1, 1, '2022-01-27 11:49:17', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (216, 'vswayselements', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayselements.png', '엘리멘탈 젬 메가웨이즈', 'Elemental Gems Megaways', NULL, 0, 0, '2022-03-04 22:07:45', '2022-11-10 09:33:53');
INSERT INTO `games` VALUES (217, 'vswayschilheat', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayschilheat.png', '칠리 히트 메가웨이즈', 'Chilli Heat Megaways', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (218, 'vs10luckcharm', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10luckcharm.png', '럭키 그레이스 앤 참', 'Lucky Grace And Charm', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (219, 'vswaysaztecking', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysaztecking.png', '아즈텍 왕 메가웨이즈', 'Aztec King Megaways', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (220, 'vs20phoenixf', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20phoenixf.png', '피닉스 포지', 'Phoenix Forge', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (221, 'vs576hokkwolf', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs576hokkwolf.png', '홋카이도 늑대', 'Hokkaido Wolf', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (222, 'vs20trsbox', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20trsbox.png', '트래저 와일드', 'Treasure Wild', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (223, 'vs243chargebull', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243chargebull.png', '레이징 불', 'Raging Bull', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (224, 'vswaysbankbonz', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysbankbonz.png', '캐시 보난자', 'Cash Bonanza', NULL, 0, 0, '2022-01-27 11:49:18', '2022-11-10 09:33:50');
INSERT INTO `games` VALUES (225, 'vs20pbonanza', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20pbonanza.png', '피라미드 보난자', 'Pyramid Bonanza', NULL, 1, 1, '2022-01-27 11:49:18', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (226, 'vs243empcaishen', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243empcaishen.png', '제물신 황제™', 'Emperor Caishen', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (227, 'vs25tigeryear', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25tigeryear.png', '행운의 새해 타이거 트레져스™', 'New Year Tiger Treasures ™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (228, 'vs20amuleteg', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20amuleteg.png', '기자의 포천™', 'Fortune of Giza™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (229, 'vs10runes', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10runes.png', '발할라의 문™', 'Gates of Valhalla™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (230, 'vs25goldparty', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25goldparty.png', '골드 파티™', 'Gold Party®', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (231, 'vswaysxjuicy', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswaysxjuicy.png', '엑스트라 쥬시 메가웨이즈™', 'Extra Juicy Megaways™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (232, 'vs40wanderw', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40wanderw.png', '와일드 뎁쓰™', 'Wild Depths™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-09-28 08:37:05');
INSERT INTO `games` VALUES (233, 'vs4096magician', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs4096magician.png', '마법사의 비밀™', 'Magician\'s Secrets™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (234, 'vs20smugcove', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20smugcove.png', '스머글러 코브™', 'Smugglers Cove™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (235, 'vswayscryscav', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayscryscav.png', '크리스탈 캐번 메가웨이즈™', 'Crystal Caverns Megaways™', NULL, 1, 1, '2022-03-04 22:07:43', '2022-07-29 17:20:46');
INSERT INTO `games` VALUES (236, 'vs20superx', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20superx.png', '슈퍼X™', 'Super X™', NULL, 0, 0, '2022-03-04 22:07:43', '2022-11-10 09:33:48');
INSERT INTO `games` VALUES (237, 'vs20rockvegas', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20rockvegas.png', '록 베가스', 'Rock Vegas Mega Hold & Spin', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (238, 'vs25copsrobbers', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25copsrobbers.png', '케시 패트롤', 'Cash Patrol', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (239, 'vs20colcashzone', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20colcashzone.png', '클로샬 캐시 존', 'Colossal Cash Zone', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (240, 'vs20ultim5', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20ultim5.png', '더 얼티메이트 5', 'The Ultimate 5', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (241, 'vs20bchprty', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20bchprty.png', '와일드 비치파티', 'Wild Beach Party', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (242, 'vs10bookazteck', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10bookazteck.png', '북 오브 아즈텍 킹', 'Book of Aztec King', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (243, 'vs10snakeladd', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10snakeladd.png', '스넥스 앤 리더스 메가다이스', 'Snakes and Ladders Megadice', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (244, 'vs50mightra', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50mightra.png', '마이트 오 브라', 'Might of Ra', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (245, 'vs25bullfiesta', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs25bullfiesta.png', '황소 피에스타', 'Bull Fiesta', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (246, 'vs20rainbowg', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20rainbowg.png', '레인보우 골드', 'Rainbow Gold', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (247, 'vs10tictac', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10tictac.png', '틱택 테이크', 'Tic Tac Take', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (248, 'vs243discolady', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243discolady.png', '디스코 레이디', 'Disco Lady', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (249, 'vs243queenie', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs243queenie.png', '퀸 니어', 'Queenie', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (250, 'vs20farmfest', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20farmfest.png', '반 페스티발', 'Barn Festival', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (251, 'vs10chkchase', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10chkchase.png', '치킨 체스', 'Chicken Chase', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (252, 'vswayswildwest', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayswildwest.png', '와일드 웨스트 골드 메가웨이즈', 'Wild West Gold Megaways', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (253, 'vs20mustanggld2', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20mustanggld2.png', '클로버 골드', 'Clover Gold', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (254, 'vs20drtgold', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20drtgold.png', '드릴 뎃 골드', 'Drill That Gold', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (255, 'vs10spiritadv', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10spiritadv.png', '스피리트 오브 어드벤투어', 'Spirit of Adventure', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (256, 'vs10firestrike2', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10firestrike2.png', '파이어 스트라이크스2', 'Fire Strike 2', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:41');
INSERT INTO `games` VALUES (257, 'vs40cleoeye', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs40cleoeye.png', '클레오파트라의눈', 'Eye of Cleopatra', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:42');
INSERT INTO `games` VALUES (258, 'vs20gobnudge', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20gobnudge.png', '고블린 해이츠 파워넛지', 'Goblin Heist Powernudge', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:42');
INSERT INTO `games` VALUES (259, 'vs20stickysymbol', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20stickysymbol.png', '더 그레이트 스티거', 'The Great Stick-up', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:42');
INSERT INTO `games` VALUES (260, 'vswayszombcarn', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vswayszombcarn.png', '좀비 카니발', 'Zombie Carnival', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:42');
INSERT INTO `games` VALUES (261, 'vs50northgard', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs50northgard.png', '노스 가디언스', 'North Guardians', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (262, 'vs20sugarrush', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20sugarrush.png', 'Sugar Rush', 'Sugar Rush', NULL, 0, 0, '2022-08-01 08:59:55', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (263, 'vs20cleocatra', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs20cleocatra.png', 'Cleocatra', 'Cleocatra', NULL, 0, 0, '2022-08-01 08:59:56', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (264, 'vs5littlegem', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs5littlegem.png', 'Little Gem Hold and Spin', 'Little Gem Hold and Spin', NULL, 0, 0, '2022-08-01 08:59:56', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (265, 'vs10egrich', 'https://solawins-sg0.pragmaticplay.net/game_pic/rec/325/vs10egrich.png', 'Queen of Gods', 'Queen of Gods', NULL, 0, 0, '2022-08-01 08:59:56', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (266, 'vs243koipond', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs243koipond.png', '코이 폰드', 'Koi Pond', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (267, 'vs40samurai3', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs40samurai3.png', '라이즈 오브 사무라이3', 'Rise of Samurai 3', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (268, 'vs40cosmiccash', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs40cosmiccash.png', '코스미 캐시', 'Cosmic Cash', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (269, 'vs25bomb', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs25bomb.png', '밤브 보나자', 'Bomb Bonanza', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (270, 'vs1024mahjpanda', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs1024mahjpanda.png', '메종 판다', 'Mahjong Panda', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (272, 'vs10coffee', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs10coffee.png', '커피 와일드', 'Coffee Wild', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (273, 'vs100sh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs100sh.png', '샤이닝 핫 100', 'Shining Hot 100', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (274, 'vs20sh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20sh.png', '샤이닝 핫 20', 'Shining Hot 20', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (275, 'vs40sh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs40sh.png', '샤이닝 핫 40', 'Shining Hot 40', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (276, 'vs5sh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs5sh.png', '샤이닝 핫 5', 'Shining Hot 5', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (277, 'vswaysjkrdrop', 'https://api-sg57.ppgames.net/game_pic/rec/325/vswaysjkrdrop.png', '트로피카 티키', 'Tropical Tiki', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (278, 'vs243ckemp', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs243ckemp.png', '치키 엠퍼러', 'Cheeky Emperor', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (279, 'vs40hotburnx', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs40hotburnx.png', '핫 투 번 익스트림', 'Hot To Burn Extreme', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (280, 'vs1024gmayhem', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs1024gmayhem.png', '고릴라 메이헴', 'Gorilla Mayhem', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (281, 'vs20octobeer', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20octobeer.png', '오프비어 포춘즈', 'Octobeer Fortunes', NULL, 0, 0, '2022-09-18 17:56:24', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (282, 'vs10txbigbass', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs10txbigbass.png', '빅 베이스 스플래쉬', 'Big Bass Splash', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (283, 'vs100firehot', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs100firehot.png', '파이어 핫 100', 'Fire Hot 100', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (284, 'vs20fh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20fh.png', '파이어 핫 20', 'Fire Hot 20', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (285, 'vs40firehot', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs40firehot.png', '파이어 핫 40', 'Fire Hot 40', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (286, 'vs5firehot', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs5firehot.png', '파이어 핫 5', 'Fire Hot 5', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (287, 'vs20wolfie', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20wolfie.png', '그리디 율프', 'Greedy Wolf', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (288, 'vs20underground', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20underground.png', '다운 더 레일즈', 'Down the Rails', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (289, 'vs10mmm', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs10mmm.png', '매직 머니 메이즈', 'Magic Money Maze', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (290, 'vswaysfltdrg', 'https://api-sg57.ppgames.net/game_pic/rec/325/vswaysfltdrg.png', '플로팅 드레곤 홀드 앤 메가웨이즈 ', 'Floating Dragon Hold & Spin Megaways', NULL, 0, 0, '2022-10-21 10:53:17', '2022-11-10 09:33:32');
INSERT INTO `games` VALUES (291, 'vs20wildman', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20wildman.png', '와일드맨 슈퍼 보나자 ', 'Wildman Super Bonanza', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (292, 'vs20trswild2', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20trswild2.png', '블랙 불', 'Black Bull', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (293, 'vswaysstrwild', 'https://api-sg57.ppgames.net/game_pic/rec/325/vswaysstrwild.png', '캔디 스타', 'Candy Stars', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (294, 'vs10crownfire', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs10crownfire.png', '크라운 오브 파이어', 'Crown of Fire', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (295, 'vs20muertos', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20muertos.png', '무에르토스 멀티플라이어 메가웨이즈', 'Muertos Multiplier Megaways', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (296, 'vswayslofhero', 'https://api-sg57.ppgames.net/game_pic/rec/325/vswayslofhero.png', '레전드 오브 히어로즈  메가웨이즈', 'Legend of Heroes', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (297, 'vs5strh', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs5strh.png', '스트리킹 핫5', 'Striking Hot 5', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (298, 'vs10snakeeyes', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs10snakeeyes.png', '스넥스 앤 래더스-스네이크 아이즈', 'Snakes & Ladders - Snake Eyes', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (299, 'vswaysbook', 'https://api-sg57.ppgames.net/game_pic/rec/325/vswaysbook.png', '북 오브 골든 쌘즈', 'Book of Golden Sands', NULL, 0, 0, '2022-10-21 10:53:18', '2022-11-10 09:33:33');
INSERT INTO `games` VALUES (300, 'vs20mparty', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20mparty.png', '와일드 홉 앤드 드롭', 'Wild Hop and Drop', NULL, 0, 0, '2022-11-10 09:16:46', '2022-11-10 09:17:15');
INSERT INTO `games` VALUES (301, 'vs20swordofares', 'https://api-sg57.ppgames.net/game_pic/rec/325/vs20swordofares.png', '소드 오브 아레스', 'Sword of Ares', NULL, 0, 0, '2022-11-10 09:16:46', '2022-11-10 09:17:15');

-- ----------------------------
-- Table structure for histories
-- ----------------------------
DROP TABLE IF EXISTS `histories`;
CREATE TABLE `histories`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `userCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `gameCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `agentCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  `roundID` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `bet` double NOT NULL DEFAULT 0,
  `win` double NOT NULL DEFAULT 0,
  `balance` double NOT NULL DEFAULT 0,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `createdAt` datetime(0) NOT NULL,
  `updatedAt` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of histories
-- ----------------------------

-- ----------------------------
-- Table structure for players
-- ----------------------------
DROP TABLE IF EXISTS `players`;
CREATE TABLE `players`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `token` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `userCode` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `gameCode` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `agentCode` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `txnID` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `connected` tinyint(0) NOT NULL DEFAULT 0,
  `gameMode` tinyint(0) NULL DEFAULT 0 COMMENT '0: 그시그시생성방식, 1: 그라프방식',
  `patRequested` int(0) NOT NULL DEFAULT 0,
  `curIndex` bigint(0) NOT NULL DEFAULT 0,
  `lastJackpotIndex` bigint(0) NOT NULL DEFAULT 0,
  `nextJackpot` int(0) NOT NULL DEFAULT 100,
  `totalDebit` double(50, 2) NOT NULL DEFAULT 0.00,
  `totalCredit` double(50, 2) NOT NULL DEFAULT 0.00,
  `realRtp` double(10, 2) NOT NULL DEFAULT 0.00,
  `callHistId` int(0) NOT NULL DEFAULT -1,
  `settings` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `totalBet` double(20, 2) NOT NULL DEFAULT 0.00 COMMENT '마지막bet per line값',
  `virtualBet` double(20, 2) NOT NULL DEFAULT 0.00 COMMENT '//프리스핀시 그전 베이스 토탈벳(콜생성위해)',
  `callStatus` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `jackpotCome` int(0) NULL DEFAULT 100,
  `baseWinCome` int(0) NULL DEFAULT 7,
  `highBaseCome` int(0) NULL DEFAULT 5,
  `jackpotLimit` int(0) NULL DEFAULT 50,
  `highBaseLimit` int(0) NULL DEFAULT 15,
  `machine` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `lastPattern` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `betPerLine` double(10, 2) NOT NULL DEFAULT 0.00,
  `viewStack` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `fsStack` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `viewHistory` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `replayLogList` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `callPattern` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `purchaseCallPattern` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `index`(`token`) USING BTREE,
  INDEX `token`(`token`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of players
-- ----------------------------

-- ----------------------------
-- Table structure for promos
-- ----------------------------
DROP TABLE IF EXISTS `promos`;
CREATE TABLE `promos`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `host` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `token` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `active` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `raceDetails` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `tournamentScores` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `racePrizes` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `raceWinners` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `tournamentDetails` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `tournamentV2Leaderboard` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `tournamentPlayerChoiceOPTIN` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of promos
-- ----------------------------

-- ----------------------------
-- Table structure for replays
-- ----------------------------
DROP TABLE IF EXISTS `replays`;
CREATE TABLE `replays`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `agentCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL,
  `userCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL,
  `gameCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL,
  `roundID` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL,
  `bet` double(20, 2) NULL DEFAULT NULL,
  `win` double(20, 0) NULL DEFAULT NULL,
  `rtp` double(10, 2) NULL DEFAULT NULL,
  `playedDate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `sharedLink` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL,
  `createdAt` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of replays
-- ----------------------------

-- ----------------------------
-- Table structure for spendings
-- ----------------------------
DROP TABLE IF EXISTS `spendings`;
CREATE TABLE `spendings`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `gameCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `betCount` int(0) NOT NULL DEFAULT 0,
  `winCount` int(0) NOT NULL DEFAULT 0,
  `betAmount` double(20, 2) NOT NULL DEFAULT 0.00,
  `winAmount` double(20, 2) NOT NULL DEFAULT 0.00,
  `spendingAmount` double(20, 2) NOT NULL DEFAULT 0.00,
  `callCount` int(0) NOT NULL DEFAULT 0,
  `callBetAmount` double(20, 2) NOT NULL DEFAULT 0.00,
  `callWinAmount` double(20, 2) NOT NULL DEFAULT 0.00,
  `createdAt` datetime(0) NOT NULL,
  `updatedAt` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of spendings
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(0) NOT NULL AUTO_INCREMENT,
  `agentCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `userCode` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `balance` double(50, 2) NULL DEFAULT NULL,
  `totalDebit` double(50, 2) NOT NULL DEFAULT 0.00,
  `totalCredit` double(50, 2) NOT NULL DEFAULT 0.00,
  `realRtp` double(10, 2) NOT NULL DEFAULT 0.00,
  `targetRtp` double(10, 2) NOT NULL DEFAULT 0.00,
  `jackpotCome` int(0) NOT NULL DEFAULT 100,
  `lang` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'ko',
  `createdAt` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updatedAt` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `token`(`token`) USING BTREE,
  INDEX `userCode`(`userCode`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
