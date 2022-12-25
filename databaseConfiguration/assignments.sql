-- Server version	5.7.31

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `idassignments` int(11) NOT NULL AUTO_INCREMENT,
  `idRequests` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `distanceDriven` int(11) DEFAULT '0',
  `timeSpent` double DEFAULT '0',
  `payAmount` int(11) DEFAULT NULL,
  PRIMARY KEY (`idassignments`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
