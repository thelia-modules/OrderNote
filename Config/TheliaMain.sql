
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_note
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_note`;

CREATE TABLE `order_note`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `note` VARCHAR(255),
    `order_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `fi_order_note_id` (`order_id`),
    CONSTRAINT `fk_order_note_id`
        FOREIGN KEY (`order_id`)
        REFERENCES `order` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
