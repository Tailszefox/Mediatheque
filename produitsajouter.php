<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
	{
		$titre = stripslashes($_POST['titre']);
		$code = stripslashes($_POST['code']);
		$type = stripslashes($_POST['type']);
		$genre = stripslashes($_POST['genre']);
		$auteur = stripslashes($_POST['auteur']);
		$acquisition = stripslashes($_POST['acquisition']);
		$numero = stripslashes($_POST['numero']);
	}
	else
	{
		$titre = $_POST['titre'];
		$code = $_POST['code'];
		$type = $_POST['type'];
		$genre = $_POST['genre'];
		$auteur = $_POST['auteur'];
		$acquisition = $_POST['acquisition'];
		$numero = $_POST['numero'];
	}
	
	//Verification de l'existence du code
	require 'config.php';
	$requete = 'SELECT * FROM produits WHERE code="'.mysql_real_escape_string($code).'" LIMIT 1';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	
	//Le code n'existe pas
	if(mysql_num_rows($resultat) == 0)
	{
		$erreur = 0;
		$message = 'Produit ajouté avec succès.';
		
		$acquisition = explode('/', $acquisition);
		$date = $acquisition[2] . '-' . $acquisition[1] . '-' . $acquisition[0];
		
		$requete = 'INSERT INTO produits(code, nom, informations, numero, type, genre, date) 
		VALUES("'.mysql_real_escape_string($code).'", "'.mysql_real_escape_string($titre).'", "'.mysql_real_escape_string($auteur).'", "'.mysql_real_escape_string($numero).'", "'.mysql_real_escape_string($type).'", "'.mysql_real_escape_string($genre).'", "'.mysql_real_escape_string($date).'")';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
		
		//On supprime toutes les transactions effectuées avec ce code
		//Le code utilisé pour le nouveau produit peut en effet être celui d'un autre produit, supprimé, portant la même code
		$requete = 'DELETE FROM entrees WHERE code = "'.mysql_real_escape_string($code).'"';
		mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		
		if(mysql_affected_rows() != 0)
			$message .= ' Note : les transactions associées à ce code (appartenant probablement à un ancien produit) ont été supprimées.';
	}
	//Le code existe
	else
	{
		$erreur = 1;
		$message = 'Le code '.$code.' est déjà attribué à un autre produit.';
	}
	
	mysql_close();
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<ajouter>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</ajouter>';
?>
