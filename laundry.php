<?php
require_once "method.php";
$laundry = new Laundry();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
        case 'GET':
                $id=intval($_GET["id"]);
                $laundry->getLaundry($id);
                break;
	case 'POST':
                $id=intval($_GET["id"]);
                $filter=$_POST["filter"];
                if($filter) $laundry->getLaundrybyFilter($filter);
                else {
                        if($id) $laundry->updateLaundryStatus($id);
                        else $laundry->createLaundry();
                }
                break;
        default:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
}
?>