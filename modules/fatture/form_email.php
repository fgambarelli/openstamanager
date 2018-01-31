<?php
	include_once __DIR__.'/../../core.php';
	$module_name = 'Fatture di vendita';
	//include($docroot."/lib/permissions_check.php");

	$iddocumento = save($_GET['iddocumento']);

	// Lettura dati fattura
	$query = "SELECT * FROM co_documenti WHERE id=\"".$iddocumento."\"";
	$rs = $dbo->fetchArray( $query );
	$n = sizeof($rs);
	if( $n==0 ){
		echo "Fattura inesistente!";
		exit;
	}
	
	//cliente, numero e data in base al documento
	$idanagrafica = $rs[0]['idanagrafica'];
	$idcliente = $rs[0]['idanagrafica'];
	$numero = $rs[0]['numero_esterno'];
	$data = date( "d/m/Y", strtotime($rs[0]['data']) );
	
	$rapportino_nome = sanitizeFilename("Fattura_".$numero.".pdf");
	$filename = $docroot.'/files/'.strtolower($module_name).'/'.$rapportino_nome; 

	$oggetto = "Invio fattura num. $numero del $data";
	$testo = "Gentile Cliente,<br/>\n
				inviamo in allegato la fattura num. $numero del $data.<br/><br/>\n
				Distinti saluti<br/>";
	
	$dst_dir = $docroot."/files/".strtolower($module_name)."/";

				
	$iddocumento = $id_record; // Fix temporaneo per la stampa
	$ptype = 'fatture';


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// creazione file da allegare

	require $docroot.'/pdfgen.php';

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if( !file_exists($filename) ){
		echo "<p align='center'><br/><br/>per procedere all'invio genera prima il pdf della fattura tramite il comando stampa.</p>\n";
		exit;
	}


	echo "<script type=\"text/javascript\" charset=\"utf-8\" src=\"".$rootdir."/lib/jscripts/jquery/plugins/ckeditor/ckeditor.js\"></script>\n";


	echo "<form id=\"send_email\" action=\"".$rootdir."/editor.php?id_module=$id_module&id_record=$id_record\" onsubmit=\"return check_submit()\" method=\"post\">\n";

	echo "	<input type='hidden' name='backto' value='record-edit'>\n";
	echo "	<input type='hidden' name='op' value='sendemail'>\n";
	echo "	<input type='hidden' name='id_record' value='".$id_record."'>\n";

	// Mittente
	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-2'>\n";
	echo "			<span><b>Mittente:</b><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-8'>\n";

	//Mi ricavo l'email della mia azienda
	$qp = "SELECT an_anagrafiche.idanagrafica, email, ragione_sociale FROM an_anagrafiche INNER JOIN an_tipianagrafiche_anagrafiche ON an_anagrafiche.idanagrafica=an_tipianagrafiche_anagrafiche.idanagrafica WHERE idtipoanagrafica = (SELECT idtipoanagrafica FROM an_tipianagrafiche WHERE descrizione='Azienda') AND an_anagrafiche.idanagrafica = '".get_var("Azienda predefinita")."' ORDER BY ragione_sociale ASC";
	$rsp = $dbo->fetchArray($qp);
	$ragione_sociale_azienda = $rsp[0]['ragione_sociale'];
	//$email_azienda = $rsp[0]['email'];
	$email_azienda = get_var ('Indirizzo per le email in uscita');
	

	//echo "			<span>".$ragione_sociale_azienda." &lt;".$email_azienda."&gt;</span>\n";
	echo "			<input type=\"text\" name=\"from_name\" class=\"form-control\" id=\"from_name\" value=\"".$ragione_sociale_azienda."\" />\n";
	echo "			<input type=\"text\" name=\"from_address\" class=\"form-control\" id=\"from_address\" value=\"".$email_azienda."\" />\n";
	echo "		</div>\n";
	echo "	</div>\n";

	// destinatario
	$qp = "SELECT ragione_sociale, email FROM an_anagrafiche WHERE idanagrafica = '".$idanagrafica."'";
	$rsmail = $dbo->fetchArray($qp);
	$destinatario = $rsmail[0]['email'];

	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-2'>\n";
	echo "			<span><b>Destinatario:</b><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-8'>\n";
	echo "			<input type=\"text\" name=\"destinatario\" id=\"destinatario\" maxlength=\"255\"  value=\"".$destinatario."\" class=\"form-control\" />\n";
	echo "		</div>\n";
	echo "	</div>\n";

	// copia CC + conf.lettura
	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-2'>\n";
	echo "			<span><b>CC:</b><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-4'>\n";
	echo "			<span><i>".get_var("Destinatario fisso in copia (campo CC)")."</i><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-4'>\n";
	echo "			<label><input type=\"checkbox\" name=\"confermalettura\" checked=\"checked\" id=\"confermalettura\"> <b>Richiedi conferma lettura</b></label>\n";
	echo "		</div>\n";
	echo "	</div>\n";


	// allegato
	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-2'>\n";
	echo "			<span><b>Allegato:</b><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-8'>\n";
	echo "			<a href=\"$rootdir/files/".strtolower($module_name)."/$rapportino_nome\" target=\"_blank\">$rapportino_nome</a><span>\n";
	echo "			<input type=\"hidden\" name=\"allegato\" id=\"allegato\" value=\"$docroot/files/".strtolower($module_name)."/$rapportino_nome\" />\n";
	echo "		</div>\n";
	echo "	</div>\n";


	// oggetto
	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-2'>\n";
	echo "			<span><b>Oggetto:</b><span>";
	echo "		</div>\n";
	echo "		<div class='col-md-8'>\n";
	echo "			<input type=\"text\" name=\"oggetto\" id=\"oggetto\" maxlength=\"255\"  value=\"".$oggetto."\" class=\"form-control\"  />\n";
	echo "		</div>\n";
	echo "	</div>\n";
	

	// destinatario
	echo "	<div class='row form-group'>\n";
	echo "		<div class='col-md-12'>\n";
	echo "			<textarea placeholder=\"\" class=\"form-control\" id=\"body\" name=\"body\">$testo</textarea>\n";
	echo "		</div>\n";
	echo "	</div>\n";
	
	
	echo "	<button type='button' class='btn btn-primary pull-right' onclick=\"$('#send_email').submit();\"><i class=\"fa fa-envelope\"></i> Invia email</button>\n";
	
	echo "</form>\n";
	echo "<div class='clearfix'></div>\n";
?>


<script type="text/javascript">
	
	function check_submit(){

		if( $('#destinatario').val()!='' && $('#oggetto').val()!='' ){

			if( confirm('Inviare email?') ) 
					return true;
			else 
					return false;

		} else {
			alert("Imposta destinatario ed oggetto!");
			return false;
		}
	}
	


	$(document).ready(function(){

		// autocompletamento destinatario
		$(document).load("ajax_autocomplete.php?module=Anagrafiche&op=getemail&idanagrafica=<?php echo $idanagrafica ?>", function(response){
			$("#destinatario").autocomplete({source: response.split("|")});

		});

	
		CKEDITOR.replace( 'body', {
			toolbar: [
				{ name: 'document', items: [ 'NewPage', 'Preview', '-', 'Templates' ] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
				['Undo','Redo','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Scayt', '-','Link','Unlink','-','Bold','Italic','Underline','Superscript','SpecialChar','HorizontalRule','-','NumberedList','BulletedList','Outdent','Indent','Blockquote','-','Styles','Format','Image','Table'], 	// Defines toolbar group without name.
				
			]
		});
		

	});

	
</script>
