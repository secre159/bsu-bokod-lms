<?php
	$conn = new mysqli('localhost', 'root', '', 'libsystem4');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
	// Disable ONLY_FULL_GROUP_BY for compatibility with legacy queries
	$conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
	
?>
