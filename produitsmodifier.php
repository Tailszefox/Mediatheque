<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
	{
		$codeActuel = stripslashes($_POST['id']);
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
		$codeActuel = $_POST['id'];
		$titre = $_POST['titre'];
		$code = $_POST['code'];
		$type = $_POST['type'];
		$genre = $_POST['genre'];
		$auteur = $_POST['auteur'];
		$acquisition = $_POST['acquisition'];
		$numero = $_POST['numero'];
	}
	
	require 'config.php';
	
	 //Si la code produit a changé, on vérifie qu'il n'est pas déjà attribué à un autre produit
	 if($code != $codeActuel)
	 {
	 
		//Verification de l'existence du code
		$requete = 'SELECT * FROM produits WHERE code="'.mysql_real_escape_string($code).'" AND code !="'.mysql_real_escape_string($codeActuel).'"';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
		
		//Le code existe déjà
		if(mysql_num_rows($resultat) == 1)
		{
			$erreur = 1;
			$message = 'Le code '.$code.' est déjà attribué à un autre produit.';
		}
		else
		{
			$erreur = 0;
			$message = 'Produit modifié avec succès.';
			
			//On supprime toutes les transactions effectuées avec ce nouveau code
			//Le code utilisé peut en effet être celui d'un autre produit, supprimé, portant la même code
			$requete = 'DELETE FROM entrees WHERE code = "'.mysql_real_escape_string($code).'"';
			mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			
			//On met à jour la base des transactions avec le nouveau code
			$requete = 'UPDATE entrees SET code="'.mysql_real_escape_string($code).'" WHERE code="'.mysql_real_escape_string($codeActuel).'"';
			mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		}
	 }
	else
	{
		$erreur = 0;
		$message = 'Produit modifié avec succès.';
	}
	
	if($erreur == 0)
	{
		$acquisition = explode('/', $acquisition);
		$date = $acquisition[2] . '-' . $acquisition[1] . '-' . $acquisition[0];
		
		$requete = 'UPDATE produits 
		SET code="'.mysql_real_escape_string($code).'", nom="'.mysql_real_escape_string($titre).'", informations="'.mysql_real_escape_string($auteur).'", numero="'.mysql_real_escape_string($numero).'", type="'.mysql_real_escape_string($type).'", genre="'.mysql_real_escape_string($genre).'", date="'.mysql_real_escape_string($date).'" WHERE code="'.mysql_real_escape_string($codeActuel).'"'; 
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	}
	
	mysql_close();
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<ajouter>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</ajouter>';
?>
