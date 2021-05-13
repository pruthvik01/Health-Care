<?php

namespace App\Helpers;

session_start();
//error_reporting(0);
include('include/config.php');
include('include/checklogin.php');
check_login();

if (isset($_POST['submit'])) {
	$specilization = $_POST['Doctorspecialization'];
	$doctorid = $_POST['doctor'];
	$userid = $_SESSION['id'];
	$fees = $_POST['fees'];
	$appdate = $_POST['appdate'];
	$time = $_POST['apptime'];
	$userstatus = 1;
	$docstatus = 1;
	class ZoomApiHelper
	{

		public static function createZoomMeeting($meetingConfig = [])
		{
			$jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6InotM3VSVXF4UmpXUWdhTjI1a1kzWGciLCJleHAiOjE2NDE2ODExODAsImlhdCI6MTYxODM0NzgxOX0.jlh_ps9U34sXJdlZ8s2kEDrZ_LXKc2SvtJO3tNM8fIo';
			$requestBody = [
				'topic'			=> $meetingConfig['topic'] 		?? 'Doctor',
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
				CURLOPT_URL => "https://api.zoom.us/v2/users/" . $zoomUserId . "/meetings",
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
					"Authorization: Bearer " . $jwtToken,
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$data = json_decode($response);
			$start = $data->start_url;
			$join = $data->join_url;
			$password = $data->password;
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return [
					'success' 	=> false,
					'msg' 		=> 'cURL Error #:' . $err,
					'response' 	=> ''
				];
			} else {

				$out['start'] = $start;
				$out['join'] = $join;
				$out['password'] = $password;

				return $out;
			}
		}
	}
	$jd = ZoomApiHelper::createZoomMeeting();
	$start_url = $jd['start'];
	$join_url = $jd['join'];
	$password = $jd['password'];

	$query = mysqli_query($con, "insert into appointment(doctorSpecialization,doctorId,userId,consultancyFees,appointmentDate,appointmentTime,userStatus,doctorStatus,start_url,join_url,password) values('$specilization','$doctorid','$userid','$fees','$appdate','$time','$userstatus','$docstatus','$start_url','$join_url','$password')");
	if ($query) {
		echo "<script>alert('Your appointment successfully booked');</script>";
		header('location:appointment-history.php');
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>User | Book Appointment</title>

	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
	<link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
	<link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
	<link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" media="screen">
	<link href="vendor/select2/select2.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
	<link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" href="assets/css/styles.css">
	<link rel="stylesheet" href="assets/css/plugins.css">
	<link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
	<script>
		function getdoctor(val) {
			$.ajax({
				type: "POST",
				url: "get_doctor.php",
				data: 'specilizationid=' + val,
				success: function(data) {
					$("#doctor").html(data);
				}
			});
		}
	</script>
	<script>
		function getfee(val) {
			$.ajax({
				type: "POST",
				url: "get_doctor.php",
				data: 'doctor=' + val,
				success: function(data) {
					$("#fees").html(data);
				}
			});
		}
	</script>
</head>

<body>
	<div id="app">
		<?php include('include/sidebar.php'); ?>
		<div class="app-content">

			<?php include('include/header.php'); ?>

			<!-- end: TOP NAVBAR -->
			<div class="main-content">
				<div class="wrap-content container" id="container">
					<!-- start: PAGE TITLE -->
					<section id="page-title">
						<div class="row">
							<div class="col-sm-8">
								<h1 class="mainTitle">User | Book Appointment</h1>
							</div>
							<ol class="breadcrumb">
								<li>
									<span>User</span>
								</li>
								<li class="active">
									<span>Book Appointment</span>
								</li>
							</ol>
					</section>
					<!-- end: PAGE TITLE -->
					<!-- start: BASIC EXAMPLE -->
					<div class="container-fluid container-fullw bg-white">
						<div class="row">
							<div class="col-md-12">

								<div class="row margin-top-30">
									<div class="col-lg-8 col-md-12">
										<div class="panel panel-white">
											<div class="panel-heading">
												<h5 class="panel-title">Book Appointment</h5>
											</div>
											<div class="panel-body">
												<p style="color:red;"><?php echo htmlentities($_SESSION['msg1']); ?>
													<?php echo htmlentities($_SESSION['msg1'] = ""); ?></p>
												<form role="form" name="book" method="post">

													<div class="form-group">
														<label for="DoctorSpecialization">
															Doctor Specialization
														</label>
														<select name="Doctorspecialization" class="form-control" onChange="getdoctor(this.value);" required="required">
															<option value="">Select Specialization</option>
															<?php $ret = mysqli_query($con, "select * from doctorspecilization");
															while ($row = mysqli_fetch_array($ret)) {
															?>
																<option value="<?php echo htmlentities($row['specilization']); ?>">
																	<?php echo htmlentities($row['specilization']); ?>
																</option>
															<?php } ?>

														</select>
													</div>

													<div class="form-group">
														<label for="doctor">
															Doctors
														</label>
														<select name="doctor" class="form-control" id="doctor" onChange="getfee(this.value);" required="required">
															<option value="">Select Doctor</option>
														</select>
													</div>

													<div class="form-group">
														<label for="consultancyfees">
															Consultancy Fees
														</label>
														<select name="fees" class="form-control" id="fees" readonly>

														</select>
													</div>

													<div class="form-group">
														<label for="AppointmentDate">
															Date
														</label>
														<input class="form-control datepicker" name="appdate" required="required" data-date-format="yyyy-mm-dd" autocomplete="off">

													</div>

													<div class="form-group">
														<label for="Appointmenttime">
															Time
														</label>
														<input class="form-control" name="apptime" id="timepicker1" required="required">eg : 10:00 PM
													</div>
													<button type="submit" name="submit" class="btn btn-o btn-primary">
														Submit
													</button>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- end: BASIC EXAMPLE -->
					<!-- end: SELECT BOXES -->
				</div>
			</div>
		</div>
		<!-- start: FOOTER -->
		<?php include('include/footer.php'); ?>
		<!-- end: FOOTER -->

		<!-- start: SETTINGS -->
		<?php include('include/setting.php'); ?>

		<!-- end: SETTINGS -->
	</div>
	<!-- start: MAIN JAVASCRIPTS -->
	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/modernizr/modernizr.js"></script>
	<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
	<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
	<script src="vendor/switchery/switchery.min.js"></script>
	<!-- end: MAIN JAVASCRIPTS -->
	<!-- start: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
	<script src="vendor/maskedinput/jquery.maskedinput.min.js"></script>
	<script src="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
	<script src="vendor/autosize/autosize.min.js"></script>
	<script src="vendor/selectFx/classie.js"></script>
	<script src="vendor/selectFx/selectFx.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
	<script src="vendor/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
	<!-- end: JAVASCRIPTS REQUIRED FOR THIS PAGE ONLY -->
	<!-- start: CLIP-TWO JAVASCRIPTS -->
	<script src="doctor/assets/js/main.js"></script>
	<!-- start: JavaScript Event Handlers for this page -->
	<script src="doctor/assets/js/form-elements.js"></script>
	<script>
		jQuery(document).ready(function() {
			Main.init();
			FormElements.init();
		});
		$('.datepicker').datepicker({
			format: 'yyyy-mm-dd',
			startDate: '0d'
		});
	</script>
	<script type="text/javascript">
		$('#timepicker1').timepicker();
	</script>
	<!-- end: JavaScript Event Handlers for this page -->
	<!-- end: CLIP-TWO JAVASCRIPTS -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</body>

</html>