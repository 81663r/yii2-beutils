--
-- Table structure for table `service_analytics`
--

CREATE TABLE `service_analytics` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `item` varchar(45) NOT NULL,
  `value` float NOT NULL,
  `influence` enum('positive','negative') NOT NULL,
  `weight` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_branch`
--

CREATE TABLE `service_branch` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `datetime` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_conf`
--

CREATE TABLE `service_conf` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `stability` enum('dev','test','prod') NOT NULL DEFAULT 'dev',
  `type` enum('parameter','credential','other') NOT NULL DEFAULT 'other',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `key` varchar(128) NOT NULL,
  `value` varchar(1024) NOT NULL,
  `datetime` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_provider`
--

CREATE TABLE `service_provider` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `datetime` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `service_type`
--

CREATE TABLE `service_type` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `datetime` datetime NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `service_analytics`
--
ALTER TABLE `service_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_provider_analytics_service_type_branch1_idx` (`branch_id`);

--
-- Indexes for table `service_branch`
--
ALTER TABLE `service_branch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_type_branch_service_type_idx` (`type_id`);

--
-- Indexes for table `service_conf`
--
ALTER TABLE `service_conf`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_provider_conf_service_provider1_idx` (`provider_id`),
  ADD KEY `fk_service_provider_conf_service_type_branch1_idx` (`branch_id`);

--
-- Indexes for table `service_provider`
--
ALTER TABLE `service_provider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_type`
--
ALTER TABLE `service_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `service_analytics`
--
ALTER TABLE `service_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_branch`
--
ALTER TABLE `service_branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_conf`
--
ALTER TABLE `service_conf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_provider`
--
ALTER TABLE `service_provider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `service_type`
--
ALTER TABLE `service_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `service_analytics`
--
ALTER TABLE `service_analytics`
  ADD CONSTRAINT `fk_service_provider_analytics_service_type_branch1` FOREIGN KEY (`branch_id`) REFERENCES `service_branch` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `service_branch`
--
ALTER TABLE `service_branch`
  ADD CONSTRAINT `fk_service_type_branch_service_type` FOREIGN KEY (`type_id`) REFERENCES `service_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `service_conf`
--
ALTER TABLE `service_conf`
  ADD CONSTRAINT `fk_service_provider_conf_service_provider1` FOREIGN KEY (`provider_id`) REFERENCES `service_provider` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_service_provider_conf_service_type_branch1` FOREIGN KEY (`branch_id`) REFERENCES `service_branch` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

