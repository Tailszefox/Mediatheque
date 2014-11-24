<?php
	header('Content-Type: text/xml');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	
	if(get_magic_quotes_gpc())
		$code = stripslashes($_POST['code']);
	else
		$code = $_POST['code'];
	
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
		$erreur = 0;
		$message = '';
	}
	
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<verifier>';
	echo '<erreur>'.$erreur.'</erreur>';
	echo '<message>'.$message.'</message>';
	echo '</verifier>';
	
?>
