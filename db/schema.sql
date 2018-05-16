--
-- Table structure for table `api`
--
CREATE TABLE `api` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `domain` varchar(350) NOT NULL,
  `description` varchar(140) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `creation_time` time NOT NULL,
  `creation_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `api_endpoint`
--
CREATE TABLE `api_endpoint` (
  `id` int(11) NOT NULL,
  `api_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(140) NOT NULL,
  `creation_time` time NOT NULL,
  `creation_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `api_endpoint_access`
--
CREATE TABLE `api_endpoint_access` (
  `id` int(11) NOT NULL,
  `api_endpoint_id` int(11) NOT NULL,
  `api_key_id` int(11) NOT NULL,
  `creation_time` time NOT NULL,
  `creation_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `api_key`
--
CREATE TABLE `api_key` (
  `id` int(11) NOT NULL,
  `api_id` int(11) NOT NULL,
  `api_user_id` int(11) NOT NULL,
  `passkey` varchar(64) NOT NULL,
  `stability` enum('dev','prod','test') NOT NULL DEFAULT 'dev',
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `creation_time` time NOT NULL,
  `creation_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Table structure for table `api_user`
--
CREATE TABLE `api_user` (
  `id` int(11) NOT NULL,
  `domain` varchar(350) NOT NULL,
  `username` varchar(350) NOT NULL,
  `password` varchar(64) NOT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `creation_time` time NOT NULL,
  `creation_date` date NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `api`
--
ALTER TABLE `api`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_UNIQUE` (`name`,`domain`);

--
-- Indexes for table `api_endpoint`
--
ALTER TABLE `api_endpoint`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_endpoint_UNIQUE` (`api_id`,`name`),
  ADD KEY `fk_api_endpoint_api1_idx` (`api_id`);

--
-- Indexes for table `api_endpoint_access`
--
ALTER TABLE `api_endpoint_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_api_endpoint_access_api_endpoint1_idx` (`api_endpoint_id`),
  ADD KEY `fk_api_endpoint_access_api_key1_idx` (`api_key_id`);

--
-- Indexes for table `api_key`
--
ALTER TABLE `api_key`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_key_UNIQUE` (`api_id`,`api_user_id`,`stability`),
  ADD KEY `fk_api_key_api_idx` (`api_id`),
  ADD KEY `fk_api_key_api_user1_idx` (`api_user_id`);

--
-- Indexes for table `api_user`
--
ALTER TABLE `api_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- AUTO_INCREMENT for table `api`
--
ALTER TABLE `api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_endpoint`
--
ALTER TABLE `api_endpoint`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_endpoint_access`
--
ALTER TABLE `api_endpoint_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_key`
--
ALTER TABLE `api_key`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `api_user`
--
ALTER TABLE `api_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for table `api_endpoint`
--
ALTER TABLE `api_endpoint`
  ADD CONSTRAINT `fk_api_endpoint_api1` FOREIGN KEY (`api_id`) REFERENCES `api` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `api_endpoint_access`
--
ALTER TABLE `api_endpoint_access`
  ADD CONSTRAINT `fk_api_endpoint_access_api_endpoint1` FOREIGN KEY (`api_endpoint_id`) REFERENCES `api_endpoint` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_api_endpoint_access_api_key1` FOREIGN KEY (`api_key_id`) REFERENCES `api_key` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `api_key`
--
ALTER TABLE `api_key`
  ADD CONSTRAINT `fk_api_key_api` FOREIGN KEY (`api_id`) REFERENCES `api` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_api_key_api_user1` FOREIGN KEY (`api_user_id`) REFERENCES `api_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
