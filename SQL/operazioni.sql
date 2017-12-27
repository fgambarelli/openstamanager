

--Pulizia DB

ALTER TABLE `in_interventi` ADD `num_rapp` VARCHAR(50) NULL AFTER `tipo_sconto_globale`;

ALTER TABLE `my_impianti` ADD `idtipoimpianto` int(11) NULL AFTER `interno`;
