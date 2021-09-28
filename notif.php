<?php
require_once "method.php";
$notif = new Notif();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
	case 'GET':
                $id=intval($_GET["id"]);
                $notif->getNotif($id);
                break;
	case 'POST':
                $notif->createNotif();
                break;
        default:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
}
?>