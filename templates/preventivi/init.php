<?php

include_once __DIR__.'/../../core.php';

$module_name = 'Preventivi';

// Lettura info fattura
$records = $dbo->fetchArray('SELECT *, data_bozza AS data FROM co_preventivi WHERE co_preventivi.id='.prepare($idpreventivo));

$id_cliente = $records[0]['idanagrafica'];
$id_sede = $records[0]['idsede'];

$mostra_prezzi = get_var('Stampa i prezzi sui preventivi');
