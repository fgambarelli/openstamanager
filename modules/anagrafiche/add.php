<?php

include_once __DIR__.'/../../core.php';

if (get('tipoanagrafica') != '') {
    $rs = $dbo->fetchArray('SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='.prepare(get('tipoanagrafica')));
    $idtipoanagrafica = $rs[0]['idtipoanagrafica'];
} else {
    $idtipoanagrafica = '';
}

echo '
<form action="editor.php?id_module=$id_module$" method="post" onsubmit="return add_anagrafica();">
	<input type="hidden" name="op" value="add">
	<input type="hidden" name="backto" value="record-edit">

	<div class="row">
		<div class="col-md-6">
			{[ "type": "text", "label": "'.tr('Ragione sociale').'", "name": "ragione_sociale", "required": 1, "value": "" ]}
		</div>

		<div class="col-md-6">
			{[ "type": "select", "label": "'.tr('Tipo di anagrafica').'", "name": "idtipoanagrafica[]", "multiple": "1", "required": 1, "values": "query=SELECT idtipoanagrafica AS id, descrizione FROM an_tipianagrafiche WHERE idtipoanagrafica NOT IN (SELECT DISTINCT(x.idtipoanagrafica) FROM an_tipianagrafiche_anagrafiche x INNER JOIN an_tipianagrafiche t ON x.idtipoanagrafica = t.idtipoanagrafica INNER JOIN an_anagrafiche ON an_anagrafiche.idanagrafica = x.idanagrafica  WHERE t.descrizione = \'Azienda\'  AND deleted = 0) ORDER BY descrizione", "value": "'.$idtipoanagrafica .'" ]}
		</div>
	</div>

	<!-- PULSANTI -->
	<div class="row">
		<div class="col-md-12 text-right">
			<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> '. tr("Aggiungi").'</button>
		</div>
	</div>
</form>';
?>
<script>
	function add_anagrafica(){
		if ( $('#ragione_sociale').val()==''){
			alert( "Specifica una ragione sociale." );
			return false;
		}
		else if( $('#idtipoanagrafica option:selected').length == 0 ){
			alert( "Seleziona almeno un tipo di anagrafica." );
			return false;
		}
	}
</script>
