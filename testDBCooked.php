<?php
	require_once('databaseCooked.php');
	$mysqli=Database::dbConnect();
	 $mysqli -> setAttribute(PDO::ATTR_ERRMODE,
 PDO::ERRMODE_EXCEPTION);

?>