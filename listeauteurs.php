<?php

require 'config.php';

if($_GET['genre'])
{
	if(get_magic_quotes_gpc())
		$genre = stripslashes($_GET['genre']);
	else
		$genre = $_GET['genre'];
	
	$requete = 'SELECT SQL_BIG_RESULT DISTINCT informations FROM produits WHERE genre="'.mysql_real_escape_string($genre).'" ORDER BY informations';
}
else
{
	$requete = 'SELECT SQL_BIG_RESULT DISTINCT informations FROM produits ORDER BY informations';
}
$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

$i = 0;

while($donnees = mysql_fetch_array($resultat))
{
	$auteurs[$i] = $donnees['informations'];
	$i++;
}

$q = $_GET['q'];

if($q == '')
{
		for($c = 0; $c < $i; $c++)
		{
			echo $auteurs[$c]."\n";
		}
}
else
{
	for($c = 0; $c < $i; $c++)
	{
		if(stripos($auteurs[$c], $q) !== false) 
		{
			echo $auteurs[$c]."\n";
		}
	}
}
?>
