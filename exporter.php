<?php

//Enlève les guillemets doubles et les points-virgules
function enleverCaracteres($chaine)
{
	return str_replace('"', '', str_replace(';', '', $chaine));
}

$nomfichier = $_GET['export'] . '-' . date('j-n-y-H-i-s') . '.csv'; 
$csv = fopen('conversion/'.$nomfichier, 'a+');

if($_GET['export'] == 'adherents')
{
	require 'config.php';
	$requete = 'SELECT * FROM adherents ORDER BY matricule';
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	mysql_close();
	
	while($donnees = mysql_fetch_array($resultat))
	{
		if($donnees['cotisation'] == 1)
		{
			$cotisation = 'Oui';
			$dateCotisation = strftime('%d/%m/%Y', strtotime($donnees['datecotisation']));
		}
		else
		{
			$cotisation = 'Non';
			$dateCotisation = '';
		}
		
		fputs($csv, '"'.$donnees['matricule'].'";"'.enleverCaracteres($donnees['nom']).'";"'.enleverCaracteres($donnees['prenom']).'";"'.$donnees['numero'].'";"'.enleverCaracteres($donnees['service']).'";"'.$cotisation.'";"'.$dateCotisation.'"');
		fputs($csv,"\n");
	}
}
elseif($_GET['export'] == 'transactions')
{
	require 'config.php';

	$code = $_GET['code'];
	$matricule = $_GET['matricule'];
	$nonRendus = $_GET['nonRendus'];
	$dates = $_GET['dates'];

	$requete = 'SELECT entrees.ID, entrees.matricule, adherents.nom as aNom, adherents.prenom as aPrenom, entrees.code, entrees.date, entrees.entree, entrees.rendu, produits.nom as pNom, produits.informations, produits.genre FROM entrees 
LEFT OUTER JOIN produits  ON entrees.code = produits.code
LEFT OUTER JOIN adherents ON entrees.matricule = adherents.matricule
WHERE ';
	$et = 0;
	
	if($code != -1)
	{
		$requete .= 'entrees.code="'.$code.'" ';
		$et = 1;
	}
	
	if($matricule != -1)
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'entrees.matricule="'.$matricule.'" ';
		$et = 1;
	}
	
	if($nonRendus == 1)
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'entrees.rendu="0" AND entrees.entree="0" ';
		$et = 1;
	}
	
	if($dates != -1)
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		
		$datesIntervalle = explode(' - ', $dates);
		
		$dateGauche = explode('/', $datesIntervalle[0]);
		$dateDroite = explode('/', $datesIntervalle[1]);
		
		$timestampGauche = $dateGauche[2] . '-' . $dateGauche[1] . '-' . $dateGauche[0];
		$timestampDroite = $dateDroite[2] . '-' . $dateDroite[1] . '-' . $dateDroite[0];
		
		$requete .= 'entrees.date >="'.$timestampGauche.'" AND entrees.date <="'.$timestampDroite.'"  ';
		$et = 1;
	}
	
	$requete .= 'ORDER BY entrees.date DESC';
	
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	
	mysql_close();
	
	if(mysql_num_rows($resultat) > 0)
	{
		while($donnees = mysql_fetch_array($resultat))
		{
			if($donnees['date'] != '0000-00-00')
			{
				$date = strftime('%d/%m/%Y %H:%M', strtotime($donnees['date']));
			}
			else
			{
				$date = '';
			}
			
			if($donnees['entree'] == 0)
			{
				$type = 'Sortie'; 
			}
			else 
			{
				$type = 'Entrée'; 
			}
			
			if($donnees['aNom'] == '' && $donnees['aPrenom'] == '')
			{
				$nomPrenom = 'Adhérent supprimé';
			}
			else
			{
				$nomPrenom = enleverCaracteres($donnees['aNom']).' '.enleverCaracteres($donnees['aPrenom']);
			}
			
			if($donnees['pNom'] == '')
			{
				$nomProduit = 'Produit supprimé';
			}
			else
			{
				$nomProduit = enleverCaracteres($donnees['pNom']);
			}
			
			fputs($csv,'"'.$donnees['matricule'].'";"'.$nomPrenom.'";"'.$donnees['code'].'";"'.$nomProduit.'";"'.enleverCaracteres($donnees['informations']).'";"'.enleverCaracteres($donnees['genre']).'";"'.$type.'";"'.$date.'"');
			fputs($csv,"\n");
		}
	}
}
elseif($_GET['export'] == 'produits')
{
	require 'config.php';

	$titre = $_GET['titre'];
	$code = $_GET['code'];
	$type = $_GET['type'];
	$genre = $_GET['genre'];
	$auteur = $_GET['auteur'];
	$numero = $_GET['numero'];
	$acquisition = $_GET['acquisition'];
	
	$requete = 'SELECT * FROM produits WHERE ';
	$et = 0;
	
	if($titre != '')
	{
		$requete .= 'nom LIKE "%'.$titre.'%" ';
		$et = 1;
	}
	
	if($code != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'code="'.$code.'" ';
		$et = 1;
	}
	
	if($type != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'type="'.$type.'" ';
		$et = 1;
	}
	
	if($genre != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'genre="'.$genre.'" ';
		$et = 1;
	}
	
	if($auteur != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'informations="'.$auteur.'" ';
		$et = 1;
	}
	
	if($numero != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		$requete .= 'numero="'.$numero.'" ';
		$et = 1;
	}
	
	if($acquisition != '')
	{
		if($et == 1)
		{
			$requete .= 'AND ';
		}
		
		$dates = explode(' - ', $acquisition);
		
		$dateGauche = explode('/', $dates[0]);
		$dateDroite = explode('/', $dates[1]);
		
		$timestampGauche = $dateGauche[2] . '-' . $dateGauche[1] . '-' . $dateGauche[0];
		$timestampDroite = $dateDroite[2] . '-' . $dateDroite[1] . '-' . $dateDroite[0];
		
		$requete .= 'date >="'.$timestampGauche.'" AND date <="'.$timestampDroite.'"  ';
		$et = 1;
	}
	
	$requete .= 'ORDER BY code ASC';
	
	$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());
	
	mysql_close();
	
	if(mysql_num_rows($resultat) > 0)
	{
		while($donnees = mysql_fetch_array($resultat))
		{	
			if($donnees['date'] != '0000-00-00')
				$date = strftime('%d/%m/%Y', strtotime($donnees['date']));
			else
				$date = '';
			
			fputs($csv, '"'.$donnees['code'].'";"'.enleverCaracteres($donnees['nom']).'";"'.enleverCaracteres($donnees['informations']).'";"'.$donnees['numero'].'";"'.enleverCaracteres($donnees['type']).'";"'.enleverCaracteres($donnees['genre']).'";"'.$date.'"');
			fputs($csv,"\n");
		}
	}
}

fclose($csv);

echo '<a href="conversion/'.$nomfichier.'">Télécharger le fichier</a>';
?>
