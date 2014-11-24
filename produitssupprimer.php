<?php
if(get_magic_quotes_gpc())
	$code = stripslashes($_POST['id']);
else
	$code = $_POST['id'];

require 'config.php';

$requete = 'DELETE FROM produits WHERE code="'.mysql_real_escape_string($code).'"';
$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
mysql_close();
?>
