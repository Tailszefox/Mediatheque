<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Gestion des adhérents</title>
	
	<script type="text/javascript" src="javascript/jquery.js"></script>
	<script type="text/javascript" src="javascript/jquery.livequery.js"></script>
	<script type="text/javascript" src="javascript/jquery-ui.js"></script>
	<script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="javascript/jquery.suggest.js"></script>
	<script type="text/javascript" src="javascript/ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="javascript/distance.js"></script>
	<script type="text/javascript">
	//Afficher le tableau des adhérents
	function chargerTableau()
	{
		edition = 0;
		
		$("#chargement").css("visibility", "visible");
		
		$("#tableau").load("adherentsafficher.php", function(){
					$("#leTableau").tablesorter({
							 headers: {
								 7: { sorter: false } 
							 },
							sortList: [[0,0]]
					});
					
					$("#chargement").css("visibility", "hidden");
					$("#exporter").html('<span id="lienexporter" class="lien">Exporter vers Excel</span>');
			});
	}
	
	//Verifier le statut de la case Cotisation
	function verifierCotisation()
	{
		if($("#cotisation").attr("checked"))
		{
			$("#datecotisation").attr("disabled","");
			 var date = new Date();
			 var jour;
			 var mois;
			 
			 //Date d'aujourd'hui
			 if(date.getDate() < 10)
			 {
				 jour = "0" + date.getDate();
			 }
			 else
			 {
				  jour = date.getDate();
			 }
			 
			 if(date.getMonth() < 9)
			 {
				 mois = date.getMonth() + 1;
				 mois = "0" + mois;
			 }
			 else
			 {
				 mois = date.getMonth() + 1;
			 }
			 
			 var aujourdhui = jour + "/" +  mois + "/" + date.getFullYear();
			 $("#datecotisation").val(aujourdhui);
		}
		else
		{
			$("#datecotisation").attr("disabled","disabled");
			$("#datecotisation").val("");
		}
	}
	
	//Verifier le statut de la case Cotisation (en mode édition)
	function verifierCotisationEditer()
	{
		if($("#cotisationEditer").attr("checked"))
		{
			$("#datecotisationEditer").attr("disabled","");
			 var date = new Date();
			 var jour;
			 var mois;
			 
			 //Date d'aujourd'hui
			 if(date.getDate() < 10)
			 {
				 jour = "0" + date.getDate();
			 }
			 else
			 {
				  jour = date.getDate();
			 }
			 
			 if(date.getMonth() < 9)
			 {
				 mois = date.getMonth() + 1;
				 mois = "0" + mois;
			 }
			 else
			 {
				 mois = date.getMonth() + 1;
			 }
			 
			 var aujourdhui = jour + "/" +  mois + "/" + date.getFullYear();
			 $("#datecotisationEditer").val(aujourdhui);
		}
		else
		{
			$("#datecotisationEditer").attr("disabled","disabled");
			$("#datecotisationEditer").val("");
		}
	}
	
	$(document).ready(function(){
			//Ajout des onglets
			$("#onglets > ul").tabs();
			
			//Et du calendrier
			$("#datecotisation").datepicker({showOn: 'focus', firstDay: 1});
			$.datepicker.setDefaults($.datepicker.regional['fr']);
			
			//Pas d'autocomplete
			$("input").attr("autocomplete", "off");
			
			//Charger le premier tableau des adhérents
			chargerTableau();
			
			//Configuration du bouton Supprimer
			$(".supprimer").livequery("click", function() {
					ligne = $(this).parent().parent();
					var matricule = ligne.attr("identification");
					
					//On vérifie que l'adhérent a rendu tout ce qu'il a emprunté
					var ok = '';
					
					//On bloque le navigateur le temps de la vérification
					$.ajaxSetup({
							async: false
					});

					$.post("empruntsverifier.php", { matricule: matricule  }, function(reponse){
						ok = reponse;
					});
					
					$.ajaxSetup({
							async: true
					});
					
					//S'il a rendu tout ce qu'il avait emprunté, on peut le supprimer
					if(ok == 'OK')
					{
						confirmation = confirm("Voulez-vous vraiment supprimer cet adhérent ?");
						if(confirmation == true)
						{
							$.post("adherentssupprimer.php", { id: matricule  }, function(){
									ligne.html('<td colspan="8">Suppresion...</td>');
									chargerTableau();
							});
						}
					}
					//Sinon, on le signale
					else
					{
						alert(ok);
					}
			});
			
			//Configuration des liens du menu
			$("#gauche").distance();
			$("#droite").distance();
			
			edition = 0;
			
			//Configuration du bouton Editer
			$(".editer").livequery("click", function() {
					
					var cellule = $(this).parent();
					var identifiantClique = cellule.parent().attr("identification");
					
					//Si on n'est pas déjà en train d'éditer
					if(edition == 0)
					{
						$(this).attr("src", "images/editerActif.png");
						
						//Récupération des valeurs actuelles et transformation des cellules en champs
						edition = 1;
						identifiantActuel = identifiantClique;
						
						cellule = cellule.prev();
						var datecotisation = cellule.html();
						
						cellule = cellule.prev();
						var cotisation = cellule.html();
						
						if(cotisation == 'Oui')
						{
							cellule.html('<input type="checkbox" name="cotisation" id="cotisationEditer" checked="checked">');
							cellule.next().html('<input type="text" name="datecotisation" id="datecotisationEditer" class="champEditer">')
							$('#datecotisationEditer').attr('value', datecotisation);
						}
						else
						{
							cellule.html('<input type="checkbox" name="cotisation" id="cotisationEditer">');
							cellule.next().html('<input type="text" name="datecotisation" id="datecotisationEditer" disabled="disabled" class="champEditer">')
						}
						
						cellule = cellule.prev();
						var service = cellule.html();
						cellule.html('<input type="text" name="service" id="serviceEditer" autocomplete="off" class="champEditer">');
						$('#serviceEditer').attr('value', service);
						
						cellule = cellule.prev();
						var numero = cellule.html();
						cellule.html('<input type="text" name="numero" id="numeroEditer" size="5" class="champEditer petit">');
						$('#numeroEditer').attr('value', numero);
						
						cellule = cellule.prev();
						var prenom = cellule.html();
						cellule.html('<input type="text" name="prenom" id="prenomEditer" class="champEditer grand">');
						$('#prenomEditer').attr('value', prenom);
						
						cellule = cellule.prev();
						var nom = cellule.html();
						cellule.html('<input type="text" name="nom" id="nomEditer" class="champEditer grand">');
						$('#nomEditer').attr('value', nom);
						
						cellule = cellule.prev();
						var matricule = cellule.html();
						cellule.html('<input type="text" name="matricule" id="matriculeEditer" size="5" class="champEditer petit">');
						$('#matriculeEditer').attr('value', matricule);
						
						//Gestion de la case Cotisation
						$("#cotisationEditer").click(function() {
								verifierCotisationEditer();
						});
						
						//Ajout du calendrier
						$("#datecotisationEditer").datepicker({showOn: 'focus', firstDay: 1});
						$.datepicker.setDefaults($.datepicker.regional['fr']);
						
						//Autocomplétion sur Service
						$("#serviceEditer").focus(function() {
								$("#serviceEditer").suggest("listeservices.php")
						});
					}
					else
					{
						//Si on clic sur le bouton de la même case
						if(identifiantClique == identifiantActuel)
						{
							//On vérifie que toutes les cases ont été remplies comme il faut
							var matricule = $("#matriculeEditer").val();
							var nom = $("#nomEditer").val();
							var prenom = $("#prenomEditer").val();
							var numero = $("#numeroEditer").val();
							var service = $("#serviceEditer").val();
							var cotisation = $("#cotisationEditer").attr("checked");
							var datecotisation = $("#datecotisationEditer").val();
							
							erreur = 0;
							
							$("#matriculeEditer").css("border", "1px solid black");
							$("#nomEditer").css("border", "1px solid black");
							$("#prenomEditer").css("border", "1px solid black");
							$("#numeroEditer").css("border", "1px solid black");
							$("#serviceEditer").css("border", "1px solid black");
							$("#datecotisationEditer").css("border", "1px solid black");
							
							//Verification du remplissage de tous les champs nécessaires
							if(matricule == "" || nom == "" || prenom == "" || numero == "" || service == "" || ($("#cotisationEditer").attr("checked") && datecotisation == ""))
							{
								message = "Certains champs obligatoires n'ont pas été remplis.";
								erreur = 1;
							}
							
							if(matricule == "")
							{
								$("#matriculeEditer").css("border", "1px solid red");
							}
							
							if(nom == "")
							{
								$("#nomEditer").css("border", "1px solid red");
							}
							
							if(prenom == "")
							{
								$("#prenomEditer").css("border", "1px solid red");
							}
							
							if(numero == "")
							{
								$("#numeroEditer").css("border", "1px solid red");
							}
							
							if(service == "")
							{
								$("#serviceEditer").css("border", "1px solid red");
							}
							
							if($("#cotisationEditer").attr("checked") && datecotisation == "")
							{
								$("#datecotisationEditer").css("border", "1px solid red");
							}
							
							//Si tout est OK
							if(erreur == 0)
							{
								$.post("adherentsediter.php", { matricule: matricule, nom: nom, prenom: prenom, numero: numero, service: service, cotisation: cotisation, datecotisation: datecotisation, id: identifiantActuel  }, function(reponse){
									erreur = $("erreur", reponse).text();
									message = $("message", reponse).text();
									
									//Si le matricule existe déjà
									if(erreur == 1)
									{
										$("#matriculeEditer").css("border", "1px solid red");
										alert(message);	
									}
									//Sinon, c'est ok, on recharge du tableau
									else
									{
										cellule.parent().html('<td colspan="8">Modification...</td>');
										chargerTableau();
										edition = 0;
									}
								});
							}
							else
							{
								alert("Certains champs obligatoires n'ont pas été remplis.");
							}
						}
						else
						{
							alert("Vous devez enregistrer les modifications appliquées sur l'adhérent actuellement édité avant de pouvoir en modifier un autre.");
						}
					}
			});
			
			//Desactivation de la date de cotisation
			verifierCotisation();
			
			//Clic sur Cotisation
			$("#cotisation").click(function() {
					verifierCotisation();
			});
			
			//Focus sur Services
			$("#service").suggest("listeservices.php");
			
			//Clic pour Exporter
			$("#lienexporter").livequery("click", function () {
					if($("#lienexporter").attr("done") != 1)
					{
						$("#exporter").fadeOut(1000);
						$("#exporter").queue(function() {
								$("#exporter").load("exporter.php?export=adherents");
								$("#exporter").dequeue();
						});
						$("#exporter").fadeIn(2000);
					
						$("#lienexporter").attr("done", "1");
					}
			});
			
			//Clic sur Remise à Zéro
			$("#boutonRemiseAZero").click(function (){
					confirmation = confirm("Êtes-vous sûr de vouloir remettre à zéro toutes les cotisations ?");
					if(confirmation == true)
					{
						$.post("adherentsremiseazero.php", {}, function(){
								chargerTableau();
						});
					}
			});
			
			//Ajouter un adhérent
			$("#formulaireAjouter").submit(function() {
					var matricule = $("#matricule").val();
					var nom = $("#nom").val();
					var prenom = $("#prenom").val();
					var numero = $("#numero").val();
					var service = $("#service").val();
					var cotisation = $("#cotisation").attr("checked");
					var datecotisation = $("#datecotisation").val();
					
					erreur = 0;
					message = "";
					
					$("#matricule").css("border", "1px solid black");
					$("#nom").css("border", "1px solid black");
					$("#prenom").css("border", "1px solid black");
					$("#numero").css("border", "1px solid black");
					$("#service").css("border", "1px solid black");
					$("#datecotisation").css("border", "1px solid black");
					
					//Verification du remplissage de tous les champs nécessaires
					if(matricule == "" || nom == "" || prenom == "" || numero == "" || service == "" || ($("#cotisation").attr("checked") && datecotisation == ""))
					{
						message = "Certains champs obligatoires n'ont pas été remplis.";
						erreur = 1;
					}
					
					if(matricule == "")
					{
						$("#matricule").css("border", "1px solid red");
					}
					
					if(nom == "")
					{
						$("#nom").css("border", "1px solid red");
					}
					
					if(prenom == "")
					{
						$("#prenom").css("border", "1px solid red");
					}
					
					if(numero == "")
					{
						$("#numero").css("border", "1px solid red");
					}
					
					if(service == "")
					{
						$("#service").css("border", "1px solid red");
					}
					
					if($("#cotisation").attr("checked") && datecotisation == "")
					{
						$("#datecotisation").css("border", "1px solid red");
					}
					
					//Si tous les champs ont été remplis
					if(erreur == 0)
					{
						$.post("adherentsajouter.php", { matricule: matricule, nom: nom, prenom: prenom, numero: numero, service: service, cotisation: cotisation, datecotisation: datecotisation  }, function(reponse){
								erreur = $("erreur", reponse).text();
								message = $("message", reponse).text();
								
								//Si le matricule existe déjà
								if(erreur == 1)
								{
									$("#matricule").css("border", "1px solid red");
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "red");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);		
								}
								//Sinon, on recharge le tableau et on efface les champs
								else
								{
									$("#matricule").val("");
									$("#nom").val("");
									$("#prenom").val("");
									$("#numero").val("");
									$("#service").val("");
									$("#cotisation").attr("checked", false);
									$("#datecotisation").attr("disabled","disabled");
									$("#datecotisation").val("");
									
									$("#datecotisation").val("");
									
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "black");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);	
									
									//On efface le message après 10 secondes
									setTimeout(function(){
											$("#message").fadeOut(1000);
									}, 10000);
									
									
									chargerTableau();
								}
									
						});
					}
					//Si un ou plusieurs champs n'ont pas été remplis
					else
					{
						
						$("#message").fadeOut(500);
						$("#message").queue(function() {
								$("#message").css("color", "red");
								$("#message").html(message);
								$("#message").dequeue();
						});
						$("#message").fadeIn(1000);
					}
					
					
					return false;
			});
			
			
	});
	</script>
	
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
	<div id="haut" class="adherents"><span id="gauche"><a href="produits.php">Produits</a></span><span id="milieu">Gestion des adhérents</span><span id="droite"><a href="transactions.php">Transactions</a></span></div>
	
	<div id="onglets">
		<ul>
			<li><a href="#ajouter"><span>Ajouter un adhérent</span></a></li>
			<li><a href="#raz"><span>Remise à zéro des cotisations</span></a></li>
		</ul>
		<div id="ajouter">
			<form method="post" action="" id="formulaireAjouter">
				<p class="labelChamps">
					<strong>Matricule</strong><br />
					<strong>Nom</strong><br />
					<strong>Prénom</strong><br />
					<strong>Numéro</strong><br />
					<strong>Service</strong><br />
					<strong>Cotisation</strong><br />
					<strong>Date de cotisation</strong><br />
				</p>
				<p class="listeChamps">
					<input type="text" name="matricule" id="matricule" size="5"><br />
					<input type="text" name="nom" id="nom"><br />
					<input type="text" name="prenom" id="prenom"><br />
					<input type="text" name="numero" id="numero" size="5"><br />
					<input type="text" name="service" id="service"><br />
					<input type="checkbox" name="cotisation" id="cotisation"><br />
					<input type="text" name="datecotisation" id="datecotisation">
				</p>
				<p>
				<div class="valider"><input type="submit" value="Ajouter l'adhérent" id="boutonAjouter"></div>
				<div class="message" id="message">&nbsp;</div>
				</p>
			</form>
		</div>
		<div id="raz">
			<form method="post" action="" id="remiseAZero">
			<p>
				<input type="button" value="Remettre à zéro les cotisations" id="boutonRemiseAZero">
			</p>
			</form>
			<p>Cliquez sur ce bouton pour remettre à zéro toutes les cotisations. Attention, une fois cette action effectuée, vous ne pourrez pas revenir en arrière.</p>
		</div>
	</div>
	
	<div id="exporter"><span id="lienexporter" class="lien"></span></div>
	<div id="tableau"></div>
	<div id="chargement">Rechargement du tableau...<br /><br /><img src="images/chargement.gif"></div>
</body>
</html>
