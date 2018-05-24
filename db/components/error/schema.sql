-- -----------------------------------------------------
-- Table `error`
-- -----------------------------------------------------
CREATE TABLE `error` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `action` VARCHAR(26) NOT NULL,
  `entity` VARCHAR(46) NOT NULL,
  `object` VARCHAR(46) NOT NULL,
  `user_message` VARCHAR(128) NOT NULL,
  `system_message` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;
