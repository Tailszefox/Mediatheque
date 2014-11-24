<?php
require 'config.php';

$requete = 'SELECT * FROM adherents ORDER BY nom';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

$i = 0;

while($donnees = mysql_fetch_array($resultat))
{
	$noms[$i] = $donnees['nom'];
	$prenoms[$i] = $donnees['prenom'];
	$i++;
}

$q = $_REQUEST["q"];

if($q == '')
{
	for($c = 0; $c < $i; $c++)
	{
		echo $noms[$c]."\n";
	}
}
else
{
	for($c = 0; $c < $i; $c++)
	{
		if(stripos($noms[$c], $q) === 0) 
		{
			//On affiche également le prénom si deux adhérents portent le même nom
			if($noms[$c] == $noms[$c+1]||$noms[$c] == $noms[$c-1])
			{
				echo $noms[$c].' - '.$prenoms[$c]."\n";
			}
			else
			{
				echo $noms[$c]."\n";
			}
		}
	}
}

?>
