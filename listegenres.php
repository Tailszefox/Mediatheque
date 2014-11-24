<?php

require 'config.php';

$requete = 'SELECT SQL_SMALL_RESULT DISTINCT genre FROM produits ORDER BY genre';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

$i = 0;

while($donnees = mysql_fetch_array($resultat))
{
	$genres[$i] = $donnees['genre'];
	$i++;
}

$q = $_REQUEST["q"];

if($q == '')
{
	for($c = 0; $c < $i; $c++)
	{
		echo $genres[$c]."\n";
	}
}
else
{
	for($c = 0; $c < $i; $c++)
	{
		if(stripos($genres[$c], $q) === 0) 
		{
			echo $genres[$c]."\n";
		}
	}
}
?>
