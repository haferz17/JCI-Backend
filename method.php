<?php
require_once "koneksi.php";
class User {
    public function getUser($id=0){
		global $mysqli;
		$query="SELECT * FROM user";
		if($id) $query.=" WHERE id='$id' LIMIT 1";
		else $query.=" ORDER BY created_at DESC";
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
		$arrcheckpost = array('name' => '', 'email' => '', 'password' => '', 'role' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost) && $_POST['name'] && $_POST['email'] && $_POST['password'] && $_POST['role']){
			$result = $mysqli->query( 
				"INSERT INTO user SET
				name='$_POST[name]',
				email='$_POST[email]',
				password=md5('$_POST[password]'),
				role='$_POST[role]',
				phone='',
				address='',
				latitude='',
				longitude='',
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
class Laundry {
    public function getLaundry($id=0){
		global $mysqli;
		$query="SELECT * FROM laundry";
		if($id) $query.=" WHERE id='$id' LIMIT 1";
		else $query.=" ORDER BY created_at DESC";
		$data=array();
		$result=$mysqli->query($query);
		while($row=mysqli_fetch_object($result)) {
			$newRow=$row;
			$newRow->user=json_decode($newRow->user)?:(object)[];
			$newRow->admin=json_decode($newRow->admin)?:(object)[];
			$data[]=$newRow;
		}

		$response=array(
            'status' => 1,
            'message' =>'Get Laundry Successfully.',
            'data' => $data
        );

		header('Content-Type: application/json');
		echo json_encode($response);
	}

    public function getLaundrybyFilter($filter=0){
		global $mysqli;
		$query="SELECT * FROM laundry ";
		if($filter==1) $query.="WHERE status='unconfirmed'";
		elseif($filter==2) $query.="WHERE id_user='$_POST[id_user]' and status NOT IN('unconfirmed')";
		elseif($filter==3) $query.="WHERE id_user='$_POST[id_user]'";
		$query.=" ORDER BY created_at DESC";
		$data=array();
		$result=$mysqli->query($query);

		if($result){
			while($row=mysqli_fetch_object($result)) {
				$newRow=$row;
				$newRow->user=json_decode($newRow->user)?:(object)[];
				$newRow->admin=json_decode($newRow->admin)?:(object)[];
				$data[]=$newRow;
			}
			$response=array(
				'status' => 1,
				'message' =>'Get Laundry Successfully.',
				'data' => $data
			);
		} else {
			$response=array(
				'status' => 0,
				'message' =>'Get Laundry Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function createLaundry(){
		global $mysqli;
		$arrcheckpost = array('id_user' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost) && $_POST['id_user']){
			$resultUser=$mysqli->query("SELECT * FROM user WHERE id='$_POST[id_user]' LIMIT 1");
			$data=$resultUser->fetch_assoc();
			if($data){
				$dataUser = new StdClass();
				$dataUser->name = $data['name'];
				$dataUser->phone = $data['phone'];
				$dataUser->address = $data['address'];
				$dataUser->latitude = $data['latitude'];
				$dataUser->longitude = $data['longitude'];
				$dataUser->avatar = $data['avatar'];
				$newDataUser = json_encode($dataUser);

				$result = $mysqli->query( 
					"INSERT INTO laundry SET
					id_user='$_POST[id_user]',
					user='$newDataUser',
					note='$_POST[note]',
					status='unconfirmed',
					created_at=now(),
					updated_at=now()"
				);	

				if($result){
					$response=array(
						'status' => 1,
						'message' =>'Laundry Request Successfully.',
					);
				} else {
					$response=array(
						'status' => 0,
						'message' =>'Laundry Request Failed.',
						'error' => $mysqli->error
					);
				}
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

	public function updateLaundryStatus($id){
		global $mysqli;
		$arrcheckpost = array('status' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost) && $_POST['status']){
			$query= "UPDATE laundry SET status='$_POST[status]',updated_at=now()";
			if($_POST['status']=='confirmed'){
				$resultAdmin=$mysqli->query("SELECT * FROM user WHERE id='$_POST[id_admin]' LIMIT 1");
				$data=$resultAdmin->fetch_assoc();
				if($data){
					$dataAdmin = new StdClass();
					$dataAdmin->name = $data['name'];
					$dataAdmin->phone = $data['phone'];
					$dataAdmin->latitude = $data['latitude'];
					$dataAdmin->longitude = $data['longitude'];
					$dataAdmin->avatar = $data['avatar'];
					$newDataAdmin = json_encode($dataAdmin);
					$query.=",id_admin='$_POST[id_admin]',admin='$newDataAdmin'";
				}
			}
			$query.=" WHERE id='$id'";
			$result = $mysqli->query($query);	
			if($result){
				$response=array(
					'status' => 1,
					'message' =>'Update Laundry Status Successfully.',
				);
			} else {
				$response=array(
					'status' => 0,
					'message' =>'Update Laundry Status Failed.',
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
class Notif {
    public function getNotif($id=0){
		global $mysqli;
		$query="SELECT * FROM notif";
		if($id) $query.=" WHERE id='$id' LIMIT 1";
		else $query.=" ORDER BY created_at DESC";
		$data=array();
		$result=$mysqli->query($query);
		
		if($result){
			while($row=mysqli_fetch_object($result)) $data[]=$row;
			$response=array(
				'status' => 1,
				'message' =>'Get Notif Successfully.',
				'data' => $data
			);
		} else {
			$response=array(
				'status' => 0,
				'message' =>'Get Notif Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function createNotif(){
		global $mysqli;
		$arrcheckpost = array('title' => '', 'description' => '', 'sender_id' => '', 'receiver_id' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if($hitung == count($arrcheckpost) && $_POST['title'] && $_POST['description'] && $_POST['sender_id'] && $_POST['receiver_id']){
			$result = $mysqli->query( 
				"INSERT INTO notif SET
				title='$_POST[title]',
				description='$_POST[description]',
				sender_id='$_POST[sender_id]',
				receiver_id='$_POST[receiver_id]',
				created_at=now()"
			);	
			if($result){
				$response=array(
					'status' => 1,
					'message' =>'Create Notif Successfully.',
				);
			} else {
				$response=array(
					'status' => 0,
					'message' =>'Create Notif Failed.',
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