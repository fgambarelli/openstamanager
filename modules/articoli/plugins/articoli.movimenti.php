<?php

include_once __DIR__.'/../../../core.php';

// Movimentazione degli articoli

echo '
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">'.tr('Movimenti').'</h3>
    </div>
    <div class="box-body">';

// Calcolo la quantità dai movimenti in magazzino
$rst = $dbo->fetchArray('SELECT SUM(qta) AS qta_totale FROM mg_movimenti WHERE idarticolo='.prepare($id_record).' AND (idintervento IS NULL OR idautomezzo = 0)');
$qta_totale = $rst[0]['qta_totale'];

echo '
<p>'.tr('Quantità calcolata dai movimenti').': '.Translator::numberToLocale($qta_totale).' '.$rs[0]['unita_misura'].'</p>';

// Elenco movimenti magazzino
$query = 'SELECT *, (SELECT an_anagrafiche.ragione_sociale from an_anagrafiche WHERE an_anagrafiche.idanagrafica =
(CASE WHEN mg_movimenti.iddocumento >0
THEN (SELECT co_documenti.idanagrafica from co_documenti where co_documenti.id=mg_movimenti.iddocumento)
ELSE CASE WHEN mg_movimenti.idintervento >0
THEN (SELECT in_interventi.idanagrafica from in_interventi where in_interventi.id=mg_movimenti.idintervento)
END END)) AS CLIENTE  FROM mg_movimenti WHERE idarticolo='.prepare($id_record).' ORDER BY created_at DESC';
if (empty($_GET['show_all1'])) {
    $query .= ' LIMIT 0, 20';
}

$rs2 = $dbo->fetchArray($query);

if (!empty($rs2)) {
    if (empty($_GET['show_all1'])) {
        echo '
        <p><a href="'.$rootdir.'/editor.php?id_module='.$id_module.'&id_record='.$id_record.'&show_all1=1#tab_'.$id_plugin.'">[ '.tr('Mostra tutti i movimenti').' ]</a></p>';
    } else {
        echo '
        <p><a href="'.$rootdir.'/editor.php?id_module='.$id_module.'&id_record='.$id_record.'&show_all1=0#tab_'.$id_plugin.'">[ '.tr('Mostra solo gli ultimi 20 movimenti').' ]</a></p>';
    }

    echo '
        <table class="table table-striped table-condensed table-bordered">
            <tr>
                <th class="text-center" width="100">'.tr('Q.tà').'</th>
                <th width="720">'.tr('Causale').'</th>
                <th>'.tr('Data').'</th>
                <th class="text-center">#</th>
            </tr>';
    foreach ($rs2 as $r) {
        // Quantità
        echo '
            <tr>
                <td class="text-right">'.Translator::numberToLocale($r['qta']).'</td>';

        // Causale
        echo '
                <td>'.$r['movimento'].'</td>';

        // Data
        echo '
                <td>'.Translator::timestampToLocale($r['created_at']).'</td>';

        // Operazioni
        echo '
                <td class="text-center">';

        if (Auth::admin()) {
            echo '
                    <a class="btn btn-danger btn-sm ask" data-backto="record-edit" data-op="delmovimento" data-idmovimento="'.$r['id'].'">
                        <i class="fa fa-trash"></i>
                    </a>';
        }

        echo '
                </td>
            </tr>';
    }
    echo '
        </table>';
} else {
    echo '
        <p>'.tr('Nessun movimento disponibile').'...</p>';
}

echo '
    </div>
</div>';
