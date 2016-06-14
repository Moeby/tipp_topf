-- phpMyAdmin SQL Dump
-- Host: localhost

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `verified` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `username_UNIQUE` (`username` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`team`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`team` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `flag` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`game`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`game` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `result_team1` INT NULL,
  `result_team2` INT NULL,
  `team_id1` INT NOT NULL,
  `team_id2` INT NOT NULL,
  `date` VARCHAR(45) NOT NULL,
  `matchday` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_game_team1_idx` (`team_id1` ASC),
  INDEX `fk_game_team2_idx` (`team_id2` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_game_team1`
    FOREIGN KEY (`team_id1`)
    REFERENCES `mydb`.`team` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_game_team2`
    FOREIGN KEY (`team_id2`)
    REFERENCES `mydb`.`team` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `owner` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `fk_group_user1_idx` (`owner` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC),
  CONSTRAINT `fk_group_user1`
    FOREIGN KEY (`owner`)
    REFERENCES `mydb`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`user_has_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`user_has_group` (
  `user_id` INT NOT NULL,
  `group_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `group_id`),
  INDEX `fk_user_has_group_group1_idx` (`group_id` ASC),
  INDEX `fk_user_has_group_user1_idx` (`user_id` ASC),
  CONSTRAINT `fk_user_has_group_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `mydb`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_has_group_group1`
    FOREIGN KEY (`group_id`)
    REFERENCES `mydb`.`group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`bet`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`bet` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `result_team1` INT NOT NULL,
  `result_team2` INT NOT NULL,
  `game_id` INT NOT NULL,
  `user_has_group_user_id` INT NOT NULL,
  `user_has_group_group_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_bet_game1_idx` (`game_id` ASC),
  INDEX `fk_bet_user_has_group1_idx` (`user_has_group_user_id` ASC, `user_has_group_group_id` ASC),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  CONSTRAINT `fk_bet_game1`
    FOREIGN KEY (`game_id`)
    REFERENCES `mydb`.`game` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_bet_user_has_group1`
    FOREIGN KEY (`user_has_group_user_id` , `user_has_group_group_id`)
    REFERENCES `mydb`.`user_has_group` (`user_id` , `group_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`invitation`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`invitation` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `group_id` INT NOT NULL,
  `email` VARCHAR(90) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idinvitation_UNIQUE` (`id` ASC),
  INDEX `fk_invitation_group1_idx` (`group_id` ASC),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  CONSTRAINT `fk_invitation_group1`
    FOREIGN KEY (`group_id`)
    REFERENCES `mydb`.`group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
