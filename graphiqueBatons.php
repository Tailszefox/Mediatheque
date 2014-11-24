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
	
	$requete = 'SELECT COUNT(*) as nb, DATE_FORMAT(date, "%m/%Y") as date FROM entrees WHERE matricule = "'.mysql_real_escape_string($matricule).'" AND entree = 0 '.$date.' GROUP BY DATE_FORMAT(date, "%m/%Y") ORDER BY entrees.date';
}
elseif(isset($_GET['code']))
{
	if(get_magic_quotes_gpc())
		$code = stripslashes($_GET['code']);
	else
		$code = $_GET['code'];
	
	$requete = 'SELECT COUNT(*) as nb, DATE_FORMAT(date, "%m/%Y") as date FROM entrees WHERE code = "'.mysql_real_escape_string($code).'" AND entree = 0 '.$date.' GROUP BY DATE_FORMAT(date, "%m/%Y") ORDER BY entrees.date';
}
else
{
	$requete = 'SELECT COUNT(*) as nb, DATE_FORMAT(date, "%m/%Y") as date FROM entrees WHERE entree = 0 '.$date.' GROUP BY DATE_FORMAT(date, "%m/%Y") ORDER BY entrees.date';
}
$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

//Définition des dimensions de l'image et du graphique
$hauteurImage = 350;
$largeurImage = 70;

$espaceGauche = 50;
$espaceHaut = 50;
$espaceBas = 50;

$espaceEntre = 15;

$hauteurGraphique = $hauteurImage - ($espaceHaut+$espaceBas);
$largeurGraphique = 50;

$police = './LiberationSans-Regular.ttf';

//Récupérations des données et calcul de la largeur de l'image
$max = 0;
$i = 0;
while($donnees = mysql_fetch_array($resultat))
{
	if($donnees['nb'] > $max)
		$max =  $donnees['nb'];
	
	$stats[$i]['nb'] = $donnees['nb'];
	$stats[$i]['date'] = $donnees['date'];
	
	$largeurImage += $largeurGraphique + ($espaceEntre*2);
	$i++;	
}

//Calcul de la hauteur de chaque rectangle
for($i = 0; $i < count($stats); $i++)
{
	$stats[$i]['hauteur'] = floor(($stats[$i]['nb'] * $hauteurGraphique)/$max);
}

//On règle une largeur minimale
if($largeurImage < 350)
	$largeurImage = 350;

//Création de l'image
$image = imagecreatetruecolor($largeurImage, $hauteurImage);

//Couleurs utilisées
$blanc = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
$noir = imagecolorallocate($image, 0, 0, 0);
$interieur = imagecolorallocate($image, 0xBE, 0xEB, 0xE9); 

//Fond blanc
imagefill($image, 0, 0, $blanc);

//Axe des ordonnées
imagefilledrectangle($image, $espaceGauche, $espaceHaut-5, $espaceGauche+2, $espaceHaut+$hauteurGraphique, $noir);

//Axe des abscisses
imagefilledrectangle($image, $espaceGauche, $hauteurImage-$espaceBas, $largeurImage, ($hauteurImage-$espaceBas)+2, $noir);

$depart = $espaceGauche + $espaceEntre;

//Dessin de chaque rectangle
for($i = 0; $i < count($stats); $i++)
{
	$x = $depart+$espaceEntre;
	$y = $espaceHaut+$hauteurGraphique;
	
	imagefilledrectangle($image, $x, $y, $x+$largeurGraphique, $y-$stats[$i]['hauteur'], $interieur);
	imagerectangle($image, $x, $y, $x+$largeurGraphique, $y-$stats[$i]['hauteur'], $noir);
	
	//Écriture de la date
	imagettftext($image, 11, 0, $x-1, $y+20, $noir, $police, $stats[$i]['date']);
	
	$depart = $espaceEntre+$x+$largeurGraphique;
}

//Titre du graphique et texte des axes
if($date != '')
	$texteDate = "\n". 'du ' . $dateGauche[0] . '/' . $dateGauche[1].'/'.$dateGauche[2] . ' au ' . $dateDroite[0] . '/' . $dateDroite[1].'/'.$dateDroite[2];
else
	$texteDate = '';

if(isset($matricule))
{
	imagettftext($image, 12, 0, 5, 20, $noir, $police, 'Emprunts par mois de l\'adhérent ' . $matricule . $texteDate);
	imagettftext($image, 12, 90, 15, ($hauteurImage)-80, $noir, $police, 'Nombre de produits empruntés');
}
elseif(isset($code))
{
	imagettftext($image, 12, 0, 5, 20, $noir, $police, 'Emprunts par mois du produit ' . $code . $texteDate);
	imagettftext($image, 12, 90, 15, ($hauteurImage)-80, $noir, $police, 'Nombre de fois emprunté');
}
else
{
	imagettftext($image, 12, 0, 5, 20, $noir, $police, 'Emprunts par mois ' . $texteDate);
	imagettftext($image, 12, 90, 15, ($hauteurImage)-80, $noir, $police, 'Emprunts');
}

imagettftext($image, 12, 0, ($largeurImage/2), ($hauteurImage-$espaceBas)+40, $noir, $police, 'Mois');

//Graduation de l'axe des ordonnées
$graduations = $max;
if($graduations > 10)
	$graduations = 10;

$espace = $hauteurGraphique/$graduations;
$divise = $max / $graduations;

for($i = 0; $i < $graduations; $i++)
{
	$x = $espaceGauche-20;
	$y = ($espaceHaut+15)+($i*$espace);
	
	imagettftext($image, 8, 0, $x, $y, $noir, $police, round($max-($divise*$i)));
	imagefilledrectangle($image, $x, $y-15, $x+20, $y-13, $noir);
}

//Le zéro
imagettftext($image, 8, 0, $espaceGauche-20, ($espaceHaut+15)+($i*$espace), $noir, $police, 0);

//Affichage de l'image
header('Content-type: image/gif');
imagegif($image);
imagedestroy($image);
?>
