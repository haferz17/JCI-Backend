<?php
require_once "koneksi.php";
class User {
    public function getUser(){
		global $mysqli;
		$query="SELECT * FROM user";
		$data=array();
		$result=$mysqli->query($query);
		while($row=mysqli_fetch_object($result)){
			$data[]=$row;
		}
		$response=array(
            'status' => 1,
            'message' =>'Get List User Successfully.',
            'data' => $data
        );
		header('Content-Type: application/json');
		echo json_encode($response);
	}

    public function getUserById($id=0){
		global $mysqli;
		$query="SELECT * FROM user";
		if($id != 0){
			$query.=" WHERE id=".$id." LIMIT 1";
		}
		$data=array();
		$result=$mysqli->query($query);
		while($row=mysqli_fetch_object($result)){
			$data[]=$row;
		}
		$response=array(
            'status' => 1,
            'message' =>'Get User Successfully.',
            'data' => $data
        );
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
?>