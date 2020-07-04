-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dateIn` datetime NOT NULL,
  `partName` text NOT NULL,
  `partNo` int(11) NOT NULL,
  `dateOut` datetime NOT NULL,
  `quantityInStore` int(11) NOT NULL,
  `balanceInStore` int(11) NOT NULL,
  `suppliedBy` text NOT NULL,
  `takenBy` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `invoice` (
  `invoiceId` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `contactPerson` text NOT NULL,
  `currency` varchar(9) NOT NULL,
  `tinNo` int(11) NOT NULL,
  `modeOfPayment` text NOT NULL,
  `customer` text NOT NULL,
  `address` text NOT NULL,
  `areaCountry` text NOT NULL,
  `phone` text NOT NULL,
  `email` text NOT NULL,
  `otherContactDetails` text NOT NULL,
  `lpoNo` int(11) NOT NULL,
  `carRegNo` varchar(11) NOT NULL,
  `mileage` int(11) NOT NULL,
  `carType` text NOT NULL,
  `preparedBy` text NOT NULL,
  PRIMARY KEY (`invoiceId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `invoiceitems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serialNo` varchar(11) NOT NULL,
  `description` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `unitCost` int(11) NOT NULL,
  `invoiceId` varchar(11) NOT NULL,
  `total` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `receipt` (
  `receiptId` int(11) NOT NULL AUTO_INCREMENT,
  `refNo` int(11) NOT NULL,
  `accountName` text NOT NULL,
  `narration` text NOT NULL,
  `preparedBy` text NOT NULL,
  `date` datetime NOT NULL,
  `refDate` datetime NOT NULL,
  `receivedFrom` text NOT NULL,
  `amount` int(11) NOT NULL,
  `description` text NOT NULL,
  `currency` text NOT NULL,
  `receivedBy` text NOT NULL,
  PRIMARY KEY (`receiptId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` text NOT NULL,
  `password` text NOT NULL,
  `username` text NOT NULL,
  `accessLevel` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `fullName`, `password`, `username`, `accessLevel`) VALUES
(1,	'Administrator',	'5f4dcc3b5aa765d61d8327deb882cf99',	'admin',	3),
(2,	'Rodney Sentamu',	'5f4dcc3b5aa765d61d8327deb882cf99',	'MATHIASR',	1);

-- 2020-05-03 05:45:27