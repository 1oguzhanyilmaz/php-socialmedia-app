<?php

require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';
include 'includes/classes/Message.php';
include 'includes/classes/Notification.php';


if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}else{
	header("Location:register.php");
}

?>

<html>
<head>
	<title>Social Media Uygulamasi</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<link rel="stylesheet" href="assets/css/message.css">

	<!-- JAVASCRIPT -->
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/popper.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.js"></script>
	<script type="text/javascript" src="assets/js/bootbox.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.jcrop.js"></script>
	<script type="text/javascript" src="assets/js/jcrop_bits.js"></script>
	<script type="text/javascript" src="assets/js/main.js"></script>

</head>
<body>
	
<div class="container-fluid">
	<?php
		// okunmamis mesajlar
		$messages = new Message($con, $userLoggedIn);
		$num_messages = $messages->getUnreadNumber();

		// okunmamis bildirimler
		$notifications = new Notification($con, $userLoggedIn);
		$num_notifications = $notifications->getUnreadNumber();

		// okunmamis bildirimler
		$user_obj = new User($con, $userLoggedIn);
		$num_requests = $user_obj->getNumberOfFriendRequest();
	?>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="index.php">
			<i class="fa fa-free-code-camp" style="font-size:36px"></i>
		</a>
		<button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<!-- ///// SEARCH ///// -->
		<div class="mt-3 p-0 search" style="position:relative;">

			<form class="form-inline searchhh" action="search.php" method="GET" name="search_form">
				<input class="form-control mr-sm-2 mr-0" type="text" placeholder="Search" name="q" 
						onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" 
						autocomplete="off" 
						id="search_text_input">
				<button class="btn btn-info ml-0" type="submit"><i class="fa fa-search"></i></button>
			</form>

			<div class="ic-kapsayici" style="position:absolute;">
				<div class="search_results p-0 m-0" style="background-color:white;">

				</div>

				<div class="search_results_footer_empty" style="">

				</div>
			</div>

			

		</div>

		<div class="collapse navbar-collapse" id="navbarTogglerDemo02">
			<ul class="navbar-nav mt-2 mt-lg-0 ml-auto">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $userLoggedIn; ?>">
						<?php echo $user['first_name']; ?>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php">
						<i class="fa fa-home" style="font-size:24px"></i>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
						<i class="fa fa-envelope" style="font-size:24px"></i>
						<?php
							if ($num_messages > 0) {
								// echo '<span class="badge badge-pill badge-danger">'. $num_messages .'</span>';
								echo '<span class="notification_badge" id="unread_message">'. $num_messages .'</span>';
							}
						?>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
						<i class="fa fa-bell" style="font-size:24px"></i>
						<?php
							if ($num_notifications > 0) {
								// echo '<span class="badge badge-pill badge-danger">'. $num_messages .'</span>';
								echo '<span class="notification_badge" id="unread_notification">'. $num_notifications .'</span>';
							}
						?>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="requests.php">
						<i class="fa fa-users" style="font-size:24px"></i>
						<?php
							if ($num_requests > 0) {
								// echo '<span class="badge badge-pill badge-danger">'. $num_messages .'</span>';
								// echo '<span class="notification_badge" id="unread_notification">'. $num_notifications .'</span>';
								echo '<span class="notification_badge" id="unread_requests">'. $num_requests .'</span>';
							}
						?>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="settings.php">
						<i class="fa fa-cog" style="font-size:24px;"></i>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="includes/handlers/logout.php">
						<i class="fa fa-sign-out" style="font-size:24px"></i>
					</a>
				</li>
				
			</ul>
			<!--
			<form class="form-inline my-2 my-lg-0">
				<input class="form-control mr-sm-2" type="search" placeholder="Search">
				<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
			</form>
			-->
		</div>
	</nav>

	<div class="dropdown_data_window" style="height:0px;border:none;overflow-x:auto;"></div>
	<input type="hidden" id="dropdown_data_type" value="">

</div>

<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	$(document).ready(function(){

		$('.dropdown_data_window').scroll(function(){
			var inner_height = $('.dropdown_data_window').innerHeight();
			var scroll_top = $('.dropdown_data_window').scrollTop();
			var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
			var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

			// alert(document.body.scrollHeight+"__"+document.body.scrollTop+"__"+window.innerHeight+" Toplami:"+(document.body.scrollTop+window.innerHeight));

			if ( (scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && (noMoreData == 'false') ) {
				
				var pageName; // ajax icin sayfa ismi tut
				var type = $('#dropdown_data_type').val();


				if (type == 'notification') {
					pageName = "ajax_load_notifications.php";
				}else if(type == 'message'){
					pageName = "ajax_load_messages.php";
				}


				var ajaxReq = $.ajax({
									url:"includes/handlers/" + pageName,
									type:"POST",
									data:"page="+page+"&userLoggedIn="+userLoggedIn,
									cache:false,

									success:function(response){
										$('.dropdown_data_window').find('.nextPageDropdownData').remove();
										$('.dropdown_data_window').find('.noMoreDropdownData').remove();

										$('.dropdown_data_window').append(response);
									}
								});
			} // END IF

			return false;

		}); // (window).scroll(function()

	});
</script>
