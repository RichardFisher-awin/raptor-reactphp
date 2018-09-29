USE reacttest;
CREATE TABLE healthcheck (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    hostname VARCHAR(100) NOT NULL,
    payload VARCHAR(50),
    PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO healthcheck(`hostname`, `payload`) VALUES ('any', 'pre-populated');

