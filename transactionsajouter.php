<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
	{
		$matricule = stripslashes($_POST['matricule']);
		$code = stripslashes($_POST['code']);
		$datetransaction = stripslashes($_POST['datetransaction']);
		$heure = stripslashes($_POST['heure']);
		$minute = stripslashes($_POST['minute']);
	}
	else
	{
		$matricule = $_POST['matricule'];
		$code = $_POST['code'];
		$datetransaction = $_POST['datetransaction'];
		$heure = $_POST['heure'];
		$minute = $_POST['minute'];
	}
	
	//Verification de l'existence du code
	require 'config.php';
	$requete = 'SELECT * FROM produits WHERE code="'.mysql_real_escape_string($code).'" LIMIT 1';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	
	//Le code n'existe pas
	if(mysql_num_rows($resultat) == 0)
	{
		$erreur = 1;
		$message = 'Le code '.$code.' n\'est attribué à aucun produit.';
	}
	//Le code existe
	else
	{		
		$datetransaction = explode('/', $datetransaction);
		$secondes = date('s');
		$datetransaction = $datetransaction[2] . '-' . $datetransaction[1] . '-' . $datetransaction[0] . ' ' . $heure . ':' . $minute . ':' . $secondes;
		
		//On détermine si c'est une entrée ou une sortie
		//On récupère la dernière transaction concernant le produit
		$requete = 'SELECT entrees.entree as entree, adherents.matricule as matricule, adherents.nom as nom, adherents.prenom as prenom FROM entrees, adherents WHERE entrees.code="'.mysql_real_escape_string($code).'" AND entrees.matricule = adherents.matricule ORDER BY date DESC LIMIT 1';
		$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
		
		$donnees = mysql_fetch_array($resultat);
		$statut = $donnees['entree'];
		$matriculeTransaction = $donnees['matricule'];
		$nomTransaction = $donnees['nom'];
		$prenomTransaction = $donnees['prenom'];
		
		//Si la dernière transaction est une sortie du produit
		if(isset($statut) && $statut == 0)
		{
			//On vérifie que c'est la même personne qui le rend
			if($matricule != $matriculeTransaction)
			{
				$erreur = 2;
				$message = 'Ce produit a déjà été emprunté par '.$nomTransaction.' '.$prenomTransaction.' (matricule '.$matriculeTransaction.').';
			}
			//Si c'est le cas, on ajoute la transaction et on modifie la précédente.
			else
			{
				$erreur = 0;
				$message = 'Transaction ajoutée avec succès.';
				
				$requete = 'UPDATE entrees SET rendu="1" WHERE matricule="'.mysql_real_escape_string($matricule).'" AND code="'.mysql_real_escape_string($code).'" ORDER BY date DESC LIMIT 1';
				$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
				
				$requete = 'INSERT INTO entrees(ID, matricule, code, entree, rendu, date) VALUES("", "'.mysql_real_escape_string($matricule).'" , "'.mysql_real_escape_string($code).'", "1", "1", "'.mysql_real_escape_string($datetransaction).'")';
				$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
			}
		}
		//Si c'était une entrée (ou que le produit n'a jamais été emprunté)
		else
		{
			$requete = 'INSERT INTO entrees(ID, matricule, code, entree, rendu, date) VALUES("", "'.mysql_real_escape_string($matricule).'" , "'.mysql_real_escape_string($code).'", "0", "0", "'.mysql_real_escape_string($datetransaction).'")';
			$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
			
			$erreur = 0;
			$message = 'Transaction ajoutée avec succès.';
		}
	}
	
	mysql_close();
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<ajouter>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</ajouter>';
?>
