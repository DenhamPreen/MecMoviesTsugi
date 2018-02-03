<?php
// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
    "drop table if exists {$CFG->dbprefix}mecmovies"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}mecmovies",
"create table {$CFG->dbprefix}mecmovies (
    `id` INT NOT NULL AUTO_INCREMENT,
    `link_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `completed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `duration` bigint(20) NOT NULL DEFAULT 0,
    `section_id` VARCHAR(255) NOT NULL DEFAULT '0',
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),

    CONSTRAINT `{$CFG->dbprefix}mecmovies_ibfk_1`
        FOREIGN KEY (`link_id`)
        REFERENCES `{$CFG->dbprefix}lti_link` (`link_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}mecmovies_ibfk_2`
        FOREIGN KEY (`user_id`)
        REFERENCES `{$CFG->dbprefix}lti_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    UNIQUE(link_id, user_id, section_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);
