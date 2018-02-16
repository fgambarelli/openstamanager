<?php

include_once __DIR__.'/../../core.php';

include_once $docroot.'/modules/interventi/modutil.php';

$report_name = 'intervento_'.$idintervento.'.pdf';

/*
    Dati intervento
*/
echo '
<table class="table table-bordered">
    <tr>
        <th colspan="5" style="font-size:13pt;" class="text-center">'.tr('Rapporto operazioni e interventi', [], ['upper' => true]).'</th>
    </tr>

    <tr>
        <td class="text-center" style="width:20%">'.tr('Num. Interno').': <b>'.$records[0]['codice'].'</b></td>
        <td class="text-center" style="width:20%">'.tr('Rapportino num.').': <b>'.$records[0]['num_rapp'].'</b></td>
        <td class="text-center" style="width:20%">'.tr('Data').': <b>'.Translator::dateToLocale($records[0]['data_richiesta']).'</b></td>
        <td class="text-center" style="width:20%">'.tr('Preventivo num.').': <b>'.$records[0]['numero_preventivo'].'</b></td>
        <td class="text-center" style="width:20%">'.tr('Contratto num.').': <b>'.$records[0]['numero_contratto'].'</b></td>
    </tr>';

// Dati cliente
echo '
    <tr>
        <td colspan=4>
            '.tr('Cliente').': <b>'.$c_ragionesociale.'</b>
        </td>';

// Codice fiscale
echo '
        <td>
            '.tr('P.Iva').': <b>'.strtoupper($c_piva).'</b>
        </td>
    </tr>';

// riga 2
echo '
    <tr>
        <td colspan="5">
            '.tr('Via').': <b>'.$c_indirizzo.'</b> -
            '.tr('CAP').': <b>'.$c_cap.'</b> -
            '.tr('Comune').': <b>'.$c_citta.' ('.strtoupper($c_provincia).')</b>
        </td>
    </tr>';

echo '
    <tr>
        <td colspan="5">
            '.tr('Telefono').': <b>'.$c_telefono.'</b>';
if (!empty($c_cellulare)) {
    echo' - '.tr('Cellulare').': <b>'.$c_cellulare.'</b>';
}
echo '
        </td>
    </tr>';

// Richiesta
echo '
    <tr>
        <td colspan="5" style="height:20mm;">
            <b>'.tr('Richiesta').':</b>
            <p>'.nl2br($records[0]['richiesta']).'</p>
        </td>
    </tr>';

// Descrizione
echo '
    <tr>
        <td colspan="5" style="height:20mm;">
            <b>'.tr('Descrizione').':</b>
            <p>'.nl2br($records[0]['descrizione_intervento']).'</p>
        </td>
    </tr>';

echo '
</table>';

$totale = [];

// MATERIALE UTILIZZATO
$rs2 = $dbo->fetchArray('SELECT *, (SELECT codice FROM mg_articoli WHERE id=idarticolo) AS codice_art FROM `mg_articoli_interventi` WHERE idintervento='.prepare($idintervento)." AND NOT idarticolo='0' ORDER BY idarticolo ASC");
if (!empty($rs2)) {
    echo '
<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="4" class="text-center">
                <b>'.tr('Materiale utilizzato', [], ['upper' => true]).'</b>
            </th>
        </tr>

        <tr>
            <th style="font-size:8pt;width:20%" class="text-center">
                <b>'.tr('Codice').'</b>
            </th>

            <th style="font-size:8pt;width:50%" class="text-center">
                <b>'.tr('Descrizione').'</b>
            </th>

            <th style="font-size:8pt;width:15%" class="text-center">
                <b>'.tr('Q.tà').'</b>
            </th>

            <th style="font-size:8pt;width:15%" class="text-center">
                <b>'.tr('Prezzo').'</b>
            </th>
        </tr>
    </thead>

    <tbody>';

    foreach ($rs2 as $r) {
        echo '
        <tr>';

        // Codice
        echo '
            <td>
                '.$r['codice_art'].'
            </td>';

        // Descrizione
        echo '
            <td>
                '.$r['descrizione'].'
            </td>';

        // Quantità
        echo '
            <td class="text-center">
                '.Translator::numberToLocale($r['qta']).' '.$r['um'].'
            </td>';

        // Netto
        $netto = $r['prezzo_vendita'] * $r['qta'] - $r['sconto'];
        echo '
            <td class="text-center">
                '.($mostra_prezzi ? Translator::numberToLocale($netto) : '-').'
            </td>
        </tr>';
    }

    echo '
    </tbody>';

    // Totale spesa articoli
    if ($mostra_prezzi) {
        echo '
    <tr>
        <td colspan="2" class="text-right">
            <b>'.tr('Totale materiale utilizzato', [], ['upper' => true]).':</b>
        </td>

        <th colspan="2" class="text-center">
            <b>'.Translator::numberToLocale($costi_intervento['ricambi_scontato']).' &euro;</b>
        </th>
    </tr>';
    }

    echo '
</table>';
}

// FINE MATERIALE UTILIZZATO

// Conteggio SPESE AGGIUNTIVE
$rs2 = $dbo->fetchArray('SELECT * FROM in_righe_interventi WHERE idintervento='.prepare($idintervento).' ORDER BY id ASC');
if (!empty($rs2)) {
    echo '
<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="4" class="text-center">
                <b>'.tr('Spese aggiuntive', [], ['upper' => true]).'</b>
            </th>
        </tr>

        <tr>
            <th style="font-size:8pt;width:50%" class="text-center">
                <b>'.tr('Descrizione').'</b>
            </th>

            <th style="font-size:8pt;width:15%" class="text-center">
                <b>'.tr('Q.tà').'</b>
            </th>

            <th style="font-size:8pt;width:15%" class="text-center">
                <b>'.tr('Prezzo unitario').'</b>
            </th>

            <th style="font-size:8pt;width:20%" class="text-center">
                <b>'.tr('Subtot.').'</b>
            </th>
        </tr>
    </thead>

    <tbody>';

    foreach ($rs2 as $r) {
        // Articolo
        echo '
    <tr>
        <td>
            '.nl2br($r['descrizione']).'
        </td>';

        // Quantità
        echo '
        <td class="text-center">
            '.Translator::numberToLocale($r['qta']).'
        </td>';

        // Prezzo unitario
        echo '
        <td class="text-center">
            '.($mostra_prezzi ? Translator::numberToLocale($r['prezzo_vendita']).' &euro;' : '-').'
        </td>';

        // Prezzo totale
        $netto = $r['prezzo_vendita'] * $r['qta'] - $r['sconto'];
        echo '
        <td class="text-center">
            '.($mostra_prezzi ? Translator::numberToLocale($netto) : '-').'
        </td>
    </tr>';
    }
    echo '
    </tbody>';

    if ($mostra_prezzi) {
        // Totale spese aggiuntive
        echo '
    <tr>
        <td colspan="3" class="text-right">
            <b>'.tr('Totale spese aggiuntive', [], ['upper' => true]).':</b>
        </td>

        <th class="text-center">
            <b>'.Translator::numberToLocale($costi_intervento['altro_scontato']).' &euro;</b>
        </th>
    </tr>';
    }

    echo '
</table>';
}

// FINE SPESE AGGIUNTIVE

// INTESTAZIONE ELENCO TECNICI
echo '
<table class="table table-bordered vertical-middle">
    <thead>
        <tr>
            <th class="text-center" colspan="5" style="font-size:11pt;">
                <b>'.tr('Ore tecnici', [], ['upper' => true]).'</b>
            </th>
        </tr>
        <tr>
            <th class="text-center" style="font-size:8pt;width:30%">
                <b>'.tr('Tecnico').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:15%">
                <b>'.tr('Data').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:10%">
                <b>'.tr('Dalle').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:10%">
                <b>'.tr('Alle').'</b>
            </th>

            <td class="text-center" style="font-size:6pt;width:35%">
                '.tr('I dati del ricevente verrano trattati in base al D.lgs n. 196/2003').'
            </td>
        </tr>
    </thead>

    <tbody>';

// Sessioni di lavoro dei tecnici
$rst = $dbo->fetchArray('SELECT an_anagrafiche.*, in_interventi_tecnici.* FROM in_interventi_tecnici JOIN an_anagrafiche ON in_interventi_tecnici.idtecnico=an_anagrafiche.idanagrafica WHERE in_interventi_tecnici.idintervento='.prepare($idintervento).' ORDER BY in_interventi_tecnici.orario_inizio');

foreach ($rst as $i => $r) {
    echo '
    <tr>';

    // nome tecnico
    echo '
    	<td>
    	    '.$r['ragione_sociale'].'
    	</td>';

    // data
    echo '
    	<td class="text-center">
            '.Translator::dateToLocale($r['orario_inizio'], '-').'
    	</td>';

    // ora inizio
    echo '
    	<td class="text-center">
            '.Translator::timeToLocale($r['orario_inizio'], '-').'
    	</td>';

    // ora fine
    echo '
    	<td class="text-center">
            '.Translator::timeToLocale($r['orario_fine'], '-').'
        </td>';

    // Spazio aggiuntivo
    if ($i == 0) {
        echo '
    	<td class="text-center" style="font-size:8pt;">
            '.tr('Si dichiara che i lavori sono stati eseguiti ed i materiali installati').'
        </td>';
    } else {
        echo '
    	<td class="text-center" style="border-bottom:0px;border-top:0px;"></td>';
    }

    echo '
    </tr>';
}



// Ore lavorate
$ore = get_ore_intervento($idintervento);

echo '
    <tr>
        <td class="text-center">
            <small>'.tr('Ore lavorate').':</small><br/><b>'.Translator::numberToLocale($ore).'</b>
        </td>';

// Diritto di chiamata
if ($mostra_prezzi) {
  echo '
      <td class="text-center" colspan="3">
          <small>'.tr('Diritto di chiamata').':</small><br/><b>'.Translator::numberToLocale($records[0]['tot_dirittochiamata']).' &euro;</b>
      </td>';
} else {
    echo '
      <td class="text-center" colspan="2">-</td>
  ';
}


// Costo totale manodopera
if ($mostra_prezzi) {
    echo '
        <td colspan="2" class="text-center">
            <small>'.tr('Totale manodopera').':</small><br/><b>'.Translator::numberToLocale($costi_intervento['manodopera_addebito']).' &euro;</b>
        </td>';
} else {
    echo '
        <td colspan="2" class="text-center">-</td>';
}

// Timbro e firma



// Totale km



// Costo trasferta


// TOTALE COSTI FINALI
if ($mostra_prezzi) {
    // Totale imponibile
    echo '
    <tr>
        <td colspan="4" class="text-right">
            <b>'.tr('Imponibile', [], ['upper' => true]).':</b>
        </td>

        <th class="text-center">
            <b>'.Translator::numberToLocale($costi_intervento['totale_addebito']).' &euro;</b>
        </th>
    </tr>';

    $sconto_addebito = $costi_intervento['totale_addebito'] - $costi_intervento['totale_scontato'];

    $totale_sconto = $costi_intervento['sconto_globale'] + $sconto_addebito;

    // Eventuale sconto totale
    if (!empty($totale_sconto)) {
        echo '
        <tr>
            <td colspan="4" class="text-right">
            <b>'.tr('Sconto', [], ['upper' => true]).':</b>
            </td>

            <th class="text-center">
                <b>-'.Translator::numberToLocale($totale_sconto).' &euro;</b>
            </th>
        </tr>';

        // Imponibile scontato
        echo '
        <tr>
            <td colspan="4" class="text-right">
                <b>'.tr('Imponibile scontato', [], ['upper' => true]).':</b>
            </td>

            <th class="text-center">
                <b>'.Translator::numberToLocale($costi_intervento['totale']).' &euro;</b>
            </th>
        </tr>';
    }

    // Leggo iva da applicare
    $rs1 = $dbo->fetchArray('SELECT percentuale FROM co_iva WHERE id='.prepare(get_var('Iva predefinita')));
    $percentuale_iva = $rs1[0]['percentuale'];

    $iva = ($costi_intervento['totale'] / 100 * $percentuale_iva);

    // IVA
    // Totale intervento
    echo '
    <tr>
        <td colspan="4" class="text-right">
            <b>'.tr('Iva (_PRC_%)', [
                '_PRC_' => Translator::numberToLocale($percentuale_iva, 0),
            ], ['upper' => true]).':</b>
        </td>

        <th class="text-center">
            <b>'.Translator::numberToLocale($iva).' &euro;</b>
        </th>
    </tr>';

    $totale = sum($costi_intervento['totale'], $iva);

    // TOTALE INTERVENTO
    echo '
    <tr>
    	<td colspan="4" class="text-right">
            <b>'.tr('Totale intervento', [], ['upper' => true]).':</b>
    	</td>
    	<th class="text-center">
    		<b>'.Translator::numberToLocale($totale).' &euro;</b>
    	</th>
    </tr>';
}

echo '
</table>';


// INTESTAZIONE impianti
echo '
<table class="table table-bordered vertical-middle">
    <thead>
        <tr>
            <th class="text-center" colspan="6" style="font-size:11pt;">
                <b>'.tr('impianti', [], ['upper' => true]).'</b>
            </th>
        </tr>
        <tr>
            <th class="text-center" style="font-size:8pt;width:10%">
                <b>'.tr('Matr.').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:7%">
                <b>'.tr('Tipo').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:25%">
                <b>'.tr('Nome').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:22%">
                <b>'.tr('Descrizione').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:20%">
                <b>'.tr('Ubicazione').'</b>
            </th>

            <th class="text-center" style="font-size:8pt;width:16%">
                <b>'.tr('Referente').'</b>
            </th>

        </tr>
    </thead>

    <tbody>';

    // TABELLA IMPIANTI
    $rst = $dbo->fetchArray('SELECT *, (select descrizione from my_impianti_tipiimpianto WHERE my_impianti_tipiimpianto.id = my_impianti.idtipoimpianto) AS tipoimpianto FROM my_impianti JOIN my_impianti_interventi ON my_impianti_interventi.idimpianto=my_impianti.id WHERE my_impianti_interventi.idintervento='.prepare($idintervento).' ORDER BY my_impianti.id');

    foreach ($rst as $i => $r) {
        echo '
        <tr>';

        // matricola
        echo '
        	<td class="text-center" style="font-size:6pt">
        	    '.$r['matricola'].'
        	</td>';

        // tipo
          echo '
          <td class="text-center" style="font-size:6pt">
                '.$r['tipoimpianto'].'
          </td>';

        // nome
        echo '
        	<td class="text-center" style="font-size:7pt">
                '.$r['nome'].'
        	</td>';

        // descrizione
        echo '
        	<td class="text-center" style="font-size:7pt">
                '.$r['descrizione'].'
        	</td>';

        // Ubicazione
        echo '
        	<td class="text-center" style="font-size:7pt">
                '.$r['ubicazione'].'
            </td>';

        // Ubicazione
        echo '
          <td class="text-center" style="font-size:7pt">
                '.$r['occupante'].'
          </td>';



        echo '
        </tr>

        ';
    }


echo '
</table>
  <div>
  </div>'
;
