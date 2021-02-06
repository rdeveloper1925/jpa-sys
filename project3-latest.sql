-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2020 at 02:20 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project3`
--

-- --------------------------------------------------------

--
-- Table structure for table `check_voucher_items`
--

CREATE TABLE `check_voucher_items` (
  `id` int(11) NOT NULL,
  `particulars` text NOT NULL,
  `code` text NOT NULL,
  `amount` int(11) NOT NULL,
  `voucherId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cheque_voucher`
--

CREATE TABLE `cheque_voucher` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `supplier` text NOT NULL,
  `address` text NOT NULL,
  `chequeNo` varchar(100) NOT NULL,
  `maker` text NOT NULL,
  `date` datetime NOT NULL,
  `passer` text NOT NULL,
  `authorizer` text NOT NULL,
  `receiver` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customerName` text NOT NULL,
  `contactPerson` text NOT NULL,
  `tinNo` int(11) NOT NULL,
  `address` text NOT NULL,
  `areaCountry` text NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` text NOT NULL,
  `otherContactDetails` text DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `dateIn` datetime NOT NULL,
  `partName` text NOT NULL,
  `partNo` varchar(20) NOT NULL,
  `dateOut` datetime DEFAULT NULL,
  `quantityInStore` int(11) NOT NULL,
  `balanceInStore` int(11) DEFAULT NULL,
  `suppliedBy` text NOT NULL,
  `takenBy` text DEFAULT NULL,
  `unitOfMeasure` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `inventory-sync` AFTER INSERT ON `inventory` FOR EACH ROW BEGIN
INSERT into inventorytemp VALUES (new.id,new.partName);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventorytemp`
--

CREATE TABLE `inventorytemp` (
  `id` int(11) NOT NULL,
  `itemName` text NOT NULL,
  `inputBy` varchar(30) NOT NULL DEFAULT 'INVENTORY'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoiceId` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `currency` varchar(9) NOT NULL,
  `modeOfPayment` text NOT NULL,
  `lpoNo` int(11) NOT NULL,
  `carRegNo` varchar(11) NOT NULL,
  `mileage` int(11) NOT NULL,
  `carType` text NOT NULL,
  `preparedBy` int(11) NOT NULL,
  `proformaId` int(11) DEFAULT NULL,
  `narration` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoiceitems`
--

CREATE TABLE `invoiceitems` (
  `id` int(11) NOT NULL,
  `serialNo` varchar(11) NOT NULL,
  `description` text NOT NULL,
  `quantity` float NOT NULL,
  `unitCost` int(11) NOT NULL,
  `invoiceId` varchar(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoiceitems2`
--

CREATE TABLE `invoiceitems2` (
  `id` int(11) NOT NULL,
  `inventoryItem` text NOT NULL,
  `quantity` float NOT NULL,
  `unitCost` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `units` varchar(100) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invoiceitemstemp`
--

CREATE TABLE `invoiceitemstemp` (
  `id` int(11) NOT NULL,
  `inventoryItem` text NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `unitCost` int(11) NOT NULL,
  `units` text NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `proforma`
--

CREATE TABLE `proforma` (
  `invoiceId` int(11) NOT NULL,
  `customerId` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `currency` varchar(9) NOT NULL,
  `modeOfPayment` text NOT NULL,
  `lpoNo` int(11) NOT NULL,
  `carRegNo` varchar(11) NOT NULL,
  `mileage` int(11) NOT NULL,
  `carType` text NOT NULL,
  `preparedBy` int(11) NOT NULL,
  `proformaId` int(11) DEFAULT NULL,
  `narration` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proformadiscounts`
--

CREATE TABLE `proformadiscounts` (
  `id` int(11) NOT NULL,
  `discount` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `proformainvoicenumbers`
--

CREATE TABLE `proformainvoicenumbers` (
  `id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `proformaitems2`
--

CREATE TABLE `proformaitems2` (
  `id` int(11) NOT NULL,
  `inventoryItem` text NOT NULL,
  `quantity` float NOT NULL,
  `unitCost` int(11) NOT NULL,
  `invoiceId` int(11) NOT NULL,
  `units` varchar(100) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receiptId` int(11) NOT NULL,
  `refNo` int(11) NOT NULL,
  `accountName` text NOT NULL,
  `narration` text NOT NULL,
  `preparedBy` text NOT NULL,
  `date` datetime NOT NULL,
  `refDate` datetime NOT NULL,
  `receivedFrom` text NOT NULL,
  `description` text NOT NULL,
  `currency` text NOT NULL,
  `receivedBy` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receiptdevices`
--

CREATE TABLE `receiptdevices` (
  `id` int(11) NOT NULL,
  `receiptId` int(11) NOT NULL,
  `amountDue` int(11) NOT NULL,
  `amountPaid` int(11) NOT NULL,
  `balance` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `receiptitems`
--

CREATE TABLE `receiptitems` (
  `id` int(11) NOT NULL,
  `inventoryItem` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `unitCost` int(11) NOT NULL,
  `receiptId` int(11) NOT NULL,
  `units` varchar(100) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `stocktracker`
--

CREATE TABLE `stocktracker` (
  `id` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `quantityBefore` int(11) NOT NULL,
  `stockAction` text NOT NULL,
  `quantity` float NOT NULL,
  `quantityAfter` float NOT NULL,
  `date` datetime NOT NULL,
  `doneBy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` text NOT NULL,
  `balance` float NOT NULL,
  `contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_ledgers`
--

CREATE TABLE `supplier_ledgers` (
  `id` int(11) NOT NULL,
  `supply_date` datetime DEFAULT NULL,
  `item` text DEFAULT NULL,
  `invoice_no` varchar(188) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `cartype` text DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `part_no` varchar(90) DEFAULT NULL,
  `debit_note_no` varchar(115) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `settled` varchar(20) NOT NULL DEFAULT 'NO',
  `unitCost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` text NOT NULL,
  `password` text NOT NULL,
  `username` text NOT NULL,
  `accessLevel` varchar(50) NOT NULL DEFAULT 'RECEPTIONIST'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `check_voucher_items`
--
ALTER TABLE `check_voucher_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cheque_voucher`
--
ALTER TABLE `cheque_voucher`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoiceId`),
  ADD KEY `FK-INVOICE-CUSTOMER` (`customerId`),
  ADD KEY `FK-USERS-INVOICES` (`preparedBy`);

--
-- Indexes for table `invoiceitems`
--
ALTER TABLE `invoiceitems`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoiceitems2`
--
ALTER TABLE `invoiceitems2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK-INVOICEITEMS-INVOICE` (`invoiceId`);

--
-- Indexes for table `invoiceitemstemp`
--
ALTER TABLE `invoiceitemstemp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `proforma`
--
ALTER TABLE `proforma`
  ADD PRIMARY KEY (`invoiceId`),
  ADD KEY `FK-PROFORMA-CUSTOMER` (`customerId`),
  ADD KEY `FK-PROFORMA-USER` (`preparedBy`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receiptId`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_ledgers`
--
ALTER TABLE `supplier_ledgers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `check_voucher_items`
--
ALTER TABLE `check_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cheque_voucher`
--
ALTER TABLE `cheque_voucher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoiceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoiceitems2`
--
ALTER TABLE `invoiceitems2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoiceitemstemp`
--
ALTER TABLE `invoiceitemstemp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `proforma`
--
ALTER TABLE `proforma`
  MODIFY `invoiceId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipt`
--
ALTER TABLE `receipt`
  MODIFY `receiptId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_ledgers`
--
ALTER TABLE `supplier_ledgers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK-INVOICE-CUSTOMER` FOREIGN KEY (`customerId`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `FK-USERS-INVOICES` FOREIGN KEY (`preparedBy`) REFERENCES `users` (`id`);

--
-- Constraints for table `proforma`
--
ALTER TABLE `proforma`
  ADD CONSTRAINT `FK-PROFORMA-CUSTOMER` FOREIGN KEY (`customerId`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `FK-PROFORMA-USER` FOREIGN KEY (`preparedBy`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
