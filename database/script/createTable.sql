SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `webserver` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `webserver` ;

-- -----------------------------------------------------
-- Table `webserver`.`Institution`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`Institution` (
  `institution_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `institution_number` INT NOT NULL ,
  `institution_desc` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`institution_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`user`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`user` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `institution` INT(11) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `ra` BIGINT UNSIGNED NOT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `gender` ENUM('F','M') NOT NULL ,
  `progress` DECIMAL(5,2) NULL ,
  `time` DATETIME NULL ,
  `scene` INT NULL ,
  `money` DECIMAL NOT NULL DEFAULT 0 ,
  `reset_token` VARCHAR(255) NULL ,
  PRIMARY KEY (`user_id`) ,
  INDEX `fk_instution_id` (`institution` ASC) ,
  CONSTRAINT `fk_instution_id`
    FOREIGN KEY (`institution` )
    REFERENCES `webserver`.`Institution` (`institution_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`competence`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`competence` (
  `competence_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `competence_desc` VARCHAR(500) NOT NULL ,
  PRIMARY KEY (`competence_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`competence_score`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`competence_score` (
  `competence_score_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) NOT NULL ,
  `competence_id` INT(11) NOT NULL ,
  `score` INT NULL DEFAULT 0 ,
  PRIMARY KEY (`competence_score_id`) ,
  INDEX `fk_user_id_idx` (`user_id` ASC) ,
  INDEX `fk_competence_id_idx` (`competence_id` ASC) ,
  CONSTRAINT `fk_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `webserver`.`user` (`user_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_competence_id`
    FOREIGN KEY (`competence_id` )
    REFERENCES `webserver`.`competence` (`competence_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`question`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`question` (
  `question_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `question_desc` VARCHAR(500) NOT NULL ,
  PRIMARY KEY (`question_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`answer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`answer` (
  `answer_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `question_id` INT(11) NOT NULL ,
  `competence_id` INT(11) NOT NULL ,
  `answer_score` INT NOT NULL ,
  PRIMARY KEY (`answer_id`) ,
  INDEX `fk_competence` (`competence_id` ASC) ,
  INDEX `fk_question` (`question_id` ASC) ,
  CONSTRAINT `fk_competence`
    FOREIGN KEY (`competence_id` )
    REFERENCES `webserver`.`competence` (`competence_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_question_id`
    FOREIGN KEY (`question_id` )
    REFERENCES `webserver`.`question` (`question_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webserver`.`teacher`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `webserver`.`teacher` (
  `teacher_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `institution_id` INT(11) NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`teacher_id`) ,
  INDEX `fk_inst_id` (`institution_id` ASC) ,
  CONSTRAINT `fk_inst_id`
    FOREIGN KEY (`institution_id` )
    REFERENCES `webserver`.`Institution` (`institution_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
