<?php

include_once __DIR__.'/../../core.php';

if (isset($id_record)) {
    $records = $dbo->fetchArray('SELECT *, dt_ddt.note, dt_ddt.idpagamento, dt_ddt.id AS idddt, dt_statiddt.descrizione AS `stato`, dt_tipiddt.descrizione AS `descrizione_tipodoc` FROM ((dt_ddt LEFT OUTER JOIN dt_statiddt ON dt_ddt.idstatoddt=dt_statiddt.id) INNER JOIN an_anagrafiche ON dt_ddt.idanagrafica=an_anagrafiche.idanagrafica) INNER JOIN dt_tipiddt ON dt_ddt.idtipoddt=dt_tipiddt.id WHERE dt_ddt.id='.prepare($id_record));
}
