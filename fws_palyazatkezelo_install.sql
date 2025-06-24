CREATE SCHEMA `fwsproba` DEFAULT CHARACTER SET utf8 COLLATE utf8_hungarian_ci ;

use `fwsproba`;

CREATE TABLE `fwsproba`.`project` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `status_id` INT NOT NULL DEFAULT 1,
  `description` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) );

CREATE TABLE `fwsproba`.`status` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `status_name_UNIQUE` (`status_name` ASC) );

ALTER TABLE `fwsproba`.`project` 
ADD INDEX `status_id_idx` (`status_id` ASC) ;

ALTER TABLE `fwsproba`.`project` 
ADD CONSTRAINT `fk_status_id`
  FOREIGN KEY (`status_id`)
  REFERENCES `fwsproba`.`status` (`id`)
  ON DELETE RESTRICT
  ON UPDATE CASCADE;

CREATE TABLE `fwsproba`.`contact` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) );

  CREATE TABLE `fwsproba`.`project_contact_relationship` (
  `project_id` INT NOT NULL,
  `contact_id` INT NOT NULL,
  PRIMARY KEY (`project_id`, `contact_id`));

INSERT INTO `fwsproba`.`status` (`id`, `status_name`) VALUES ('1', 'Fejlesztésre vár'),('2', 'Folyamatban'),('3', 'Kész');

INSERT INTO `fwsproba`.`project` (`name`, `description`, `status_id`) VALUES
('GINOP-2025-000874', 'Gyártási folyamatok fejlesztése', '1'),
('KEHOP-2025-000642', 'Megújuló energiaforrások integrálása', '2'),
('EFOP-2025-000214', 'Digitális oktatási programok', '3'),
('GINOP-2025-000953', 'Automatizáció és robotika fejlesztése', '1'),
('KEHOP-2025-000318', 'Zöld infrastruktúra fejlesztése', '2'),
('EFOP-2025-000765', 'Tanulási kompetenciák fejlesztése', '3'),
('GINOP-2025-000567', 'Kis- és középvállalkozások támogatása', '1'),
('KEHOP-2025-000921', 'Környezetvédelmi beruházások', '2'),
('EFOP-2025-000382', 'Innováció az oktatásban', '3'),
('GINOP-2025-000412', 'Termelési hatékonyság növelése', '1'),
('KEHOP-2025-000678', 'Fenntartható fejlődési projektek', '2'),
('EFOP-2025-000819', 'Digitális készségek fejlesztése', '3'),
('GINOP-2025-000725', 'Gyártási technológiák modernizálása', '1'),
('KEHOP-2025-000534', 'Energiahatékonysági fejlesztések', '2'),
('EFOP-2025-000497', 'Digitális oktatási reform', '3'),
('GINOP-2025-000821', 'Ipar 4.0 technológiák bevezetése', '1'),
('KEHOP-2025-000229', 'Vízgazdálkodási rendszerek fejlesztése', '2'),
('EFOP-2025-000634', 'Felnőttképzési programok fejlesztése', '3'),
('GINOP-2025-000912', 'Biotechnológiai innovációk támogatása', '1'),
('KEHOP-2025-000745', 'Hulladékkezelési megoldások', '2'),
('EFOP-2025-000588', 'E-learning fejlesztések', '3'),
('GINOP-2025-000369', 'Kutatás-fejlesztési beruházások', '1'),
('KEHOP-2025-000987', 'Természetvédelmi projektek', '2'),
('EFOP-2025-000754', 'Iskolai digitális eszközbeszerzés', '3');

INSERT INTO `fwsproba`.`contact` (`name`, `email`) VALUES
('Nagy Péter', 'nagy.peter82@gmail.com'),
('Szabó Anna', 'szabo.anna99@yahoo.com'),
('Tóth Gergely', 'toth.gergely77@outlook.com'),
('Varga Mária', 'varga.maria54@gmail.com'),
('Kovács László', 'kovacs.laszlo22@hotmail.com'),
('Molnár Zoltán', 'molnar.zoltan45@gmail.com'),
('Balogh Éva', 'balogh.eva31@yahoo.com'),
('Farkas Dénes', 'farkas.denes88@outlook.com'),
('Kiss Júlia', 'kiss.julia29@gmail.com'),
('Horváth Csaba', 'horvath.csaba67@gmail.com'),
('Lukács Tamás', 'lukacs.tamas53@freemail.hu'),
('Fehér Nóra', 'feher.nora12@gmail.com'),
('Papp Róbert', 'papp.robi34@yahoo.com'),
('Szalai Edit', 'szalai.edit76@outlook.com'),
('Kerekes Ádám', 'kerekes.adam88@gmail.com'),
('Fodor Beáta', 'fodor.beata99@yahoo.com'),
('Bognár Gábor', 'bognar.gabor55@freemail.hu'),
('Hegedűs Tamara', 'hegedus.tamara24@gmail.com'),
('Károlyi István', 'karolyi.istvan68@yahoo.com'),
('Szűcs Veronika', 'szucs.veronika80@outlook.com'),
('Németh Richárd', 'nemeth.richard41@gmail.com'),
('Orsós Bianka', 'orsos.bianka23@yahoo.com'),
('Juhász Dániel', 'juhasz.daniel39@outlook.com'),
('Béres Eszter', 'beres.eszter78@gmail.com'),
('Sipos Márton', 'sipos.marton92@freemail.hu'),
('Vass Noémi', 'vass.noemi34@yahoo.com'),
('Barta Csilla', 'barta.csilla56@outlook.com'),
('Pintér Tamás', 'pinter.tamas82@gmail.com'),
('Takács Gergő', 'takacs.gergo99@hotmail.com'),
('Fekete Attila', 'fekete.attila63@yahoo.com'),
('Oláh Viktória', 'olah.viktoria47@gmail.com'),
('Simon Zsolt', 'simon.zsolt28@freemail.hu'),
('Veres Kitti', 'veres.kitti76@yahoo.com'),
('Győri Norbert', 'gyori.norbert85@gmail.com'),
('Major Nikolett', 'major.nikolett31@outlook.com'),
('Mészáros Csaba', 'meszaros.csaba49@gmail.com'),
('Török Rózsa', 'torok.rozsa92@yahoo.com'),
('Illés Bence', 'illes.bence34@freemail.hu'),
('Hollósi Balázs', 'hollosi.balazs57@outlook.com'),
('Csordás Gabriella', 'csordas.gabriella88@gmail.com'),
('Vincze Dominik', 'vincze.dominik99@yahoo.com'),
('Gál Adrienn', 'gal.adrienn45@gmail.com'),
('Barna Kristóf', 'barna.kristof76@outlook.com'),
('Szöllősi Emese', 'szollosi.emese23@freemail.hu'),
('Novák Roland', 'novak.roland58@gmail.com'),
('Lengyel Klaudia', 'lengyel.klaudia90@yahoo.com'),
('Tímár Lajos', 'timar.lajos35@outlook.com'),
('Fazekas Ivett', 'fazekas.ivett67@gmail.com'),
('Sárközi Ákos', 'sarkozi.akos84@hotmail.com');

ALTER TABLE `fwsproba`.`project_contact_relationship` 
ADD INDEX `fk_project_id_idx` (`project_id` ASC) ,
ADD INDEX `fk_contact_id_idx` (`contact_id` ASC) ;
;
ALTER TABLE `fwsproba`.`project_contact_relationship` 
ADD CONSTRAINT `fk_project_id`
  FOREIGN KEY (`project_id`)
  REFERENCES `fwsproba`.`project` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_contact_id`
  FOREIGN KEY (`contact_id`)
  REFERENCES `fwsproba`.`contact` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

INSERT INTO `fwsproba`.`project_contact_relationship` (`project_id`, `contact_id`) VALUES
(1, 10), (1, 25),
(2, 8), (2, 12),
(3, 15), (3, 22), (3, 35),
(4, 5), (4, 27),
(5, 30),
(6, 14), (6, 32),
(7, 11),
(8, 9), (8, 21), (8, 33),
(9, 16),
(10, 20), (10, 31),
(11, 2), (11, 18),
(12, 24),
(13, 3), (13, 41),
(14, 23),
(15, 6), (15, 29),
(16, 7),
(17, 19), (17, 28),
(18, 4),
(19, 17),
(20, 1),
(21, 13),
(22, 26),
(23, 34),
(24, 40), (24, 39);
