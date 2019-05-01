<?php 

include 'includes/header.php';
include 'includes/form_handlers/settings_handler.php';

 ?>

 <div class="container">

 	<h4 class='text-secondary'>Hesap Ayarlari</h4><br>

 	<!-- /////////////// PROFILE RESMI /////////////// -->
 	<div class="row">
 		<div class="col-sm-4">
 			<?php 
 				echo "<img src='". $user['profile_pic'] ."' id='small_profile_pics'  width='100' height='100' class='img-fluid'>";
 	 		?>
 	 		<br><br>
			<a class='' href="upload.php">Upload new profile picture</a>
 		</div>
 	</div>

 	<hr>


 	<!-- ///////////// GENEL GUNCELLEMELER ///////////// -->
 	<div class="row">

 		<!-- Kullanici Bilgileri Guncelle -->
 		<div class="col-sm-4 border-left m-4 p-4">
 			<h5 class='text-secondary'>Bilgileri Guncelle</h5>

 			<?php 

				$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
				$row = mysqli_fetch_array($user_data_query);

				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$email = $row['email'];

			 ?>

			<form action="settings.php" method="POST">
				<div class="form-group">
					<label for="fn">First Name : </label>
					<input type="text" class="form-control" id="fn" name="first_name" value="<?php echo $first_name; ?>">
				</div>
				<div class="form-group">
					<label for="ln">Last Name : </label>
					<input type="text" class="form-control" id="ln" name="last_name" value="<?php echo $last_name; ?>">
				</div>
				<div class="form-group">
					<label for="em">Last Name : </label>
					<input type="text" class="form-control" id="em" name="email" value="<?php echo $email; ?>">
				</div>
				 
				 <?php echo $message; ?>

				 <input type="submit" class="btn btn-primary" name="update_details" id="update_details_id" value="Guncelle"><br>
			</form>

 		</div>

 		<!-- Sifre Bilgileri Guncelle -->
 		<div class="col-sm-4 border-left m-4 p-4">
 			<h5 class='text-secondary'>Sifre Degistir</h5>

			<form action="settings.php" method="POST">

				<div class="form-group">
					<label for="pwd1">Suan ki Sifre :</label>
					<input type="password" class="form-control" id="pwd1" name="old_password">
				</div>
				<div class="form-group">
					<label for="pwd2">Yeni Sifre :</label>
					<input type="password" class="form-control" id="pwd2" name="new_password_1">
				</div>
				<div class="form-group">
					<label for="pwd3">Yeni Sifre Tekrar :</label>
					<input type="password" class="form-control" id="pwd3" name="new_password_2">
				</div>

				<?php echo $password_message; ?>

				 <input type="submit" class="btn btn-primary" name="update_password" id="update_password_id" value="Degistir"><br>

			</form>
 		</div>

 		<hr>
	</div>

	<hr>

	<!-- Hesabi Kapat -->
	<div class="row">
		<div class="col-sm-4">
			<p class='text-secondary'>Hesabi kapatmak mi istiyorsun ? </p>
			<form action="settings.php" method="POST">
			 	<input type="submit" class="btn btn-danger" name="close_account" id="close_account_id" value="Hesabi Kapat">
			</form>
		</div>
	</div>

 </div>