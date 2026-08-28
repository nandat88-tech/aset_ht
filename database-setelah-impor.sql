/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: aset_ht_db
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `borrow_items`
--

DROP TABLE IF EXISTS `borrow_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrow_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `borrow_transaction_id` bigint(20) unsigned NOT NULL,
  `handy_talky_id` bigint(20) unsigned DEFAULT NULL,
  `charger_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrow_items_borrow_transaction_id_foreign` (`borrow_transaction_id`),
  KEY `borrow_items_handy_talky_id_foreign` (`handy_talky_id`),
  KEY `borrow_items_charger_id_foreign` (`charger_id`),
  CONSTRAINT `borrow_items_borrow_transaction_id_foreign` FOREIGN KEY (`borrow_transaction_id`) REFERENCES `borrow_transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `borrow_items_charger_id_foreign` FOREIGN KEY (`charger_id`) REFERENCES `chargers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrow_items_handy_talky_id_foreign` FOREIGN KEY (`handy_talky_id`) REFERENCES `handy_talkies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_items`
--

LOCK TABLES `borrow_items` WRITE;
/*!40000 ALTER TABLE `borrow_items` DISABLE KEYS */;
INSERT INTO `borrow_items` VALUES
(17,4,45,NULL,'2026-08-05 02:32:58','2026-08-05 02:32:58'),
(18,5,45,NULL,'2026-08-05 02:35:35','2026-08-05 02:35:35'),
(19,6,46,NULL,'2026-08-05 18:31:42','2026-08-05 18:31:42'),
(20,6,47,NULL,'2026-08-05 18:31:42','2026-08-05 18:31:42'),
(21,7,48,NULL,'2026-08-05 18:35:57','2026-08-05 18:35:57'),
(22,8,49,NULL,'2026-08-06 00:20:30','2026-08-06 00:20:30'),
(23,9,50,NULL,'2026-08-06 00:21:05','2026-08-06 00:21:05'),
(24,10,51,NULL,'2026-08-06 00:40:14','2026-08-06 00:40:14'),
(25,10,NULL,1,'2026-08-06 00:40:14','2026-08-06 00:40:14');
/*!40000 ALTER TABLE `borrow_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrow_transactions`
--

DROP TABLE IF EXISTS `borrow_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrow_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `destination_location_id` bigint(20) unsigned DEFAULT NULL,
  `loan_type` enum('sementara','tetap') NOT NULL DEFAULT 'sementara',
  `borrow_date` datetime NOT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `status` enum('active','returned','returned_late') NOT NULL DEFAULT 'active',
  `last_reminder_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrow_transactions_employee_id_foreign` (`employee_id`),
  KEY `borrow_transactions_destination_location_id_foreign` (`destination_location_id`),
  CONSTRAINT `borrow_transactions_destination_location_id_foreign` FOREIGN KEY (`destination_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrow_transactions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_transactions`
--

LOCK TABLES `borrow_transactions` WRITE;
/*!40000 ALTER TABLE `borrow_transactions` DISABLE KEYS */;
INSERT INTO `borrow_transactions` VALUES
(4,9,11,'sementara','2026-08-01 00:00:00','2026-08-04','','pawai',NULL,'returned_late',NULL,'2026-08-05 02:32:58','2026-08-05 02:33:36'),
(5,9,11,'tetap','2026-08-05 00:00:00',NULL,'','untuk bencana alam ',NULL,'active',NULL,'2026-08-05 02:35:35','2026-08-05 02:35:35'),
(6,9,11,'sementara','2026-08-02 00:00:00','2026-08-04','','pawai',NULL,'active',NULL,'2026-08-05 18:31:42','2026-08-05 18:31:42'),
(7,13,18,'sementara','2026-07-31 00:00:00','2026-08-04','','panen',NULL,'returned_late','2026-08-05 18:36:22','2026-08-05 18:35:57','2026-08-06 00:21:32'),
(8,9,10,'sementara','2026-08-04 00:00:00','2026-08-04','','pawai',NULL,'active',NULL,'2026-08-06 00:20:30','2026-08-06 00:20:30'),
(9,8,10,'sementara','2026-08-04 00:00:00','2026-08-06','','mtq',NULL,'returned',NULL,'2026-08-06 00:21:05','2026-08-06 00:21:13'),
(10,9,9,'sementara','2026-08-06 00:00:00','2026-08-06','Untuk evakuasi warga yang kena musibah longsor','untuk bencana alam ','borrow-documents/kHU2nvjSaI9j1ePMcBHnioX7jUb4ysq5Rs4Lc42g.jpg','active',NULL,'2026-08-06 00:40:14','2026-08-06 00:40:14');
/*!40000 ALTER TABLE `borrow_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab','i:1;',1786002070),
('laravel-cache-356a192b7913b04c54574d18c28d46e6395428ab:timer','i:1786002070;',1786002070);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chargers`
--

DROP TABLE IF EXISTS `chargers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `chargers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(255) NOT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `condition` enum('good','damaged','under_repair') NOT NULL DEFAULT 'good',
  `status` enum('available','borrowed','under_repair','damaged') NOT NULL DEFAULT 'available',
  `handy_talky_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chargers_serial_number_unique` (`serial_number`),
  UNIQUE KEY `chargers_inventory_number_unique` (`inventory_number`),
  KEY `chargers_handy_talky_id_foreign` (`handy_talky_id`),
  CONSTRAINT `chargers_handy_talky_id_foreign` FOREIGN KEY (`handy_talky_id`) REFERENCES `handy_talkies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chargers`
--

LOCK TABLES `chargers` WRITE;
/*!40000 ALTER TABLE `chargers` DISABLE KEYS */;
INSERT INTO `chargers` VALUES
(1,'01','01','good','borrowed',NULL,'2026-07-29 01:26:21','2026-08-06 00:40:14');
/*!40000 ALTER TABLE `chargers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES
(8,'Ajudan Walikota(fauzan)','Ajudan Walikota(fauzan)',NULL,'2026-07-31 23:29:19','2026-07-31 23:31:49'),
(9,'Ajudan Sekda','Ajudan Sekda',NULL,'2026-07-31 23:29:19','2026-07-31 23:31:49'),
(12,'Kepada BPBD ','Badan Penanggulangan Bencana Daerah','','2026-08-05 01:39:29','2026-08-05 01:39:29'),
(13,'hendri yamalta','-','085161405160','2026-08-05 18:34:05','2026-08-05 18:34:59');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `handy_talkies`
--

DROP TABLE IF EXISTS `handy_talkies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `handy_talkies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(255) NOT NULL,
  `inventory_number` varchar(255) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `condition` enum('good','damaged','under_repair') NOT NULL DEFAULT 'good',
  `status` enum('available','borrowed','under_repair','damaged') NOT NULL DEFAULT 'available',
  `purchase_date` date DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `handy_talkies_serial_number_unique` (`serial_number`),
  UNIQUE KEY `handy_talkies_inventory_number_unique` (`inventory_number`),
  KEY `handy_talkies_location_id_foreign` (`location_id`),
  CONSTRAINT `handy_talkies_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `handy_talkies`
--

LOCK TABLES `handy_talkies` WRITE;
/*!40000 ALTER TABLE `handy_talkies` DISABLE KEYS */;
INSERT INTO `handy_talkies` VALUES
(45,'SN-001','INV-001','Hytera','Digital Radio','-',NULL,'good','borrowed',NULL,11,'2026-08-05 02:31:11','2026-08-05 02:35:35'),
(46,'SN-002','INV-002','Hytera','Digital Radio','',NULL,'good','borrowed',NULL,11,'2026-08-05 18:21:30','2026-08-05 18:31:42'),
(47,'SN-003','INV-003','Hytera','Digital Radio','',NULL,'good','borrowed',NULL,11,'2026-08-05 18:22:01','2026-08-05 18:31:42'),
(48,'SN-004','INV-004','Hytera','Digital Radio','',NULL,'damaged','damaged',NULL,8,'2026-08-05 18:22:32','2026-08-06 00:21:32'),
(49,'SN-005','INV-005','Hytera','Digital Radio','',NULL,'good','borrowed',NULL,10,'2026-08-05 18:26:42','2026-08-06 00:20:30'),
(50,'SN-006','INV-006','Hytera','Digital Radio','',NULL,'damaged','damaged',NULL,8,'2026-08-05 18:28:11','2026-08-06 00:21:13'),
(51,'SN-007','INV-007','Hytera','Digital Radio','',NULL,'good','borrowed',NULL,9,'2026-08-06 00:18:43','2026-08-06 00:40:14');
/*!40000 ALTER TABLE `handy_talkies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES
(8,'DISKOMINFO',NULL,'2026-07-29 18:34:49','2026-07-29 18:34:49'),
(9,'Badan Penanggulangan Bencana Daerah',NULL,'2026-07-29 18:34:49','2026-07-29 18:34:49'),
(10,'Ajudan Walikota(fauzan)',NULL,'2026-07-29 18:34:49','2026-07-29 18:34:49'),
(11,'Ajudan Sekda',NULL,'2026-07-29 18:34:49','2026-07-29 18:34:49'),
(18,'komunitas tani','','2026-08-05 18:35:24','2026-08-05 18:35:24');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_07_29_023828_create_locations_table',2),
(5,'2026_07_29_024847_create_employees_table',3),
(6,'2026_07_29_025614_create_handy_talkies_table',4),
(7,'2026_07_29_031115_create_chargers_table',5),
(8,'2026_07_29_031619_create_borrow_transactions_table',6),
(9,'2026_07_29_031806_create_borrow_items_table',7),
(10,'2026_07_29_032750_create_return_transactions_table',8),
(11,'2026_07_29_033110_create_return_items_table',9),
(12,'2026_07_31_021322_add_purpose_to_borrow_transactions_table',10),
(13,'2026_07_31_024454_add_condition_note_to_return_items_table',11),
(14,'2026_07_31_031941_add_destination_location_to_borrow_transactions_table',12),
(15,'2026_07_31_070146_add_last_reminder_sent_at_to_borrow_transactions_table',13),
(16,'2026_08_01_055406_add_phone_to_employees_table',14),
(17,'2026_08_01_060618_add_loan_type_to_borrow_transactions_table',15),
(18,'2026_08_02_061258_add_role_to_users_table',16),
(19,'2026_08_03_065024_update_role_enum_add_viewer_to_users_table',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_items`
--

DROP TABLE IF EXISTS `return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `return_transaction_id` bigint(20) unsigned NOT NULL,
  `handy_talky_id` bigint(20) unsigned DEFAULT NULL,
  `charger_id` bigint(20) unsigned DEFAULT NULL,
  `condition` enum('good','damaged','under_repair') NOT NULL,
  `condition_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `return_items_return_transaction_id_foreign` (`return_transaction_id`),
  KEY `return_items_handy_talky_id_foreign` (`handy_talky_id`),
  KEY `return_items_charger_id_foreign` (`charger_id`),
  CONSTRAINT `return_items_charger_id_foreign` FOREIGN KEY (`charger_id`) REFERENCES `chargers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_items_handy_talky_id_foreign` FOREIGN KEY (`handy_talky_id`) REFERENCES `handy_talkies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `return_items_return_transaction_id_foreign` FOREIGN KEY (`return_transaction_id`) REFERENCES `return_transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_items`
--

LOCK TABLES `return_items` WRITE;
/*!40000 ALTER TABLE `return_items` DISABLE KEYS */;
INSERT INTO `return_items` VALUES
(1,1,45,NULL,'good',NULL,'2026-08-05 02:33:36','2026-08-05 02:33:36'),
(2,2,50,NULL,'damaged','Terjadi saat dipinjam oleh Ajudan Walikota(fauzan) (Ajudan Walikota(fauzan)), dipinjamkan ke lokasi: Ajudan Walikota(fauzan).','2026-08-06 00:21:13','2026-08-06 00:21:13'),
(3,3,48,NULL,'damaged','lcd Terjadi saat dipinjam oleh hendri yamalta (-), dipinjamkan ke lokasi: komunitas tani.','2026-08-06 00:21:32','2026-08-06 00:21:32');
/*!40000 ALTER TABLE `return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return_transactions`
--

DROP TABLE IF EXISTS `return_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `return_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `borrow_transaction_id` bigint(20) unsigned NOT NULL,
  `return_date` datetime NOT NULL,
  `notes` text DEFAULT NULL,
  `documentation_url` varchar(255) DEFAULT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `return_transactions_borrow_transaction_id_unique` (`borrow_transaction_id`),
  CONSTRAINT `return_transactions_borrow_transaction_id_foreign` FOREIGN KEY (`borrow_transaction_id`) REFERENCES `borrow_transactions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return_transactions`
--

LOCK TABLES `return_transactions` WRITE;
/*!40000 ALTER TABLE `return_transactions` DISABLE KEYS */;
INSERT INTO `return_transactions` VALUES
(1,4,'2026-08-05 00:00:00','',NULL,1,'2026-08-05 02:33:36','2026-08-05 02:33:36'),
(2,9,'2026-08-06 00:00:00','',NULL,0,'2026-08-06 00:21:13','2026-08-06 00:21:13'),
(3,7,'2026-08-06 00:00:00','',NULL,1,'2026-08-06 00:21:32','2026-08-06 00:21:32');
/*!40000 ALTER TABLE `return_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('1AVq6cglaJzqS9VSKBEkCVV8fSK5Y9TNBuG7z4u4',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJhcWd2SUtncEpmSk93NzBHaW5jUHRJMjNTR2dFZHpmbW5HMEVrM2ZlIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1786048149),
('dQ0QpOl7AnxQyFwOmnQ3N3dXMSx2QpvS7G21rgzM',1,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJWM3Jabnl3YXFmZU1NS05MRGlSOGV5eUJjVVNOWlpXR0RHZ05ZRlVSIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9oYW5keS10YWxreSIsInJvdXRlIjoiaGFuZHktdGFsa3kuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=',1786071383);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','operator','viewer') NOT NULL DEFAULT 'admin',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','admin@test.com','admin',NULL,'$2y$12$dXLUPGXXngQfcQGV67XlDu64iGkY2N1nAJw2YrckAn8aBXmSLi9X6',NULL,'2026-07-28 19:36:39','2026-07-28 19:36:39'),
(2,'admin1','admin1@test.com','operator',NULL,'$2y$12$dg4ix7wjdaHfQsmFsliZFuWNLYFydoGNhrdcLeNzW6WWyGX.QuHnG',NULL,'2026-08-01 23:48:09','2026-08-01 23:48:09'),
(3,'admin2','admin2@test.com','viewer',NULL,'$2y$12$d8bp7zNeyS6wpIxzpikVB.7ZGbPWvXn0OylgMJaW2GrlYIItyMDp6',NULL,'2026-08-02 00:45:29','2026-08-02 00:45:29');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20  7:55:40
