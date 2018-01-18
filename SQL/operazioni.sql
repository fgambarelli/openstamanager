

--Pulizia DB

ALTER TABLE `in_interventi` ADD `num_rapp` VARCHAR(50) NULL AFTER `tipo_sconto_globale`;

ALTER TABLE `my_impianti` ADD `idtipoimpianto` int(11) NULL AFTER `interno`;

ALTER TABLE `co_contratti` ADD `visite_num` INT(10) NOT NULL AFTER `tipo_sconto_globale`;
ALTER TABLE `co_contratti` ADD `reperibilita` TINYINT(1)  NOT NULL AFTER `tipo_sconto_globale`;
ALTER TABLE `co_contratti` ADD `controllo_num` INT(10) NOT NULL AFTER `visite_num`;
ALTER TABLE `co_contratti` ADD `tempo_medio` INT(10) NOT NULL AFTER `controllo_num`;

CREATE VIEW `v_mg_movimenti_all` AS
select  mg_movimenti.*,
(select `an_anagrafiche`.`ragione_sociale` from `an_anagrafiche` where (`an_anagrafiche`.`idanagrafica` =
		(case when (`mg_movimenti`.`iddocumento` > 0) then (select `co_documenti`.`idanagrafica` from `co_documenti` where (`co_documenti`.`id` = `mg_movimenti`.`iddocumento`))
			  else (case when (`mg_movimenti`.`idintervento` > 0) then (select `in_interventi`.`idanagrafica` from `in_interventi` where (`in_interventi`.`id` = `mg_movimenti`.`idintervento`)) end) end))
) AS `cliente`
from `mg_movimenti`;


ALTER TABLE `my_impianti` ADD `delega_criter` TINYINT(1)  NOT NULL AFTER `idtipoimpianto`;
ALTER TABLE `my_impianti` ADD `minuti` TINYINT(10)  NOT NULL AFTER `delega_criter`;
ALTER TABLE `my_impianti` ADD `km` TINYINT(10)  NOT NULL AFTER `minuti`;

-- Aggiunta in articolo se servizio
ALTER TABLE `mg_articoli` ADD `servizio` TINYINT(1) NOT NULL AFTER `id_sottocategoria`;
