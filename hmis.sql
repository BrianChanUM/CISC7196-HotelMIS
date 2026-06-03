/*
 Navicat Premium Dump SQL

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 80044 (8.0.44)
 Source Host           : localhost:3306
 Source Schema         : hmis

 Target Server Type    : MySQL
 Target Server Version : 80044 (8.0.44)
 File Encoding         : 65001

 Date: 01/06/2026 14:38:03
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for accessrole
-- ----------------------------
DROP TABLE IF EXISTS `accessrole`;
CREATE TABLE `accessrole`  (
  `RoleID` int NOT NULL COMMENT 'Admin/Staff/User',
  `RoleType` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CreateDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of accessrole
-- ----------------------------
INSERT INTO `accessrole` VALUES (1, 'Admin', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `accessrole` VALUES (2, 'Staff', '2023-12-24 21:57:27', '2023-12-24 21:57:27');
INSERT INTO `accessrole` VALUES (3, 'Guest', '2023-12-24 21:57:27', '2023-12-24 21:57:27');

-- ----------------------------
-- Table structure for enquiry
-- ----------------------------
DROP TABLE IF EXISTS `enquiry`;
CREATE TABLE `enquiry`  (
  `eID` int NOT NULL AUTO_INCREMENT,
  `eUser` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `eEmail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `eiscall` int NOT NULL,
  `ePhone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `eType` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `eContent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `eCreatedDate` datetime NOT NULL,
  `eModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eID`) USING BTREE,
  INDEX `eType`(`eType` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of enquiry
-- ----------------------------
INSERT INTO `enquiry` VALUES (25, 'fdf', 'ert3445@www', 0, ' 12345678900', 'Dining', '1', '2026-05-30 16:43:06', '2026-05-30 16:43:06');
INSERT INTO `enquiry` VALUES (26, 'fdf', 'ert3445@www', 0, ' 12345678900', 'Dining', '1', '2026-05-30 16:45:36', '2026-05-30 16:45:36');

-- ----------------------------
-- Table structure for enquirytype
-- ----------------------------
DROP TABLE IF EXISTS `enquirytype`;
CREATE TABLE `enquirytype`  (
  `etypeID` int NOT NULL AUTO_INCREMENT,
  `etype` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `etypeCreatedOn` datetime NOT NULL,
  `etypeModifiedOn` datetime NOT NULL,
  `eStatus` int NOT NULL,
  PRIMARY KEY (`etypeID`) USING BTREE,
  UNIQUE INDEX `etype`(`etype` ASC) USING BTREE,
  INDEX `etype_2`(`etype` ASC) USING BTREE,
  INDEX `etype_3`(`etype` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of enquirytype
-- ----------------------------
INSERT INTO `enquirytype` VALUES (1, 'Dining', '2024-01-29 21:42:31', '2024-01-29 21:42:31', 1);
INSERT INTO `enquirytype` VALUES (2, 'Limo', '2024-01-29 21:42:40', '2024-01-29 21:42:40', 1);
INSERT INTO `enquirytype` VALUES (3, 'Event', '2024-01-29 21:43:04', '2024-01-29 21:43:04', 1);
INSERT INTO `enquirytype` VALUES (4, 'Other', '2024-01-29 21:43:12', '2024-01-29 21:43:12', 1);

-- ----------------------------
-- Table structure for hoteldriver
-- ----------------------------
DROP TABLE IF EXISTS `hoteldriver`;
CREATE TABLE `hoteldriver`  (
  `DriverID` int NOT NULL AUTO_INCREMENT,
  `DriverName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `DriverSource` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `DriverLic` int NOT NULL,
  `DriverCreatedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `DriverModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`DriverID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hoteldriver
-- ----------------------------
INSERT INTO `hoteldriver` VALUES (1, 'Chan', 'internal', 5543211, '2024-03-10 01:17:49', '2024-03-10 01:17:49');
INSERT INTO `hoteldriver` VALUES (2, 'Leong', 'internal', 7524657, '2024-03-10 01:20:42', '2024-03-10 01:20:42');
INSERT INTO `hoteldriver` VALUES (3, 'Tang', 'internal', 8524767, '2024-03-10 01:20:58', '2024-03-10 01:20:58');
INSERT INTO `hoteldriver` VALUES (4, 'Daniel', 'external', 9725767, '2024-03-10 01:21:07', '2024-03-10 01:21:07');
INSERT INTO `hoteldriver` VALUES (5, 'Sunny', 'internal', 8278685, '2024-03-10 01:21:17', '2024-03-10 01:21:17');
INSERT INTO `hoteldriver` VALUES (6, 'Kobe', 'internal', 4378676, '2024-03-10 01:21:24', '2024-03-10 01:21:24');
INSERT INTO `hoteldriver` VALUES (7, 'Roberto', 'external', 3523858, '2024-03-10 01:21:37', '2024-03-10 01:21:37');

-- ----------------------------
-- Table structure for hoteloutlet
-- ----------------------------
DROP TABLE IF EXISTS `hoteloutlet`;
CREATE TABLE `hoteloutlet`  (
  `OutletID` int NOT NULL AUTO_INCREMENT,
  `OutletName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `OutletSlogan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `OutletMenu` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Opening Hour` time NOT NULL,
  `status` int NOT NULL,
  `Style` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `capacity` int NULL DEFAULT 50,
  PRIMARY KEY (`OutletID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hoteloutlet
-- ----------------------------
INSERT INTO `hoteloutlet` VALUES (1, 'In Room Dining services', '', '', '00:00:00', 1, 'IRD', 50);
INSERT INTO `hoteloutlet` VALUES (2, 'Lunch Services', '', '', '00:00:00', 1, 'IRD', 50);
INSERT INTO `hoteloutlet` VALUES (3, 'try', '', '', '00:00:00', 0, 'IRD', 50);
INSERT INTO `hoteloutlet` VALUES (4, 'lunch services', '', '', '00:00:00', 0, 'FnB', 50);
INSERT INTO `hoteloutlet` VALUES (5, 'lunch services', '', '', '00:00:00', 0, 'FnB', 50);
INSERT INTO `hoteloutlet` VALUES (17, 'Chinese Restaurant', 'Taste of East', 'Cantonese, Sichuan, Hunan', '11:00:00', 1, 'Chinese', 100);
INSERT INTO `hoteloutlet` VALUES (18, 'Western Restaurant', 'Western Elegance', 'Steak, Pasta, Salad', '12:00:00', 1, 'Western', 80);
INSERT INTO `hoteloutlet` VALUES (19, 'Japanese Restaurant', 'Fresh Sushi', 'Sushi, Sashimi, Teppanyaki', '11:30:00', 1, 'Japanese', 60);
INSERT INTO `hoteloutlet` VALUES (20, 'Coffee Shop', 'Relaxing Coffee', 'Coffee, Desserts, Sandwiches', '08:00:00', 1, 'Cafe', 40);
INSERT INTO `hoteloutlet` VALUES (21, 'Lobby Bar', 'Elegant Drinks', 'Cocktails, Wine, Snacks', '18:00:00', 1, 'Bar', 50);
INSERT INTO `hoteloutlet` VALUES (22, 'Chinese Restaurant', 'Taste of East', 'Cantonese, Sichuan, Hunan', '11:00:00', 1, 'Chinese', 100);
INSERT INTO `hoteloutlet` VALUES (23, 'Western Restaurant', 'Western Elegance', 'Steak, Pasta, Salad', '12:00:00', 1, 'Western', 80);
INSERT INTO `hoteloutlet` VALUES (24, 'Japanese Restaurant', 'Fresh Sushi', 'Sushi, Sashimi, Teppanyaki', '11:30:00', 1, 'Japanese', 60);
INSERT INTO `hoteloutlet` VALUES (25, 'Coffee Shop', 'Relaxing Coffee', 'Coffee, Desserts, Sandwiches', '08:00:00', 1, 'Cafe', 40);
INSERT INTO `hoteloutlet` VALUES (26, 'Lobby Bar', 'Elegant Drinks', 'Cocktails, Wine, Snacks', '18:00:00', 1, 'Bar', 50);

-- ----------------------------
-- Table structure for hotelpoi
-- ----------------------------
DROP TABLE IF EXISTS `hotelpoi`;
CREATE TABLE `hotelpoi`  (
  `POIID` int NOT NULL AUTO_INCREMENT,
  `POIName` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `POIPrice` int NULL DEFAULT NULL,
  `POICreatedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `POIModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`POIID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hotelpoi
-- ----------------------------

-- ----------------------------
-- Table structure for hotelroomtype
-- ----------------------------
DROP TABLE IF EXISTS `hotelroomtype`;
CREATE TABLE `hotelroomtype`  (
  `HotelID` int NOT NULL AUTO_INCREMENT,
  `HotelRoomtype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `HotelRoomPrice` int NOT NULL,
  `ModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `HotelRoomImage` longblob NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `daily_quantity` int NULL DEFAULT 1,
  `status` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`HotelID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hotelroomtype
-- ----------------------------
INSERT INTO `hotelroomtype` VALUES (1, '123', 123, '2024-07-13 22:56:13', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (2, 'Hotel Macau - City View', 800, '2024-07-13 23:00:33', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (3, 'Hotel Macau - Lake View', 1200, '2024-07-13 23:16:25', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (4, 'Hotel Macau - Villa View', 1300, '2024-07-13 23:17:17', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (5, 'Hotel Macau - Test', 555, '2024-07-13 23:23:20', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (6, 'Hotel', 12, '2024-07-13 23:29:41', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (7, 'Macau', 1, '2024-07-13 23:35:23', '', NULL, 1, 1);
INSERT INTO `hotelroomtype` VALUES (8, 'Hotel LV - City View', 7000, '2024-07-14 22:39:49', '', NULL, 1, 1);

-- ----------------------------
-- Table structure for hotelvehicle
-- ----------------------------
DROP TABLE IF EXISTS `hotelvehicle`;
CREATE TABLE `hotelvehicle`  (
  `VehicleID` int NOT NULL AUTO_INCREMENT,
  `VehicleType` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `VehicleName` int NOT NULL,
  `VehiclePlate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `VehicleCreatedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `VehicleModifiedDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`VehicleID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hotelvehicle
-- ----------------------------
INSERT INTO `hotelvehicle` VALUES (12, 'Rolls Royce', 0, 'MX8888', '2024-01-25 00:31:45', '2024-01-25 00:31:45');
INSERT INTO `hotelvehicle` VALUES (13, 'Rolls Royce', 0, 'MX8899', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (14, 'Rolls Royce', 0, 'MX8866', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (15, 'Bentley', 0, 'MN2266', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (16, 'Bentley', 0, 'MP6688', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (17, 'Bentley', 0, 'MQ7722', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (18, 'Alphard', 0, 'AA1234', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (19, 'Alphard', 0, 'AA8899', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (20, 'Alphard', 0, 'AB1010', '2024-01-25 00:32:18', '2024-01-25 00:32:18');
INSERT INTO `hotelvehicle` VALUES (21, 'toyota', 0, 'MN6622', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for hotelvehicletype
-- ----------------------------
DROP TABLE IF EXISTS `hotelvehicletype`;
CREATE TABLE `hotelvehicletype`  (
  `VehicleID` int NOT NULL AUTO_INCREMENT,
  `VehicleType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL,
  `createddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifieddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `daily_quantity` int NULL DEFAULT 1,
  PRIMARY KEY (`VehicleID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hotelvehicletype
-- ----------------------------
INSERT INTO `hotelvehicletype` VALUES (1, 'Rolls Royce', 1, '2024-07-14 00:33:06', '2024-07-14 00:33:06', NULL, 1);
INSERT INTO `hotelvehicletype` VALUES (2, 'Bentley', 1, '2024-07-14 00:33:10', '2024-07-14 00:33:10', NULL, 1);
INSERT INTO `hotelvehicletype` VALUES (3, 'Alphard', 1, '2024-07-14 00:33:32', '2024-07-14 00:33:32', NULL, 1);
INSERT INTO `hotelvehicletype` VALUES (4, 'toyota', 1, '2024-07-14 22:52:29', '2024-07-14 22:52:29', NULL, 1);
INSERT INTO `hotelvehicletype` VALUES (6, 'Luxury Sedan', 1, '2026-05-31 16:12:30', '2026-05-31 16:12:30', NULL, 5);
INSERT INTO `hotelvehicletype` VALUES (7, 'Business Van', 1, '2026-05-31 16:12:30', '2026-05-31 16:12:30', NULL, 8);
INSERT INTO `hotelvehicletype` VALUES (8, 'SUV', 1, '2026-05-31 16:12:30', '2026-05-31 16:12:30', NULL, 3);
INSERT INTO `hotelvehicletype` VALUES (9, 'MPV', 1, '2026-05-31 16:12:30', '2026-05-31 16:12:30', NULL, 4);
INSERT INTO `hotelvehicletype` VALUES (10, 'Luxury Bus', 1, '2026-05-31 16:12:30', '2026-05-31 16:12:30', NULL, 2);
INSERT INTO `hotelvehicletype` VALUES (11, 'Luxury Sedan', 1, '2026-05-31 16:13:37', '2026-05-31 16:13:37', NULL, 5);
INSERT INTO `hotelvehicletype` VALUES (12, 'Business Van', 1, '2026-05-31 16:13:37', '2026-05-31 16:13:37', NULL, 8);
INSERT INTO `hotelvehicletype` VALUES (13, 'SUV', 1, '2026-05-31 16:13:37', '2026-05-31 16:13:37', NULL, 3);
INSERT INTO `hotelvehicletype` VALUES (14, 'MPV', 1, '2026-05-31 16:13:37', '2026-05-31 16:13:37', NULL, 4);
INSERT INTO `hotelvehicletype` VALUES (15, 'Luxury Bus', 1, '2026-05-31 16:13:37', '2026-05-31 16:13:37', NULL, 2);

-- ----------------------------
-- Table structure for orderbookings
-- ----------------------------
DROP TABLE IF EXISTS `orderbookings`;
CREATE TABLE `orderbookings`  (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `OrderType` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Hotel/F&B/Limo/InRoomService',
  `Time` datetime NOT NULL,
  `ContactNo` int NOT NULL,
  `Email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Confirmation email',
  `NoofGuest` int NOT NULL,
  `OrderRemark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Comment/Perference',
  `Status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `OrderCreatedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `OrderModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `isRequired` int NOT NULL,
  `AssignedTo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Assigned Limo & IRD user',
  PRIMARY KEY (`OrderID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 187 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of orderbookings
-- ----------------------------
INSERT INTO `orderbookings` VALUES (141, 'F&B', '2026-05-31 18:00:00', 123, 'aaa', 1, '12312312', 'TBC', '2026-05-30 16:56:09', '2026-05-30 16:56:09', 0, '');
INSERT INTO `orderbookings` VALUES (142, 'F&B', '2026-05-31 17:30:00', 123, 'aaa', 1, '12312312', 'TBC', '2026-05-30 17:00:31', '2026-05-30 17:00:31', 0, '');
INSERT INTO `orderbookings` VALUES (143, 'Limo', '2222-02-02 00:30:00', 12344, 'aaa', 5, 'menm', 'TBC', '2026-05-31 15:42:24', '2026-05-31 15:42:24', 0, '');
INSERT INTO `orderbookings` VALUES (144, 'Dining', '2026-05-01 12:30:00', 88001, 'guest1@example.com', 4, 'Chinese Restaurant - Family Dinner', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff1');
INSERT INTO `orderbookings` VALUES (145, 'Dining', '2026-05-01 19:00:00', 88002, 'guest2@example.com', 2, 'Western Restaurant - Couple Dinner', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff2');
INSERT INTO `orderbookings` VALUES (146, 'Dining', '2026-05-02 12:00:00', 88003, 'guest3@example.com', 6, 'Chinese Restaurant - Business Lunch', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff1');
INSERT INTO `orderbookings` VALUES (147, 'Dining', '2026-05-02 18:30:00', 88004, 'guest4@example.com', 3, 'Japanese Restaurant - Friends Gathering', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff3');
INSERT INTO `orderbookings` VALUES (148, 'Dining', '2026-05-03 11:30:00', 88005, 'guest5@example.com', 2, 'Coffee Shop - Afternoon Tea', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff4');
INSERT INTO `orderbookings` VALUES (149, 'Dining', '2026-05-03 19:30:00', 88006, 'guest6@example.com', 8, 'Chinese Restaurant - Birthday Party', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff1');
INSERT INTO `orderbookings` VALUES (150, 'Dining', '2026-05-04 12:15:00', 88007, 'guest7@example.com', 4, 'Western Restaurant - Business Lunch', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff2');
INSERT INTO `orderbookings` VALUES (151, 'Dining', '2026-05-04 18:00:00', 88008, 'guest8@example.com', 2, 'Japanese Restaurant - Date', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff3');
INSERT INTO `orderbookings` VALUES (152, 'Dining', '2026-05-05 14:00:00', 88009, 'guest9@example.com', 5, 'Coffee Shop - Meeting Break', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff4');
INSERT INTO `orderbookings` VALUES (153, 'Dining', '2026-05-05 20:00:00', 88010, 'guest10@example.com', 10, 'Lobby Bar - Celebration', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff5');
INSERT INTO `orderbookings` VALUES (154, 'Dining', '2026-05-10 12:00:00', 88011, 'guest11@example.com', 3, 'Chinese Restaurant - Family', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff1');
INSERT INTO `orderbookings` VALUES (155, 'Dining', '2026-05-10 19:30:00', 88012, 'guest12@example.com', 4, 'Western Restaurant - Birthday', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff2');
INSERT INTO `orderbookings` VALUES (156, 'Dining', '2026-05-15 11:30:00', 88013, 'guest13@example.com', 2, 'Coffee Shop - Breakfast', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff4');
INSERT INTO `orderbookings` VALUES (157, 'Dining', '2026-05-15 18:45:00', 88014, 'guest14@example.com', 6, 'Japanese Restaurant - Team Dinner', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff3');
INSERT INTO `orderbookings` VALUES (158, 'Dining', '2026-05-20 12:30:00', 88015, 'guest15@example.com', 2, 'Lobby Bar - Drinks', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff5');
INSERT INTO `orderbookings` VALUES (159, 'F&B', '2026-05-21 15:00:00', 88016, 'guest16@example.com', 10, 'Coffee Shop - Afternoon Tea Set', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff4');
INSERT INTO `orderbookings` VALUES (160, 'F&B', '2026-05-22 21:00:00', 88017, 'guest17@example.com', 8, 'Lobby Bar - Cocktail Party', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff5');
INSERT INTO `orderbookings` VALUES (161, 'F&B', '2026-05-25 14:30:00', 88018, 'guest18@example.com', 4, 'Coffee Shop - Business Tea', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff4');
INSERT INTO `orderbookings` VALUES (162, 'Dining', '2026-05-28 12:00:00', 88019, 'guest19@example.com', 12, 'Chinese Restaurant - Wedding', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff1');
INSERT INTO `orderbookings` VALUES (163, 'Dining', '2026-05-30 19:00:00', 88020, 'guest20@example.com', 2, 'Japanese Restaurant - Anniversary', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'staff3');
INSERT INTO `orderbookings` VALUES (164, 'Limo', '2026-05-01 09:00:00', 99001, 'limo1@example.com', 3, 'Luxury Sedan - Airport Pickup', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver1');
INSERT INTO `orderbookings` VALUES (165, 'Limo', '2026-05-01 14:00:00', 99002, 'limo2@example.com', 6, 'Business Van - Meeting Transfer', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver2');
INSERT INTO `orderbookings` VALUES (166, 'Limo', '2026-05-02 10:30:00', 99003, 'limo3@example.com', 4, 'SUV - Sightseeing', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver3');
INSERT INTO `orderbookings` VALUES (167, 'Limo', '2026-05-02 16:00:00', 99004, 'limo4@example.com', 8, 'MPV - Group Tour', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver4');
INSERT INTO `orderbookings` VALUES (168, 'Limo', '2026-05-03 08:00:00', 99005, 'limo5@example.com', 15, 'Luxury Bus - Airport Transfer', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver5');
INSERT INTO `orderbookings` VALUES (169, 'Limo', '2026-05-05 11:00:00', 99006, 'limo6@example.com', 2, 'Luxury Sedan - Business Transfer', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver1');
INSERT INTO `orderbookings` VALUES (170, 'Limo', '2026-05-10 09:30:00', 99007, 'limo7@example.com', 5, 'Business Van - Client Transfer', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver2');
INSERT INTO `orderbookings` VALUES (171, 'Limo', '2026-05-10 15:00:00', 99008, 'limo8@example.com', 3, 'SUV - Shopping Trip', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver3');
INSERT INTO `orderbookings` VALUES (172, 'Limo', '2026-05-15 08:30:00', 99009, 'limo9@example.com', 12, 'Luxury Bus - Tour Group', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver5');
INSERT INTO `orderbookings` VALUES (173, 'Limo', '2026-05-15 17:00:00', 99010, 'limo10@example.com', 4, 'MPV - Family Trip', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver4');
INSERT INTO `orderbookings` VALUES (174, 'Limo', '2026-05-20 10:00:00', 99011, 'limo11@example.com', 1, 'Luxury Sedan - Personal Trip', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver1');
INSERT INTO `orderbookings` VALUES (175, 'Limo', '2026-05-22 14:30:00', 99012, 'limo12@example.com', 7, 'Business Van - Team Building', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver2');
INSERT INTO `orderbookings` VALUES (176, 'Limo', '2026-05-25 09:00:00', 99013, 'limo13@example.com', 20, 'Luxury Bus - Large Group', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver5');
INSERT INTO `orderbookings` VALUES (177, 'Limo', '2026-05-28 16:00:00', 99014, 'limo14@example.com', 4, 'SUV - Mountain Trip', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver3');
INSERT INTO `orderbookings` VALUES (178, 'Limo', '2026-05-30 11:30:00', 99015, 'limo15@example.com', 6, 'MPV - Airport Dropoff', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'driver4');
INSERT INTO `orderbookings` VALUES (179, 'Hotel', '2026-05-01 14:00:00', 77001, 'hotel1@example.com', 2, 'Deluxe King Room - 2 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception1');
INSERT INTO `orderbookings` VALUES (180, 'Hotel', '2026-05-02 15:00:00', 77002, 'hotel2@example.com', 4, 'Family Suite - 3 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception2');
INSERT INTO `orderbookings` VALUES (181, 'Hotel', '2026-05-05 12:00:00', 77003, 'hotel3@example.com', 1, 'Standard Room - 1 night', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception1');
INSERT INTO `orderbookings` VALUES (182, 'Hotel', '2026-05-10 16:00:00', 77004, 'hotel4@example.com', 3, 'Business Suite - 2 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception2');
INSERT INTO `orderbookings` VALUES (183, 'Hotel', '2026-05-15 11:00:00', 77005, 'hotel5@example.com', 2, 'Deluxe Twin Room - 1 night', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception1');
INSERT INTO `orderbookings` VALUES (184, 'Hotel', '2026-05-20 14:30:00', 77006, 'hotel6@example.com', 6, 'Executive Suite - 4 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception2');
INSERT INTO `orderbookings` VALUES (185, 'Hotel', '2026-05-25 10:00:00', 77007, 'hotel7@example.com', 2, 'Standard Room - 2 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception1');
INSERT INTO `orderbookings` VALUES (186, 'Hotel', '2026-05-30 17:00:00', 77008, 'hotel8@example.com', 4, 'Family Suite - 5 nights', 'Completed', '2026-05-31 16:13:37', '2026-05-31 16:13:37', 1, 'reception2');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `UID` int NOT NULL AUTO_INCREMENT,
  `UserName` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CreateDate` datetime NOT NULL,
  `ModifiedDate` datetime NOT NULL,
  `Email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `in_house_status` tinyint(1) NULL DEFAULT 0,
  `checkin_date` date NULL DEFAULT NULL,
  `checkout_date` date NULL DEFAULT NULL,
  UNIQUE INDEX `ID`(`UID` ASC) USING BTREE,
  INDEX `Role`(`Role` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'ITadmin', '123', 'admin', '2024-01-25 15:23:41', '2024-01-25 15:23:41', 'NA', 0, NULL, NULL);
INSERT INTO `user` VALUES (2, '511430', '1', 'admin', '2023-12-25 00:00:00', '2024-03-30 22:55:53', 'eform@gmail', 0, NULL, NULL);
INSERT INTO `user` VALUES (3, '511431', 'Abcd2134@', 'guest', '2023-12-25 00:00:00', '2023-12-25 00:00:00', 'BrianC@hotmail.com', 0, NULL, NULL);
INSERT INTO `user` VALUES (4, '511111', '123', 'staff', '2023-12-25 00:00:00', '2024-01-08 23:36:23', 'bb20045@umac.mo', 0, NULL, NULL);
INSERT INTO `user` VALUES (5, '511439', 'P@511439', 'admin', '2023-12-25 15:37:21', '2024-01-08 23:54:51', 'MC14887@umac', 0, NULL, NULL);
INSERT INTO `user` VALUES (6, '789111', '123', 'admin', '2024-01-25 16:02:03', '2024-01-25 16:02:03', '789111@itadmin', 0, NULL, NULL);
INSERT INTO `user` VALUES (7, '789777', '123', 'admin', '2024-01-25 16:14:52', '2024-01-25 16:14:52', '789777@hmis', 0, NULL, NULL);
INSERT INTO `user` VALUES (8, '556677', '123', 'guest', '2024-01-25 16:38:53', '2024-01-25 16:38:53', 'guest@guest.com', 0, NULL, NULL);
INSERT INTO `user` VALUES (9, '789333', '123', 'staff', '2024-01-25 15:59:20', '2024-01-25 15:59:20', '789333@ITadmin.com', 0, NULL, NULL);
INSERT INTO `user` VALUES (10, '101026', '123', 'admin', '2024-07-11 17:47:12', '2024-07-11 17:47:12', '10@gmail.com', 0, NULL, NULL);
INSERT INTO `user` VALUES (11, '511112', '123', 'guest', '2024-10-16 15:51:26', '2024-10-16 15:51:26', 'bb20046@umac.mo', 0, NULL, NULL);
INSERT INTO `user` VALUES (12, '611111', '123', 'guest', '2024-10-16 16:05:27', '2024-10-16 16:05:27', '123@123', 0, NULL, NULL);

-- ----------------------------
-- Table structure for user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `module` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `permission_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_allowed` tinyint(1) NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_permission`(`user_id` ASC, `module` ASC, `permission_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for userhistory
-- ----------------------------
DROP TABLE IF EXISTS `userhistory`;
CREATE TABLE `userhistory`  (
  `HistoryID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `Datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EventType` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `EventDetails` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`HistoryID`) USING BTREE,
  INDEX `userhistory_ibfk_1`(`UserID` ASC) USING BTREE,
  CONSTRAINT `userhistory_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of userhistory
-- ----------------------------

-- ----------------------------
-- Table structure for userprofile
-- ----------------------------
DROP TABLE IF EXISTS `userprofile`;
CREATE TABLE `userprofile`  (
  `ProfileID` int NOT NULL AUTO_INCREMENT,
  `UID` int NOT NULL,
  `Department` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `Level` int NOT NULL,
  `SalaryRate` int NOT NULL,
  `OnboardDate` date NOT NULL,
  `ModifiedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ProfileID`) USING BTREE,
  UNIQUE INDEX `UID`(`UID` ASC) USING BTREE,
  CONSTRAINT `userprofile_ibfk_1` FOREIGN KEY (`UID`) REFERENCES `user` (`UID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of userprofile
-- ----------------------------
INSERT INTO `userprofile` VALUES (1, 2, 'EO', 1, 50000, '0000-00-00', '2024-01-22 22:30:35');
INSERT INTO `userprofile` VALUES (2, 4, 'Limo', 3, 30000, '0000-00-00', '2024-01-22 22:32:41');
INSERT INTO `userprofile` VALUES (3, 5, 'Limo', 2, 40000, '0000-00-00', '2024-01-22 22:33:05');
INSERT INTO `userprofile` VALUES (4, 7, 'Department', 0, 0, '2024-01-25', '2024-01-25 16:14:52');
INSERT INTO `userprofile` VALUES (5, 8, 'Department', 0, 0, '2024-01-25', '2024-01-25 16:38:53');
INSERT INTO `userprofile` VALUES (6, 1, 'IT', 2, 40000, '2023-11-10', '2024-01-26 00:27:19');
INSERT INTO `userprofile` VALUES (7, 9, 'IT', 4, 20000, '2023-11-25', '2024-01-26 00:28:51');
INSERT INTO `userprofile` VALUES (11, 6, 'Limo', 4, 18000, '2023-12-25', '2024-01-26 00:30:27');
INSERT INTO `userprofile` VALUES (12, 10, 'Department', 0, 0, '2024-07-11', '2024-07-11 17:47:12');
INSERT INTO `userprofile` VALUES (13, 11, 'Department', 0, 0, '2024-10-16', '2024-10-16 15:51:26');
INSERT INTO `userprofile` VALUES (14, 12, 'Department', 0, 0, '2024-10-16', '2024-10-16 16:05:27');

-- ----------------------------
-- Table structure for usertimesheet
-- ----------------------------
DROP TABLE IF EXISTS `usertimesheet`;
CREATE TABLE `usertimesheet`  (
  `CycleID` int NOT NULL AUTO_INCREMENT,
  `UID` int NOT NULL,
  `Date` date NOT NULL,
  `StartTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `End Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Hours Worked` int NOT NULL,
  PRIMARY KEY (`CycleID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of usertimesheet
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
