<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Gestion des transactions</title>
	
	<script type="text/javascript" src="javascript/jquery.js"></script>
	<script type="text/javascript" src="javascript/jquery.livequery.js"></script>
	<script type="text/javascript" src="javascript/jquery-ui.js"></script>
	<script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="javascript/jquery.suggest.js"></script>
	<script type="text/javascript" src="javascript/ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="javascript/distance.js"></script>
	<script type="text/javascript">
	//Afficher le tableau des sorties
	function chargerTableau(matricule, code, nonRendus, dates)
	{
		$("#chargement").css("visibility", "visible");
		
		$.post("transactionsafficher.php", {matricule : matricule, code : code, nonRendus : nonRendus, dates : dates}, function(reponse){
				$("#tableau").html(reponse);
				if($("#leTableau").length)
				{
					$("#leTableau").tablesorter({
							headers: {
								8: { sorter: false } 
							},
							sortList: [[7,1]]
					});
					$("#lienExporter").html('Exporter vers Excel');
					$("#genererGraphique").html('Générer un graphique');
					
					if($("#graphiques").html() == '')
						$("#graphiques").html('Aucun graphique ne peut être généré avec les critères de sélection choisis.');
				}
				else
				{
					$("#lienExporter").html('');
					$("#genererGraphique").html('');
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
		 var heure = date.getHours();
		 var minute = date.getMinutes();
				 
		 if(heure < 10)
		 {
			 heure = "0" + heure;
		 }
				 
		 if(minute < 10)
		 {
			 minute = "0" + minute;
		 }
				 
		 var minutes = date.getMinutes();
		 $("#date").val(aujourdhui);
		 $("#heure").val(heure);
		 $("#minute").val(minute);
				 
		}
	
	$(document).ready(function(){
			
			//Ajout des onglets
			$("#onglets > ul").tabs();
			
			//Et du calendrier
			$("#date").datepicker({showOn: 'focus', firstDay: 1});
			$("#dateRecherche").datepicker({showOn: 'focus', firstDay: 1, rangeSelect: true});
			$.datepicker.setDefaults($.datepicker.regional['fr']);
			
			//Pas d'autocomplete
			$("input").attr("autocomplete", "off");
			
			//Chargement initial du tableau
			chargerTableau(-1, -1, 1, -1);
			
			//Affichage des produits non rendus
			$("#afficherNonRendus").click(function() {
					chargerTableau(-1, -1, 1, -1);
			});
			
			//Autocomplete sur le nom et le nomRecherche
			$("#nom").suggest("listenoms.php");
			$("#nomRecherche").suggest("listenoms.php");
			
			//Configuration des liens du menu
			$("#gauche").distance();
			$("#droite").distance();
			
			//Clic sur Exporter
			$("#lienExporter").livequery("click", function() {
					if($("#lienExporter").attr("done") != 1)
					{
						//On récupére les propriétés du tableau actuel
						matricule = encodeURI($("#leTableau").attr("matricule"));
						code = encodeURI($("#leTableau").attr("code"));
						nonRendus = encodeURI($("#leTableau").attr("nonRendus"));
						dates = encodeURI($("#leTableau").attr("dates"));
						
						envoiGet = '?export=transactions&matricule=' + matricule + '&code=' + code + '&nonRendus=' + nonRendus + '&dates=' + dates;
						
						$("#lienExporter").fadeOut(1000);
						$("#lienExporter").queue(function() {
								$("#lienExporter").load("exporter.php" + envoiGet);
								$("#lienExporter").dequeue();
						});
						$("#lienExporter").fadeIn(2000);
						
						$("#lienExporter").attr("done", "1");
					}
			});
			
			//Affichage du menu permettant de sélectionner un graphique à générer
			$("#genererGraphique").livequery("click", function(e) {
					$("#graphiques").show(1000);
					$("#graphiques").css('top', e.pageY + 'px');
					$("#graphiques").css('left', e.pageX + 'px');
					
					//On le cache automatiquement au bout de 20 secondes
					setTimeout(function(){ 
							if($("#graphiques").length)
								$("#graphiques").hide(1000);
					}, 20000);
			});
			
			$("#graphiques").livequery("click", function(){
				$(this).hide(1000);
			});
			
			//Recherche du nom à partir du matricule
			$("#matricule").keyup(function() {
					var matricule = $("#matricule").val();
					$.get("adherentsnommatricule.php", { matricule: matricule }, function(reponse) {
							cotisation = $("cotisation", reponse).text();
							nom = $("nom", reponse).text();
							$("#nom").val(nom);
							
							if(cotisation != '' && cotisation == 0)
							{
								$("#cotisation").html("Cet adhérent n'a pas payé sa cotisation");
							}
							else
							{
								$("#cotisation").html("");
							}
					});
			});
			
			$("#matricule").blur(function() {
					var matricule = $("#matricule").val();
					$.get("adherentsnommatricule.php", { matricule: matricule }, function(reponse) {
							cotisation = $("cotisation", reponse).text();
							nom = $("nom", reponse).text();
							$("#nom").val(nom);
							
							if(cotisation != '' && cotisation == 0)
							{
								$("#cotisation").html("Cet adhérent n'a pas payé sa cotisation");
							}
							else
							{
								$("#cotisation").html("");
							}
					});
			});
			
			
			//Recherche du matricule à partir du nom
			$("#nom").keyup(function() {
					var nom = $("#nom").val();
						$.get("adherentsnommatricule.php", { nom: nom }, function(reponse) {
							cotisation = $("cotisation", reponse).text();
							matricule = $("matricule", reponse).text();
							$("#matricule").val(matricule);
							
							if(cotisation != '' && cotisation == 0)
							{
								$("#cotisation").html("Cet adhérent n'a pas payé sa cotisation");
							}
							else
							{
								$("#cotisation").html("");
							}
						});
			});
			
			$("#nom").blur(function() {
					var nom = $("#nom").val();
						$.get("adherentsnommatricule.php", { nom: nom }, function(reponse) {
							cotisation = $("cotisation", reponse).text();
							matricule = $("matricule", reponse).text();
							$("#matricule").val(matricule);
							
							if(cotisation != '' && cotisation == 0)
							{
								$("#cotisation").html("Cet adhérent n'a pas payé sa cotisation");
							}
							else
							{
								$("#cotisation").html("");
							}
						});
			});
			
			//Idem pour les champs de recherche
			//Recherche du nom à partir du matricule
			$("#matriculeRecherche").keyup(function() {
					var matricule = $("#matriculeRecherche").val();
					$.get("adherentsnommatricule.php", { matricule: matricule }, function(reponse) {
						nom = $("nom", reponse).text();
						$("#nomRecherche").val(nom);
					});
			});
			
			$("#matriculeRecherche").blur(function() {
					var matricule = $("#matriculeRecherche").val();
					$.get("adherentsnommatricule.php", { matricule: matricule }, function(reponse) {
						nom = $("nom", reponse).text();
						$("#nomRecherche").val(nom);
					});
			});
			
			//Recherche du matricule à partir du nom
			$("#nomRecherche").keyup(function() {
					var nom = $("#nomRecherche").val();
					if(nom != '')
					{
						$.get("adherentsnommatricule.php", { nom: nom }, function(reponse) {
							matricule = $("matricule", reponse).text();
							$("#matriculeRecherche").val(matricule);
						});
					}
			});
			
			$("#nomRecherche").blur(function() {
					var nom = $("#nomRecherche").val();
					if(nom != '')
					{
						$.get("adherentsnommatricule.php", { nom: nom }, function(reponse) {
								matricule = $("matricule", reponse).text();
								$("#matriculeRecherche").val(matricule);
						});
					}
			});
			
			//On l'appelle une première fois, elle est ensuite appelée toutes les 10 secondes
			afficherDate();
			setInterval("afficherDate();", 10000);
			
			//Ajouter une transaction
			$("#formulaireAjouter").submit(function() {
					var matricule = $("#matricule").val();
					var nom = $("#nom").val();
					var code= $("#code").val();
					var datetransaction= $("#date").val();
					var heure = $("#heure").val();
					var minute = $("#minute").val();
					
					erreur = 0;
					message = "";
					
					$("#matricule").css("border", "1px solid black");
					$("#nom").css("border", "1px solid black");
					$("#code").css("border", "1px solid black");
					$("#date").css("border", "1px solid black");
					$("#heure").css("border", "1px solid black");
					$("#minute").css("border", "1px solid black");

					if(matricule == '' && nom == '')
					{
						erreur = 1;
						message = 'Vous devez entrer un matricule ou un nom.';
						$("#matricule").css("border", "1px solid red");
						$("#nom").css("border", "1px solid red");
					}
					else if(matricule == '')
					{
						erreur = 1;
						message = 'Le nom entré ne correspond à aucun matricule.';
						$("#matricule").css("border", "1px solid red");
						$("#nom").css("border", "1px solid red");
					}
					else if(nom == '')
					{
						erreur = 1;
						message = 'Le matricule entré ne correspond à aucun adhérent.';
						$("#matricule").css("border", "1px solid red");
						$("#nom").css("border", "1px solid red");
					}
					
					if(code == '')
					{
						message = "Le code du produit n'a pas été renseigné.";
						$("#code").css("border", "1px solid red");
					}
					
					if(datetransaction == '' || heure == "" || minute == "")
					{
						erreur = 1;
						message = "La date de la transaction n'a pas été renseignée.";
						$("#date").css("border", "1px solid red");
						$("#heure").css("border", "1px solid red");
						$("#minute").css("border", "1px solid red");
					}
					
					//Si tous les champs ont été remplis
					if(erreur == 0)
					{
						$.post("transactionsajouter.php", { matricule: matricule, code: code, datetransaction: datetransaction, heure: heure, minute: minute  }, function(reponse){
								erreur = $("erreur", reponse).text();
								message = $("message", reponse).text();
								
								//Si le code n'existe pas
								if(erreur == 1)
								{	
									$("#code").css("border", "1px solid red");
									$("#code").val("");
									$("#code").focus();
									
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "red");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);		
								}
								//Si le produit a déjà été emprunté par un autre adhérent
								else if(erreur == 2)
								{
									$("#code").val("");
									$("#code").focus();
									
									$("#matricule").css("border", "1px solid red");
									$("#nom").css("border", "1px solid red");
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "red");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);		
								}
								//Si tout est OK
								else
								{
									$("#code").val("");
									$("#code").focus();
									
									$("#message").fadeOut(500);
									$("#message").queue(function() {
										$("#message").html(message);
										$("#message").css("color", "black");
										$("#message").dequeue();
									});
									$("#message").fadeIn(1000);	
								}
								
								chargerTableau(matricule, -1, 1, -1);
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
			
			//Recherche de transactions
			$("#formulaireRechercher").submit(function() {
					var matricule = $("#matriculeRecherche").val();
					var nom = $("#nomRecherche").val();
					var code = $("#codeRecherche").val();
					var dates = $("#dateRecherche").val();
					
					erreur = 0;
					message = "";
					
					$("#matriculeRecherche").css("border", "1px solid black");
					$("#nomRecherche").css("border", "1px solid black");
					$("#codeRecherche").css("border", "1px solid black");
					$("#dateRecherche").css("border", "1px solid black");
					
					if(matricule == '' && nom == '' && code == '' && dates == "")
					{
						erreur = 1;
						message = 'Vous devez entrer au moins un matricule, un nom, un code ou un intervalle de date.';
						$("#matriculeRecherche").css("border", "1px solid red");
						$("#nomRecherche").css("border", "1px solid red");
						$("#codeRecherche").css("border", "1px solid red");
						$("#dateRecherche").css("border", "1px solid red");
					}
					else if(matricule == '' && nom != '')
					{
						erreur = 1;
						message = 'Le nom entré ne correspond à aucun matricule.';
						$("#matriculeRecherche").css("border", "1px solid red");
						$("#nomRecherche").css("border", "1px solid red");
					}
					
					//Si tout les champs ont été remplis
					if(erreur == 0)
					{
						if(matricule == '')
							matricule = -1;
						else if(nom == '' && matricule != '')
						{
							message = "Attention, le matricule entré ne correspond à aucun adhérent connu.";
						}
						
						if(code == '')
							code = -1;
						else
						{
							//On vérifie que le code existe
							$.post("produitsverifier.php", { code: code  }, function(reponse){
								inexistant = $("erreur", reponse).text();
								message = $("message", reponse).text();
								
								//Si le code n'existe pas
								if(inexistant == 1)
								{
									message = "Attention, le code entré ne correspond à aucun produit connu.";
								}
							});
						}
						
						if(dates == '')
						{
							dates = -1;
						}
						
						chargerTableau(matricule, code, -1, dates);
						
						$("#messageRecherche").fadeOut(500);
						$("#messageRecherche").queue(function() {
								$("#messageRecherche").html(message);
								$("#messageRecherche").css("color", "black");
								$("#messageRecherche").dequeue();
						});
						$("#messageRecherche").fadeIn(1000);	
					}
					//En cas d'erreur avec les champs
					else
					{
						$("#messageRecherche").fadeOut(500);
						$("#messageRecherche").queue(function() {
								$("#messageRecherche").css("color", "red");
								$("#messageRecherche").html(message);
								$("#messageRecherche").dequeue();
						});
						$("#messageRecherche").fadeIn(1000);
					}
					
					return false;
			});
			
			//Afficher l'historique d'un matricule
			$(".historiqueMatricule").livequery("click", function() {
					matricule = $(this).html();
					
					chargerTableau(matricule, -1, -1, -1);
			});
			
			//Afficher l'historique d'une code
			$(".historiqueCode").livequery("click", function() {
					code = $(this).html();
					
					chargerTableau(-1, code, -1, -1);
			});
			
	});
	</script>
	<link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
	<div id="haut" class="transactions"><span id="gauche"><a href="adherents.php">Adhérents</a></span><span id="milieu">Gestion des transactions</span><span id="droite"><a href="produits.php">Produits</a></span></div>
	
	<div id="onglets">
		<ul>
			<li><a href="#ajouter"><span>Ajouter une transaction</span></a></li>
			<li><a href="#historique"><span>Afficher un historique</span></a></li>
		</ul>
		
		<div id="ajouter">
			<form method="post" action="" id="formulaireAjouter">
				<p class="labelChamps">
					<strong>Matricule</strong><br />
					<i>ou</i><br />
					<strong>Nom</strong><br /><br />
					<strong>Code</strong><br /><br />
					<strong>Date</strong>
				</p>
				<p class="listeChamps">
					<input type="text" name="matricule" id="matricule" size="5"><br />
					<span id="cotisation"></span><br />
					<input type="text" name="nom" id="nom" autocomplete="off"><br /><br />
					<input type="text" name="code" id="code" autocomplete="off"><br /><br />
					<input type="text" name="date" id="date"> <input type="text" name="heure" id="heure" size="2">:<input type="text" name="minute" id="minute" size="2">
				</p>
				<p>
					<div class="valider"><input type="submit" value="Ajouter la transaction" id="boutonAjouter"></div>
					<div id="message" class="message">&nbsp;</div>
				</p>
			</form>
		</div>
		<div id="historique">
			<form method="post" action="" id="formulaireRechercher">
				<p class="labelChamps">
					<strong>Matricule</strong><br />
					<i>ou</i><br />
					<strong>Nom</strong><br /><br />
					<strong>Code</strong><br /><br />
					<strong>Date de la transaction</strong>
				</p>
				<p class="listeChamps">
					<input type="text" name="matriculeRecherche" id="matriculeRecherche" size="5"><br />
					<br />
					<input type="text" name="nomRecherche" id="nomRecherche" autocomplete="off"><br /><br />
					<input type="text" name="codeRecherche" id="codeRecherche" autocomplete="off"><br /><br />
					<input type="text" name="dateRecherche" id="dateRecherche">
				</p>
				<p>
					<div class="valider"><input type="submit" value="Rechercher" id="boutonRecherche"></div>
					<div id="messageRecherche" class="message">&nbsp;</div>
				</p>
			</form>
		</div>
	</div>
	
	<div id="exporter">
		<span id="afficherNonRendus" class="lien">Afficher tous les produits non-rendus</span><br />
		<span id="lienExporter" class="lien"></span><br />
		<span id="genererGraphique" class="lien"></span>
	</div>
	<div id="tableau"></div>
	<div id="chargement">Rechargement du tableau...<br /><br /><img src="images/chargement.gif"></div>
</body>
</html>
