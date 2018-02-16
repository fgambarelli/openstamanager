<?php
echo '
<!-- Intestazione fornitore -->
<div class="row">
    <div class="col-xs-4">
        <img src="'.__DIR__.'/logo_azienda.jpg" alt="Logo" border="0"/>
    </div>
    <div class="col-xs-6 text-right">
        <p><b>$f_ragionesociale$</b></p>
        <p>'.tr('Sede ').'$f_indirizzo$ $f_citta_full$</p>
        <p>'.(!empty($f_telefono) ? tr('Tel/Fax').': ' : '').'$f_telefono$'.tr(' Cell. ').' $f_cellulare$</p>
		<p>'.(!empty($f_fax) ? tr('Email ').': ' : '').'$f_email$'.(!empty($f_sitoweb) ? tr(' PEC ').': ' : '').'$f_sitoweb$</p>		
        <p>'.(!empty($f_piva) ? tr('P.Iva ').': ' : '').'$f_piva$'.(!empty($f_codicefiscale) ? tr(' C.F. ').': ' : '').'$f_codicefiscale$'.tr(' Reg.Imp. ').' $f_codiceri$</p>
        <p>'.(!empty($f_codiceiban) ? tr('Banca di appoggio ').': ' : '').'$f_appoggiobancario$'.tr(' IBAN ').' $f_codiceiban$</p>
    </div>
</div>
<br>
<div class="row">
    <!-- Dati Fattura -->
    <div class="col-xs-6">
        <div class="text-left" style="height:5mm;">
            <b>$tipo_doc$</b>
        </div>
        <table class="table">
            <tr>
                <td valign="top" class="border-full text-center">
                    <p class="small-bold">'.tr('Nr. documento', [], ['upper' => true]).'</p>
                </td>
                <td class="border-right border-bottom border-top text-center">
                    <p class="small-bold">'.tr('Data documento', [], ['upper' => true]).'</p>
                </td>
                <td class="border-right border-bottom border-top center text-center">
                    <p class="small-bold">'.tr('Foglio', [], ['upper' => true]).'</p>
                </td>
            </tr>
            <tr>
                <td valign="top" class="border-full text-center">
                    <p><b>$numero_doc$</b></p>
                </td>
                <td class="border-right border-bottom border-top text-center">
                    <p><b>$data$</b></p>
                </td>
                <td class="border-right border-bottom border-top center text-center">
                    <p>{PAGENO}/{nb}</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('Pagamento', [], ['upper' => true]).'</p>
                    <p>$pagamento$</p>
                </td>
				<td colspan="3" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('Banca appoggio (Cliente)', [], ['upper' => true]).'</p>
                    <p class="small">$c_appoggiobancario$</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('IBAN (Cliente)').'</p>
                    <p>$c_codiceiban$</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-xs-5 col-xs-offset-1">
        <table class="table" style="width:100%;margin-top:5mm;">
            <tr>
                <td colspan=2 class="border-full" style="height:20mm;">
                    <p class="small-bold">'.tr('Spett.le', [], ['upper' => true]).'</p>
                    <p><b>$c_ragionesociale$</b></p>
                    <p>$c_indirizzo$ $c_citta_full$</p>
                </td>
            </tr>
            <tr>
                <td class="border-bottom border-left">
                    <p class="small-bold">'.tr('Partita IVA', [], ['upper' => true]).'</p>
                </td>
                <td class="border-right border-bottom text-right">
                    <p>$c_piva$</p>
                </td>
            </tr>
            <tr>
                <td class="border-bottom border-left">
                    <p class="small-bold">'.tr('Codice fiscale', [], ['upper' => true]).'</p>
                </td>
                <td class="border-right border-bottom text-right">
                    <p>$c_codicefiscale$</p>
                </td>
            </tr>
        </table>
    </div>
</div>';
