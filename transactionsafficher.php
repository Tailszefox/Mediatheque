<?php
require 'config.php';

if(get_magic_quotes_gpc())
{
	$code = stripslashes($_POST['code']);
	$matricule = stripslashes($_POST['matricule']);
	$nonRendus = stripslashes($_POST['nonRendus']);
	$dates = stripslashes($_POST['dates']);
}
else
{
	$code = $_POST['code'];
	$matricule = $_POST['matricule'];
	$nonRendus = $_POST['nonRendus'];
	$dates = $_POST['dates'];
}

$requete = 'SELECT entrees.ID, entrees.matricule, adherents.nom as aNom, adherents.prenom as aPrenom, entrees.code, entrees.date, entrees.entree, entrees.rendu, produits.nom as pNom, produits.informations, produits.genre FROM entrees 
LEFT OUTER JOIN produits  ON entrees.code = produits.code
LEFT OUTER JOIN adherents ON entrees.matricule = adherents.matricule
WHERE ';
$et = 0;

if($code != -1)
{
	$requete .= 'entrees.code="'.mysql_real_escape_string($code).'" ';
	$et = 1;
}

if($matricule != -1)
{
	if($et == 1)
	{
		$requete .= 'AND ';
	}
	$requete .= 'entrees.matricule="'.mysql_real_escape_string($matricule).'" ';
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
	
	$requete .= 'entrees.date >="'.mysql_real_escape_string($timestampGauche).'" AND entrees.date <="'.mysql_real_escape_string($timestampDroite).'"  ';
	$et = 1;
}

$requete .= 'ORDER BY entrees.date DESC';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$requete.'<br>'.mysql_error());

mysql_close();

if(mysql_num_rows($resultat) == 0)
{
	if($matricule != -1 && $code != -1)
	{
		if($dates != -1)
			echo 'Aucun résultat. Il se peut que l\'adhérent n\'ait pas emprunté ce produit durant la période demandée, ou que ce matricule ou ce code n\'ait jamais existé.';
		else
			echo 'Aucun résultat. Il se peut que l\'adhérent n\'ait jamais empunté ce produit ou que ce matricule ou ce code n\'ait jamais existé.';
	}
	elseif($matricule != -1)
	{
		if($dates != -1)
			echo 'Aucun résultat. Il se peut que l\'adhérent n\'ait empunté aucun produit durant la période demandée, ou que ce matricule n\'ait jamais existé.';
		elseif($nonRendus == 1)
			echo 'Aucun résultat. L\'adhérent a rendu tous les produits qu\'il avait empruntés.';
		else
			echo 'Aucun résultat. Il se peut que l\'adhérent n\'ait jamais empunté aucun produit ou que ce matricule n\'ait jamais existé.';
	}
	elseif($code != -1)
	{
		if($dates != -1)
			echo 'Aucun résultat. Il se peut que le produit n\'ait pas été emprunté durant la période demandée, ou que ce code n\'ait jamais existé.';
		else
			echo 'Aucun résultat. Il se peut que le produit n\'ait jamais été emprunté, ou que ce code n\'ait jamais existé.';
	}
	else
		echo 'Aucun résultat. Aucune transaction n\'a eu lieu durant la période demandée.';
}
else
{
?>
<div id="graphiques"><?php  
if($matricule != -1)
{
	if($dates != -1)
	{
		echo '<p><a href="graphique.php?matricule='.urlencode($matricule).'&dates='.urlencode($dates).'&type=1" target="_blank">Emprunts de l\'adhérent par mois sur la période choisie</a></p>';
		echo '<p><a href="graphique.php?matricule='.urlencode($matricule).'&dates='.urlencode($dates).'&type=2" target="_blank">Catégories des produits empruntés par l\'adhérent sur la période choisie</a></p>';
	}
	else
	{
		echo '<p><a href="graphique.php?matricule='.urlencode($matricule).'&type=1" target="_blank">Emprunts de l\'adhérent par mois</a></p>';
		echo '<p><a href="graphique.php?matricule='.urlencode($matricule).'&type=2" target="_blank">Catégories des produits empruntés par l\'adhérnt</a></p>';
	}
}

if($code != -1)
{
	if($dates != -1)
	{
		echo '<p><a href="graphique.php?code='.urlencode($code).'&dates='.urlencode($dates).'&type=1" target="_blank">Emprunts du produit par mois sur la période choisie</a></p>';
		echo '<p><a href="graphique.php?code='.urlencode($code).'&dates='.urlencode($dates).'&type=2" target="_blank">Services ayant emprunté ce produit sur la période choisie</a></p>';
	}
	else
	{
		echo '<p><a href="graphique.php?code='.urlencode($code).'&type=1" target="_blank">Emprunts du produit par mois</a></p>';
		echo '<p><a href="graphique.php?code='.urlencode($code).'&type=2" target="_blank">Services ayant emprunté ce produit</a></p>';
	}
}

if($dates != -1)
{
	echo '<p><a href="graphique.php?dates='.urlencode($dates).'&type=1" target="_blank">Emprunts par mois sur la période choisie</p>';
	echo '<p><a href="graphique.php?matricule=-1&dates='.urlencode($dates).'&type=2" target="_blank">Catégorie des produits empruntés sur la période choisie</a></p>';
	echo '<p><a href="graphique.php?code=-1&dates='.urlencode($dates).'&type=2" target="_blank">Services ayant emprunté sur la période choisie</a></p>';
}
?></div>
<table id="leTableau" <?php echo 'code="'.str_replace('"', '', $code).'" matricule="'.str_replace('"', '', $matricule).'" nonRendus="'.str_replace('"', '', $nonRendus).'" dates="'.str_replace('"', '', $dates).'"'?>>
	<thead>
	<tr>
		<th>Adhérent</th>
		<th>Nom</th>
		<th>Code</th>
		<th>Titre</th>
		<th>Auteur</th>
		<th>Genre</th>
		<th>Type</th>
		<th>Date</th>
	</tr>
	</thead>
	<tbody>
		<?php
			while($donnees = mysql_fetch_array($resultat))
			{
				if($donnees['entree'] == 0)
				{
					$type='<span class="sortie">Sortie</span>';
				}
				else
				{
					$type='<span class="entree">Entrée</span>';
				}
				
				echo '<tr identification='.$donnees['ID'].' class="ligne">';
				echo '<td><span class="historiqueMatricule lien">'.$donnees['matricule'].'</span></td>';
				if($donnees['aNom'] != '' || $donnees['aPrenom'] != '') 
					echo '<td>'.$donnees['aNom'].' '.$donnees['aPrenom'].'</td>';
				else
					echo '<td><em>Adhérent supprimé</em></td>';
				echo '<td><span class="historiqueCode lien">'.$donnees['code'].'</span></td>';
				if($donnees['pNom'] != '')
					echo '<td>'.$donnees['pNom'].'</td>';
				else
					echo '<td><em>Produit supprimé</em></td>';
				echo '<td>'.$donnees['informations'].'</td>';
				echo '<td>'.$donnees['genre'].'</td>';
				echo '<td>'.$type.'</td>';
				if($donnees['date'] != '0000-00-00')
					echo '<td>'.strftime('%d/%m/%Y %H:%M', strtotime($donnees['date'])).'</td>';
				else
					echo '<td></td>';
				echo '</tr>';
			}
		?>
		</tbody>
</table>
<?php
}
?>
