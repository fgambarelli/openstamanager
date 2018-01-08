

--Pulizia DB

ALTER TABLE `in_interventi` ADD `num_rapp` VARCHAR(50) NULL AFTER `tipo_sconto_globale`;

ALTER TABLE `my_impianti` ADD `idtipoimpianto` int(11) NULL AFTER `interno`;

ALTER TABLE `co_contratti` ADD `visite_num` INT(10) NOT NULL AFTER `tipo_sconto_globale`;
ALTER TABLE `co_contratti` ADD `reperibilita` TINYINT(1)  NOT NULL AFTER `tipo_sconto_globale`;
ALTER TABLE `co_contratti` ADD `controllo_num` INT(10) NOT NULL AFTER `visite_num`;
ALTER TABLE `co_contratti` ADD `tempo_medio` INT(10) NOT NULL AFTER `controllo_num`;
