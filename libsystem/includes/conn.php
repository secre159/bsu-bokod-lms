<?php
	$conn = new mysqli('localhost', 'root', '', 'libsystem4');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
?>