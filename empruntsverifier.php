<?php

if(get_magic_quotes_gpc())
{
	$code = stripslashes($_POST['code']);
	$matricule = stripslashes($_POST['matricule']);
}
else
{
	$code = $_POST['code'];
	$matricule = $_POST['matricule'];
}

require 'config.php';

if(isset($_POST['code']))
{
	//Verification que le produit n'a pas été emprunté
	$requete = 'SELECT produits.nom as titre, adherents.nom as nom, adherents.prenom as prenom FROM entrees, adherents, produits WHERE entrees.code="'.mysql_real_escape_string($code).'" AND entrees.rendu="0" AND entrees.code = produits.code AND entrees.matricule = adherents.matricule';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	
	//Si le produit a été emprunté, on affiche qui l'a emprunté
	if(mysql_num_rows($resultat) == 1)
	{
		$donnees = mysql_fetch_array($resultat);
		$titre = $donnees['titre'];
		$nom = $donnees['nom'];
		$prenom = $donnees['prenom'];
	
		echo 'Le produit '.$titre.' (code '.$code.') ne peut être supprimé : il a été emprunté par '.$nom.' '.$prenom.'.';
	}
	else
	{
		echo 'OK';
	}
}
elseif(isset($_POST['matricule']))
{
	//Vérification que l'adhérent n'a aucun emprunt en cours
	$requete = 'SELECT code FROM entrees WHERE matricule = "'.mysql_real_escape_string($matricule).'" AND rendu="0"';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	
	if(mysql_num_rows($resultat) > 0)
	{
		$message = 'L\'adhérent ne peut être supprimé tant qu\'il n\'a pas rendu les produits suivants : ';

		while($donnees = mysql_fetch_array($resultat))
		{
			$message .= $donnees['code'] . ' ';
		}
		
		echo $message;
	}
	else
	{
		echo 'OK';
	}
}

mysql_close();
?>
