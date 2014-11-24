<?php 
//On désactive l'affichage des erreurs (lorsqu'on est en production)
error_reporting(0);

//On lit les informations depuis le fichier
$fichier = file('config.txt', FILE_IGNORE_NEW_LINES);

//On se connecte à la base
$db = mysql_connect($fichier[0], $fichier[1], $fichier[2]);

if(!$db)
{
	echo '<div class="erreur">Erreur de connexion à la base de données. <a href="parametres.php">Veuillez vérifier les paramètres de connexion</a>.</div>';
	$erreur = 1;
}
elseif(!mysql_select_db('gestion', $db))
{
	echo '<div class="erreur">La base de données <em>gestion</em> est introuvable. <a href="creerBase.php">Veuillez la recréer.</a></div>';
	$erreur = 2;
}

?>