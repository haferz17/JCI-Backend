<?php
require_once "koneksi.php";
class User {
    public function getUser($id=0){
		global $mysqli;
		$query="SELECT * FROM user";
		if($id != 0) $query.=" WHERE id=".$id." LIMIT 1";
		$data=array();
		$result=$mysqli->query($query);
		while($row=mysqli_fetch_object($result)) $data[]=$row;

		$response=array(
            'status' => 1,
            'message' =>'Get User Successfully.',
            'data' => $data
        );

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function updateUser($id){
		global $mysqli;
		$arrcheckpost = array('name' => '', 'email' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost) && $_POST['name'] && $_POST['email']){
			$result = $mysqli->query( 
				"UPDATE user SET
				name='$_POST[name]',
				email='$_POST[email]',
				password=md5('$_POST[password]'),
				phone='$_POST[phone]',
				address='$_POST[address]',
				location='$_POST[location]',
				avatar='$_POST[avatar]',
				updated_at=now()
				WHERE id='$id'"
			);	
			if($result){
				$response=array(
					'status' => 1,
					'message' =>'Update Profile Successfully.',
				);
			} else {
				$response=array(
					'status' => 0,
					'message' =>'Update Profile Failed.',
					'error' => $mysqli->error
				);
			}
		} else {
			$response=array(
				'status' => 0,
				'message' =>'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}

class Auth {
    public function login(){
		global $mysqli;
		$query="SELECT * FROM user WHERE email='$_POST[email]' and password=md5('$_POST[password]') LIMIT 1";
		$result=$mysqli->query($query);
		$data=array();
		while($row=mysqli_fetch_object($result)) $data[]=$row;
		
		if($data){
			$response=array(
				'status' => 1,
				'message' =>'Login Successfully.',
				'data' => $data
			);
		} else {
			$response=array(
				'status' => 0,
				'message' =>'Login Failed.',
				'error' => $query
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

    public function register(){
		global $mysqli;
		$arrcheckpost = array('name' => '', 'email' => '', 'password' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost)){
			$result = $mysqli->query( 
				"INSERT INTO user SET
				name='$_POST[name]',
				email='$_POST[email]',
				password=md5('$_POST[password]'),
				role='user',
				phone='',
				address='',
				location='',
				avatar='https://www.chocolatebayou.org/wp-content/uploads/No-Image-Person-1536x1536.jpeg',
				created_at=now(),
				updated_at=now()"
			);	
			if($result){
				$response=array(
					'status' => 1,
					'message' =>'Register Successfully.',
				);
			} else {
				$response=array(
					'status' => 0,
					'message' =>'Register Failed.',
					'error' => $mysqli->error
				);
			}
		} else {
			$response=array(
				'status' => 0,
				'message' =>'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
?>