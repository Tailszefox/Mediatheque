<?php
require 'config.php';

$requete = 'SELECT * FROM adherents ORDER BY matricule';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

mysql_close();
?>

<table id="leTableau">
	<thead>
	<tr>
		<th>Matricule</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>N°</th>
		<th>Service</th>
		<th>Cotisation</th>
		<th>Date de cotisation</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		<?php
			while($donnees = mysql_fetch_array($resultat))
			{
				echo '<tr identification='.$donnees['matricule'].' class="ligne">';
				echo '<td>'.$donnees['matricule'].'</td>';
				echo '<td>'.$donnees['nom'].'</td>';
				echo '<td>'.$donnees['prenom'].'</td>';
				echo '<td>'.$donnees['numero'].'</td>';
				echo '<td>'.$donnees['service'].'</td>';
				
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
				
				echo '<td>'.$cotisation.'</td>';
				echo '<td>'.$dateCotisation.'</td>';
				echo '<td><img src="images/editer.png" class="editer" /><img src="images/supprimer.png" class="supprimer" /></td>';
				echo '</tr>';
			}
		?>
		</tbody>
</table>
