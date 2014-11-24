<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
	{
		$matricule = stripslashes($_POST['matricule']);
		$nom = stripslashes($_POST['nom']);
		$prenom = stripslashes($_POST['prenom']);
		$numero = stripslashes($_POST['numero']);
		$service = stripslashes($_POST['service']);
		$cotisation = stripslashes($_POST['cotisation']);
		$datecotisation = stripslashes($_POST['datecotisation']);
	}
	else
	{
		$matricule = $_POST['matricule'];
		$nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$numero = $_POST['numero'];
		$service = $_POST['service'];
		$cotisation = $_POST['cotisation'];
		$datecotisation = $_POST['datecotisation'];
	}
	
	//Verification de l'existence ou non du matricule
	require 'config.php';
	$requete = 'SELECT * FROM adherents WHERE matricule="'.mysql_real_escape_string($matricule).'"';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	
	if(mysql_num_rows($resultat) != 0)
	{
		$erreur = 1;
		$message = 'Le matricule '.$matricule.' est déjà attribué à un autre adhérent.';
	}
	else
	{
		$erreur = 0;
		$message = 'Adhérent ajouté avec succès.';
		
		if($cotisation == 'true')
		{
			$cotisation = 1;
			$datecotisation = explode('/', $datecotisation);
			$datecotisation = $datecotisation[2] . '-' . $datecotisation[1] . '-' . $datecotisation[0];
		}
		else
		{
			$cotisation = 0;
			$datecotisation = '';
		}
		
		//On ajoute l'adhérent dans la base
		$requete = 'INSERT INTO adherents(matricule, nom, prenom, numero, service, cotisation, datecotisation)
		VALUES("'.mysql_real_escape_string($matricule).'", "'.mysql_real_escape_string($nom).'", "'.mysql_real_escape_string($prenom).'", "'.mysql_real_escape_string($numero).'", "'.mysql_real_escape_string($service).'", "'.mysql_real_escape_string($cotisation).'", "'.mysql_real_escape_string($datecotisation).'")';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		
		//On supprime toutes les transactions effectuées avec ce matricule
		//Le matricule utilisé pour le nouvel adhérent peut en effet être celui d'un autre adhérent, supprimé, portant le même matricule
		$requete = 'DELETE FROM entrees WHERE matricule = "'.mysql_real_escape_string($matricule).'"';
		mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		
		if(mysql_affected_rows() != 0)
			$message .= ' Note : les transactions associées à ce matricule (appartenant probablement à un ancien adhérent) ont été supprimées.';
	}

	mysql_close();
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<ajouter>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</ajouter>';
?>
