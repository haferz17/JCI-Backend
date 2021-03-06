<?php
require_once "koneksi.php";
include "pushNotif.php";

class User extends Upload
{
	public function getUser($id = 0)
	{
		global $mysqli;
		$query = "SELECT * FROM user";
		if ($id) $query .= " WHERE id='$id' LIMIT 1";
		else $query .= " ORDER BY created_at DESC";
		$data = array();
		$result = $mysqli->query($query);
		while ($row = mysqli_fetch_object($result)) $data[] = $row;

		$response = array(
			'status' => 1,
			'message' => 'Get User Successfully.',
			'data' => $data
		);

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function updateUser($id)
	{
		global $mysqli;
		$arrcheckpost = array('name' => '', 'email' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if ($hitung == count($arrcheckpost) && $_POST['name'] && $_POST['email']) {
			$avatar = $_FILES['file']['name'] ? $this->createFile() : $_POST['avatar'];
			$result = $mysqli->query(
				"UPDATE user SET
				name='$_POST[name]',
				email='$_POST[email]',
				phone='$_POST[phone]',
				address='$_POST[address]',
				latitude='$_POST[latitude]',
				longitude='$_POST[longitude]',
				avatar='$avatar',
				updated_at=now()
				WHERE id='$id'"
			);
			if ($result) {
				$response = array(
					'status' => 1,
					'message' => 'Update Profile Successfully.',
				);
			} else {
				$response = array(
					'status' => 0,
					'message' => 'Update Profile Failed.',
					'error' => $mysqli->error
				);
			}
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}

class Auth
{
	public function login()
	{
		global $mysqli;
		$query = "SELECT * FROM user WHERE email='$_POST[email]' and password=md5('$_POST[password]') LIMIT 1";
		$result = $mysqli->query($query);
		$data = array();
		while ($row = mysqli_fetch_object($result)) $data[] = $row;

		if ($data) {
			$id = $data[0]->id;
			$queryUpdate = "UPDATE user SET fcm='$_POST[fcm]',updated_at=now() WHERE id='$id'";
			$mysqli->query($queryUpdate);
			$response = array(
				'status' => 1,
				'message' => 'Login Successfully.',
				'data' => $data
			);
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Login Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function register()
	{
		global $mysqli;
		$arrcheckpost = array('name' => '', 'email' => '', 'password' => '', 'role' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if ($hitung == count($arrcheckpost) && $_POST['name'] && $_POST['email'] && $_POST['password'] && $_POST['role']) {
			$result = $mysqli->query(
				"INSERT INTO user SET
				name='$_POST[name]',
				email='$_POST[email]',
				password=md5('$_POST[password]'),
				role='$_POST[role]',
				fcm='',
				phone='',
				address='',
				latitude='',
				longitude='',
				avatar='https://www.chocolatebayou.org/wp-content/uploads/No-Image-Person-1536x1536.jpeg',
				created_at=now(),
				updated_at=now()"
			);
			if ($result) {
				$response = array(
					'status' => 1,
					'message' => 'Register Successfully.',
				);
			} else {
				$response = array(
					'status' => 0,
					'message' => 'Register Failed.',
					'error' => $mysqli->error
				);
			}
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}

class Laundry extends Notif
{
	public function getLaundry($id = 0)
	{
		global $mysqli;
		$query = "SELECT * FROM laundry";
		if ($id) $query .= " WHERE id='$id' LIMIT 1";
		else $query .= " ORDER BY created_at DESC";
		$data = array();
		$result = $mysqli->query($query);
		while ($row = mysqli_fetch_object($result)) {
			$newRow = $row;
			$newRow->user = json_decode($newRow->user) ?: (object)[];
			$newRow->admin = json_decode($newRow->admin) ?: (object)[];
			$data[] = $newRow;
		}

		$response = array(
			'status' => 1,
			'message' => 'Get Laundry Successfully.',
			'data' => $data
		);

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function getLaundrybyFilter($filter = 0)
	{
		global $mysqli;
		$query = "SELECT * FROM laundry ";
		if ($filter == 1) $query .= "WHERE status='unconfirmed'";
		elseif ($filter == 2) $query .= "WHERE id_admin='$_POST[id_admin]' and status NOT IN('unconfirmed')";
		elseif ($filter == 3) $query .= "WHERE id_user='$_POST[id_user]'";
		elseif ($filter == 4) $query .= "WHERE id_user='$_POST[id_user]' and status NOT IN('done','canceled')";
		elseif ($filter == 5) $query .= "WHERE id_user='$_POST[id_user]' and status='done'";
		elseif ($filter == 6) $query .= "WHERE id_admin='$_POST[id_admin]' and status NOT IN('unconfirmed','done')";
		$query .= " ORDER BY created_at DESC LIMIT 10";
		$data = array();
		$result = $mysqli->query($query);

		if ($result) {
			while ($row = mysqli_fetch_object($result)) {
				$newRow = $row;
				$newRow->user = json_decode($newRow->user) ?: (object)[];
				$newRow->admin = json_decode($newRow->admin) ?: (object)[];
				$data[] = $newRow;
			}
			$response = array(
				'status' => 1,
				'message' => 'Get Laundry Successfully.',
				'data' => $data
			);
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Get Laundry Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function createLaundry()
	{
		global $mysqli;
		$arrcheckpost = array('id_user' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if ($hitung == count($arrcheckpost) && $_POST['id_user']) {
			$resultUser = $mysqli->query("SELECT * FROM user WHERE id='$_POST[id_user]' LIMIT 1");
			$data = $resultUser->fetch_assoc();
			if ($data) {
				$dataUser = new StdClass();
				$dataUser->name = $data['name'];
				$dataUser->phone = $data['phone'];
				$dataUser->address = $data['address'];
				$dataUser->latitude = $data['latitude'];
				$dataUser->longitude = $data['longitude'];
				$newDataUser = json_encode($dataUser);
				$image =  $_FILES['file']['name'] ? $this->createFile() : $_POST['image'];
				$result = $mysqli->query(
					"INSERT INTO laundry SET
					id_user='$_POST[id_user]',
					avatar_user='$data[avatar]',
					avatar_admin='',
					user='$newDataUser',
					id_admin='0',
					admin='',
					note='$_POST[note]',
					status='unconfirmed',
					image='$image',
					weight='$_POST[weight]',
					created_at=now(),
					updated_at=now()"
				);

				if ($result) {
					$this->createNotif('unconfirmed', $_POST['id_user'], '0', '0');
					$response = array(
						'status' => 1,
						'message' => 'Laundry Request Successfully.'
					);
				} else {
					$response = array(
						'status' => 0,
						'message' => 'Laundry Request Failed.',
						'error' => $mysqli->error
					);
				}
			}
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function updateLaundryStatus($id)
	{
		global $mysqli;
		$arrcheckpost = array('status' => '');
		$hitung = count(array_intersect_key($_POST, $arrcheckpost));

		if ($hitung == count($arrcheckpost) && $_POST['status']) {
			$query = "UPDATE laundry SET status='$_POST[status]',updated_at=now()";
			if ($_POST['status'] == 'confirmed') {
				$resultAdmin = $mysqli->query("SELECT * FROM user WHERE id='$_POST[id_admin]' LIMIT 1");
				$data = $resultAdmin->fetch_assoc();
				if ($data) {
					$dataAdmin = new StdClass();
					$dataAdmin->name = $data['name'];
					$dataAdmin->phone = $data['phone'];
					$dataAdmin->latitude = $data['latitude'];
					$dataAdmin->longitude = $data['longitude'];
					$newDataAdmin = json_encode($dataAdmin);
					$query .= ",id_admin='$_POST[id_admin]',admin='$newDataAdmin',avatar_admin='$data[avatar]'";
				}
			}
			$query .= " WHERE id='$id'";
			$result = $mysqli->query($query);
			if ($result) {
				if ($_POST['status'] != 'canceled') {
					$resultLaundry = $mysqli->query("SELECT * FROM laundry WHERE id='$id' LIMIT 1");
					if ($resultLaundry) {
						$dataLaundry = $resultLaundry->fetch_assoc();
						$this->createNotif($_POST['status'], $dataLaundry['id_admin'], $dataLaundry['id_user'], $id);
					}
				}
				$response = array(
					'status' => 1,
					'message' => 'Update Laundry Status Successfully.',
				);
			} else {
				$response = array(
					'status' => 0,
					'message' => 'Update Laundry Status Failed.',
					'error' => $mysqli->error
				);
			}
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Parameter Do Not Match'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}
}

class Upload
{
	public function createFile()
	{
		global $mysqli, $baseUrl;
		$file = $_FILES['file'];
		$name = rand(0, 9999) . $file['name'];
		$url = '';
		if ($file['name']) {
			$result = $mysqli->query(
				"INSERT INTO file SET
				name='$name',
				size='$file[size]',
				type='$file[type]',
				created_at=now()"
			);
			if ($result) {
				move_uploaded_file($file['tmp_name'], "assets/$name");
				$url = $baseUrl . "/assets/$name";
			}
		}
		return $url;
	}
}

class Notif extends Upload
{
	public function getNotif($id = 0)
	{
		global $mysqli;
		$query = "SELECT * FROM notif";
		if ($id) $query .= " WHERE id='$id' LIMIT 1";
		else $query .= " ORDER BY created_at DESC";
		$data = array();
		$result = $mysqli->query($query);

		if ($result) {
			while ($row = mysqli_fetch_object($result)) $data[] = $row;
			$response = array(
				'status' => 1,
				'message' => 'Get Notif Successfully.',
				'data' => $data
			);
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Get Notif Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function getNotifWithFilter()
	{
		global $mysqli;
		$query = "SELECT * FROM notif WHERE receiver_id='$_POST[id]' ORDER BY created_at DESC";
		$data = array();
		$result = $mysqli->query($query);

		if ($result) {
			while ($row = mysqli_fetch_object($result)) $data[] = $row;
			$response = array(
				'status' => 1,
				'message' => 'Get Notif Successfully.',
				'data' => $data
			);
		} else {
			$response = array(
				'status' => 0,
				'message' => 'Get Notif Failed.',
				'error' => $mysqli->error
			);
		}

		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function createNotif($type, $sender, $receiver, $laundry)
	{
		global $mysqli;
		$notifBody = getNotifBody($type, $sender, $receiver, $laundry);
		if ($notifBody->sender_id) {
			$result = $mysqli->query(
				"INSERT INTO notif SET
				title='$notifBody->title',
				description='$notifBody->description',
				sender_id='$notifBody->sender_id',
				receiver_id='$notifBody->receiver_id',
				laundry_id='$notifBody->laundry_id',
				created_at=now()"
			);
			if ($result) {
				$query = "SELECT * FROM user ";
				if ($receiver) $query .= "WHERE id='$receiver' LIMIT 1";
				else $query .= "WHERE role='admin'";
				$resultUser = $mysqli->query($query);
				if ($resultUser) {
					$fcm = array();
					if ($receiver) $fcm[] = mysqli_fetch_object($resultUser)->fcm;
					else while ($row = mysqli_fetch_object($resultUser)) $fcm[] = $row->fcm;
					sendNotif($notifBody->title, $notifBody->description, $fcm);
				}
			}
		}
	}
}
