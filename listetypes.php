<?php

require 'config.php';

$requete = 'SELECT SQL_SMALL_RESULT DISTINCT type FROM produits ORDER BY type';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

$i = 0;

while($donnees = mysql_fetch_array($resultat))
{
	$types[$i] = $donnees['type'];
	$i++;
}

$q = $_REQUEST["q"];

if($q == '')
{
	for($c = 0; $c < $i; $c++)
	{
		echo $types[$c]."\n";
	}
}
else
{
	for($c = 0; $c < $i; $c++)
	{
		if(stripos($types[$c], $q) === 0) 
		{
			echo $types[$c]."\n";
		}
	}
}
?>
