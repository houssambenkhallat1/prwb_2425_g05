-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: prwb_2425_g05
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `answers`
--

LOCK TABLES `answers` WRITE;
/*!40000 ALTER TABLE `answers` DISABLE KEYS */;
INSERT INTO `answers` VALUES (3,39,'cat'),(4,39,'dog'),(5,39,'pony'),(6,39,'dog'),(7,39,'dog'),(8,39,'dog'),(9,39,'cat'),(11,40,'Benoît Penelle'),(11,41,'bepenelle@epfc.eu'),(11,42,'1968-01-01'),(11,43,'No comment ;-)'),(12,40,'Not your business'),(12,41,'not.your@business.com'),(12,42,'0001-01-01'),(12,43,'Nope'),(13,40,'Bob'),(13,41,'bob@sponge.com'),(13,42,'1980-12-12'),(13,43,'');
/*!40000 ALTER TABLE `answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `form_colors`
--

LOCK TABLES `form_colors` WRITE;
/*!40000 ALTER TABLE `form_colors` DISABLE KEYS */;
INSERT INTO `form_colors` VALUES (1,'blue'),(15,'blue'),(16,'blue'),(16,'red');
/*!40000 ALTER TABLE `form_colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forms`
--

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;
INSERT INTO `forms` VALUES (1,'Data sheet','This form is intended to collect your administrative information.',1,1),(2,'Empty form',NULL,1,0),(3,'Empty public form','Test form 2',2,1),(14,'Long private form',NULL,1,0),(15,'Short public form','Short test form',1,1),(16,'A short test form',NULL,1,1);
/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `instances`
--

LOCK TABLES `instances` WRITE;
/*!40000 ALTER TABLE `instances` DISABLE KEYS */;
INSERT INTO `instances` VALUES (3,15,1,'2024-11-13 07:43:47','2024-11-13 07:43:52'),(4,15,1,'2024-11-13 07:43:57','2024-11-13 07:44:01'),(5,15,4,'2024-11-13 07:44:11','2024-11-13 07:44:17'),(6,15,2,'2024-11-13 07:44:49','2024-11-13 07:44:53'),(7,15,2,'2024-11-13 07:45:01','2024-11-13 07:45:04'),(8,15,3,'2024-11-13 07:45:16','2024-11-13 07:45:20'),(9,15,6,'2024-11-13 07:45:38','2024-11-13 07:45:43'),(10,15,6,'2024-11-13 07:45:50',NULL),(11,16,1,'2024-11-15 16:50:31','2024-11-15 16:52:06'),(12,16,6,'2024-11-15 16:52:30','2024-11-15 16:53:57'),(13,16,6,'2024-11-15 16:54:05','2024-11-15 16:55:00');
/*!40000 ALTER TABLE `instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,1,1,'Your last name?','Your last name','short',1),(2,1,2,'Your first name?','Your first name','short',1),(3,1,3,'Your date of birth?','Your date of birth','date',1),(4,1,4,'Your place of birth?','Your place of birth','short',0),(6,1,5,'Your address?','Your address','short',0),(7,1,6,'Your postal code?','Your postal code','short',0),(8,1,7,'Your city?','Your city of residence','short',0),(10,1,8,'Your phone number?','Your phone number','short',0),(11,1,9,'Your email address?','Your email address','email',1),(15,1,10,'Your profession?','Your profession','short',0),(16,1,11,'Do you have any comments for us?',NULL,'long',0),(19,14,1,'Question 1',NULL,'short',1),(20,14,2,'Question 2',NULL,'long',1),(24,14,3,'Question 3',NULL,'date',1),(26,14,4,'Question 4',NULL,'email',1),(28,14,5,'Question 5',NULL,'short',0),(29,14,6,'Question 6',NULL,'long',0),(33,14,7,'Question 7',NULL,'date',0),(35,14,8,'Question 8',NULL,'email',0),(39,15,1,'Favorite animal','in lowercase, please','short',0),(40,16,1,'Your name ?',NULL,'short',1),(41,16,2,'Your email address ?',NULL,'email',1),(42,16,3,'Your birth date ?','Not required, but helpful','date',0),(43,16,4,'Any suggestions ?','Do you have suggestions for us ?','long',0);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user_form_accesses`
--

LOCK TABLES `user_form_accesses` WRITE;
/*!40000 ALTER TABLE `user_form_accesses` DISABLE KEYS */;
INSERT INTO `user_form_accesses` VALUES (1,3,'editor'),(2,2,'editor'),(2,14,'editor'),(4,1,'editor'),(4,2,'editor'),(4,3,'editor'),(4,14,'user');
/*!40000 ALTER TABLE `user_form_accesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Benoît Penelle','bepenelle@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','user'),(2,'Marc Michel','mamichel@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','user'),(3,'Xavier Pigeolet','xapigeolet@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','user'),(4,'Boris Verhaegen','boverhaegen@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','user'),(5,'Administrator','admin@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','admin'),(6,'Anonymous User','guest@epfc.eu','$2y$10$s63N0AZ.LA5/3O05jNANZ.AAyYPKJH7IityYwOzehXKOqXWAW84xi','guest');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-15 10:35:28
