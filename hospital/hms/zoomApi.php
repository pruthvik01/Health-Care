<?php
namespace App\Helpers;

class ZoomApiHelper{

	public static function createZoomMeeting($meetingConfig = []){
		$jwtToken ='eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6InotM3VSVXF4UmpXUWdhTjI1a1kzWGciLCJleHAiOjE2MTg5MTM1MTAsImlhdCI6MTYxODMwODcxMH0.ouSSrYSWb44T714ODoVWux3zh4UHR_3tAKG9iUpt-l0';		
		$requestBody = [
			'topic'			=> $meetingConfig['topic'] 		?? 'PHP General Talk',
			'type'			=> $meetingConfig['type'] 		?? 2,
			'start_time'	=> $meetingConfig['start_time']	?? '2021-04-13T16:00:00Z',
			'duration'		=> $meetingConfig['duration'] 	?? 30,
			'password'		=> $meetingConfig['password'] 	?? mt_rand(),
			'timezone'		=> 'Asia/Kolkata',
			'agenda'		=> 'PHP Session',
			'settings'		=> [
			  	'host_video'			=> false,
			  	'participant_video'		=> true,
			  	'cn_meeting'			=> false,
			  	'in_meeting'			=> false,
			  	'join_before_host'		=> false,
			  	'mute_upon_entry'		=> true,
			  	'watermark'				=> false,
			  	'use_pmi'				=> false,
			  	'approval_type'			=> 1,
			  	'registration_type'		=> 1,
			  	'audio'					=> 'voip',
				'auto_recording'		=> 'none',
				'waiting_room'			=> false
			]
		];

		$zoomUserId = 'nm8HJnUUTc2HDB1ALceieA';

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.zoom.us/v2/users/".$zoomUserId."/meetings",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($requestBody),
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: Bearer ".$jwtToken,
		    "Content-Type: application/json",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
                return ($response);
				$data=json_decode($response);
				$start=$data->start_url;
				$join=$data->join_url;
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return [
		  	'success' 	=> false, 
		  	'msg' 		=> 'cURL Error #:' . $err,
		  	'response' 	=> ''
		  ];
		} else {
		  return [
		  	'success' 	=> true,
		  	'msg' 		=> 'success',
		  	'response' 	=> $start
		  ];
		}
	}
}


echo ZoomApiHelper::createZoomMeeting();
?>