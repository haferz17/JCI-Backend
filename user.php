<?php
require_once "method.php";
$user = new User();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
	case 'POST':
            $id=intval($_POST["id"]);
            $user->getUser($id);
			break;
    default:
			header("HTTP/1.0 405 Method Not Allowed");
			break;
}
?>