<?php

/**
 * Header di default.
 * I contenuti di questo file vengono utilizzati per generare l'header delle stampe nel caso non esista un file header.php all'interno della stampa.
 *
 * Per modificare l'header della stampa basta aggiungere un file header.php all'interno della cartella della stampa con i contenuti da mostrare (vedasi templates/fatture/header.php).
 *
 * La personalizzazione specifica dell'header deve comunque seguire lo standard della cartella custom: anche se il file header.php non esiste nella stampa originaria, se si vuole personalizzare l'header bisogna crearlo all'interno della cartella custom.
 */

return '
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
</div>';
