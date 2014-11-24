<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Gestion des produits</title>
	
	<script type="text/javascript" src="javascript/jquery.js"></script>
	<script type="text/javascript" src="javascript/jquery.livequery.js"></script>
	<script type="text/javascript" src="javascript/jquery-ui.js"></script>
	<script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="javascript/jquery.suggest.js"></script>
	<script type="text/javascript" src="javascript/ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="javascript/distance.js"></script>
	<script type="text/javascript">
	
	function afficherTableau(titre, code, type, genre, auteur, numero, acquisition)
	{
		edition = 0;
		$(".boutonSupprimer").css("visibility", "hidden");
				
		$("#chargement").css("visibility", "visible");
			$.post("produitsafficher.php", { titre: titre, code: code, type: type, genre: genre, auteur: auteur, numero: numero, acquisition: acquisition  }, function(reponse){
			
			var nbResultats = $("nb", reponse).text();
			var confirmer = false;
			
			//S'il y a beaucoup de résultats, on demande confirmation
			if(nbResultats > 500)
				confirmer = confirm("Cette recherche va afficher " + nbResultats + " résultats. Vous devriez changer vos critères de recherche pour obtenir moins de résultats.\nVoulez-vous quand même effectuer cette recherche, sachant qu'il est très probable qu'elle fasse fortement ralentir votre navigateur ?");
			else if(nbResultats == 0)
			{
				confirmer = false;
				$("#tableau").html("Aucun résultat");
			}
			else
				confirmer = true;
			
			if(confirmer == true)
			{
				$("#tableau").html($("tableau", reponse).text());
				$("#leTableau").tablesorter({
					headers: {
						7: { sorter: false } 
					},
					sortList: [[0,0]]
					}); 
				$("#lienExporter").html('<span id="lienexporter">Exporter vers Excel</span>');
			}
			
			$("#chargement").css("visibility", "hidden");
			});
	}
	
	function afficherDate()
	{
		//Date du jour dans la case Date de la transaction
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
		 
		 $("#date").val(aujourdhui);
				 
	}
	
	$(document).ready(function(){
			
			//Ajout des onglets
			$("#onglets > ul").tabs();
			
			//Et du calendrier
			$("#date").datepicker({showOn: 'focus', firstDay: 1});
			//Calendrier avec intervalle
			$("#dateRechercher").datepicker({showOn: 'focus', firstDay: 1, rangeSelect: true});
			$.datepicker.setDefaults($.datepicker.regional['fr']);
			
			//Pas d'autocomplete
			$("input").attr("autocomplete", "off");
			
			//Cacher le message de chargement
			$("#chargement").css("visibility", "hidden");
			
			//Afficher la date du jour dans le champ Date
			afficherDate();
			
			//Autocomplete
			$("#type").suggest("listetypes.php");
			$("#genre").suggest("listegenres.php");
			$("#auteur").suggest("listeauteurs.php", { auteurs: true, champ: "#genre"});
			
			$("#typeRechercher").suggest("listetypes.php");
			$("#genreRechercher").suggest("listegenres.php");
			$("#auteurRechercher").suggest("listeauteurs.php", { auteurs: true, champ: "#genreRechercher" });
			
			//Configuration des liens du menu
			$("#gauche").distance();
			$("#droite").distance();
			
			//Clic sur Exporter
			$("#lienexporter").livequery("click", function(event) {
					if($("#lienexporter").attr("done") != 1)
					{
						//On récupére les propriétés du tableau actuel
						titre = encodeURI($("#leTableau").attr("titre"));
						code = encodeURI($("#leTableau").attr("code"));
						type = encodeURI($("#leTableau").attr("type"));
						genre = encodeURI($("#leTableau").attr("genre"));
						auteur = encodeURI($("#leTableau").attr("auteur"));
						numero = encodeURI($("#leTableau").attr("numero"));
						acquisition = encodeURI($("#leTableau").attr("acquisition"));
						
						envoiGet = '?export=produits&titre=' + titre + '&code=' + code + '&type=' + type + '&genre=' + genre + '&auteur=' + auteur + '&numero=' + numero + '&acquisition=' + acquisition; 
						
						$("#lienexporter").fadeOut(1000);
						$("#lienexporter").queue(function() {
								$("#lienexporter").load("exporter.php" + envoiGet);
								$("#lienexporter").dequeue();
						});
						$("#lienexporter").fadeIn(2000);
						
						$("#lienexporter").attr("done", "1");
					}
			});
			
			//Configuration du bouton Supprimer
			$(".supprimer").livequery("click", function() {
					//Si le produit n'est pas marqué comme A supprimer
					if(!$(this).attr("aSupprimer"))
					{
						var cellule = $(this).parent();
						var code = cellule.parent().attr("identification");
						ok = '';
						
						//On bloque le navigateur le temps de la vérification
						$.ajaxSetup({
								async: false
						});
						
						//On vérifie que le produit n'est pas sorti
						$.post("empruntsverifier.php", { code: code  }, function(reponse){
							ok = reponse;
						});
						
						$.ajaxSetup({
								async: true
						});
						
						//S'il n'est pas sorti, on peut le marquer
						if(ok == 'OK')
						{
							//Produit marqué comme A supprimer
							$(this).attr("aSupprimer", code);
							$(this).attr("src", "images/supprimer.png");
						}
						//Sinon on le signale
						else
						{
							alert(ok);
						}
					}
					//Anulation de la suppresion
					else
					{
						$(this).removeAttr("aSupprimer");
						$(this).attr("src", "images/supprimerNoir.png");
					}
					
					//Si on a choisi de supprimer au moins un produit, on affiche le bouton pour le faire
					if($(".supprimer[aSupprimer]").length == 0)
						$(".boutonSupprimer").css("visibility", "hidden");
					else
						$(".boutonSupprimer").css("visibility", "visible");
			});
			
			//Configuration du bouton Supprimer final
			$(".boutonSupprimer").livequery("click", function() {
					var confirmation = confirm("Voulez-vous vraiment supprimer les produits sélectionnés ?");
					if(confirmation == true)
					{
						//On parcourt le tableau en supprimant chaque produit marqué
						for(i = 0; i < $(".supprimer[aSupprimer]").length; i++)
						{
							$.post("produitssupprimer.php", { id: $(".supprimer[aSupprimer]").eq(i).attr("aSupprimer")  }, function(){
								$(".boutonSupprimer").html("Suppression en cours...");	
							});
						}
						
						//Rechargement du tableau
						titre = $("#leTableau").attr("titre");
						code = $("#leTableau").attr("code");
						type = $("#leTableau").attr("type");
						genre = $("#leTableau").attr("genre");
						auteur = $("#leTableau").attr("auteur");
						numero = $("#leTableau").attr("numero");
						acquisition = $("#leTableau").attr("acquisition");
										
						afficherTableau(titre, code, type, genre, auteur, numero, acquisition);
					}
			});
			
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
						var acquisition = cellule.html();
						cellule.html('<input type="text" name="dateEditer" id="dateEditer" class="champEditer">');
						$('#dateEditer').attr('value', acquisition);
						
						cellule = cellule.prev();
						var genre = cellule.html();
						cellule.html('<input type="text" name="genreEditer" id="genreEditer" class="champEditer">');
						$('#genreEditer').attr('value', genre);
						
						cellule = cellule.prev();
						var type = cellule.html();
						cellule.html('<input type="text" name="typeEditer" id="typeEditer" class="champEditer">');
						$('#typeEditer').attr('value', type);
						
						cellule = cellule.prev();
						var numero = cellule.html();
						cellule.html('<input type="text" name="numeroEditer" id="numeroEditer" class="champEditer petit">');
						$('#numeroEditer').attr('value', numero);
						
						cellule = cellule.prev();
						var auteur = cellule.html();
						cellule.html('<input type="text" name="auteurEditer" id="auteurEditer" class="champEditer grand">');
						$('#auteurEditer').attr('value', auteur);
						
						cellule = cellule.prev();
						var titre = cellule.html();
						cellule.html('<input type="text" name="titreEditer" id="titreEditer" class="champEditer grand">');
						$('#titreEditer').attr('value', titre);
						
						cellule = cellule.prev();
						var code = cellule.html();
						cellule.html('<input type="text" name="codeEditer" id="codeEditer" class="champEditer petit">');
						$('#codeEditer').attr('value', code);
						
						$("#dateEditer").datepicker({showOn: 'focus', firstDay: 1});
						
						$("#typeEditer").suggest("listetypes.php");
						$("#genreEditer").suggest("listegenres.php");
						$("#auteurEditer").suggest("listeauteurs.php", { auteurs: true, champ: "#genreEditer"});
						
						$("#codeEditer").focus();
					}
					else
					{
						//Si on clic sur le bouton de la même case
						if(identifiantClique == identifiantActuel)
						{
							var titre = $("#titreEditer").val();
							var code = $("#codeEditer").val();
							var type = $("#typeEditer").val();
							var genre = $("#genreEditer").val();
							var auteur = $("#auteurEditer").val();
							var numero = $("#numeroEditer").val();
							var acquisition = $("#dateEditer").val();
							
							erreur = 0;
							message = "";
							
							$("#titreEditer").css("border", "1px solid black");
							$("#codeEditer").css("border", "1px solid black");
							$("#typeEditer").css("border", "1px solid black");
							$("#genreEditer").css("border", "1px solid black");
							$("#auteurEditer").css("border", "1px solid black");
							$("#numeroEditer").css("border", "1px solid black");
							$("#dateEditer").css("border", "1px solid black");
							
							if(titre == '')
							{
								erreur = 1;
								message = "Le titre du produit n'a pas été renseigné.";
								$("#titreEditer").css("border", "1px solid red");
							}
							
							if(code == '')
							{
								erreur = 1;
								message = "Le code du produit n'a pas été renseigné.";
								$("#codeEditer").css("border", "1px solid red");
							}
							
							if(type == '')
							{
								erreur = 1;
								message = "Le type du produit n'a pas été renseigné.";
								$("#typeEditer").css("border", "1px solid red");
							}
							
							if(genre == '')
							{
								erreur = 1;
								message = "Le genre du produit n'a pas été renseigné.";
								$("#genreEditer").css("border", "1px solid red");
							}
							
							if(auteur == '')
							{
								erreur = 1;
								message = "Aucune information sur le produit n'a été renseignée.";
								$("#auteurEditer").css("border", "1px solid red");
							}
							
							if(acquisition == '')
							{
								erreur = 1;
								message = "La date d'acquisition du produit n'a pas été renseignée.";
								$("#dateEditer").css("border", "1px solid red");
							}
							
							if(erreur == 0)
							{
								$.post("produitsmodifier.php", { id: identifiantClique, titre: titre, code: code, type: type, genre: genre, auteur: auteur, acquisition: acquisition, numero: numero  }, function(reponse){
									erreur = $("erreur", reponse).text();
									message = $("message", reponse).text();
									
									//Si le code n'existe pas déjà
									if(erreur == 0)
									{
										titre = $("#leTableau").attr("titre");
										code = $("#leTableau").attr("code");
										type = $("#leTableau").attr("type");
										genre = $("#leTableau").attr("genre");
										auteur = $("#leTableau").attr("auteur");
										numero = $("#leTableau").attr("numero");
										acquisition = $("#leTableau").attr("acquisition");
										
										cellule.parent().html('<td colspan="8">Modification...</td>');
										//On recharge le tableau
										afficherTableau(titre, code, type, genre, auteur, numero, acquisition);
									}
									//Si le code existe déjà dans la base
									else
									{										
										$("#codeEditer").val("");
										$("#codeEditer").focus();
										$("#codeEditer").css("border", "1px solid red");
										
										alert(message);
									}
								});
							}
							//Si un des champs requis n'a pas été rempli
							else
							{
								alert(message);
							}
						}
						else
						{
							alert("Vous devez enregistrer les modifications appliquées sur le produit actuellement édité avant de pouvoir en modifier un autre.");
						}
					}
			});
			
			//Ajouter un produit
			$("#formulaireAjouter").submit(function() {
					var titre = $("#titre").val();
					var code = $("#code").val();
					var type = $("#type").val();
					var genre = $("#genre").val();
					var auteur = $("#auteur").val();
					var numero = $("#numero").val();
					var acquisition = $("#date").val();
					
					erreur = 0;
					message = "";
					
					$("#titre").css("border", "1px solid black");
					$("#code").css("border", "1px solid black");
					$("#type").css("border", "1px solid black");
					$("#genre").css("border", "1px solid black");
					$("#auteur").css("border", "1px solid black");
					$("#numero").css("border", "1px solid black");
					$("#date").css("border", "1px solid black");
					
					if(titre == '')
					{
						erreur = 1;
						message = "Le titre du produit n'a pas été renseigné.";
						$("#titre").css("border", "1px solid red");
					}
					
					if(code == '')
					{
						erreur = 1;
						message = "Le code du produit n'a pas été renseigné.";
						$("#code").css("border", "1px solid red");
					}
					
					if(type == '')
					{
						erreur = 1;
						message = "Le type du produit n'a pas été renseigné.";
						$("#type").css("border", "1px solid red");
					}
					
					if(genre == '')
					{
						erreur = 1;
						message = "Le genre du produit n'a pas été renseigné.";
						$("#genre").css("border", "1px solid red");
					}
					
					if(auteur == '')
					{
						erreur = 1;
						message = "Aucune information sur le produit n'a été renseignée.";
						$("#auteur").css("border", "1px solid red");
					}
					
					if(acquisition == '')
					{
						erreur = 1;
						message = "La date d'acquisition du produit n'a pas été renseignée.";
						$("#date").css("border", "1px solid red");
					}
					
					//Si tous les champs ont été renseignés
					if(erreur == 0)
					{
						$.post("produitsajouter.php", { titre: titre, code: code, type: type, genre: genre, auteur: auteur, acquisition: acquisition, numero: numero  }, function(reponse){
								erreur = $("erreur", reponse).text();
								message = $("message", reponse).text();
								
								//Si le code n'existe pas déjà
								if(erreur == 0)
								{
									$("#titre").val("");
									$("#titre").focus();
									$("#code").val("");
									$("#numero").val("");
									
									
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "black");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);	
									
									//On efface le message après 5 secondes
									$("#message").queue(function() {
											$("#message").fadeOut(1000);
									});
									
									setTimeout(function(){
											$("#message").dequeue();
									}, 5000);
								}
								//Si le code existe déjà dans la base
								else
								{
									$("#code").val("");
									$("#code").focus();
									$("#code").css("border", "1px solid red");
									
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "red");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);
								}
						});
					}
					//Si un champ obligatoire est vide
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
			
			//Rechercher un produit
			$("#formulaireRechercher").submit(function() {
					edition = 0;
					
					var titre = $("#titreRechercher").val();
					var code = $("#codeRechercher").val();
					var type = $("#typeRechercher").val();
					var genre = $("#genreRechercher").val();
					var auteur = $("#auteurRechercher").val();
					var numero = $("#numeroRechercher").val();
					var acquisition = $("#dateRechercher").val();
					
					erreur = 0;
					message = "";
					
					$("#titreRechercher").css("border", "1px solid black");
					$("#codeRechercher").css("border", "1px solid black");
					$("#typeRechercher").css("border", "1px solid black");
					$("#genreRechercher").css("border", "1px solid black");
					$("#auteurRechercher").css("border", "1px solid black");
					$("#numeroRechercher").css("border", "1px solid black");
					$("#dateRechercher").css("border", "1px solid black");
					
					//Si aucun champ n'est rempli
					if(titre == '' && code == '' && type == '' && genre == '' && auteur == '' && numero == '' && acquisition == '')
					{
						message = 'Vous devez remplir au moins un des champs.';
						erreur = 1;
						
						$("#titreRechercher").css("border", "1px solid red");
						$("#codeRechercher").css("border", "1px solid red");
						$("#typeRechercher").css("border", "1px solid red");
						$("#genreRechercher").css("border", "1px solid red");
						$("#auteurRechercher").css("border", "1px solid red");
						$("#numeroRechercher").css("border", "1px solid red");
						$("#dateRechercher").css("border", "1px solid red");
						
						$("#messageRechercher").fadeOut(500);
						$("#messageRechercher").queue(function() {
								$("#messageRechercher").css("color", "red");
								$("#messageRechercher").html(message);
								$("#messageRechercher").dequeue();
						});
						$("#messageRechercher").fadeIn(1000);
					}
					//Si au moins un champ est rempli
					else
					{
						afficherTableau(titre, code, type, genre, auteur, numero, acquisition);
					}
					
					return false;
			});
			
	});
	</script>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
	<div id="haut" class="produits"><span id="gauche"><a href="adherents.php">Adhérents</a></span><span id="milieu">Gestion des produits</span><span id="droite"><a href="transactions.php">Transactions</a></span></div>

	<div id="onglets">
		<ul>
			<li><a href="#ajouter"><span>Ajouter un produit</span></a></li>
			<li><a href="#rechercher"><span>Rechercher un produit</span></a></li>
		</ul>
		
		<div id="ajouter">
			<form method="post" action="" id="formulaireAjouter">
				<p class="labelChamps">
					<strong>Titre</strong><br />
					<strong>Code</strong><br />
					<strong>Type</strong><br />
					<strong>Genre</strong><br />
					<strong>Auteur / Série / Informations diverses</strong><br />
					<strong>Numéro</strong><br />
					<strong>Date d'acquisition</strong><br />
				</p>
				<p class="listeChamps">
					<input type="text" name="titre" id="titre"><br />
					<input type="text" name="code" id="code" size="5"><br />
					<input type="text" name="type" id="type"><br />
					<input type="text" name="genre" id="genre"><br />
					<input type="text" name="auteur" id="auteur"><br />
					<input type="text" name="numero" id="numero" size="5"><br />
					<input type="text" name="date" id="date">
				</p>
				<p>
					<div class="valider"><input type="submit" value="Ajouter le produit" id="boutonAjouter"></div>
					<div id="message" class="message">&nbsp;</div>
				</p>
			</form>
		</div>
		<div id="rechercher">
			<form method="post" action="" id="formulaireRechercher">
				<p class="labelChamps">
					<strong>Titre</strong><br />
					<strong>Code</strong><br />
					<strong>Type</strong><br />
					<strong>Genre</strong><br />
					<strong>Auteur / Série / Informations diverses</strong><br />
					<strong>Numéro</strong><br />
					<strong>Date d'acquisition</strong><br />
				</p>
				<p class="listeChamps">
					<input type="text" name="titreRechercher" id="titreRechercher"><br />
					<input type="text" name="codeRechercher" id="codeRechercher" size="5"><br />
					<input type="text" name="typeRechercher" id="typeRechercher"><br />
					<input type="text" name="genreRechercher" id="genreRechercher"><br />
					<input type="text" name="auteurRechercher" id="auteurRechercher"><br />
					<input type="text" name="numeroRechercher" id="numeroRechercher" size="5"><br />
					<input type="text" name="dateRechercher" id="dateRechercher">
				</p>
				<p>
					<div class="valider"><input type="submit" value="Rechercher" id="boutonRechercher"></div>
					<div id="messageRechercher" class="message">&nbsp;</div>
				</p>
			</form>
		</div>
	</div>
	
	<div id="exporter"><br /><span id="lienExporter" class="lien"></span></div>
	<div id="tableau"></div>
	<div id="chargement">Rechargement du tableau...<br /><br /><img src="images/chargement.gif"></div>
</body>
</html>
