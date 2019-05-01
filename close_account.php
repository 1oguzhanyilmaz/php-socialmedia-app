<?php 

include 'includes/header.php';

if (isset($_POST['cancel'])) {
	header("Location:settings.php");
}

if (isset($_POST['close_account'])) {
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location:register.php");
}

 ?>

 <div class="container">

 	<h4>Hesabi Kapat</h4>

 	Are u sure u want to close ur acccount ? <br><br>
 	Closing ur account will hide ur profile and all ur activity from other users. <br><br>
 	You can re-open ur account at any time by simply logging in. <br> <br>

 	<form action="close_account.php" method="POST">
 		<input type="submit" class="btn btn-outline-info" name="close_account" value="Yes Close it!">
 		<input type="submit" class="btn btn-outline-info" name="cancel" value="No way!">
 	</form>

 </div>