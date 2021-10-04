<?php
require_once ".env";
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');

global $host, $user, $pass, $db, $baseUrl;
$mysqli = new mysqli($host, $user, $pass, $db);
