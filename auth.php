<?php
require_once "method.php";
$auth = new Auth();
$request_method=$_SERVER["REQUEST_METHOD"];
switch ($request_method) {
	case 'POST':
            $type=$_GET["type"];
            $name=$_POST["name"];
            $email=$_POST["email"];
            $pass=$_POST["password"];
            if($type=='register') $auth->register($name,$email,$pass);
            elseif ($type=='login')$auth->login($email,$pass);
            else header("HTTP/1.0 400 Bad Request");
			break;
    default:
			header("HTTP/1.0 405 Method Not Allowed");
			break;
}
?>