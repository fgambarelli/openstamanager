<?php

include_once __DIR__.'/../../core.php';

$report_name = 'fattura_'.$numero.'.pdf';

$autofill = [
    'count' => 0, // Conteggio delle righe
    'words' => 70, // Numero di parolo dopo cui contare una riga nuova
    'rows' => 20, // Numero di righe massimo presente nella pagina
    'additional' => 15, // Numero di righe massimo da aggiungere
    'columns' => 5, // Numero di colonne della tabella
];

$v_iva = [];
$v_totale = [];

$sconto = [];
$imponibile = [];
$iva = [];

// Intestazione tabella per righe
echo "
<table class='table table-striped table-bordered' id='contents'>
    <thead>
        <tr>
            <th class='text-center' style='width:50%'>".tr('Descrizione', [], ['upper' => true])."</th>
            <th class='text-center' style='width:14%'>".tr('Q.tà', [], ['upper' => true])."</th>
            <th class='text-center' style='width:16%'>".tr('Prezzo unitario', [], ['upper' => true])."</th>
            <th class='text-center' style='width:20%'>".tr('Importo', [], ['upper' => true])."</th>
            <th class='text-center' style='width:10%'>".tr('IVA', [], ['upper' => true]).' (%)</th>
        </tr>
    </thead>

    <tbody>';

// RIGHE FATTURA CON ORDINAMENTO UNICO
$righe = $dbo->fetchArray("SELECT *, IFNULL((SELECT codice FROM mg_articoli WHERE id=idarticolo),'') AS codice_articolo, (SELECT percentuale FROM co_iva WHERE id=idiva) AS perc_iva FROM `co_righe_documenti` WHERE iddocumento=".prepare($iddocumento).' ORDER BY `order`');
foreach ($righe as $r) {
    $count = 0;
    $count += ceil(strlen($r['descrizione']) / $autofill['words']);
    $count += substr_count($r['descrizione'], PHP_EOL);

    echo '
        <tr>
            <td>
                '.nl2br($r['descrizione']);

    if (!empty($r['codice_articolo'])) {
        echo '
                <br><small>'.tr('COD. _COD_', [
                    '_COD_' => $r['codice_articolo'],
                ]).'</small>';

        if ($count <= 1) {
            $count += 0.4;
        }
    }

    // Aggiunta riferimento a ordine
    if (!empty($r['idordine'])) {
        $rso = $dbo->fetchArray('SELECT numero, numero_esterno, data FROM or_ordini WHERE id='.prepare($r['idordine']));
        $numero = !empty($rso[0]['numero_esterno']) ? $rso[0]['numero_esterno'] : $rso[0]['numero'];

        if (!empty($rso)) {
            $descrizione = tr('Rif. ordine num. _NUM_ del _DATE_', [
                '_NUM_' => $numero,
                '_DATE_' => Translator::dateToLocale($rso[0]['data']),
            ]);
        }
    }

    // Aggiunta riferimento a ddt
    elseif (!empty($r['idddt'])) {
        $rso = $dbo->fetchArray('SELECT numero, numero_esterno, data FROM dt_ddt WHERE id='.prepare($r['idddt']));
        $numero = !empty($rso[0]['numero_esterno']) ? $rso[0]['numero_esterno'] : $rso[0]['numero'];

        if (!empty($rso)) {
            $descrizione = tr('Rif. ddt num. _NUM_ del _DATE_', [
                '_NUM_' => $numero,
                '_DATE_' => Translator::dateToLocale($rso[0]['data']),
            ]);
        }
    }

    // Aggiunta riferimento al preventivo
    elseif (!empty($r['idpreventivo'])) {
        $rso = $dbo->fetchArray('SELECT numero, data_bozza FROM co_preventivi WHERE id='.prepare($r['idpreventivo']));

        if (!empty($rso)) {
            $descrizione = tr('Rif. preventivo num. _NUM_ del _DATE_', [
                '_NUM_' => $rso[0]['numero'],
                '_DATE_' => Translator::dateToLocale($rso[0]['data_bozza']),
            ]);
        }
    }

    // Aumento del conteggio
    if ((!empty($r['idordine']) || !empty($r['idddt']) || !empty($r['idpreventivo'])) && $count <= 1 && !empty($descrizione)) {
        echo '<br><small>'.$descrizione.'</small>';
        $count += 0.4;
    }

    echo '
            </td>';

    echo "
            <td class='text-center'>
                ".Translator::numberToLocale($r['qta']).' '.$r['um'].'
            </td>';

    // Prezzo unitario
    echo "
            <td class='text-right'>
                ".(empty($r['qta']) || empty($r['subtotale']) ? '' : Translator::numberToLocale($r['subtotale'] / $r['qta'])).' &euro;';

    if ($r['sconto'] > 0) {
        echo "
                <br><small class='text-muted'>- ".tr('sconto _TOT_ _TYPE_', [
                    '_TOT_' => Translator::numberToLocale($r['sconto_unitario']),
                    '_TYPE_' => ($r['tipo_sconto'] == 'PRC' ? '%' : '&euro;'),
                ]).'</small>';

        if ($count <= 1) {
            $count += 0.4;
        }
    }

    echo '
            </td>';

    // Imponibile
    echo "
            <td class='text-right'>
                ".(empty($r['subtotale']) ? '' : Translator::numberToLocale($r['subtotale'])).' &euro;';

    if ($r['sconto'] > 0) {
        echo "
                <br><small class='text-muted'>- ".tr('sconto _TOT_ _TYPE_', [
                    '_TOT_' => Translator::numberToLocale($r['sconto']),
                    '_TYPE_' => '&euro;',
                ]).'</small>';

        if ($count <= 1) {
            $count += 0.4;
        }
    }

    echo '
            </td>';

    // Iva
    echo '
            <td class="text-center">
                '.Translator::numberToLocale($r['perc_iva']).'
            </td>
        </tr>';

    $autofill['count'] += $count;

    $imponibile[] = $r['subtotale'];
    $iva[] = $r['iva'];
    $sconto[] = $r['sconto'];

    $v_iva[$r['desc_iva']] += $r['iva'];
    $v_totale[$r['desc_iva']] += $r['subtotale'] - $r['sconto'];
}

echo '
        |autofill|
    </tbody>
</table>';


// AGGIUNGO TABELLA IMPIANTI
$rs2 = $dbo->fetchArray('SELECT DISTINCT my_impianti_tipiimpianto.descrizione AS tipo_impianto, my_impianti.nome AS nome, my_impianti.matricola AS matricola FROM co_righe_documenti JOIN my_impianti_interventi ON co_righe_documenti.idintervento=my_impianti_interventi.idintervento JOIN my_impianti ON my_impianti.id = my_impianti_interventi.idimpianto JOIN my_impianti_tipiimpianto ON my_impianti_tipiimpianto.id=my_impianti.idtipoimpianto WHERE co_righe_documenti.iddocumento='.prepare($iddocumento));
$impianti = [];
for ($j = 0; $j < sizeof($rs2); ++$j) {
    $impianti[] = '<b> ['.$rs2[$j]['tipo_impianto'].'] - '.$rs2[$j]['nome']."</b> <small style='color:#777;'>(".$rs2[$j]['matricola'].')</small>';
}
echo '
    <tr>
        <td colspan="5">
        '.tr('Il/i seguente/i intervento/i sono stati effettuati presso il/i vostro/i impianto/i: ').': '.implode(', ', $impianti).'
        </td>
    </tr>';

// Aggiungo diciture per condizioni iva particolari
foreach ($v_iva as $key => $value) {
    $dicitura = $dbo->fetchArray('SELECT dicitura FROM co_iva WHERE descrizione = '.prepare($key));

    if (!empty($dicitura[0]['dicitura'])) {
        $testo = $dicitura[0]['dicitura'];

        echo "
<p class='text-center'>
    <b>".nl2br($testo).'</b>
</p>';
    }
}

if (!empty($records[0]['note'])) {
    echo '
<br>
<p class="small-bold">'.tr('Note', [], ['upper' => true]).':</p>
<p>'.nl2br($records[0]['note']).'</p>';
}

if (abs($records[0]['bollo']) > 0) {
    echo '
<br>
<table style="width: 20mm; font-size: 50%; text-align: center" class="table-bordered">
    <tr>
        <td style="height: 20mm;">
            <br><br>
            '.tr('Spazio per applicazione marca da bollo', [], ['upper' => true]).'
        </td>
    </tr>
</table>';
}

// Info per il footer
$imponibile = sum($imponibile);
$iva = sum($iva) + $records[0]['iva_rivalsainps'];
$sconto = sum($sconto);

$totale = $imponibile + $iva - $sconto + $records[0]['rivalsainps'];
