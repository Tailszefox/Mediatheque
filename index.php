<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Menu principal</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	<script type="text/javascript" src="javascript/jquery.js"></script>
	<script type="text/javascript" src="javascript/jquery-ui.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){

			$("#menuadherents a").mouseover(function (){
					$(this).css("color", "blue");
			});
			
			$("#menuadherents a").mouseout(function (){
					$(this).css("color", "black");
			});
			
			$("#menuproduits a").mouseover(function (){
					$(this).css("color", "blue");
			});
			
			$("#menuproduits a").mouseout(function (){
					$(this).css("color", "black");
			});
			
			$("#menutransactions a").mouseover(function (){
					$(this).css("color", "blue");
			});
			
			$("#menutransactions a").mouseout(function (){
					$(this).css("color", "black");
			});
	});
	</script>
</head>
<body>
	<div id="haut">
		Projet Gestion de médiathèque - Thibaut RENAUX - DEUST IOSI 2
	</div>
<?php
//Teste de connexion à la base
require('config.php');

if(!isset($erreur))
{
?>
	<div id="menu">
		<p id="menuadherents"><a href="adherents.php"><img src="images/adherents.png"><br />Gérer les adhérents</a></p>
		<p id="menuproduits"><a href="produits.php"><img src="images/produits.png"><br />Gérer les produits</a></p>
		<p id="menutransactions"><a href="transactions.php"><img src="images/transactions.png"><br />Gérer les transactions</a></p>
	</div>
<?php
}
?>
</body>
</html>
