<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');

$host	= "localhost";
$user	= "root";
$pass	= "";
$db	= "jci";

$mysqli = new mysqli($host, $user, $pass, $db);
?>