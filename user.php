<?php
require_once "userMethod.php";
$user = new User();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
	case 'GET':
            $id=intval($_GET["id"]);
            $user->getUserById($id);
			break;
    default:
			header("HTTP/1.0 405 Method Not Allowed");
			break;
}
?>