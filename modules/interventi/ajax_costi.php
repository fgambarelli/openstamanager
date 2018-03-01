<?php

include_once __DIR__.'/../../core.php';
include_once __DIR__.'/modutil.php';

$idiva = get_var('Iva predefinita');
$rs_iva = $dbo->fetchArray('SELECT descrizione, percentuale, indetraibile FROM co_iva WHERE id='.prepare($idiva));
($rs_iva[0]['percentuale']>0) ? $hide = '' : $hide = 'hide';

if (Auth::admin() || $_SESSION['gruppo'] != 'Tecnici') {
    $costi = get_costi_intervento($id_record);
    
    $rss = $dbo->fetchArray('SELECT in_statiintervento.completato FROM in_statiintervento INNER JOIN in_interventi ON in_statiintervento.idstatointervento=in_interventi.idstatointervento WHERE in_interventi.id='.prepare($id_record));
    $flg_completato = $rss[0]['completato'];
    
    if( $flg_completato ){
        $readonly = 'readonly';
    } else {
        $readonly = '';
    }

    echo '
<!-- Riepilogo dei costi -->
<table class="table table condensed table-striped table-hover table-bordered">
    <tr>
        <th width="40%"></th>
        <th width="20%" class="text-center">'.tr('Costo', [], ['upper' => true]).'</th>
        <th width="20%" class="text-center">'.tr('Addebito', [], ['upper' => true]).'</th>
        <th width="20%" class="text-center">'.tr('Tot. Scontato', [], ['upper' => true]).'</th>
    </tr>

    <tr>
        <th>'.tr('Totale manodopera', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['manodopera_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['manodopera_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['manodopera_scontato']).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Totale diritto di chiamata', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['dirittochiamata_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['dirittochiamata_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['dirittochiamata_scontato']).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Totale viaggio', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['viaggio_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['viaggio_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['viaggio_scontato']).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Totale articoli', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['ricambi_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['ricambi_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['ricambi_scontato']).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Totale altre spese', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['altro_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['altro_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['altro_scontato']).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Sconto incondizionato', [], ['upper' => true]).'</th>
        <td class="text-right">-</td>
        <td class="text-right">-</td>
        <td class="text-right">'.Translator::numberToLocale(-$costi['sconto_globale']).' &euro;</td>
    </tr>


	<tr class='.$hide.' >
        <th>'.tr('Imponibile', [], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale($costi['totale_costo']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['totale_addebito']).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale($costi['totale']).' &euro;</td>
    </tr>


	<tr class='.$hide.' >
        <th>'.tr('IVA (_DESC_)', [ _DESC_ => $rs_iva[0]['descrizione']], ['upper' => true]).'</th>
        <td class="text-right">'.Translator::numberToLocale(($costi['totale_costo']/100*$rs_iva[0]['percentuale'])).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale(($costi['totale_addebito']/100*$rs_iva[0]['percentuale'])).' &euro;</td>
        <td class="text-right">'.Translator::numberToLocale(($costi['totale']/100*$rs_iva[0]['percentuale'])).' &euro;</td>
    </tr>

    <tr>
        <th>'.tr('Totale', [], ['upper' => true]).'</th>
        <th class="text-right">'.Translator::numberToLocale($costi['totale_costo']+($costi['totale_costo']/100*$rs_iva[0]['percentuale'])).' &euro;</th>
        <th class="text-right">'.Translator::numberToLocale($costi['totale_addebito']+($costi['totale_addebito']/100*$rs_iva[0]['percentuale'])).' &euro;</th>
        <th class="text-right">'.Translator::numberToLocale($costi['totale']+($costi['totale']/100*$rs_iva[0]['percentuale'])).' &euro;</th>
    </tr>
</table>';
}

// Lettura dello sconto incondizionato
$rss = $dbo->fetchArray('SELECT sconto_globale, tipo_sconto_globale FROM in_interventi WHERE id='.prepare($id_record));
$sconto = $rss[0]['sconto_globale'];
$tipo_sconto = $rss[0]['tipo_sconto_globale'];

echo '

<!-- SCONTO -->
<div class="row">
    <div class="col-md-4 pull-right">
        {[ "type": "number", "label": "'.tr('Sconto incondizionato').'", "name": "sconto_globale", "value": "'.$sconto.'", "icon-after": "choice|untprc|'.$tipo_sconto.'", "extra": "'.$readonly.'" ]}
    </div>
</div>';

echo '
<script src="'.$rootdir.'/lib/init.js"></script>';
