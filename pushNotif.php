<?php
function getNotifBody($type, $sender, $receiver, $laundry) {
	$notifBody = new StdClass();
	$notifBody->sender_id = $sender;
	$notifBody->receiver_id = $receiver;
	$notifBody->laundry_id = $laundry;
	switch ($type) {
		case 'unconfirmed':
			$notifBody->title = 'Laundry Baru!';
			$notifBody->description = 'Ada request laundry baru. Segera cek sebelum keduluan ya :)';
			break;
		case 'confirmed':
			$notifBody->title = 'Laundry Dikonfirmasi';
			$notifBody->description = 'Request laundry mu sudah dikonfirmasi dan akan segera diproses :)';
			break;
		case 'rejected':
			$notifBody->title = 'Laundry Ditolak';
			$notifBody->description = 'Maaf request laundry mu ditolak. Silahkan request lagi di kemudian waktu :)';
			break;
		case 'pickup':
			$notifBody->title = 'Laundry Dalam Pengambilan';
			$notifBody->description = 'Laundry mu sedang dalam proses pengambilan. Mohon untuk tetap stand by di titik pengambilan :)';
			break;
		case 'washing':
			$notifBody->title = 'Laundry Dicuci';
			$notifBody->description = 'Laundry mu sedang dicuci sekarang. Mohon ditunggu ya :)';
			break;
		case 'ironing':
			$notifBody->title = 'Laundry Disetrika';
			$notifBody->description = 'Laundry mu sedang disetrika sekarang. Mohon ditunggu ya :)';
			break;
		case 'delivery':
			$notifBody->title = 'Laundry Dalam Pengantaran';
			$notifBody->description = 'Laundry mu sedang dalam proses pengantaran. Mohon untuk tetap stand by di titik pengantaran :)';
			break;
		case 'done':
			$notifBody->title = 'Laundry Selesai';
			$notifBody->description = 'Laundry mu telah selesai. Terima kasih :)';
			break;
		default:
			$notifBody->title = 'Laundry';
			$notifBody->description = 'Laundry sedang diproses';
			break;
	}
	return $notifBody;
}

function sendNotif($title, $description, $fcm) {
  $url = 'https://fcm.googleapis.com/fcm/send';
  // Server Key
  $apiKey = "AAAA6ebio0c:APA91bER4-UrLBCvCZnr-xffmviI98oJLJLOJbDryUuDSoBL_6nUP0oh75xaU5p6WpJKH_a33tZ0TabXHDIkqZG5rditAPM7fOCBBbi2KByibLqnPS0Gk50ND9Fie1sv4ghqgZh9d3rJ";
  // Compile headers in one variable
  $headers = array (
    'Authorization:key=' . $apiKey,
    'Content-Type:application/json'
  );
  // Add notification content to a variable for easy reference
  $notifData = [
    'title' => $title,
    'body' => $description,
    // "click_action"=> "HelloCordova"
  ];
  // Create the api body
  $apiBody = ['notification' => $notifData];
  if(count($fcm)<=1) $apiBody['to'] = $fcm[0];
  else $apiBody['registration_ids'] = $fcm;
  // Initialize curl with the prepared headers and body
  $ch = curl_init();
  curl_setopt ($ch, CURLOPT_URL, $url);
  curl_setopt ($ch, CURLOPT_POST, true);
  curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode($apiBody));
  // Execute call and save result
  $result = curl_exec($ch);
  // Close curl after call
  curl_close($ch);

  return $result;
}
?>