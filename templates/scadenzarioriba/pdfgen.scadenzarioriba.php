<?php

include_once __DIR__.'/../../core.php';

$module_name = 'Scadenzario';

// carica report html
$report = file_get_contents($docroot.'/templates/scadenzario/scadenzario.html');
$body = file_get_contents($docroot.'/templates/scadenzario/scadenzario_body.html');

include_once $docroot.'/templates/pdfgen_variables.php';

/*
    Dati scadenzario
*/

$titolo = 'Riba da presentare';
$add_where = "AND co_scadenziario.riba_da_pres=1";


$body .= '<h3>'.$titolo.' dal '.Translator::dateToLocale($_SESSION['period_start']).' al '.Translator::dateToLocale($_SESSION['period_end'])."</h3>\n";
$body .= "<table class=\"table_values\" cellspacing=\"0\" border=\"0\" cellpadding=\"0\" style=\"table-layout:fixed; border-color:#aaa;\">\n";
$body .= "<col width=\"100\"><col width=\"50\"><col width=\"50\"><col width=\"100\"><col width=\"100\"><col width=\"70\"><col width=\"50\"><col width=\"70\">\n";

$body .= "<thead>\n";
$body .= "	<tr>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Anagrafica</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Indirizzo</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Num</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Data</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Tipo di pagamento</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Banca Appoggio</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>IBAN</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Data scadenza</th>\n";
$body .= "		<th style='padding:2mm; background:#eee;'>Importo</th>\n";
$body .= "	</tr>\n";
$body .= "</thead>\n";

$body .= "<tbody>\n";

$rs = $dbo->fetchArray("SELECT co_scadenziario.id AS id, ragione_sociale AS `Anagrafica`, co_pagamenti.descrizione AS `Tipo di pagamento`, CONCAT( co_tipidocumento.descrizione, CONCAT( ' numero ', IF(numero_esterno<>'', numero_esterno, numero) ) ) AS `Documento`, DATE_FORMAT(data_emissione, '%d/%m/%Y') AS `Data emissione`, DATE_FORMAT(scadenza, '%d/%m/%Y') AS `Data scadenza`, da_pagare AS `Importo`, pagato AS `Pagato`, IF(scadenza<NOW(), '#ff7777', '') AS _bg_, co_documenti.numero num_fatt, DATE_FORMAT(co_documenti.data, '%d/%m/%Y') AS data_fatt, an_anagrafiche.codiceiban AS iban, an_anagrafiche.appoggiobancario AS appoggiobancario, CONCAT (an_anagrafiche.indirizzo,' ',an_anagrafiche.citta, ' ', an_anagrafiche.cap , ' ' ,an_anagrafiche.provincia ) AS indirizzo_completo FROM co_scadenziario
    INNER JOIN co_documenti ON co_scadenziario.iddocumento=co_documenti.id
    INNER JOIN an_anagrafiche ON co_documenti.idanagrafica=an_anagrafiche.idanagrafica
    INNER JOIN co_pagamenti ON co_documenti.idpagamento=co_pagamenti.id
    INNER JOIN co_tipidocumento ON co_documenti.idtipodocumento=co_tipidocumento.id
WHERE ABS(pagato) < ABS(da_pagare) ".$add_where." AND scadenza >= '".$_SESSION['period_start']."' AND scadenza <= '".$_SESSION['period_end']."' ORDER BY scadenza ASC");

for ($i = 0; $i < sizeof($rs); ++$i) {
    $body .= '	<tr>';
    $body .= '		<td>'.$rs[$i]['Anagrafica']."</td>\n";
    $body .= '		<td>'.$rs[$i]['indirizzo_completo']."</td>\n";
    $body .= '		<td>'.$rs[$i]['num_fatt']."</td>\n";
	$body .= '		<td>'.$rs[$i]['data_fatt']."</td>\n";	
    $body .= '		<td>'.$rs[$i]['Tipo di pagamento']."</td>\n";
    $body .= '		<td>'.$rs[$i]['appoggiobancario']."</td>\n";	
	$body .= '		<td>'.$rs[$i]['iban']."</td>\n";	
    $body .= "		<td align='center'>".$rs[$i]['Data scadenza']."</td>\n";
    $body .= "		<td align='right'>".Translator::numberToLocale($rs[$i]['Importo'])."</td>\n";
    $body .= "	</tr>\n";

    $totale_da_pagare += $rs[$i]['Importo'];
    $totale_pagato += $rs[$i]['Pagato'];
}

$body .= "	<tr>\n";
$body .= "		<td colspan='7' align='right'><b>TOTALE:</b></td><td align='right'>".Translator::numberToLocale($totale_da_pagare)."</td><td align='right'>".Translator::numberToLocale($totale_pagato)."</td>\n";
$body .= "	</tr>\n";

$body .= "</tbody>\n";
$body .= "</table>\n";

$orientation = 'L';
$report_name = 'Scadenzario_Totale.pdf';
