-- Server version	5.7.31

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `idRequests` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) DEFAULT NULL,
  `pickupLocation` varchar(45) DEFAULT NULL,
  `destination` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `WCRequest` tinyint(4) DEFAULT NULL,
  `fulfilled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idRequests`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;
