-- MySQL dump 10.13  Distrib 5.5.46, for Win32 (x86)
--
-- Host: localhost    Database: datingsystem
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `criteria`
--

DROP TABLE IF EXISTS `criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criteria` (
  `infoEmail` varchar(60) NOT NULL,
  `ageRange` varchar(8) DEFAULT NULL,
  `location` text,
  `mStatus` varchar(80) DEFAULT NULL,
  `religion` text,
  `haveChildren` varchar(8) DEFAULT NULL,
  `heightRange` varchar(20) DEFAULT NULL,
  `build` varchar(150) DEFAULT NULL,
  `education` varchar(200) DEFAULT NULL,
  `occupation` text,
  `smoking` varchar(50) DEFAULT NULL,
  `drinking` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`infoEmail`),
  CONSTRAINT `criteria_ibfk_1` FOREIGN KEY (`infoEmail`) REFERENCES `profile` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criteria`
--

LOCK TABLES `criteria` WRITE;
/*!40000 ALTER TABLE `criteria` DISABLE KEYS */;
/*!40000 ALTER TABLE `criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `normalmessage`
--

DROP TABLE IF EXISTS `normalmessage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `normalmessage` (
  `sender` varchar(60) NOT NULL,
  `time` bigint(20) NOT NULL,
  `textContent` text,
  `photoContent` varchar(130) DEFAULT NULL,
  `reciever` varchar(60) NOT NULL,
  `isSeen` varchar(1) NOT NULL COMMENT '0:unseen 1:seen',
  PRIMARY KEY (`sender`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `normalmessage`
--

LOCK TABLES `normalmessage` WRITE;
/*!40000 ALTER TABLE `normalmessage` DISABLE KEYS */;
/*!40000 ALTER TABLE `normalmessage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `postmessage`
--

DROP TABLE IF EXISTS `postmessage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postmessage` (
  `sender` varchar(60) NOT NULL,
  `time` bigint(20) NOT NULL,
  `textContent` text,
  `photoContent` varchar(130) DEFAULT NULL,
  `comments` text NOT NULL,
  `likes` text NOT NULL,
  PRIMARY KEY (`sender`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `postmessage`
--

LOCK TABLES `postmessage` WRITE;
/*!40000 ALTER TABLE `postmessage` DISABLE KEYS */;
/*!40000 ALTER TABLE `postmessage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `screenName` varchar(50) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `birthday` date NOT NULL,
  `location` varchar(50) NOT NULL,
  `profileHeadline` varchar(90) NOT NULL,
  `profilePhoto` varchar(130) DEFAULT NULL,
  `lastActive` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profileinformation`
--

DROP TABLE IF EXISTS `profileinformation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profileinformation` (
  `infoEmail` varchar(60) NOT NULL,
  `mStatus` varchar(40) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `haveChildren` varchar(7) DEFAULT NULL,
  `height` varchar(30) DEFAULT NULL,
  `build` varchar(30) DEFAULT NULL,
  `education` varchar(60) DEFAULT NULL,
  `occupation` varchar(80) DEFAULT NULL,
  `smoking` varchar(20) DEFAULT NULL,
  `drinking` varchar(20) DEFAULT NULL,
  `nomineeList` text,
  `phoneNumber` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`infoEmail`),
  CONSTRAINT `profileinformation_ibfk_1` FOREIGN KEY (`infoEmail`) REFERENCES `profile` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profileinformation`
--

LOCK TABLES `profileinformation` WRITE;
/*!40000 ALTER TABLE `profileinformation` DISABLE KEYS */;
/*!40000 ALTER TABLE `profileinformation` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-06-22 17:59:58
