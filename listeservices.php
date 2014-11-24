<?php
require 'config.php';

$requete = 'SELECT DISTINCT service FROM adherents ORDER BY service';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

mysql_close();

$i = 0;

while($donnees = mysql_fetch_array($resultat))
{
	$services[$i] = $donnees['service'];
	$i++;
}

$q = $_REQUEST["q"];

if($q == '')
{
	foreach ($services as $service) 
	{
		echo "$service\n";
	}
}
else
{
	foreach ($services as $service) 
	{
		if (stripos($service, $q) === 0) 
		{
			echo "$service\n";
		}
	}
}

?>
