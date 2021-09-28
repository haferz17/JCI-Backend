<?php
require_once "method.php";
$user = new User();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
	case 'GET':
        $id=intval($_GET["id"]);
        $user->getUser($id);
        break;
	case 'POST':
        $id=intval($_GET["id"]);
        $user->updateUser($id);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>