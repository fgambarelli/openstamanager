<?php

include_once __DIR__.'/../../../core.php';

// CONTRATTI COLLEGATI A QUESTO IMPIANTO
echo '
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">'.tr('Contratti collegati a questo impianto').'</h3>
    </div>
    <div class="box-body">';

$results = $dbo->fetchArray('SELECT co_contratti.id, co_contratti.numero, co_contratti.nome as descrizione, co_contratti.data_accettazione, co_contratti.data_conclusione FROM my_impianti_contratti INNER JOIN co_contratti ON my_impianti_contratti.idcontratto=co_contratti.id WHERE idimpianto='.prepare($id_record).' ORDER BY co_contratti.numero DESC');

if (!empty($results)) {
    echo '
        <table class="table table-striped table-hover">
            <tr>
                <th width="60%">'.tr('Contratto').'</th>
                <th>'.tr('Descrizione').'</th>
            </tr>';

    foreach ($results as $result) {
        echo '
            <tr>
                <td>
                    '.Modules::link('Contratti', $result['id'], tr('Contratto num. _NUM_ del _DATE_', [
                        '_NUM_' => $result['numero'],
                        '_DATE_' => Translator::dateToLocale($result['data_accettazione']),
                    ])).'
                </td>
                <td>'.nl2br($result['descrizione']).'</td>
            </tr>';
    }

    echo '
        </table>';
} else {
    echo '
<p>'.tr('Nessun Contratto legato a questo impianto').'...</p>';
}

echo '
    </div>
</div>';
