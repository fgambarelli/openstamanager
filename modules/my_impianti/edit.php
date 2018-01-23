<?php

include_once __DIR__.'/../../core.php';

?><form action="" method="post" role="form" enctype="multipart/form-data">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="update">
	<input type="hidden" name="matricola" value="<?php echo $id_record ?>">

	<!-- DATI ANAGRAFICI -->
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo tr('Dati impianto'); ?></h3>
		</div>

		<div class="panel-body">
			<div class="pull-right">
				<button type="submit" class="btn btn-success"><i class="fa fa-check"></i> <?php echo tr('Salva modifiche'); ?></button>
			</div>
			<div class="clearfix"></div>

			<div class="row">
				<div class="col-md-4">
					<?php
                    $immagine = ($records[0]['immagine'] == '') ? '' : $rootdir.'/files/my_impianti/'.$records[0]['immagine'];
                    ?>
					{[ "type": "image", "label": "<?php echo tr('Immagine'); ?>", "name": "immagine", "class": "img-thumbnail", "value": "<?php echo $immagine ?>" ]}
				</div>
				<div class="col-md-5">
					{[ "type": "select", "label": "<?php echo tr('Tipo impianto'); ?>", "name": "idtipoimpianto", "required": 1, "values": "query=SELECT id, descrizione FROM my_impianti_tipiimpianto", "value": "$idtipoimpianto$" ]}
				</div>

				<div class="col-md-9">
					<div class="row">
						<div class="col-md-4">
							{[ "type": "text", "label": "<?php echo tr('Matricola'); ?>", "name": "matricola", "required": 1, "class": "text-center", "value": "$matricola$" ]}
						</div>

						<div class="col-md-8">
							{[ "type": "text", "label": "<?php echo tr('Nome'); ?>", "name": "nome", "required": 1, "value": "$nome$" ]}
						</div>
						<div class="clearfix"></div>

						<div class="col-md-12">
							{[ "type": "select", "label": "<?php echo tr('Cliente'); ?>", "name": "idanagrafica", "required": 1, "values": "query=SELECT an_anagrafiche.idanagrafica AS id, ragione_sociale AS descrizione FROM an_anagrafiche INNER JOIN (an_tipianagrafiche_anagrafiche INNER JOIN an_tipianagrafiche ON an_tipianagrafiche_anagrafiche.idtipoanagrafica=an_tipianagrafiche.idtipoanagrafica) ON an_anagrafiche.idanagrafica=an_tipianagrafiche_anagrafiche.idanagrafica WHERE descrizione='Cliente' AND deleted=0 ORDER BY ragione_sociale", "value": "$idanagrafica$", "extra": "onchange=\"load_preventivi( this.value ); load_contratti( this.value ); $('#idsede').load( '<?php echo $rootdir ?>/ajax_autocomplete.php?module=Anagrafiche&op=get_sedi_select&idanagrafica='+$('#idanagrafica option:selected').val() ); load_impianti( $('#idanagrafica option:selected').val(), $('#idsede option:selected').val() );\"", "ajax-source": "clienti" ]}
						</div>

					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					{[ "type": "select", "label": "<?php echo tr('Tecnico assegnato'); ?>", "name": "idtecnico", "values": "query=SELECT an_anagrafiche.idanagrafica AS id, ragione_sociale AS descrizione FROM an_anagrafiche INNER JOIN (an_tipianagrafiche_anagrafiche INNER JOIN an_tipianagrafiche ON an_tipianagrafiche_anagrafiche.idtipoanagrafica=an_tipianagrafiche.idtipoanagrafica) ON an_anagrafiche.idanagrafica=an_tipianagrafiche_anagrafiche.idanagrafica WHERE descrizione='Tecnico' AND deleted=0 ORDER BY ragione_sociale ASC", "value": "$idtecnico$" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "date", "label": "<?php echo tr('Data installazione'); ?>", "name": "data", "value": "$data$" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "select", "label": "<?php echo tr('Sede'); ?>", "name": "idsede", "values": "query=SELECT 0 AS id, 'Sede legale' AS descrizione UNION SELECT id, CONCAT_WS( ' - ', nomesede, citta ) AS descrizione FROM an_sedi WHERE idanagrafica='$idanagrafica$'", "value": "$idsede$" ]}
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					{[ "type": "textarea", "label": "<?php echo tr('Descrizione'); ?>", "name": "descrizione", "value": "$descrizione$" ]}
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					{[ "type": "text", "label": "<?php echo tr('Proprietario'); ?>", "name": "proprietario", "value": "$proprietario$" ]}
				</div>

			</div>

			<div class="row">
				<div class="col-md-4">
					{[ "type": "text", "label": "<?php echo tr('Ubicazione'); ?>", "name": "ubicazione", "value": "$ubicazione$" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "select", "label": "<?php echo tr('Zona'); ?>", "name": "idzona", "values": "query=SELECT id, CONCAT_WS( ' - ', nome, descrizione) AS descrizione FROM an_zone ORDER BY descrizione ASC", "value": "$idzona$" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "text", "label": "<?php echo tr('Referente Impianto'); ?>", "name": "occupante", "value": "$occupante$" ]}
				</div>
			</div>

			<div class="row">
				<div class="col-md-4">
					{[ "type": "checkbox", "label": "<?php echo tr('Delega criter '); ?>", "name": "delega_criter", "value": "$delega_criter$" ]}
				</div>

				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('Trasferta'); ?>", "name": "minuti", "decimals": "0", "value": "$minuti$", "icon-after": "minuti"  ]}
				</div>
				
				<div class="col-md-4">
					{[ "type": "number", "label": "<?php echo tr('KM (Andata e Ritorno)'); ?>", "name": "km", "decimals": "0", "value": "$km$", "icon-after": "km"  ]}
				</div>
				
			</div>

		</div>
	</div>
</form>

{( "name": "filelist_and_upload", "id_module": "<?php echo $id_module ?>", "id_record": "<?php echo $id_record ?>" )}

<a class="btn btn-danger ask" data-backto="record-list">
    <i class="fa fa-trash"></i> <?php echo tr('Elimina'); ?>
</a>
