-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 03, 2025 at 04:20 PM
-- Server version: 8.0.41-cll-lve
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nexustr2_bitrader`
--

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `flutterwave_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('initiated','successful','failed','pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'initiated',
  `method` enum('card','bank_transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'card',
  `date_initiated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_completed` timestamp NULL DEFAULT NULL,
  `flutterwave_fee` decimal(10,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `id` int NOT NULL,
  `error_message` text NOT NULL,
  `error_context` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `seen` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `username`, `message`, `seen`, `created_at`) VALUES
(1, 'nexustrader8@gmail.com', 'Your duplicate trade has been resolved and the amount (N5433.2) deducted as loss has been reimbursed.', 1, '2025-01-08 23:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `payload`
--

CREATE TABLE `payload` (
  `id` int NOT NULL,
  `tran_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `mdaCode` varchar(50) NOT NULL,
  `revenueCode` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `amount` varchar(100) NOT NULL,
  `message` varchar(100) NOT NULL,
  `paid_at` varchar(100) NOT NULL,
  `created_at` varchar(100) NOT NULL,
  `channel` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `ip_address` varchar(200) NOT NULL,
  `stampdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `payload`
--

INSERT INTO `payload` (`id`, `tran_id`, `first_name`, `last_name`, `email`, `phone`, `domain`, `mdaCode`, `revenueCode`, `status`, `reference`, `amount`, `message`, `paid_at`, `created_at`, `channel`, `currency`, `ip_address`, `stampdate`) VALUES
(1, '4531109853', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-6771a447eb225-nexustrader8@gmail.com', '400000', '', 'OYYKS36N9EU9G', '4000', 'madePayment', '2024-12-29T19:35:37.000Z', '2024-12-29T19:34:32.000Z', 'bank', 'NGN', '197.211.63.169', '2024-12-29 19:35:40'),
(2, '4545767880', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-6777af743a1a3-nexustrader8@gmail.com', '300000', '', '6KS7V10M0IB0N', '3000', 'madePayment', '2025-01-03T09:37:05.000Z', '2025-01-03T09:35:48.000Z', 'bank', 'NGN', '197.211.63.58', '2025-01-03 09:37:10'),
(3, '4545902633', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-6777bb8a32a03-nexustrader8@gmail.com', '200000', '', 'XO1DUK34BE8SX', '2000', 'madePayment', '2025-01-03T10:28:03.000Z', '2025-01-03T10:27:22.000Z', 'bank', 'NGN', '197.211.63.58', '2025-01-03 10:28:05'),
(4, '4547412854', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-677836b865879-nexustrader8@gmail.com', '100000', '', 'ACDN8Y8T1UPY7', '1000', 'madePayment', '2025-01-03T19:17:14.000Z', '2025-01-03T19:12:56.000Z', 'bank', 'NGN', '197.211.63.58', '2025-01-03 19:17:17'),
(5, '4547468850', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-67783da8a0e3a-nexustrader8@gmail.com', '100000', '', '3G93NUSEAP2BA', '1000', 'madePayment', '2025-01-03T19:44:19.000Z', '2025-01-03T19:42:32.000Z', 'bank', 'NGN', '197.211.63.58', '2025-01-03 19:44:22'),
(6, '4550915626', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-67799511519a7-nexustrader8@gmail.com', '100000', '', 'S2WJ2FIVIS3X7', '1000', '', '2025-01-04T20:09:14.000Z', '2025-01-04T20:07:45.000Z', 'bank_transfer', 'NGN', '105.112.121.197', '2025-01-04 20:09:17'),
(7, '4551692365', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-677a1cfed7c2f-nexustrader8@gmail.com', '100000', '', 'GPOMBSBTBZA81', '1000', '', '2025-01-05T05:50:30.000Z', '2025-01-05T05:47:43.000Z', 'bank_transfer', 'NGN', '105.112.112.160', '2025-01-05 05:50:32'),
(8, '4555265736', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-677b733d27b95-nexustrader8@gmail.com', '200000', '', 'JV1XT5IRB0S5A', '2000', 'madePayment', '2025-01-06T06:08:55.000Z', '2025-01-06T06:07:57.000Z', 'bank', 'NGN', '197.211.63.58', '2025-01-06 06:08:57'),
(9, '4561608018', '', '', 'nexustrader8@gmail.com', '', 'live', 'TX-677dacf3c292f-nexustrader8@gmail.com', '200000', '', 'BLTAPZS76K55T', '2000', '', '2025-01-07T22:39:49.000Z', '2025-01-07T22:38:44.000Z', 'bank_transfer', 'NGN', '197.211.63.58', '2025-01-07 22:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `trades`
--

CREATE TABLE `trades` (
  `id` int NOT NULL,
  `symbol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `position_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `order_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `duration` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `result` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Open','Closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Open',
  `profit_or_loss_amount` decimal(15,2) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trades`
--

INSERT INTO `trades` (`id`, `symbol`, `position_id`, `order_type`, `duration`, `result`, `status`, `profit_or_loss_amount`, `username`, `created_at`, `updated_at`) VALUES
(73, 'ETHUSDm', '1113873425', 'sell', '5 minutes', 'Loss', 'Open', 306.91, 'nexustrader8@gmail.com', '2025-01-06 06:12:16', NULL),
(74, 'ETHUSDm', '1113874258', 'sell', '5 minutes', 'Profit', 'Open', 92.07, 'nexustrader8@gmail.com', '2025-01-06 06:13:37', NULL),
(75, 'EURUSDm', '1114166539', 'buy', '5 minutes', 'Profit', 'Open', 1534.53, 'nexustrader8@gmail.com', '2025-01-06 09:08:57', NULL),
(77, 'AUDCHFm', '1114320671', 'buy', '5 minutes', 'Loss', 'Open', 2363.18, 'nexustrader8@gmail.com', '2025-01-06 10:52:34', NULL),
(80, 'EURJPYm', '1119232932', 'sell', '5 hours', 'Profit', 'Open', 1059.02, 'nexustrader8@gmail.com', '2025-01-08 17:22:18', NULL),
(81, 'EURCADm', '1119257319', 'buy', '5 hours', 'Loss', 'Open', 5433.23, 'nexustrader8@gmail.com', '2025-01-08 17:33:28', NULL),
(83, 'AUDUSDm', '1119625759', 'buy', '5 seconds', 'Loss', 'Open', 13813.29, 'spacedevs600@gmail.com', '2025-01-08 21:07:54', NULL),
(84, 'AUDUSDm', '1119627401', 'buy', '2 minutes', 'Loss', 'Open', 13813.29, 'spacedevs600@gmail.com', '2025-01-08 21:09:16', NULL),
(85, 'BTCUSDm', '1119649234', 'buy', '2 minutes', 'Loss', 'Open', 47072.62, 'spacedevs600@gmail.com', '2025-01-08 21:39:19', NULL),
(86, 'ETHUSDm', '1123968753', 'buy', '5 seconds', 'Loss', 'Open', 277.85, 'spacedevs600@gmail.com', '2025-01-11 01:40:57', NULL),
(87, 'NZDUSDm', '1127135020', 'sell', '5 seconds', 'Loss', 'Open', 2782.84, 'spacedevs600@gmail.com', '2025-01-13 18:19:53', NULL),
(88, 'ETHUSDm', '1127137043', 'sell', '1 minutes', 'Profit', 'Open', 185.52, 'spacedevs600@gmail.com', '2025-01-13 18:21:16', NULL),
(90, 'AUDUSDm', '1133962924', 'buy', '1 minutes', 'Loss', 'Open', 1552.13, 'spacedevs600@gmail.com', '2025-01-17 02:35:52', NULL),
(91, 'AUDUSDm', '1133963169', 'buy', '1 minutes', 'Loss', 'Open', 1552.13, 'spacedevs600@gmail.com', '2025-01-17 02:36:38', NULL),
(92, 'AUDUSDm', '1133963233', 'sell', '1 minutes', 'Loss', 'Open', 465.64, 'spacedevs600@gmail.com', '2025-01-17 02:37:54', NULL),
(93, 'BTCUSDm', '1133963755', 'sell', '1 minutes', 'Loss', 'Open', 6224.04, 'spacedevs600@gmail.com', '2025-01-17 02:39:12', NULL),
(94, 'BTCUSDm', '1133981181', 'sell', '2 minutes', 'Loss', 'Open', 6130.91, 'spacedevs600@gmail.com', '2025-01-17 03:45:21', NULL),
(95, 'BTCUSDm', '1133981595', 'buy', '1 minutes', 'Loss', 'Open', 6037.79, 'spacedevs600@gmail.com', '2025-01-17 03:48:29', NULL),
(96, 'GBPUSDm', '1133988310', 'buy', '1 minutes', 'Loss', 'Open', 2017.77, 'spacedevs600@gmail.com', '2025-01-17 04:12:46', NULL),
(97, 'BTCUSDm', '1133988605', 'buy', '1 minutes', 'Loss', 'Open', 4454.61, 'spacedevs600@gmail.com', '2025-01-17 04:14:03', NULL),
(98, 'BTCUSDm', '1133988879', 'sell', '1 minutes', 'Loss', 'Open', 4159.71, 'spacedevs600@gmail.com', '2025-01-17 04:15:49', NULL),
(99, 'BTCUSDm', '1133989832', 'buy', '2 minutes', 'Profit', 'Open', 2048.81, 'spacedevs600@gmail.com', '2025-01-17 04:20:10', NULL),
(100, 'BTCUSDm', '1133990360', 'sell', '1 minutes', 'Loss', 'Open', 7248.45, 'spacedevs600@gmail.com', '2025-01-17 04:21:32', NULL),
(101, 'GBPUSDm', '1133991154', 'sell', '1 minutes', 'Loss', 'Open', 155.21, 'spacedevs600@gmail.com', '2025-01-17 04:23:30', NULL),
(102, 'GBPUSDm', '1133991736', 'sell', '1 minutes', 'Profit', 'Open', 1086.49, 'spacedevs600@gmail.com', '2025-01-17 04:25:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trading_deposits`
--

CREATE TABLE `trading_deposits` (
  `id` int NOT NULL,
  `transaction` varchar(50) NOT NULL,
  `username` varchar(30) NOT NULL,
  `amount` varchar(30) NOT NULL,
  `profit` varchar(30) NOT NULL,
  `loss` varchar(20) NOT NULL,
  `cumulative` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `email`, `amount`, `type`, `transaction_date`) VALUES
(1, 'spacedevs600@gmail.com', 5000.00, 'credit', '2025-01-04 16:55:30'),
(2, 'spacedevs600@gmail.com', 3000.00, 'credit', '2025-01-04 16:55:30'),
(3, 'nexustrader8@gmail.com', 1000.00, 'credit', '2025-01-04 20:09:17'),
(4, 'nexustrader8@gmail.com', 1000.00, 'credit', '2025-01-05 05:50:32'),
(5, 'nexustrader8@gmail.com', 2000.00, 'credit', '2025-01-06 06:08:57'),
(6, 'nexustrader8@gmail.com', 2000.00, 'credit', '2025-01-07 22:39:51');

-- --------------------------------------------------------

--
-- Table structure for table `transfers`
--

CREATE TABLE `transfers` (
  `id` int NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `from_user` varchar(50) NOT NULL,
  `to_user` varchar(50) NOT NULL,
  `amount` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `country` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `transaction_status` enum('pending','ready','verified') DEFAULT 'pending',
  `email_verified` tinyint(1) DEFAULT '0',
  `phone_verified` tinyint(1) DEFAULT '0',
  `kyc_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `code_expires_at` timestamp NULL DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `emp_status` varchar(50) DEFAULT NULL,
  `address` text,
  `avatar` varchar(255) DEFAULT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `failed_attempts` int DEFAULT '0',
  `last_attempt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `country`, `password`, `transaction_status`, `email_verified`, `phone_verified`, `kyc_verified`, `created_at`, `updated_at`, `deleted_at`, `verification_code`, `code_expires_at`, `dob`, `gender`, `phone`, `emp_status`, `address`, `avatar`, `security_question`, `security_answer`, `failed_attempts`, `last_attempt`) VALUES
(1, 'Alex Ejike', 'Ale', 'ejike6483@gmail.com', 'Unknown', '$2y$10$Cur7fzb0a8ErcSBVGqpm3uqYLzMrs6hS0T3kr1rsxQJPHZIob1w3a', 'pending', 0, 0, 0, '2024-12-28 18:09:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(3, 'Alexander Ejike', 'nexustrader8@gmail.com', 'nexustrader8@gmail.com', 'Unknown', '$2y$10$ACu5FG/I5eLSjXpmhjVtd./ryAogSChFVpH7sbvAM6/5Ap/xm7Sm.', 'ready', 1, 0, 0, '2024-12-29 06:37:47', '2024-12-29 08:20:57', NULL, '153287', '2024-12-29 07:31:55', '2022-08-01', 'male', '08159376128', 'self-employed', 'Zone 1', '11c90615-1559-49b1-87f4-6932d36022e9.jpeg', NULL, NULL, 0, NULL),
(4, 'John Steve', 'spacedevs600@gmail.com', 'spacedevs600@gmail.com', 'Unknown', '$2y$10$RaaTy5P95VcHUUmSK0Oag.QApFvSzeuH3c1sHvkSsKKEVvi1SLh0q', 'ready', 1, 0, 0, '2024-12-29 18:54:54', '2025-01-02 11:33:58', NULL, '327003', '2025-01-02 10:45:36', '2005-02-20', 'male', '08121271199', 'student', 'No. 2A, Calabar Street, Coal Camp, Enugu, Nigeria', 'johnny.jpg', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int NOT NULL,
  `wallet_id` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `balance` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_deposited` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_withdrawn` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_deposit` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_deposit_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `wallet_id`, `username`, `balance`, `transaction_type`, `total_deposited`, `total_withdrawn`, `last_deposit`, `last_deposit_date`, `created_at`, `updated_at`) VALUES
(1, 'NT-49682571', 'Ale', '0.00', '', '', '', NULL, NULL, '2024-12-28 18:09:10', NULL),
(2, 'NT-48072391', 'nexustrader8@gmail.com', '4676.5', '', '14500', '', NULL, NULL, '2024-12-28 18:10:28', '2025-01-08 21:37:28'),
(4, 'NT-76184952', 'spacedevs600@gmail.com', '10261.74', '', '', '', NULL, NULL, '2024-12-29 18:54:54', '2025-01-17 04:25:34');

-- --------------------------------------------------------

--
-- Table structure for table `webhook_logs`
--

CREATE TABLE `webhook_logs` (
  `id` int NOT NULL,
  `event_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payload`
--
ALTER TABLE `payload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trades`
--
ALTER TABLE `trades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_position_id` (`position_id`);

--
-- Indexes for table `trading_deposits`
--
ALTER TABLE `trading_deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payload`
--
ALTER TABLE `payload`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trades`
--
ALTER TABLE `trades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `trading_deposits`
--
ALTER TABLE `trading_deposits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `webhook_logs`
--
ALTER TABLE `webhook_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
