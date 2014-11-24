<?php
require 'config.php';

$requete = 'UPDATE adherents SET cotisation="0", datecotisation="" WHERE cotisation="1"';

$resultat = mysql_query($requete) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

mysql_close();
?>
