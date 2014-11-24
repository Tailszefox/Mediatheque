<?php

header('Content-Type: text/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if(isset($_GET['matricule']))
{
	if(get_magic_quotes_gpc())
		$matricule = stripslashes($_GET['matricule']);
	else
		$matricule = $_GET['matricule'];
		
	require 'config.php';
	$requete = 'SELECT * FROM adherents WHERE matricule="'.mysql_real_escape_string($matricule).'" LIMIT 1';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	mysql_close();
	
	while($donnees = mysql_fetch_array($resultat))
	{
		$cotisation = $donnees['cotisation'];
		$nom = $donnees['nom'];
	}
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<adherent>';
	echo '<cotisation>'.$cotisation.'</cotisation>';
	echo '<nom>'.$nom.'</nom>';
	echo '</adherent>';
}
elseif(isset($_GET['nom']))
{
	if(get_magic_quotes_gpc())
		$nom = stripslashes($_GET['nom']);
	else
		$nom = $_GET['nom'];
	
	if(strpos($nom, ' - '))
	{
		$nomPrenom = explode(' - ', $nom);
		
		$nom = $nomPrenom[0];
		$prenom = $nomPrenom[1];
	}
	
	require 'config.php';
	if(isset($prenom))
	{
		$requete = 'SELECT * FROM adherents WHERE nom="'.mysql_real_escape_string($nom).'" AND prenom="'.mysql_real_escape_string($prenom).'" LIMIT 1';
	}
	else
	{
		$requete = 'SELECT * FROM adherents WHERE nom="'.mysql_real_escape_string($nom).'" LIMIT 1';
	}
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	mysql_close();
	
	while($donnees = mysql_fetch_array($resultat))
	{
		$cotisation = $donnees['cotisation'];
		$matricule = $donnees['matricule'];
	}
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<adherent>';
	echo '<cotisation>'.$cotisation.'</cotisation>';
	echo '<matricule>'.$matricule.'</matricule>';
	echo '</adherent>';
}
?>
