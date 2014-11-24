<?php
if(get_magic_quotes_gpc())
	$id = stripslashes($_POST['id']);
else
	$id = $_POST['id'];
	

require 'config.php';
$requete = 'DELETE FROM adherents WHERE matricule="'.mysql_real_escape_string($id).'"';
$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
mysql_close();
?>
