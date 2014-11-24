<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Création de la base de données</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	</head>                                       
<body>
	<div id="haut">
		Création de la base de données
	</div>

<?php

require('config.php');

if($erreur == 2)
{
?>
	<div style="text-align: center">
		<p>Création de la base <em>gestion</em> et de ses tables...</p>
		
		<?php
		mysql_query('CREATE DATABASE `gestion` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
		mysql_select_db('gestion', $db);
		echo mysql_error();
		mysql_query('CREATE TABLE IF NOT EXISTS `adherents` (
  `matricule` int(11) NOT NULL,
  `nom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `prenom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `numero` int(11) NOT NULL,
  `service` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `cotisation` tinyint(1) NOT NULL,
  `datecotisation` date DEFAULT NULL,
  PRIMARY KEY (`matricule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
		mysql_query('CREATE TABLE IF NOT EXISTS `entrees` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` int(11) NOT NULL,
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `date` datetime NOT NULL,
  `entree` tinyint(1) NOT NULL,
  `rendu` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5001 ;');
		mysql_query('CREATE TABLE IF NOT EXISTS `produits` (
  `code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nom` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `informations` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `numero` int(4) NOT NULL,
  `type` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `genre` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');
		?>
		
		<p>Insertion de données dans la table <em>adhérents</em>...</p>
		
		<?php
		$adherents = file('echantillonAdherents.csv', FILE_IGNORE_NEW_LINES);

		foreach($adherents as $adherent)
		{
			$donneesAdherent = explode(';', $adherent);
			mysql_query('INSERT INTO adherents VALUES('.$donneesAdherent[0].', '.$donneesAdherent[1].', '.$donneesAdherent[2].', '.$donneesAdherent[3].', '.$donneesAdherent[4].', '.$donneesAdherent[5].', '.$donneesAdherent[6].')');
		}
		?>
		
		<p>Insertion de données dans la table <em>produits</em>...</p>
		
		<?php
		$produits = file('echantillonProduits.csv', FILE_IGNORE_NEW_LINES);

		foreach($produits as $produit)
		{
			$donneesProduit = explode(';', $produit);
			mysql_query('INSERT INTO produits VALUES('.$donneesProduit[0].', '.$donneesProduit[1].', '.$donneesProduit[2].', '.$donneesProduit[3].', '.$donneesProduit[4].', '.$donneesProduit[5].', '.$donneesProduit[6].')');
		}
		?>
		
		<p>Insertion de données dans la table <em>transactions</em>...</p>
		
		<?php
		$transactions = file('echantillonTransactions.csv', FILE_IGNORE_NEW_LINES);

		foreach($transactions as $transaction)
		{
			$donneesTransaction = explode(';', $transaction);
			mysql_query('INSERT INTO entrees VALUES('.$donneesTransaction[0].', '.$donneesTransaction[1].', '.$donneesTransaction[2].', '.$donneesTransaction[3].', '.$donneesTransaction[4].', '.$donneesTransaction[5].')');
		}
		?>
	</div>
	
	<div style="text-align: center; color: #00FF00;">Création de la base réussie. Vous pouvez maintenant <a href="index.php">utiliser l'application</a>.</div>
<?php
}
elseif(!isset($erreur))
{
	?>
	<div style="text-align: center; color: #00FF00;">La base de données est déjà créée. Vous pouvez <a href="index.php">utiliser l'application</a>.</div>
	<?php
}

?>

</body>
</html>
