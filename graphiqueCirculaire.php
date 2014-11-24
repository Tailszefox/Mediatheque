<?php
//Interrogation de la base
if(isset($_GET['dates']))
{
	$dates = $_GET['dates'];
	$datesIntervalle = explode(' - ', $dates);
	
	$dateGauche = explode('/', $datesIntervalle[0]);
	$dateDroite = explode('/', $datesIntervalle[1]);
	
	$timestampGauche = $dateGauche[2] . '-' . $dateGauche[1] . '-' . $dateGauche[0];
	$timestampDroite = $dateDroite[2] . '-' . $dateDroite[1] . '-' . $dateDroite[0];
	
	$date .= 'AND entrees.date >="'.mysql_real_escape_string($timestampGauche).'" AND entrees.date <="'.mysql_real_escape_string($timestampDroite).'"';
}
else
	$date = '';

if(isset($_GET['matricule']))
{
	if(get_magic_quotes_gpc())
		$matricule = stripslashes($_GET['matricule']);
	else
		$matricule = $_GET['matricule'];
	
	if($matricule != -1)
		$requeteMatricule = 'AND matricule = "'.mysql_real_escape_string($matricule).'"';
	else
		$requeteMatricule = '';

	$requete = 'SELECT produits.genre as element, COUNT(produits.genre) as nb
FROM produits, 
(SELECT code FROM entrees WHERE entree = 0 '.$requeteMatricule.' '.$date.' GROUP BY code) empruntes
WHERE produits.code = empruntes.code
GROUP BY produits.genre';
}
elseif(isset($_GET['code']))
{
	if(get_magic_quotes_gpc())
		$code = stripslashes($_GET['code']);
	else
		$code = $_GET['code'];
	
	if($code != -1)
		$requeteCode = 'AND code = "'.mysql_real_escape_string($code).'"';
	else
		$requeteCode = '';

	$requete = 'SELECT adherents.service as element, COUNT(adherents.service) as nb
FROM adherents, 
(SELECT matricule FROM entrees WHERE entree = 0 '.$requeteCode.' '.$date.') empruntes
WHERE adherents.matricule = empruntes.matricule
GROUP BY adherents.service';
}

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

//Récupération des données
$total = 0;
$i = 0;
while($donnees = mysql_fetch_array($resultat))
{
	$stats[$i]['nb'] = $donnees['nb'];
	$stats[$i]['element'] = $donnees['element'];

	$i++;
	$total += $donnees['nb'];
}

//Définition des dimensions de l'image et du graphique
$largeurImage = 600;
$hauteurImage = 600;

$hauteurGraphique = 300;

$police = './LiberationSans-Regular.ttf';

//Création de l'image
$image = imagecreatetruecolor($largeurImage, $hauteurImage);

//Couleurs utilisées
$blanc = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$noir = imagecolorallocate($image, 0, 0, 0);

//Fond blanc
imagefill($image, 0, 0, $blanc);

$angleActuel = 0;
$note = 1;
$report = '';

//Dessin de chaque portion
for($i = 0; $i < count($stats); $i++)
{
	$pourcentage = ($stats[$i]['nb']*100)/$total;
	if(round($pourcentage) > 0)
	{
		$angle = $angleActuel + ($pourcentage*360)/100;
		
		if($i == count($stats) - 1)
			$angle = 360;
		
		$couleur = 255/(($i/5)+1);
		$interieur = imagecolorallocate($image, $couleur, $couleur, 200);

		//Dessin des portions
		imagefilledarc($image, $largeurImage/2, $hauteurImage/2, $hauteurGraphique, $hauteurGraphique, round($angleActuel), round($angle), $interieur, IMG_ARC_PIE);
		imagefilledarc($image, $largeurImage/2, $hauteurImage/2, $hauteurGraphique, $hauteurGraphique, round($angleActuel), round($angle), $noir, IMG_ARC_NOFILL | IMG_ARC_EDGED);
		
		//Calcul des coordonnées du texte
		$moitieAngle = $angle - ($angle - $angleActuel)/2;
		
		$x = ($largeurImage/2) + (($hauteurGraphique/2)) * cos(deg2rad($moitieAngle));
		$y = ($hauteurImage/2) + (($hauteurGraphique/2)) * sin(deg2rad($moitieAngle)); 
	
		//Ajustement de la position et report du texte des parts trop petites
		if($pourcentage > 4)
		{
			if($x > $largeurImage/2)
				$ajoutX = 10;
			else
				$ajoutX = -70;
				
			if($y > $hauteurImage/2)
				$ajoutY = +10;
			else
				$ajoutY = -20;
				
			$label = $stats[$i]['element'] . "\n" . round($pourcentage) . '%';
		}
		else
		{
			if($x > $largeurImage/2)
				$ajoutX = +5;
			else
				$ajoutX = -15;
				
			if($y > $hauteurImage/2)
				$ajoutY = +10;
			else                          
				$ajoutY = -5;
			
			$report .= '('.$note . ') ' . $stats[$i]['element'] . " " . round($pourcentage) . '%  ';
			$label = $note++;
			
			if(($note%5) == 0)
				$report .= "\n";
		}
			
		//Écriture
		imagettftext($image, 10, 0, $x+$ajoutX, $y+$ajoutY, $noir, $police, $label);
		
		$angleActuel = $angle;
	}
}

//Écriture des portions trop petites en dessous du graphique
imagettftext($image, 10, 0, 0, $hauteurImage-60, $noir, $police, $report);

//Titre du graphique
if($date != '')
	$texteDate = "\n". 'du ' . $dateGauche[0] . '/' . $dateGauche[1].'/'.$dateGauche[2] . ' au ' . $dateDroite[0] . '/' . $dateDroite[1].'/'.$dateDroite[2];
else
	$texteDate = '';

if(isset($matricule))
{
	if($matricule != -1)
		$texteMatricule = ' par l\'adhérent ' .$matricule;
	else
		$texteMatricule = '';
	imagettftext($image, 12, 0, 0, 15, $noir, $police, 'Pourcentage des catégories de produits empruntés' .$texteMatricule . $texteDate);
}
elseif(isset($code))
{
	if($code != -1)
		$texteCode = ' le produit ' . $code;
	else
		$texteCode = '';
	imagettftext($image, 12, 0, 0, 15, $noir, $police, 'Pourcentage des services ayant emprunté' .$texteCode . $texteDate);
}

//Affichage de l'image
header('Content-type: image/gif');
imagegif($image);
imagedestroy($image);
?>
