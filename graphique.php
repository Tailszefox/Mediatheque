<?php
require 'config.php';

if($_GET['type'] == 1)
	require 'graphiqueBatons.php';
elseif($_GET['type'] == 2)
	require 'graphiqueCirculaire.php';
?>
