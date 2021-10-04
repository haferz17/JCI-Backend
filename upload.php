<?php
require_once "method.php";
$upload = new Upload();
$request_method = $_SERVER["REQUEST_METHOD"];
switch ($request_method) {
    case 'POST':
        $upload->createFile();
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
