-- MySQL dump 10.13  Distrib 8.4.6, for Win64 (x86_64)
--
-- Host: localhost    Database: stridesync2025
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `buddy_likes`
--

DROP TABLE IF EXISTS `buddy_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buddy_likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `liker_id` bigint unsigned NOT NULL,
  `liked_id` bigint unsigned NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `buddy_likes_liker_id_liked_id_unique` (`liker_id`,`liked_id`),
  KEY `buddy_likes_liked_id_status_index` (`liked_id`,`status`),
  CONSTRAINT `buddy_likes_liked_id_foreign` FOREIGN KEY (`liked_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `buddy_likes_liker_id_foreign` FOREIGN KEY (`liker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buddy_likes`
--

LOCK TABLES `buddy_likes` WRITE;
/*!40000 ALTER TABLE `buddy_likes` DISABLE KEYS */;
INSERT INTO `buddy_likes` VALUES (1,7,5,'like','2026-01-20 19:14:44','2026-01-20 19:14:44'),(2,17,5,'like','2026-01-21 21:55:44','2026-01-21 21:55:44'),(3,16,5,'dislike','2026-01-21 22:07:35','2026-01-21 22:07:35'),(4,16,7,'dislike','2026-01-21 22:07:41','2026-01-21 22:07:41'),(5,23,7,'dislike','2026-01-25 20:54:40','2026-01-25 20:54:40'),(6,23,17,'like','2026-01-25 20:54:55','2026-01-25 20:54:55'),(7,18,7,'like','2026-01-26 04:59:14','2026-01-26 04:59:14'),(8,18,23,'like','2026-01-26 04:59:35','2026-01-26 04:59:35'),(9,24,10,'dislike','2026-01-26 06:39:09','2026-01-26 06:39:09');
/*!40000 ALTER TABLE `buddy_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-event_calendar_2026','a:5:{s:2:\"ok\";b:1;s:6:\"source\";s:54:\"https://pm1.blogspot.com/p/running-event-2026.html?m=1\";s:7:\"updated\";s:105:\" * Last update on 19/01/2026 by PM1 (email to der_pacemaker@yahoo.com to get your event publish here)\";s:6:\"months\";a:12:{i:0;a:2:{s:5:\"label\";s:8:\"DEC 2026\";s:6:\"events\";a:3:{i:0;a:2:{s:4:\"date\";s:6:\"20 Dec\";s:5:\"title\";s:53:\" KBS X KLCFM Potato Run (Dataran DBKL, Kuala Lumpur)\";}i:1;a:2:{s:4:\"date\";s:6:\"13 Dec\";s:5:\"title\";s:40:\" CIDB Half Marathon (Dataran Putrajaya)\";}i:2;a:2:{s:4:\"date\";s:6:\"06 Dec\";s:5:\"title\";s:42:\" KBS X KLCFM (Dataran DBKL, Kuala Lumpur)\";}}}i:1;a:2:{s:5:\"label\";s:8:\"NOV 2026\";s:6:\"events\";a:8:{i:0;a:2:{s:4:\"date\";s:6:\"29 Nov\";s:5:\"title\";s:51:\" KL Big Breakfast Run (Dataran DBKL, Kuala Lumpur)\";}i:1;a:2:{s:4:\"date\";s:6:\"22 Nov\";s:5:\"title\";s:53:\" Quill Half Marathon (Quill City Mall, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"22 Nov\";s:5:\"title\";s:42:\" KBS X KLCFM (Dataran DBKL, Kuala Lumpur)\";}i:3;a:2:{s:4:\"date\";s:6:\"22 Nov\";s:5:\"title\";s:81:\" RSawit International Marathon (Dataran Tun Tuanku Bujang, Sibu Town, Sarawak) \";}i:4;a:2:{s:4:\"date\";s:6:\"15 Nov\";s:5:\"title\";s:46:\" Zakat Wakaf Run (Dataran DBKL, Kuala Lumpur)\";}i:5;a:2:{s:4:\"date\";s:6:\"08 Nov\";s:5:\"title\";s:31:\" Mamee Run (Dataran Putrajaya)\";}i:6;a:2:{s:4:\"date\";s:6:\"08 Nov\";s:5:\"title\";s:48:\" Kota Belud Half Marathon (Kota Belud, Sabah) \";}i:7;a:2:{s:4:\"date\";s:6:\"01 Nov\";s:5:\"title\";s:60:\" Selangor Marathon (Persiaran Flora, Cyberjaya, Selangor) \";}}}i:2;a:2:{s:5:\"label\";s:8:\"OCT 2026\";s:6:\"events\";a:8:{i:0;a:2:{s:4:\"date\";s:6:\"25 Oct\";s:5:\"title\";s:66:\" POLIO RUN x Malaysia Book of Record (Dataran DBKL, Kuala Lumpur)\";}i:1;a:2:{s:4:\"date\";s:6:\"24 Oct\";s:5:\"title\";s:76:\" Malaysia Taman Negara Ultra (Taman Negara Kuala Tahan, Jerantut, Pahang) \";}i:2;a:2:{s:4:\"date\";s:6:\"18 Oct\";s:5:\"title\";s:48:\" Sharp HSNKL Half Marathon (KLCC, Kuala Lumpur)\";}i:3;a:2:{s:4:\"date\";s:6:\"11 Oct\";s:5:\"title\";s:49:\" HSN Putrajaya Half Marathon (Dataran Putrajaya)\";}i:4;a:2:{s:4:\"date\";s:6:\"10 Oct\";s:5:\"title\";s:47:\" HSN Putrajaya 5KM and 3KM (Dataran Putrajaya)\";}i:5;a:2:{s:4:\"date\";s:6:\"08 Oct\";s:5:\"title\";s:56:\" Kokol Ultra (Padang Manggatal, Kota Kinabalu, Sabah) \";}i:6;a:2:{s:4:\"date\";s:6:\"04 Oct\";s:5:\"title\";s:71:\" Kuala Lumpur Standard Chartered Marathon - 42KM & 21KM (Kuala Lumpur)\";}i:7;a:2:{s:4:\"date\";s:6:\"03 Oct\";s:5:\"title\";s:70:\" Kuala Lumpur Standard Chartered Marathon - 10KM & 5KM (Kuala Lumpur)\";}}}i:3;a:2:{s:5:\"label\";s:8:\"SEP 2026\";s:6:\"events\";a:12:{i:0;a:2:{s:4:\"date\";s:6:\"27 Sep\";s:5:\"title\";s:61:\" Matrix Half Marathon (Matrix D\'Tempat Sendayan, N.Sembilan)\";}i:1;a:2:{s:4:\"date\";s:6:\"27 Sep\";s:5:\"title\";s:45:\" Malaysia Aktif (Dataran DBKL, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"26 Sep\";s:5:\"title\";s:70:\" Lipis Geopark Ultra (Dataran Orang Kaya Haji, Kuala Lipis, Pahang) \";}i:3;a:2:{s:4:\"date\";s:6:\"20 Sep\";s:5:\"title\";s:40:\" Peace Run (Dataran DBKL, Kuala Lumpur)\";}i:4;a:2:{s:4:\"date\";s:6:\"19 Sep\";s:5:\"title\";s:51:\" 5K Malaysia Speed (KLCC, Kuala Lumpur) ⭐⭐⭐\";}i:5;a:2:{s:4:\"date\";s:6:\"19 Sep\";s:5:\"title\";s:48:\" Malaysia Sarong Music Run (KLCC, Kuala Lumpur)\";}i:6;a:2:{s:4:\"date\";s:6:\"13 Sep\";s:5:\"title\";s:40:\" Putrajaya Marathon (Dataran Putrajaya)\";}i:7;a:2:{s:4:\"date\";s:6:\"06 Sep\";s:5:\"title\";s:51:\" Pahang Marathon (Dataran Sayangi Kuantan, Pahang)\";}i:8;a:2:{s:4:\"date\";s:6:\"06 Sep\";s:5:\"title\";s:74:\" Wasiyyah Larian Sawah Padi Tanjong Karang (SK Sri Tiram, Kuala Selangor)\";}i:9;a:2:{s:4:\"date\";s:6:\"05 Sep\";s:5:\"title\";s:64:\" Wasiyyah Tanjong Karang Half Marathon (SK Sri Tiram, Selangor)\";}i:10;a:2:{s:4:\"date\";s:6:\"05 Sep\";s:5:\"title\";s:53:\" Borneo Trail Classic (REDEEMS Centre, Bau, Sarawak)\";}i:11;a:2:{s:4:\"date\";s:6:\"04 Sep\";s:5:\"title\";s:57:\" Tunggul Melati Trail (Jenderam Hilir, Sepang, Selangor)\";}}}i:4;a:2:{s:5:\"label\";s:8:\"AUG 2026\";s:6:\"events\";a:11:{i:0;a:2:{s:4:\"date\";s:6:\"23 Aug\";s:5:\"title\";s:52:\" Merdeka Half Marathon (Dataran DBKL, Kuala Lumpur)\";}i:1;a:2:{s:4:\"date\";s:6:\"23 Aug\";s:5:\"title\";s:52:\" MKN Run x KBS X KLCFM (Dataran DBKL, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"23 Aug\";s:5:\"title\";s:68:\" Batu Gajah Half Marathon (Silverlake Village Outlet, Batu Gajah) \";}i:3;a:2:{s:4:\"date\";s:6:\"21 Aug\";s:5:\"title\";s:48:\" Pahang ECO (Teluk Cempedak, Kuantan, Pahang) \";}i:4;a:2:{s:4:\"date\";s:6:\"16 Aug\";s:5:\"title\";s:46:\" Baker Tilly Run (Dataran DBKL, Kuala Lumpur)\";}i:5;a:2:{s:4:\"date\";s:6:\"16 Aug\";s:5:\"title\";s:58:\" Tangkak Merdeka Half Marathon (Dataran Tangkak, Johor) \";}i:6;a:2:{s:4:\"date\";s:6:\"14 Aug\";s:5:\"title\";s:52:\" Alor Setar Marathon (Alor Setar, Kedah) ⭐⭐⭐\";}i:7;a:2:{s:4:\"date\";s:6:\"14 Aug\";s:5:\"title\";s:61:\" Langkawi International Ultra Run (Langkawi Island, Kedah) \";}i:8;a:2:{s:4:\"date\";s:6:\"09 Aug\";s:5:\"title\";s:29:\"HALAL Run (Dataran Putrajaya)\";}i:9;a:2:{s:4:\"date\";s:6:\"08 Aug\";s:5:\"title\";s:74:\" Cultural Night Run (Jambatan Sultan Abdul Halim Mua\'dzam Shah, Penang) \";}i:10;a:2:{s:4:\"date\";s:6:\"02 Aug\";s:5:\"title\";s:78:\" IJM Allianz Duo Highway Challenge (NPE highway -Sunway, Selangor) ⭐⭐⭐\";}}}i:5;a:2:{s:5:\"label\";s:8:\"JUL 2026\";s:6:\"events\";a:14:{i:0;a:2:{s:4:\"date\";s:6:\"26 Jul\";s:5:\"title\";s:41:\" KLCC Half Marathon (KLCC, Kuala Lumpur)\";}i:1;a:2:{s:4:\"date\";s:6:\"26 Jul\";s:5:\"title\";s:42:\" KBS X KLCFM (Dataran DBKL, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"24 Jul\";s:5:\"title\";s:39:\" Kadamaian Ultra (Kota Belud, Sabah) \";}i:3;a:2:{s:4:\"date\";s:6:\"19 Jul\";s:5:\"title\";s:45:\" KL Balloon Run (Dataran DBKL, Kuala Lumpur)\";}i:4;a:2:{s:4:\"date\";s:6:\"19 Jul\";s:5:\"title\";s:69:\" SCORE Marathon by AIA Vitality - 21km & 42km (Putrajaya) ⭐⭐⭐\";}i:5;a:2:{s:4:\"date\";s:6:\"19 Jul\";s:5:\"title\";s:48:\" Seremban Half Powered By JomRun (Padang MPS) \";}i:6;a:2:{s:4:\"date\";s:6:\"18 Jul\";s:5:\"title\";s:68:\" SCORE Marathon by AIA Vitality - 5km & 10km (Putrajaya) ⭐⭐⭐\";}i:7;a:2:{s:4:\"date\";s:6:\"18 Jul\";s:5:\"title\";s:54:\" Cameron Ultra (Cameron Highlands, Pahang) ⭐⭐⭐\";}i:8;a:2:{s:4:\"date\";s:6:\"12 Jul\";s:5:\"title\";s:36:\"N8 Half Marathon (Dataran Putrajaya)\";}i:9;a:2:{s:4:\"date\";s:6:\"12 Jul\";s:5:\"title\";s:55:\"Ipoh Half Marathon (Jabatan Belia & Sukan Perak, Perak)\";}i:10;a:2:{s:4:\"date\";s:6:\"12 Jul\";s:5:\"title\";s:41:\" Sulaman Half Marathon (Tuaran, Sabah) \";}i:11;a:2:{s:4:\"date\";s:6:\"05 Jul\";s:5:\"title\";s:65:\" Miri Half Marathon (Marina Park City Miri City Hall, Sarawak) \";}i:12;a:2:{s:4:\"date\";s:6:\"04 Jul\";s:5:\"title\";s:53:\" Semenyih Ultra (Hulu Langat - Semenyih, Selangor) \";}i:13;a:2:{s:4:\"date\";s:6:\"04 Jul\";s:5:\"title\";s:63:\" King of Kemensah Trail (Kem Ghafar Baba, Kemensah Selangor) \";}}}i:6;a:2:{s:5:\"label\";s:8:\"JUN 2026\";s:6:\"events\";a:15:{i:0;a:2:{s:4:\"date\";s:6:\"28 Jun\";s:5:\"title\";s:34:\"IMMI Run Selangor (PKNS, Selangor)\";}i:1;a:2:{s:4:\"date\";s:6:\"28 Jun\";s:5:\"title\";s:42:\" KBS X KLCFM (Dataran DBKL, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"28 Jun\";s:5:\"title\";s:51:\" Sherun Half Marathon (Anjung Floria, Putrajaya) \";}i:3;a:2:{s:4:\"date\";s:6:\"27 Jun\";s:5:\"title\";s:40:\" CIDB Fun Run - Kuching (MBKS, Sarawak)\";}i:4;a:2:{s:4:\"date\";s:6:\"26 Jun\";s:5:\"title\";s:72:\" Mount Ophir Trail (Lagenda Taman Negara Gunung Ledang, Sagil, Johor) \";}i:5;a:2:{s:4:\"date\";s:6:\"20 Jun\";s:5:\"title\";s:68:\" Trail Des Gombak (Stadium Majlis Perbandaran Selayang, Selangor) \";}i:6;a:2:{s:4:\"date\";s:6:\"19 Jun\";s:5:\"title\";s:60:\" Kuala Lumpur 10K (KL10K) (Padang Merbok / Quill City Mall)\";}i:7;a:2:{s:4:\"date\";s:6:\"19 Jun\";s:5:\"title\";s:39:\" Skytrail Langkawi (Langkawi, Kedah) \";}i:8;a:2:{s:4:\"date\";s:6:\"14 Jun\";s:5:\"title\";s:52:\" Putrajaya Borneo Half Marathon (Dataran Putrajaya)\";}i:9;a:2:{s:4:\"date\";s:6:\"07 Jun\";s:5:\"title\";s:77:\" Hulu Selangor Half Marathon (Dataran Warisan, Kuala Kubu Bharu, Selangor) \";}i:10;a:2:{s:4:\"date\";s:6:\"07 Jun\";s:5:\"title\";s:76:\" Perak Multisport Festival Duathlon (Marina Island Pangkor, Lumut, Perak) \";}i:11;a:2:{s:4:\"date\";s:6:\"06 Jun\";s:5:\"title\";s:83:\" Durirun Balik Pulau International Marathon (Kompleks Sukan Balik Pulau, Penang) \";}i:12;a:2:{s:4:\"date\";s:6:\"06 Jun\";s:5:\"title\";s:77:\" Perak Multisport Festival Triathlon (Marina Island Pangkor, Lumut, Perak) \";}i:13;a:2:{s:4:\"date\";s:6:\"06 Jun\";s:5:\"title\";s:77:\" Perak Multisport Festival Aquathlon (Marina Island Pangkor, Lumut, Perak) \";}i:14;a:2:{s:4:\"date\";s:6:\"06 Jun\";s:5:\"title\";s:75:\" Perak Multisport Festival Fun Run (Marina Island Pangkor, Lumut, Perak) \";}}}i:7;a:2:{s:5:\"label\";s:8:\"MAY 2026\";s:6:\"events\";a:27:{i:0;a:2:{s:4:\"date\";s:6:\"31 May\";s:5:\"title\";s:69:\" Kuala Kangsar Half Marathon (Dataran Pavilion Kuala Kangsar, Perak)\";}i:1;a:2:{s:4:\"date\";s:6:\"24 May\";s:5:\"title\";s:47:\" Larian Tok Gajah (Dataran DBKL, Kuala Lumpur)\";}i:2;a:2:{s:4:\"date\";s:6:\"24 May\";s:5:\"title\";s:36:\" IMMI Run Selangor (PKNS, Selangor)\";}i:3;a:2:{s:4:\"date\";s:6:\"24 May\";s:5:\"title\";s:63:\" Tanjung Tuan Trail Run (Taman Eko Rimba Tanjung Tuan, Melaka)\";}i:4;a:2:{s:4:\"date\";s:6:\"24 May\";s:5:\"title\";s:74:\" Tanjung Sepat Run (Persatuan Buddhist Liulishan, Kuala Langat, Selangor)\";}i:5;a:2:{s:4:\"date\";s:6:\"23 May\";s:5:\"title\";s:52:\" 113 International Triathlon Desaru (Desaru, Johor)\";}i:6;a:2:{s:4:\"date\";s:6:\"23 May\";s:5:\"title\";s:56:\" Trans Mount Lambak Trail (Mount Lambak, Kluang, Johor)\";}i:7;a:2:{s:4:\"date\";s:6:\"17 May\";s:5:\"title\";s:49:\" Fruit Plus Fun Run (Dataran DBKL, Kuala Lumpur)\";}i:8;a:2:{s:4:\"date\";s:6:\"17 May\";s:5:\"title\";s:76:\" HSL Samarahan Half Marathon (La Promenade Mall, Kota Samarahan, Sarawak) \";}i:9;a:2:{s:4:\"date\";s:6:\"17 May\";s:5:\"title\";s:62:\" Miri City Mayfest Run (Marina Park City MBM Hall, Sarawak) \";}i:10;a:2:{s:4:\"date\";s:6:\"17 May\";s:5:\"title\";s:82:\" International Positive Energy Half Marathon (National Stadium Bukit Jalil, KL) \";}i:11;a:2:{s:4:\"date\";s:6:\"16 May\";s:5:\"title\";s:59:\" Janda Baik Ultra (Janda Baik, Bentong, Pahang) ⭐⭐⭐\";}i:12;a:2:{s:4:\"date\";s:6:\"15 May\";s:5:\"title\";s:60:\" Taiping24H (Dataran Kedamaian, Taman Tasik Taiping, Perak)\";}i:13;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:34:\" MWM Marathon (Dataran Putrajaya)\";}i:14;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:59:\" RSawit International Marathon (Sibu Town Square, Sarawak)\";}i:15;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:54:\" Pantai Remis Half Marathon (SMK Pantai Remis, Perak)\";}i:16;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:34:\" Silent Run 5.0 Bintulu (Sarawak)\";}i:17;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:60:\" Sesame Street Run (Kepong Metropolitan Park, Kuala Lumpur)\";}i:18;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:67:\" Mahir Run - Larian Rakan Muda 3.0 (Dataran Kemerdekaan Shah Alam)\";}i:19;a:2:{s:4:\"date\";s:6:\"10 May\";s:5:\"title\";s:49:\" Viper Challenge MAEPS (Maeps Serdang, Selangor)\";}i:20;a:2:{s:4:\"date\";s:6:\"03 May\";s:5:\"title\";s:56:\" Batu Kawa Fun Run (Baru Batu Kawa, Kuching, Sarawak) \";}i:21;a:2:{s:4:\"date\";s:6:\"02 May\";s:5:\"title\";s:69:\" Terengganu Marathon (Stadium Sultan Mizan Zainal Abidin) ⭐⭐⭐\";}i:22;a:2:{s:4:\"date\";s:6:\"02 May\";s:5:\"title\";s:49:\" Tuba Ocean Run (Tuba Island, Langkawi, Kedah) \";}i:23;a:2:{s:4:\"date\";s:6:\"02 May\";s:5:\"title\";s:60:\" Kapas Marang Swimathon ( Venue Kapas Island, Terengganu) \";}i:24;a:2:{s:4:\"date\";s:6:\"02 May\";s:5:\"title\";s:62:\" Silabur Ultra Trail (Kampung Batu Bedang, Serian, Sarawak) \";}i:25;a:2:{s:4:\"date\";s:6:\"01 May\";s:5:\"title\";s:69:\" Boulevard Fun Run (Open Parking, Imperial Hotel Bintulu, Sarawak) \";}i:26;a:2:{s:4:\"date\";s:6:\"01 May\";s:5:\"title\";s:59:\" Merapoh Rainforest Trail (Kuala Lipis, Pahang) ⭐⭐⭐\";}}}i:8;a:2:{s:5:\"label\";s:8:\"APR 2026\";s:6:\"events\";a:21:{i:0;a:2:{s:4:\"date\";s:6:\"26 Apr\";s:5:\"title\";s:84:\" KL City Day Half Marathon - 10th Anniversary (Kuala Lumpur City Centre) ⭐⭐⭐\";}i:1;a:2:{s:4:\"date\";s:6:\"26 Apr\";s:5:\"title\";s:73:\" KIP Eco Run (Mezzanine Floor, KipMall Kota Warisan, Sepang, Selangor) \";}i:2;a:2:{s:4:\"date\";s:6:\"26 Apr\";s:5:\"title\";s:64:\" Melaka World Heritage Half Marathon (Dataran Klebang Melaka) \";}i:3;a:2:{s:4:\"date\";s:6:\"25 Apr\";s:5:\"title\";s:77:\" Perlis International Marathon (Stadium Tunku Syed Putra, Perlis) ⭐⭐⭐\";}i:4;a:2:{s:4:\"date\";s:6:\"24 Apr\";s:5:\"title\";s:63:\" Titi Ultra (Padang Taman Titiwangsa, Hulu Langat, Selangor) \";}i:5;a:2:{s:4:\"date\";s:6:\"19 Apr\";s:5:\"title\";s:36:\" IMMI Run Selangor (PKNS, Selangor)\";}i:6;a:2:{s:4:\"date\";s:6:\"19 Apr\";s:5:\"title\";s:52:\" Kajang Ultra Marathon (Stadium Kajang, Selangor) \";}i:7;a:2:{s:4:\"date\";s:6:\"18 Apr\";s:5:\"title\";s:80:\" The Music Run by AFFIN, Kuala Lumpur (Bukit Jalil National Stadium) ⭐⭐⭐\";}i:8;a:2:{s:4:\"date\";s:6:\"18 Apr\";s:5:\"title\";s:61:\" Green Run Of Love (Central Park One Utama, Petaling Jaya) \";}i:9;a:2:{s:4:\"date\";s:6:\"18 Apr\";s:5:\"title\";s:69:\" Sarawak Sunrise Aerodance (Kuching, Miri, Sibu, Bintulu, Sarawak) \";}i:10;a:2:{s:4:\"date\";s:6:\"12 Apr\";s:5:\"title\";s:47:\" Putrajaya Twin Bridge Run (Dataran Putrajaya)\";}i:11;a:2:{s:4:\"date\";s:6:\"12 Apr\";s:5:\"title\";s:74:\" CAMRUN Cameron Night Run (Padang Awam MDCH, Cameron Highlands, Pahang) \";}i:12;a:2:{s:4:\"date\";s:6:\"12 Apr\";s:5:\"title\";s:67:\" Bumi Run- Larian Rakan Muda 3.0 (Dataran Kemerdekaan Shah Alam) \";}i:13;a:2:{s:4:\"date\";s:6:\"12 Apr\";s:5:\"title\";s:72:\" Butterworth Run (Dewan Dato\' Haji Ahmad Badawi, Butterworth, Penang) \";}i:14;a:2:{s:4:\"date\";s:6:\"11 Apr\";s:5:\"title\";s:64:\" IJM RIMBAYU Half Marathon Run with Me (The Arc, IJM Rimbayu) \";}i:15;a:2:{s:4:\"date\";s:6:\"11 Apr\";s:5:\"title\";s:50:\" IJM RIMBAYU Run With Me (The Arc, IJM Rimbayu) \";}i:16;a:2:{s:4:\"date\";s:6:\"11 Apr\";s:5:\"title\";s:65:\" Oxygen Rush Fun Run (Dataran Kemerdekaan Shah Alam, Selangor) \";}i:17;a:2:{s:4:\"date\";s:6:\"11 Apr\";s:5:\"title\";s:74:\" Hidden Village Trail Run (Hidden Village Trail, Ulu Tembeling, Pahang) \";}i:18;a:2:{s:4:\"date\";s:6:\"06 Apr\";s:5:\"title\";s:60:\" Pet Walk 2.0 (Majlis Bandaraya Kuching Selatan, Sarawak) \";}i:19;a:2:{s:4:\"date\";s:6:\"05 Apr\";s:5:\"title\";s:56:\" Cybercity Half Marathon (Persiaran Flora, Cyberjaya) \";}i:20;a:2:{s:4:\"date\";s:6:\"05 Apr\";s:5:\"title\";s:42:\" Anime Run (Dataran DBKL, Kuala Lumpur) \";}}}i:9;a:2:{s:5:\"label\";s:8:\"MAR 2026\";s:6:\"events\";a:4:{i:0;a:2:{s:4:\"date\";s:6:\"29 Mar\";s:5:\"title\";s:68:\" Race Against Cancer Run (Kepong Metropolitan Park, Kuala Lumpur) \";}i:1;a:2:{s:4:\"date\";s:6:\"29 Mar\";s:5:\"title\";s:74:\" La Promenade Ekiden Relay (La Promenade Mall, Kota Samarahan, Sarawak) \";}i:2;a:2:{s:4:\"date\";s:6:\"08 Mar\";s:5:\"title\";s:62:\" Berapit CNY Run (SJK(C) Perkampungan Berapit, Butterworth) \";}i:3;a:2:{s:4:\"date\";s:6:\"07 Mar\";s:5:\"title\";s:43:\" Putrajaya Night Run (Dataran Putrajaya) \";}}}i:10;a:2:{s:5:\"label\";s:8:\"FEB 2026\";s:6:\"events\";a:34:{i:0;a:2:{s:4:\"date\";s:6:\"28 Feb\";s:5:\"title\";s:46:\" KL Night Run (Padang Merbok, Kuala Lumpur) \";}i:1;a:2:{s:4:\"date\";s:6:\"28 Feb\";s:5:\"title\";s:61:\" Civil Defence Night Run (Setia Fontaines, Bertam, Penang) \";}i:2;a:2:{s:4:\"date\";s:6:\"15 Feb\";s:5:\"title\";s:86:\" Putrajaya Lake Half Marathon (Padang Floria @ Anjung Floria, Presint 4, Putrajaya) \";}i:3;a:2:{s:4:\"date\";s:6:\"15 Feb\";s:5:\"title\";s:58:\" Pantai Malindo Half Marathon (SRJK(C) Yu Chye, Penang) \";}i:4;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:73:\" Bukit Lagong Challenge (Taman Rimba Bukit Lagong, Selayang, Selangor) \";}i:5;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:80:\" Sarawak Color Night Run Festival (Majlis Bandaraya Kuching Selatan, Sarawak) \";}i:6;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:64:\" Cabaran Gunung Rajah (Kg Pertak, Kuala Kubu Bharu, Selangor) \";}i:7;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:81:\" Batu Bersurat Half Marathon (Bulatan Batu Bersurat, Kuala Berang, Terengganu) \";}i:8;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:57:\" World Cancer Day Run (Tasik Darul Aman, Jitra, Kedah) \";}i:9;a:2:{s:4:\"date\";s:6:\"14 Feb\";s:5:\"title\";s:65:\" Plogging Lata Medang (Kg Pertak, Kuala Kubu Bharu , Selangor) \";}i:10;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:59:\" MAIWP Run 2026 - Putrajaya (Dataran Putrajaya) ⭐⭐⭐\";}i:11;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:73:\" Subang Jaya Half Marathon (Arena MBSJ Subang Jaya, Selangor) ⭐⭐⭐\";}i:12;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:43:\" KLBAR Run (Padang Merbok, Kuala Lumpur) \";}i:13;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:75:\" Johor Bahru City Half Marathon (Aeon Mall Bandar Dato Onn, Johor Bahru) \";}i:14;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:78:\" BMW Wheelcorp Premium Charity Run (Aeon Mall Bandar Dato Onn, Johor Bahru) \";}i:15;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:54:\" Ultron® Wetlands Trail Run (Gamuda Cove, Dengkil) \";}i:16;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:57:\" The Miles For Miracle (Alam Damai Park, Kuala Lumpur) \";}i:17;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:57:\" Capybara Run (Aeon Mall Bandar Dato Onn, Johor Bahru) \";}i:18;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:55:\" Petronita Orchid Run & Ride (Tower 3 KLCC, Level 4) \";}i:19;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:69:\" Litar Run - Larian Rakan Muda 3.0 (Dataran Kemerdekaan Shah Alam) \";}i:20;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:61:\" Batu Maung Go Green Briswalk (Taman Jajar Sg Ara, Penang) \";}i:21;a:2:{s:4:\"date\";s:6:\"08 Feb\";s:5:\"title\";s:48:\" Cervivor Run (Dataran Kemerdekaan Shah Alam) \";}i:22;a:2:{s:4:\"date\";s:6:\"07 Feb\";s:5:\"title\";s:54:\" SA Sentral Neon Run (Galeria SA Sentral, Selangor) \";}i:23;a:2:{s:4:\"date\";s:6:\"07 Feb\";s:5:\"title\";s:49:\" Ang Pau Run! (Marina Park City Miri, Sarawak) \";}i:24;a:2:{s:4:\"date\";s:6:\"07 Feb\";s:5:\"title\";s:64:\" Asia Triathlon Cup Putrajaya (Kompleks Sukan Air, Putrajaya) \";}i:25;a:2:{s:4:\"date\";s:6:\"07 Feb\";s:5:\"title\";s:57:\" Kampung Nature Run (Ayer Itam, Tasek Gelugor, Penang) \";}i:26;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:42:\" KL 10 (Pavilion Kuala Lumpur) ⭐⭐⭐\";}i:27;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:64:\" Larian Hari Wilayah Persekutuan (Dataran DBKL, Kuala Lumpur) \";}i:28;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:55:\" New Year Fun Run - IPOH (AEON Mall Kinta City, Perak)\";}i:29;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:65:\" Selama Ultra (Padang Bola Kampung Masjid Ijok, Selama, Perak) \";}i:30;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:67:\" Runati Samarahan Hero Run (Summer Mall Kota Samarahan, Sarawak) \";}i:31;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:43:\" Run 4 Gaza (Persiaran Flora, Cyberjaya) \";}i:32;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:67:\" Malaysia Music Night Run (Pavilion Kuala Lumpur, Bukit Bintang) \";}i:33;a:2:{s:4:\"date\";s:6:\"01 Feb\";s:5:\"title\";s:63:\" Chinese New Year Run (Taman Tasik Titiwangsa, Kuala Lumpur) \";}}}i:11;a:2:{s:5:\"label\";s:8:\"JAN 2026\";s:6:\"events\";a:40:{i:0;a:2:{s:4:\"date\";s:6:\"31 Jan\";s:5:\"title\";s:73:\" Putrajaya Open Day 2026 - Langkah Ceria! (Dataran Putrajaya) ⭐⭐⭐\";}i:1;a:2:{s:4:\"date\";s:6:\"31 Jan\";s:5:\"title\";s:65:\" Malatra Ultra (Taman Wetland Putrajaya, Precinct 11, Putrajaya)\";}i:2;a:2:{s:4:\"date\";s:6:\"31 Jan\";s:5:\"title\";s:48:\" Neon Night Run (Mydin MITC Ayer Keroh, Melaka)\";}i:3;a:2:{s:4:\"date\";s:6:\"31 Jan\";s:5:\"title\";s:41:\" Ga’lok Run (Padang Blaze Arena, Paka)\";}i:4;a:2:{s:4:\"date\";s:6:\"30 Jan\";s:5:\"title\";s:54:\" Jerai Night Half Marathon (Gunung Jerai, Yan, Kedah)\";}i:5;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:65:\" Step Up! - Hari Wilayah (Dataran DBKL, Kuala Lumpur) ⭐⭐⭐\";}i:6;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:78:\" New Year Heroes Charity Run (Alam Damai Recreation Park, Tmn Alam Damai, KL)\";}i:7;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:61:\" Oasis Eye Health Carnival (Setia Fontaines, Bertam, Penang)\";}i:8;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:55:\" Bintulu Half Marathon (Dewan Suarah Bintulu, Sarawak)\";}i:9;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:63:\" Jesselton Half Marathon (Dataran Kompleks Sukan Likas, Sabah)\";}i:10;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:50:\" CUEPACS Perak Fun Run (Dataran MBI, Ipoh, Perak)\";}i:11;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:65:\" BMW Wheelcorp Premium Charity Walkathon (Sunsuria City, Sepang)\";}i:12;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:56:\" PSP King Of The Forest (Kerinchi Hill Forest Park, KL)\";}i:13;a:2:{s:4:\"date\";s:6:\"25 Jan\";s:5:\"title\";s:84:\" Kidslympic Malaysia Run CNY Spring Edition (Piazza, Level 3, Pavilion Bukit Jalil)\";}i:14;a:2:{s:4:\"date\";s:6:\"24 Jan\";s:5:\"title\";s:54:\" UiTM Ultra Vol.8 (Stadium, UiTM Shah Alam, Selangor)\";}i:15;a:2:{s:4:\"date\";s:6:\"24 Jan\";s:5:\"title\";s:56:\" Selayang Sunglass Run (Bandar Baru Selayang, Selangor)\";}i:16;a:2:{s:4:\"date\";s:6:\"24 Jan\";s:5:\"title\";s:59:\" BMW Lee Motors Kedah Run (Darul Aman Stadium, Alor Setar)\";}i:17;a:2:{s:4:\"date\";s:6:\"24 Jan\";s:5:\"title\";s:44:\" Capybara Run (Darul Aman Stadium, Kedah) \";}i:18;a:2:{s:4:\"date\";s:6:\"18 Jan\";s:5:\"title\";s:60:\" Twin City Marathon (Persiaran Flora, Cyberjaya) ⭐⭐⭐\";}i:19;a:2:{s:4:\"date\";s:6:\"18 Jan\";s:5:\"title\";s:49:\" PMG Health Run (Marina ParkCity, Miri, Sarawak)\";}i:20;a:2:{s:4:\"date\";s:6:\"18 Jan\";s:5:\"title\";s:70:\" Run For Life (Perdana Botanical Garden, Tasik Perdana, Kuala Lumpur)\";}i:21;a:2:{s:4:\"date\";s:6:\"18 Jan\";s:5:\"title\";s:50:\" New Year Fun Run - TAIPING (AEON Mall Taiping) \";}i:22;a:2:{s:4:\"date\";s:6:\"18 Jan\";s:5:\"title\";s:58:\" RUNLIGA Team Race (Wyndham Garden, Frasers Hill, Pahang)\";}i:23;a:2:{s:4:\"date\";s:6:\"11 Jan\";s:5:\"title\";s:54:\" Godzilla Run Malaysia (Dataran Putrajaya) ⭐⭐⭐\";}i:24;a:2:{s:4:\"date\";s:6:\"11 Jan\";s:5:\"title\";s:58:\" Kuching Half Marathon (Majlis Bandaraya Kuching Selatan)\";}i:25;a:2:{s:4:\"date\";s:6:\"11 Jan\";s:5:\"title\";s:62:\" Marlborough College (MCM) Triathlon (Iskandar Puteri, Johor)\";}i:26;a:2:{s:4:\"date\";s:6:\"11 Jan\";s:5:\"title\";s:66:\" Kwasa Damansara Hari Hari Fun Run (Taman Bandar Kwasa Damansara)\";}i:27;a:2:{s:4:\"date\";s:6:\"10 Jan\";s:5:\"title\";s:63:\" Watergate 24 Hours Special Edition (Kompleks Sukan Putrajaya)\";}i:28;a:2:{s:4:\"date\";s:6:\"10 Jan\";s:5:\"title\";s:50:\" Larian Inklusif Kasih (UiTM Shah Alam, Selangor)\";}i:29;a:2:{s:4:\"date\";s:6:\"10 Jan\";s:5:\"title\";s:45:\" Run For Wellness (Padang Kawad UTHM, Johor)\";}i:30;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:72:\" Melawati Run (Lorong Sarawak, Taman Melawati, Kuala Lumpur) ⭐⭐⭐\";}i:31;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:82:\" Bukit Kiara Ultra Challenge (Taman Rimba Bukit Kiara, TTDI, K.Lumpur) ⭐⭐⭐\";}i:32;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:67:\" Skyhawk Nature Run (Rumah Melaka, Bukit Katil, Melaka) ⭐⭐⭐\";}i:33;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:64:\" Bertam Half Marathon (Mydin Bertam, Kepala Batas, Butterworth)\";}i:34;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:64:\" New Year Time Trial Challenge Run (Persiaran Flora, Cyberjaya)\";}i:35;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:70:\" Golden Paddy Half Marathon (Dewan Seri Bernam Sungai Besar Selangor)\";}i:36;a:2:{s:4:\"date\";s:6:\"04 Jan\";s:5:\"title\";s:53:\" Nasi Lemak New Year Run (Marina Park City, Sarawak)\";}i:37;a:2:{s:4:\"date\";s:6:\"03 Jan\";s:5:\"title\";s:51:\" Kopi Lari 7km Morning Run (Darulaman Park, Kedah)\";}i:38;a:2:{s:4:\"date\";s:6:\"01 Jan\";s:5:\"title\";s:58:\" New Year Resolution Charity Run (MBKS, Kuching, Sarawak)\";}i:39;a:2:{s:4:\"date\";s:6:\"01 Jan\";s:5:\"title\";s:57:\" KL New Year Run (Dataran DBKL, Kuala Lumpur) ⭐⭐⭐\";}}}}s:5:\"error\";N;}',1769436614),('laravel-cache-tg_buddy_queue_18','a:2:{s:5:\"items\";a:4:{i:0;a:2:{s:2:\"id\";i:7;s:8:\"distance\";d:0.011154964846361142;}i:1;a:2:{s:2:\"id\";i:23;s:8:\"distance\";d:0.01147246387994216;}i:2;a:2:{s:2:\"id\";i:17;s:8:\"distance\";d:0.30746511375880275;}i:3;a:2:{s:2:\"id\";i:16;s:8:\"distance\";d:0.3165986256936272;}}s:5:\"index\";i:3;}',1769434178),('laravel-cache-tg_buddy_queue_23','a:2:{s:5:\"items\";a:3:{i:0;a:2:{s:2:\"id\";i:7;s:8:\"distance\";d:0.0015909219104740596;}i:1;a:2:{s:2:\"id\";i:17;s:8:\"distance\";d:0.31893693445263244;}i:2;a:2:{s:2:\"id\";i:16;s:8:\"distance\";d:0.3280692001775277;}}s:5:\"index\";i:3;}',1769405098),('laravel-cache-tg_like_inbox_17','a:1:{i:0;i:23;}',1769662496),('laravel-cache-tg_like_inbox_23','a:0:{}',1769691622),('laravel-cache-tg_like_inbox_7','a:1:{i:0;i:18;}',1769691555),('laravel-cache-tg_link_email_7362129382','b:1;',1769435298),('laravel-cache-tg_location_update_18','b:1;',1769436958),('laravel-cache-tg_review_pending_18','a:3:{s:10:\"session_id\";i:12;s:6:\"rating\";i:5;s:4:\"step\";s:7:\"comment\";}',1769447824),('laravel-cache-tg_session_reminder_sent_12_10','b:1;',1769440262),('laravel-cache-tg_session_reminder_sent_12_30','b:1;',1769440263),('laravel-cache-tg_session_reminder_sent_19_10','b:1;',1769457601),('laravel-cache-tg_session_reminder_sent_19_30','b:1;',1769457602),('laravel-cache-tg_session_review_reminder_sent_12','b:1;',1769461862),('laravel-cache-tg_session_review_reminder_sent_21','b:1;',1769463602);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
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
-- Table structure for table `email_tacs`
--

DROP TABLE IF EXISTS `email_tacs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_tacs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NOT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_tacs_email_purpose_index` (`email`,`purpose`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_tacs`
--

LOCK TABLES `email_tacs` WRITE;
/*!40000 ALTER TABLE `email_tacs` DISABLE KEYS */;
INSERT INTO `email_tacs` VALUES (2,'luqmanannuar4141@gmail.com','$2y$12$LZAtQIvXVYQSVwQC3JvrsuLdl5NXiJkHICAzN56ZsPbQJP3zZ0h56','register',0,'2026-01-22 00:57:20',NULL,'2026-01-22 00:47:20','2026-01-22 00:47:20'),(3,'hirsyah866@gmail.com','$2y$12$oewLMkuHWSwUmqTkwEJ.V.CB8XaC4RmOWyxyZT55Hmb/tAqLb2L/C','forgot_password',0,'2026-01-22 01:09:42',NULL,'2026-01-22 00:59:42','2026-01-22 00:59:42'),(6,'syahhafiz521@gmail.com','$2y$12$yJea5LnbfGKLvjAigV6vE.R1ZIO3Iyqr3fulp.zeNDS3Q2CRP7dk6','register',1,'2026-01-22 12:43:55','2026-01-22 12:36:01','2026-01-22 12:33:55','2026-01-22 12:36:01'),(11,'2024905569@student.uitm.edu.my','$2y$12$1uEW8dOqWhDM2x.D9gnu2e2uyyT2KmlK0MjhXnbJiFWsywsetzUQC','register',0,'2026-01-24 02:57:37',NULL,'2026-01-24 02:47:37','2026-01-24 02:47:37'),(22,'faizsofianshakawee@gmail.com','$2y$12$49Lkvd.9ol8jJMslFkeGnubpW0LLoFux7LcqD9e3hx4NkGu4xhnte','register',1,'2026-01-24 03:33:57','2026-01-24 03:24:51','2026-01-24 03:23:57','2026-01-24 03:24:51'),(23,'akmalimran407@gmail.com','$2y$12$Co9x/6457zSZNNbNCe96deosIQuktyaNybpmqha9T3QvMZ7igkPnG','register',1,'2026-01-24 03:55:39','2026-01-24 03:47:12','2026-01-24 03:45:39','2026-01-24 03:47:12'),(25,'darinnisrina0304@gmail.com','$2y$12$zcsij.s0qwavmud3AtU3keqEQMNqI7KDwhbMSMbcXdetT4wDwNDIO','register',1,'2026-01-24 04:04:34','2026-01-24 03:55:00','2026-01-24 03:54:34','2026-01-24 03:55:00'),(27,'2025215994@student.uitm.edu.my','$2y$12$g38OiMINOx5fs/bdEC80IecRuIYQgNm03Snb9XyKobOs47y1ll0yK','register',1,'2026-01-25 19:55:17','2026-01-25 19:47:20','2026-01-25 19:45:17','2026-01-25 19:47:20'),(28,'syafirulyusni27@gmail.com','$2y$12$zslSmndRkFbufUxVoqXV9eWt4otlATi4/or9Bf3OouHfFq8i62klK','register',1,'2026-01-25 20:57:18','2026-01-25 20:48:35','2026-01-25 20:47:18','2026-01-25 20:48:35'),(29,'wmfbwf@gmail.com','$2y$12$V2J5vLksEeOE.SNewwDaYOeFHGPyvMvo4oKYCiZo8wYl.zPVk/naW','register',1,'2026-01-26 06:00:23','2026-01-26 05:51:02','2026-01-26 05:50:23','2026-01-26 05:51:02');
/*!40000 ALTER TABLE `email_tacs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
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
-- Table structure for table `joined_sessions`
--

DROP TABLE IF EXISTS `joined_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `joined_sessions` (
  `jsession_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `invited_user_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'joined',
  `joined_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`jsession_id`),
  UNIQUE KEY `joined_sessions_session_id_user_id_unique` (`session_id`,`user_id`),
  KEY `joined_sessions_user_id_foreign` (`user_id`),
  KEY `joined_sessions_invited_user_id_foreign` (`invited_user_id`),
  CONSTRAINT `joined_sessions_invited_user_id_foreign` FOREIGN KEY (`invited_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `joined_sessions_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `running_sessions` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `joined_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `joined_sessions`
--

LOCK TABLES `joined_sessions` WRITE;
/*!40000 ALTER TABLE `joined_sessions` DISABLE KEYS */;
INSERT INTO `joined_sessions` VALUES (1,2,8,NULL,'joined',NULL),(3,1,1,NULL,'joined',NULL),(4,6,1,NULL,'joined',NULL),(6,6,13,NULL,'joined',NULL),(8,11,5,NULL,'joined',NULL),(9,14,16,NULL,'joined',NULL),(10,12,16,NULL,'joined',NULL),(12,13,19,NULL,'joined',NULL),(13,15,19,NULL,'joined',NULL),(14,17,18,NULL,'joined',NULL),(16,13,22,NULL,'joined',NULL),(19,15,22,NULL,'joined',NULL),(20,17,22,NULL,'joined',NULL),(21,16,22,NULL,'joined',NULL),(22,15,23,NULL,'joined',NULL),(23,12,23,NULL,'joined',NULL),(24,16,18,NULL,'joined',NULL),(25,13,18,NULL,'joined',NULL),(26,19,18,NULL,'joined',NULL),(27,12,18,NULL,'joined',NULL),(28,15,18,NULL,'joined',NULL),(29,16,24,NULL,'joined',NULL);
/*!40000 ALTER TABLE `joined_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (13,'0001_01_01_000000_create_users_table',1),(14,'0001_01_01_000001_create_cache_table',1),(15,'0001_01_01_000002_create_jobs_table',1),(16,'2025_07_21_082153_create_running_sessions_table',1),(17,'2025_07_21_092234_create_joined_sessions_table',1),(18,'2025_11_04_085031_fix_joined_sessions_foreign_key',1),(19,'2025_11_11_000000_add_telegram_id_to_users_table',1),(20,'2025_11_11_100000_add_profile_fields_to_users_table',1),(21,'2025_12_09_224954_create_user_locations_table',1),(22,'2025_12_10_000000_add_invited_user_and_status_to_joined_sessions',1),(23,'2025_12_17_000001_add_lat_lng_to_running_sessions',1),(24,'2025_12_17_000002_create_session_reviews_table',1),(25,'2025_12_17_000003_add_unique_telegram_id_to_users_table',1),(26,'2025_12_31_014726_fix_telegram_state_null_values',1),(27,'2026_01_31_000001_add_activity_to_running_sessions_table',2),(28,'2026_02_01_000002_create_buddy_likes_table',2),(29,'2026_02_01_000001_create_user_reports_table',3),(30,'2026_02_01_000002_add_phone_number_to_users_table',3),(31,'2026_02_01_000003_create_email_tacs_table',3),(32,'2026_01_26_000002_add_telegram_username_to_users_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `running_sessions`
--

DROP TABLE IF EXISTS `running_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `running_sessions` (
  `session_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `average_pace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_lat` decimal(10,7) DEFAULT NULL,
  `location_lng` decimal(10,7) DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `running_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `running_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `running_sessions`
--

LOCK TABLES `running_sessions` WRITE;
/*!40000 ALTER TABLE `running_sessions` DISABLE KEYS */;
INSERT INTO `running_sessions` VALUES (1,2,'2026-01-10 07:30:00','2026-01-10 10:30:00','6','3 hour',NULL,'Padang kawad, Uitm Jasin',2.2249600,102.4566150,'2026-01-09 23:30:00','2026-01-10 02:30:00','2025-12-30 10:23:03','2026-01-26 05:25:04'),(2,4,'2025-12-31 07:04:00','2025-12-31 10:04:00','7:00/km - 6:00/km','3 hour',NULL,'Padang kawad Uitm, Jasin, Melaka (Malacca)',2.2249600,102.4566150,'2025-12-30 23:04:00','2025-12-31 02:04:00','2025-12-30 11:04:49','2026-01-26 05:25:04'),(3,4,'2025-12-31 20:00:00','2025-12-31 22:00:00','8:00/km - 7:00/km','2 hour',NULL,'Dataran orang ramai Merlimau,Jasin, Melaka (Malacca)',2.2249600,102.4566150,'2025-12-31 12:00:00','2025-12-31 14:00:00','2025-12-30 11:29:43','2026-01-26 05:25:04'),(4,4,'2025-12-31 08:00:00','2025-12-31 10:00:00','6:00/km - 5:00/km','2 hour',NULL,'Padang kawad Uitm, Jasin, Melaka (Malacca)',2.2249600,102.4566150,'2025-12-31 00:00:00','2025-12-31 02:00:00','2025-12-30 11:45:20','2026-01-26 05:25:04'),(5,5,'2026-01-02 08:00:00','2026-01-02 10:00:00','6:00/km - 5:00/km','2hour',NULL,'Padang Kawad Uitm,Jasin, Melaka (Malacca)',2.2249600,102.4566150,'2025-12-30 22:33:19','2025-12-30 22:33:23','2025-12-30 12:30:10','2025-12-30 22:33:23'),(6,5,'2026-01-15 21:00:00','2026-01-15 22:00:00','5:00/km - 4:00/km','1hour',NULL,'Kolej TS Lanang Uitm Jasin, Melaka (Malacca)',2.2249600,102.4566150,'2026-01-15 13:00:00','2026-01-15 14:00:00','2025-12-30 12:32:03','2026-01-26 05:25:04'),(7,8,'2025-12-31 20:00:00','2025-12-31 22:00:00','8:00/km - 7:00/km','2hour',NULL,'Kolej TS Lanang, Jasin, Melaka (Malacca)',2.2280331,102.4565891,'2025-12-31 12:00:00','2025-12-31 14:00:00','2025-12-30 20:44:52','2026-01-26 05:25:04'),(8,5,'2025-12-31 20:30:00','2025-12-31 22:30:00','8:00/km - 7:00/km','2hour',NULL,'Padang Kawad Uitm,Jasin, Melaka (Malacca)',2.2280389,102.4566045,'2025-12-31 12:30:00','2025-12-31 14:30:00','2025-12-30 22:31:12','2026-01-26 05:25:04'),(9,13,'2025-12-31 16:00:00','2025-12-31 19:00:00','8:00/km - 7:00/km','3 hour',NULL,'Padang Kawad Uitm,Jasin, Melaka (Malacca)',2.2280243,102.4565795,'2025-12-31 08:00:00','2025-12-31 11:00:00','2025-12-30 23:10:12','2026-01-26 05:25:04'),(10,5,'2026-01-20 06:00:00','2026-01-20 08:00:00','6:00/km - 5:00/km','120 min','Long Run','Jasin, Melaka',2.2246140,102.4559110,'2026-01-19 22:00:00','2026-01-20 00:00:00','2026-01-20 13:09:24','2026-01-26 05:25:04'),(11,5,'2026-01-21 18:00:00','2026-01-21 19:00:00','6:00/km - 5:00/km','60 min','5km','Jasin, Melaka',2.2246140,102.4559110,'2026-01-21 10:00:00','2026-01-21 11:00:00','2026-01-20 13:25:20','2026-01-26 05:25:04'),(12,17,'2026-01-26 13:11:00','2026-01-26 15:11:00','9:00/km - 8:00/km','2 hours','5km','Padang kawad, Uitm Jasin, Melaka (Malacca)',2.2273060,102.4559250,'2026-01-26 05:11:00','2026-01-26 07:11:00','2026-01-21 21:11:53','2026-01-26 07:11:02'),(13,17,'2026-01-30 13:13:00','2026-01-30 14:13:00','5:00/km - 4:00/km','1 hour','5km','Padang kawad, Uitm Jasin, Melaka (Malacca)',2.2273079,102.4559478,NULL,NULL,'2026-01-21 21:14:25','2026-01-21 21:14:25'),(14,17,'2026-01-22 14:00:00','2026-01-22 14:30:00','5:00/km - 4:00/km','30 minutes','5km','Padang kawad, Uitm Jasin, Melaka (Malacca)',2.2273045,102.4559449,'2026-01-22 06:00:00','2026-01-22 06:30:00','2026-01-21 21:47:21','2026-01-26 05:25:04'),(15,16,'2026-01-30 07:30:00','2026-01-30 10:30:00','7:00/km - 6:00/km','3 hours','Long Run','Dataran Seri Jempol, Negeri Sembilan',2.2274426,102.4558397,NULL,NULL,'2026-01-21 23:00:04','2026-01-21 23:00:04'),(16,19,'2026-02-28 20:30:00','2026-02-28 22:30:00','8:00/km - 7:00/km','2 hours','10km','Padang kawad uitm shah alam, Selangor',3.0650331,101.5056539,NULL,NULL,'2026-01-24 03:44:08','2026-01-24 03:44:08'),(17,20,'2026-02-28 19:47:00','2026-02-28 21:00:00','9:00/km - 8:00/km','1 hour 13 minutes','10km','Padang kawad uitm shah alam, Selangor',3.0650362,101.5056547,NULL,NULL,'2026-01-24 03:52:21','2026-01-24 03:52:21'),(18,18,'2026-01-26 20:25:00','2026-01-26 22:25:00','10:00/km - 9:00/km','2 hours','10km','Dataran orang kampung, Merlimau, Johor',2.2247500,102.4559600,'2026-01-25 19:33:24','2026-01-25 19:33:29','2026-01-25 19:27:15','2026-01-25 19:33:29'),(19,23,'2026-01-26 18:00:00','2026-01-26 18:30:00','8:00/km - 7:00/km','30 min','5km','Jasin, Melaka',2.2245740,102.4559600,'2026-01-26 10:00:00',NULL,'2026-01-25 20:56:53','2026-01-26 10:00:02'),(20,18,'2026-02-27 04:35:00','2026-02-27 05:35:00','10:00/km - 9:00/km','1 hour','5km','Masjid baru kampung mendapat, Jasin, Melaka (Malacca)',2.2247500,102.4559600,NULL,NULL,'2026-01-25 21:40:01','2026-01-25 21:40:01'),(21,18,'2026-01-26 13:40:00','2026-01-26 15:40:00','8:00/km - 7:00/km','2 hours','10km','Dataran orang ramai Merlimau, Melaka, Melaka (Malacca)',2.2247500,102.4559600,'2026-01-25 21:43:20','2026-01-25 21:44:13','2026-01-25 21:42:00','2026-01-25 21:44:13');
/*!40000 ALTER TABLE `running_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session_reviews`
--

DROP TABLE IF EXISTS `session_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `running_session_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL DEFAULT '5',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_reviews_running_session_id_foreign` (`running_session_id`),
  KEY `session_reviews_user_id_foreign` (`user_id`),
  CONSTRAINT `session_reviews_running_session_id_foreign` FOREIGN KEY (`running_session_id`) REFERENCES `running_sessions` (`session_id`) ON DELETE CASCADE,
  CONSTRAINT `session_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session_reviews`
--

LOCK TABLES `session_reviews` WRITE;
/*!40000 ALTER TABLE `session_reviews` DISABLE KEYS */;
INSERT INTO `session_reviews` VALUES (1,5,5,1,'Good session','2025-12-30 22:33:59','2025-12-30 22:33:59');
/*!40000 ALTER TABLE `session_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
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
INSERT INTO `sessions` VALUES ('iULKSgHGGkcxANEyE7anUdykd6HOKFhol0JZanmf',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTzZKZmtLOXFCUnBkWHBaWk9mQ0t4SDZBQXQybmNTZkgwaVp2UmhUdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1767156335),('qKuDH5ORLkeIN62lohFKTBSz3GFp1REnYzymbVM2',5,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZGNDSjU4WFFid1g3QU9ubkVEb3puOHhHMlJpS3RvcXh4T0cxa1VsaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9idWRkeS1tYXRjaCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==',1767167860),('xMg725IJC0rNTwqjbdSdmnRuRZfefp7qnfCTrqTo',14,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYVlkeDAwdFJLazNWMlJDTUM1SlNSVVQwaVdPdFdoa05xRnBHNjVBYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWdpc3RlciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE0O30=',1768920523),('YBiqcWeG2QQ72rpiP0NSkJNDqavrUATUAXWm9fSh',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiT2FkQjUycG9xZ09lV3VoTTdHeThLVWU4SFRxeER3aWtrSzZRMm1wUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1767155832);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_locations`
--

DROP TABLE IF EXISTS `user_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `telegram_user_id` bigint NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_locations_telegram_user_id_index` (`telegram_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_locations`
--

LOCK TABLES `user_locations` WRITE;
/*!40000 ALTER TABLE `user_locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_reports`
--

DROP TABLE IF EXISTS `user_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reporter_id` bigint unsigned DEFAULT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_reports_reporter_id_foreign` (`reporter_id`),
  KEY `user_reports_target_id_foreign` (`target_id`),
  CONSTRAINT `user_reports_reporter_id_foreign` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_reports_target_id_foreign` FOREIGN KEY (`target_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_reports`
--

LOCK TABLES `user_reports` WRITE;
/*!40000 ALTER TABLE `user_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avg_pace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` text COLLATE utf8mb4_unicode_ci,
  `strava_screenshot` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telegram_state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'initial',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_telegram_id_unique` (`telegram_id`),
  UNIQUE KEY `users_phone_number_unique` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Syahir Hafiz','syahir@gmail.com',NULL,NULL,NULL,'Male','6:00/km - 5:00/km','{\"state\":\"Negeri Sembilan\",\"latitude\":\"2.2249600\",\"longitude\":\"102.4566150\"}','profile_photos/ph6bmiedvIFoFseXRxooNtyvYZf1hIItcrc1mHoM.jpg','initial',NULL,'$2y$12$S0BVrJFLVwEJDYetDm7DuuPs.3BzgZzjz640LV6Z0AXqOGT0DoQam',NULL,'2025-12-30 10:16:42','2025-12-30 13:42:17',1),(2,'Admin','admin@gmail.com',NULL,NULL,NULL,'Male','8','{\"state\":\"Negeri Sembilan\",\"latitude\":\"2.2249600\",\"longitude\":\"102.4566150\"}',NULL,'initial',NULL,'$2y$12$nVX/D71GQrHCfcRvQghq/.hTPnYWRZlSdhJi.gRIdHj12dhjK15LW',NULL,'2025-12-30 10:21:47','2026-01-21 23:32:56',1),(3,'Nurul Hannah','hannah@gmail.com',NULL,'496255801',NULL,'Female','8','{\"state\":\"Johor\",\"latitude\":\"2.2249600\",\"longitude\":\"102.4566150\"}',NULL,'waiting_photo',NULL,'$2y$12$VIV2YJyIvcoi/i1IXB6ywutPA2ZqLCLJ9LEY5ndm3NliCB5aYWgxu',NULL,'2025-12-30 10:32:42','2026-01-20 09:04:35',0),(4,'Syah Sem 2','hirsyah866@gmail.com',NULL,NULL,NULL,'Male','8','{\"state\":\"Melaka (Malacca)\",\"latitude\":\"2.2249600\",\"longitude\":\"102.4566150\"}',NULL,'initial',NULL,'$2y$12$k5qS1zkT9KLVBgQ/qelcdu3geLZ1MK4skqk6BU1YuiHwinNnMJPzO',NULL,'2025-12-30 11:03:21','2026-01-22 00:02:31',1),(5,'Syah Hensem','syahsem@gmail.com',NULL,NULL,NULL,'Male','5:00/km - 4:00/km','{\"latitude\":2.224614,\"longitude\":102.455911,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-20 20:31:53\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1768935838/stridesync/telegram_profiles/zpnrr57byukqrqskjgwq.jpg','unlinked',NULL,'$2y$12$gEcqirRErJ7ecSxwsP5ejuK9liA4eiTgvUs/OG3HLw7umB9PXcZ2G',NULL,'2025-12-30 12:08:57','2026-01-25 20:34:47',0),(7,'Naqib Zulkefle','Naqibzul@gmail.com',NULL,'1046457851',NULL,'Male','5:00/km - 4:00/km','{\"latitude\":2.224577,\"longitude\":102.455946,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-21 03:14:28\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1768965150/stridesync/telegram_profiles/mrdwzxgyrol3hzvl93vv.jpg','profile_complete',NULL,'$2y$12$tce4YpjwfXs6Z0K2pnSyZOWx..s4Lzlzlmejx5yHiTGZeOAANLE.O',NULL,'2025-12-30 19:13:11','2026-01-20 19:14:28',0),(8,'Hidayatullah','Dayat@gmail.com',NULL,'1144674503',NULL,'Male','7:00/km - 6:00/km','{\"state\":\"Melaka (Malacca)\",\"latitude\":\"2.2280281\",\"longitude\":\"102.4565851\"}',NULL,'waiting_photo',NULL,'$2y$12$CXK8TGatHpm/g769xiWtKOdKPc53ax9h3ZM/oKJGNYWlTirSRihEe',NULL,'2025-12-30 20:41:45','2025-12-30 20:49:52',0),(9,'Izzyani','izz@gmail.com',NULL,'725605225',NULL,'Female','10:00/km - 9:00/km','{\"state\":\"Perak\",\"latitude\":\"2.2280306\",\"longitude\":\"102.4565923\"}',NULL,'waiting_photo',NULL,'$2y$12$Ba5mZKvSGPxhuU7EZb2soOi4Jzs8/ICyYxkNGfF2w5wmxyQ5NAaHm',NULL,'2025-12-30 21:46:24','2025-12-30 21:53:31',0),(10,'Afiq','Afiq@gmail.com',NULL,'764490895',NULL,'Male','9:00/km - 8:00/km','{\"state\":\"Melaka (Malacca)\",\"latitude\":\"2.2280282\",\"longitude\":\"102.4565910\"}','telegram_profiles/10_1767160564.jpg','profile_complete',NULL,'$2y$12$1tV37hCwHyW37hLm9LwRie8klp3xamG4xaVLUSKEh0Jz9tuQuRIBy',NULL,'2025-12-30 21:50:01','2025-12-30 21:56:05',0),(11,'Saaeb Subre','saeeb@gmail.com',NULL,'702404505',NULL,'Male','5:00/km - 4:00/km','{\"state\":\"Selangor\",\"latitude\":\"2.2280366\",\"longitude\":\"102.4565996\"}',NULL,'waiting_photo',NULL,'$2y$12$85jmr9YsITqcRNefdhBAMOkLKf8Nr//kr2djQBeuJKW9D/kFhKiSu',NULL,'2025-12-30 22:10:24','2025-12-30 22:12:32',0),(12,'fikri meran','fikri@gmail.com',NULL,NULL,NULL,'Male','7:00/km - 6:00/km','{\"state\":\"Negeri Sembilan\",\"latitude\":\"2.2280370\",\"longitude\":\"102.4566048\"}','profile_photos/EJjqI2KkxPfXPDaPeIefXovt1Wvg3kr523ZCBTjy.jpg','initial',NULL,'$2y$12$kx7hcrh66kqCD0IKNTBAouOtPauw3G.UpoBHi4YSEuUphX0xIQCB6',NULL,'2025-12-30 22:28:01','2025-12-30 22:28:01',0),(13,'Naufal Hafizul','naufal@gmail.com',NULL,NULL,NULL,'Male','7:00/km - 6:00/km','{\"state\":\"Kedah\",\"latitude\":\"2.2280337\",\"longitude\":\"102.4565923\"}',NULL,'initial',NULL,'$2y$12$dHoI1CGFCA5zrkp3WftUU.jFWRL3vP/gCOHXav3KyLHf0UHa6LnSm',NULL,'2025-12-30 23:07:20','2025-12-30 23:07:20',0),(14,'Memun','Memun@gmail.com',NULL,NULL,NULL,'Female','7:00/km - 6:00/km','{\"state\":\"Melaka (Malacca)\",\"latitude\":\"2.2245580\",\"longitude\":\"102.4560952\"}',NULL,'initial',NULL,'$2y$12$DXSmP9Td.pFM55X8.D9yjuS9DJO3oDwxGVn6UKX8K3JxyCA1Ao3Ba',NULL,'2026-01-20 06:48:39','2026-01-20 06:48:39',0),(16,'Luqman Annuar','luqmannuar@gmail.com',NULL,'910691984',NULL,'Male','4:00/km - 3:00/km','{\"latitude\":2.227522,\"longitude\":102.455841,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-22 06:06:14\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769062006/stridesync/telegram_profiles/ndfansbtqbhmsmfnxsbz.jpg','profile_complete',NULL,'$2y$12$.uzgDtTZ1PvESBYK8vuLi.LL77zgWMD6yTnF4JYx8RQlRBs3AVwtW',NULL,'2026-01-21 21:02:47','2026-01-21 22:06:47',0),(17,'Fikri Meran','fikrimeran@gmail.com',NULL,'662770158',NULL,'Male','7:00/km - 6:00/km','{\"latitude\":2.227439,\"longitude\":102.455823,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-22 05:55:06\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769062113/stridesync/telegram_profiles/vattzbr9c8vevmntncfl.jpg','profile_complete',NULL,'$2y$12$w2j4vPdIMzjvRj.lpmrHZ.Z2.8295nCJka10duVoGHLCqXaQyYFWW',NULL,'2026-01-21 21:04:45','2026-01-21 22:08:34',0),(18,'Syah Hensem','syahhafiz521@gmail.com','011-69660349','1487856163',NULL,'Male','5:00/km - 4:00/km','{\"latitude\":2.224677,\"longitude\":102.455954,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-26 05:49:13\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769406695/stridesync/telegram_profiles/jykl3klkf4sc1kf1utwd.jpg','profile_complete',NULL,'$2y$12$l.cANBfiWg14eAkhKiqJYuH6f8X6dsXROXfwX5aEVU8aBu6KUKqKq',NULL,'2026-01-22 12:36:02','2026-01-26 06:05:12',0),(19,'Faiz Sofian cute','faizsofianshakawee@gmail.com','013-9051361','714762988',NULL,'Male','4:00/km - 3:00/km','{\"state\":\"Pahang\",\"area\":\"3.06504, 101.50566\",\"latitude\":\"3.0650363\",\"longitude\":\"101.5056566\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769254500/stridesync/telegram_profiles/fqouevqlrtgrwulb2gry.jpg','profile_complete',NULL,'$2y$12$Tb5PvQP1EbimWz4ps3AIJ.frqZ6gpAqvU.lypzReL8uef/yiR5Zfq',NULL,'2026-01-24 03:24:52','2026-01-24 03:35:00',0),(20,'Imran Fanz','akmalimran407@gmail.com','012-2212121','5303706494',NULL,'Male','10:00/km - 9:00/km','{\"latitude\":3.065135,\"longitude\":101.505592,\"city\":\"Shah Alam\",\"state\":\"Selangor\",\"updated_at\":\"2026-01-24 11:50:54\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769255605/stridesync/telegram_profiles/kdgzlyixg0ynezj0btft.jpg','profile_complete',NULL,'$2y$12$4n0N8h5W3TtuFGOTzF1NNuHfpO78nKwrDM1ARPna/v5qzJjqRKH0W',NULL,'2026-01-24 03:47:13','2026-01-24 03:53:24',0),(21,'Darin miku','darinnisrina0304@gmail.com','013-905130123','756419198',NULL,'Female','4:00/km - 3:00/km','{\"state\":\"Negeri Sembilan\",\"area\":\"3.07156, 101.49621\",\"latitude\":\"3.0715550\",\"longitude\":\"101.4962050\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769256089/stridesync/telegram_profiles/g2qsoktch8fngdx4lpzx.jpg','profile_complete',NULL,'$2y$12$SDeaiz6ShGMwFdQoZXpTFuksYt75W4lrZgvrB0PdOF94IM4Wswil2',NULL,'2026-01-24 03:55:00','2026-01-24 04:01:28',0),(22,'Muz Run','2025215994@student.uitm.edu.my','013-5473026',NULL,NULL,NULL,NULL,'{\"state\":\"Melaka (Malacca)\",\"area\":\"Jasin, Melaka\",\"latitude\":\"2.2247500\",\"longitude\":\"102.4559600\"}',NULL,'initial',NULL,'$2y$12$6QevAH/HLGJbcA53nlJ6EOhon4/1rVBhhmD8S.Brlwrqnxz7yJsVy',NULL,'2026-01-25 19:47:20','2026-01-25 19:47:20',0),(23,'Iqbal k','syafirulyusni27@gmail.com','017-9073827','735039371',NULL,'Male','8:00/km - 7:00/km','{\"latitude\":2.224574,\"longitude\":102.45596,\"city\":\"Jasin\",\"state\":\"Melaka\",\"updated_at\":\"2026-01-26 04:51:56\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769403193/stridesync/telegram_profiles/ai7yvymyanobf47vjgk6.jpg','profile_complete',NULL,'$2y$12$vZ.oQmflAA9UfKPy8Sf7g..8K7FnnmgOKNpsBmZADpKSXY0TW4HpK',NULL,'2026-01-25 20:48:36','2026-01-25 20:53:13',0),(24,'Wan Muhammad Faiz Bin Wan Faizal','wmfbwf@gmail.com','019-9537706','1881067471','Faiz_Faizal','Male','7:00/km - 6:00/km','{\"state\":\"Melaka (Malacca)\",\"area\":\"Jasin, Melaka\",\"latitude\":\"2.2247500\",\"longitude\":\"102.4559600\"}','https://res.cloudinary.com/dcdwb1n3u/image/upload/v1769438129/stridesync/telegram_profiles/qaawf6zgdgjmpri0g1ul.jpg','profile_complete',NULL,'$2y$12$0MUWfFEjyBt0tWYKwuHAhe8PrRR/moL5W1xFOf451tkZ6SUhstW0C',NULL,'2026-01-26 05:51:03','2026-01-26 06:35:30',0);
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

-- Dump completed on 2026-01-27  2:26:42
