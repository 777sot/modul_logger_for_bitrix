CREATE TABLE `main_logger`
(
    `ID`               INT(11) AUTO_INCREMENT PRIMARY KEY,
    `ACTIVE`           VARCHAR(5),
    `SITE`             VARCHAR(255),
    `FILE_LOGGER_NAME` VARCHAR(255),
    `DIR_LOGGER`       TEXT
);
INSERT INTO main_logger (ACTIVE, SITE, FILE_LOGGER_NAME, DIR_LOGGER) VALUES ('Y','s1', 'debug_logger', '/local/logs/');
