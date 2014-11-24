<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Configuration de la connexion à MySQL</title>
	<link rel="stylesheet" href="style.css" type="text/css">
	</head>                                       
<body>
	<div id="haut">
		Configuration de la connexion à MySQL
	</div>

<?php
//Les paramètres ont été modifiés
if(isset($_POST['serveur'], $_POST['login']))
{
	file_put_contents('./config.txt', $_POST['serveur'] . "\n" . $_POST['login'] . "\n" . $_POST['mdp'] . "\n");
}

require('config.php');

if(!isset($erreur))
{
?>
	<div style="text-align: center; color: #00FF00;">Connexion à la base réussie. Vous pouvez maintenant <a href="index.php">utiliser l'application</a>.</div>
<?php
}

?>
	
	<form method="post" action="parametres.php">
		<p class="labelChamps">
			<strong>Serveur MySQL :</strong><br />
			<strong>Nom d'utilisateur :</strong><br />
			<strong>Mot de passe :</strong><br />
		</p>
		<p class="listeChamps">
			<input type="text" name="serveur" value="<?php echo $fichier[0] ?>"><br />
			<input type="text" name="login" value="<?php echo $fichier[1] ?>"><br />
			<input type="password" name="mdp" value="<?php echo $fichier[2] ?>"><br />
		</p>
			<div class="valider"><input type="submit" value="Modifier les paramètres de connexion"></div>
	</form>
</body>
</html>
