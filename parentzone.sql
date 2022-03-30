-- MySQL dump 10.13  Distrib 8.0.28, for Win64 (x86_64)
--
-- Host: localhost    Database: parentzone
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `registrants`
--

DROP TABLE IF EXISTS `registrants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registrants` (
  `id` int NOT NULL AUTO_INCREMENT,
  `firstname` text,
  `lastname` text,
  `email` text,
  `password` varchar(60) DEFAULT NULL,
  `gender` text,
  `created` int unsigned DEFAULT NULL,
  `lastlogin` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The database of Parent Zone registrants (technical challenge).';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registrants`
--

LOCK TABLES `registrants` WRITE;
/*!40000 ALTER TABLE `registrants` DISABLE KEYS */;
INSERT INTO `registrants` VALUES (1,'Barry','Johnson','barry.johnson@gmail.com','$2y$12$X.iCreZmhPbK08.1H6wiL.DMMwtDo3jcPS2y/USMgm1s0vNp78n7q','male',1234,1233),(2,'Daniel','Benton','nitrxgen@gmail.com','$2y$12$2ywIkwcq1RguCywR8ylsOeqzBymRNYj2DKXCkz/nbtNo4saBK7Y3K','male',1234,1233),(3,'Josh','Wilson','nitrxgen1@gmail.com','$2y$12$X.oVkRAl.OtmUSgccF0nd.91uS3lSQLiymoo6V8OVblmNqfBD3FOC','other',1648476911,NULL),(4,'Josh','Wilson','nitrxgen2@gmail.com','$2y$12$zNMMISe.IC0kZGtWRQXMJ.KHX5nsKA1GaOCrD95LR1VbhAD.63psC','other',1648478554,NULL),(5,NULL,'Wilson','nitrxgen3@gmail.com','$2y$12$u48sZf5cdKQY/nVJr24HYuUdZudoQbLJpiUSXF1fLugbbz52jnaDu','other',1648478789,0),(6,'Josh','Wilson','nitrxgen4@gmail.com','$2y$12$IMC90WoIM3j78V41/xOJbu633EO3foe9j1r11kcrmYYrfk4NRqrhi','other',1648478825,0),(7,'Billy','Bobson','bobsill@bilob.com','$2y$12$D1AlhEN2PSW5mbTWgRsMIe8hzL60hfAw/lR/3pV3HLKCQAyb..vyO','other',1648638742,0),(8,'tes,t','tes\"t','test@test.com','$2y$12$WQnFzXpsD1my7x1gSTcIH.ivltIq228F3G3PK8QViP0Pd6NCRPOf6','void',1648639502,0),(9,'Charles','Baskins','edwef@mail.com','$2y$12$cIKnnxCcZ9RPzxuuNTgZ4uhyvRtTtW.yAs1J.g1bLqKTIvdBN580u','male',1648668668,0),(10,'Boris','Johnson','email@gmail.com','$2y$12$71hEUOsCTxJCgogzQnvPA.EgI610BPgDUygoWdNLARMmXqZBWW1AC','10% something, 90% something else!',1648676477,0);
/*!40000 ALTER TABLE `registrants` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-03-31  2:49:11
