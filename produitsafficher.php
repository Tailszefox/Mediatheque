<?php
header('Content-Type: text/xml');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

require 'config.php';

if(get_magic_quotes_gpc())
{
	$titre = stripslashes($_POST['titre']);
	$code = stripslashes($_POST['code']);
	$type = stripslashes($_POST['type']);
	$genre = stripslashes($_POST['genre']);
	$auteur = stripslashes($_POST['auteur']);
	$numero = stripslashes($_POST['numero']);
	$acquisition = stripslashes($_POST['acquisition']);
}
else
{
	$titre = $_POST['titre'];
	$code = $_POST['code'];
	$type = $_POST['type'];
	$genre = $_POST['genre'];
	$auteur = $_POST['auteur'];
	$numero = $_POST['numero'];
	$acquisition = $_POST['acquisition'];
}

$requete = 'SELECT * FROM produits WHERE ';
$et = 0;

if($titre != '')
{
	$requete .= 'nom LIKE "%'.mysql_real_escape_string($titre).'%" ';
	$et = 1;
}

if($code != '')
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'code="'.mysql_real_escape_string($code).'" ';
	$et = 1;
}

if($type != '')
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'type="'.mysql_real_escape_string($type).'" ';
	$et = 1;
}

if($genre != '')
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'genre="'.mysql_real_escape_string($genre).'" ';
	$et = 1;
}

if($auteur != '')
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'informations="'.mysql_real_escape_string($auteur).'" ';
	$et = 1;
}

if($numero != '')
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'numero="'.mysql_real_escape_string($numero).'" ';
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
	
	$requete .= 'date >="'.mysql_real_escape_string($timestampGauche).'" AND date <="'.mysql_real_escape_string($timestampDroite).'"  ';
	$et = 1;
}

$requete .= 'ORDER BY code ASC';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<produits>';

echo '<nb>'.mysql_num_rows($resultat).'</nb>';
echo '<tableau><![CDATA[';
?>
<div class="boutonSupprimer">Supprimer les produits sélectionnés</div>
<table id="leTableau" <?php echo 'titre="'.str_replace('"', '', $titre).'" code="'.str_replace('"', '', $code).'" type="'.str_replace('"', '', $type).'" genre="'.str_replace('"', '', $genre).'" auteur="'.str_replace('"', '', $auteur).'" numero="'.str_replace('"', '', $numero).'" acquisition="'.str_replace('"', '', $acquisition).'"'?>>
<thead>
<tr>
	<th>Code</th>
	<th>Titre</th>
	<th>Auteur / Série / ...</th>
	<th>N°</th>
	<th>Type</th>
	<th>Genre</th>
	<th>Date d'acquisition</th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php
	while($donnees = mysql_fetch_array($resultat))
	{				
		echo '<tr identification="'.$donnees['code'].'" class="ligne">';
		echo '<td>'.$donnees['code'].'</td>';
		echo '<td>'.$donnees['nom'].'</td>';
		echo '<td>'.$donnees['informations'].'</span></td>';
		
		if($donnees['numero'] != 0)
			echo '<td>'.$donnees['numero'].'</td>';
		else
			echo '<td></td>';
			
		echo '<td>'.$donnees['type'].'</td>';
		echo '<td>'.$donnees['genre'].'</td>';
		
		if($donnees['date'] != '0000-00-00')
			echo '<td>'.strftime('%d/%m/%Y', strtotime($donnees['date'])).'</td>';
		else
			echo '<td></td>';
			
		echo '<td><img src="images/editer.png" class="editer" /><img src="images/supprimerNoir.png" class="supprimer"/></td>';
		echo '</tr>';
	}
?>
</tbody>
</table>
<?php
echo ']]></tableau></produits>'; 
?>
