<?php

echo '
<!-- Intestazione fornitore -->
<div class="row">
    <div class="col-xs-4">
        <img src="'.__DIR__.'/logo_azienda.jpg" alt="Logo" border="0"/>
    </div>
    <div class="col-xs-8 text-right">
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
       </table>
    </div>
</div>
	
<div class="row">
    <!-- Dati PAGAMENTO -->
    <div class="col-xs-7">

	   <table class="table">
            <tr>
                <td colspan="1" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('Pagamento', [], ['upper' => true]).'</p>
                    <p>$pagamento$</p>0
                </td>
                <td colspan="3" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('Banca di appoggio', [], ['upper' => true]).'</p>
                    <p>$c_appoggiobancario$</p>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="height:10mm;padding-top:2mm;">
                    <p class="small-bold">'.tr('IBAN').'</p>
                    <p>$c_codiceiban$</p>
                </td>
            </tr>

        </table>
    </div>
</div>';
