<?php
	$host = "localhost";
	$username = "root";
	$password = "";
	$db_name = "newsdb";

	$database = mysqli_connect("$host", "$username", "$password") or die("Cannot connect server!");
	mysqli_select_db($database, "$db_name") or die("Cannot select DB");
?>