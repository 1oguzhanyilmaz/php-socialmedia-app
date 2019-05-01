<html>
<head>
	<title></title>

	<!-- CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color:transparent;">

<style type="text/css">
	*{ font-size: 14px; font-family: Arial, Helvetica, Sans-serif; }
</style>


<?php

require 'config/config.php';
include 'includes/classes/User.php';
include 'includes/classes/Post.php';
include 'includes/classes/Notification.php';

if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}else{
	header("Location:register.php");
}


// post id'sini al
if (isset($_GET['post_id'])) {
	$post_id = $_GET['post_id'];
}


$get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE post_id='$post_id'");
$row = mysqli_fetch_array($get_likes);
$toplam_likes = $row['likes'];
$user_liked = $row['added_by']; 

$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
$row = mysqli_fetch_array($user_details_query);
$toplam_user_likes = $row['num_likes'];

// Like Buton
if (isset($_POST['like_button'])) {
	$toplam_likes++;
	$query = mysqli_query($con, "UPDATE posts SET likes='$toplam_likes' WHERE post_id='$post_id'");
	$toplam_user_likes++;
	$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$toplam_user_likes' WHERE username='$user_liked'");
	$inset_user = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

	// INSERT bildirim
	if ($user_liked != $userLoggedIn) {
		$notification = new Notification($con, $userLoggedIn);
		$notification->insertNotification($post_id, $user_liked, "like");
	}
}

// Unline Buton 
if (isset($_POST['unlike_button'])) {
	$toplam_likes--;
	$query = mysqli_query($con, "UPDATE posts SET likes='$toplam_likes' WHERE post_id='$post_id'");
	$toplam_user_likes--;
	$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$toplam_user_likes' WHERE username='$user_liked'");
	$inset_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");

	// INSERT bildirim
}

// Like kontrol et -> ona gore buttonu belirle
$check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
$num_rows = mysqli_num_rows($check_query);

if ($num_rows > 0) {
	echo '<form action="like.php?post_id=' . $post_id . '" method="POST" class="" style="">
				<div class="mt-0">
					<input type="submit" class="btn btn-outline-primary border-0 mt-0" name="unlike_button" value="Unlike">
					<span class="">	' . $toplam_likes . ' likes </span>
				</div>
			</form>
	';
}else{
	echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
				<div class="mt-0">
					<input type="submit" class="btn btn-outline-primary border-0" name="like_button" value="Like">
					<span class="">	' . $toplam_likes . ' likes </span>
				</div>
			</form>
	';
}

?>



</body>
</html>