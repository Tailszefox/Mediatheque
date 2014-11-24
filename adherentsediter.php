<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
	{
		$matricule = stripslashes($_POST['matricule']);
		$matriculeActuelle = stripslashes($_POST['id']);
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
		$matriculeActuelle = $_POST['id'];
		$nom = $_POST['nom'];
		$prenom = $_POST['prenom'];
		$numero = $_POST['numero'];
		$service = $_POST['service'];
		$cotisation = $_POST['cotisation'];
		$datecotisation = $_POST['datecotisation'];
	}
	
	require 'config.php';

	//Si le matricule a changé, on vérifie qu'il n'est pas déjà attribué à un autre adhérent
	if($matricule != $matriculeActuelle)
	{
		$requete = 'SELECT matricule FROM adherents WHERE matricule = "'.mysql_real_escape_string($matricule).'" AND matricule != "'.mysql_real_escape_string($matriculeActuelle).'"';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
		
		if(mysql_num_rows($resultat) == 1)
		{
			$erreur = 1;
			$message = 'Le matricule '.$matricule.' est déjà attribué à un autre adhérent.';
		}
		else
		{
			$erreur = 0;
			$message = 'Adhérent modifié avec succès.';
			
			//On supprime toutes les transactions effectuées avec cette nouvelle matricule
			//Le matricule utilisé pour l'adhérent peut en effet être celui d'un autre adhérent, supprimé, portant le même matricule
			$requete = 'DELETE FROM entrees WHERE matricule = "'.mysql_real_escape_string($matricule).'"';
			mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			
			//On met à jour la base des transactions avec le nouveau matricule
			$requete = 'UPDATE entrees SET matricule="'.mysql_real_escape_string($matricule).'" WHERE matricule="'.mysql_real_escape_string($matriculeActuelle).'"';
			mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		}
	}
	else
	{
		$erreur = 0;
		$message = 'Adhérent modifié avec succès.';
	}
	
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
	
	if($erreur == 0)
	{
		$requete = 'UPDATE adherents 
		SET matricule="'.mysql_real_escape_string($matricule).'", nom="'.mysql_real_escape_string($nom).'", prenom="'.mysql_real_escape_string($prenom).'", numero="'.mysql_real_escape_string($numero).'", service="'.mysql_real_escape_string($service).'", cotisation="'.mysql_real_escape_string($cotisation).'", datecotisation="'.mysql_real_escape_string($datecotisation).'" WHERE matricule="'.mysql_real_escape_string($matriculeActuelle).'"';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	}

	mysql_close();
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<ajouter>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</ajouter>';
?>
