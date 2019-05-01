<?php

require 'config/config.php';

require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';

?>



<html>
<head>
	<title>Register Page</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/register.css">

	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/popper.js"></script>
	<script type="text/javascript" src="assets/js/register.js"></script>
</head>
<body>

<?php

if (isset($_POST['reg_button'])) {
	echo '
	<script>
		$(document).ready(function(){
			$("#first-form").hide();
			$("#second-form").show();
		});
	</script>
	';
}

?>

<div class="container">

	<div class="row justify-content-center">
		<!-- left image -->
		<div class="col-sm-6">
			<img class="img-fluid" src="assets/images/arkaPlan.png" alt="Chania" width="100%" height="100%">
		</div>
		<!-- left image -->

		<!-- SAG LOGIN ve REGISTER FORM -->
		<div class="col-sm-4 mt-3">

			<div class="row">
				<div class="col-sm-12">
					<h3 class="text-info">Social Media Project 1</h3>
					<hr>
				</div>
			</div>

			<div class="row">

				<div class="col-sm-12">

					<!-- SAG LOGIN -->
					<div id="first-form">
						<form action="register.php" method="POST">
							<div class="form-group">
								<label for="email_id">Email:</label>
						    	<input type="email" class="form-control" id="email_id" placeholder="Enter your email" name="log_email" value="<?php 
									if (isset($_SESSION['log_email'])) {
										echo $_SESSION['log_email'];
									} ?>">
							</div>

							<div class="form-group">
								<label for="email">Password:</label>
						    	<input type="password" class="form-control" id="email" placeholder="Enter your password" name="log_password">
							</div>

							<div class="form-group">
								<input class="btn btn-outline-info btn-lg btn-block" type="submit" name="login_button" value="Login"><br>
								<?php if(in_array("Email veya sifre hatali.<br>", $error_array)) echo "Email veya sifre hatali.<br>"; ?>
							</div>
							<a href="#" id="signup" class="signup">Buradan kendine bir hesap olustur</a>
						</form>

					</div>
					
					<!-- SAG REGISTER FORM -->
					<div id="second-form">
						<form action="register.php" method="POST">
							<div class="form-group">
						    	<label for="fname">First Name:</label>
						    	<input type="text" class="form-control" id="fname" placeholder="" name="reg_fname" value="<?php 
								if (isset($_SESSION['reg_fname'])) {
									echo $_SESSION['reg_fname'];
								} ?>">
						    </div>
							<?php if(in_array("isim 25 ile 2 karakter arasi olmalidir.", $error_array)) echo "isim 25 ile 2 karakter arasi olmalidir.<br>"; ?>	

							<div class="form-group">
						    	<label for="lname">Last Name:</label>
						    	<input type="text" class="form-control" id="lname" placeholder="" name="reg_lname" value="<?php 
								if (isset($_SESSION['reg_lname'])) {
									echo $_SESSION['reg_lname'];
								} ?>">
						    </div>
							<?php if(in_array("soyad 25 ile 2 karakter arasi olmalidir.", $error_array)) echo "soyad 25 ile 2 karakter arasi olmalidir.<br>"; ?>	

							<div class="form-group">
						    	<label for="em">Email:</label>
						    	<input type="email" class="form-control" id="em" placeholder="" name="reg_email" value="<?php 
								if (isset($_SESSION['reg_email'])) {
									echo $_SESSION['reg_email'];
								} ?>">
						    </div>

						    <div class="form-group">
						    	<label for="em2">Email:</label>
						    	<input type="email" class="form-control" id="em2" placeholder="" name="reg_email2" value="<?php 
								if (isset($_SESSION['reg_email2'])) {
									echo $_SESSION['reg_email2'];
								} ?>">
						    </div> 
							<?php 	if(in_array("Email kullanilmaktadir.", $error_array)) 
										echo "Email kullanilmaktadir.<br>";
									else if(in_array("Email Format hatasi yaptiniz.", $error_array)) 
										echo "Email Format hatasi yaptiniz.<br>";	
									else if(in_array("Email eslesmiyor!", $error_array)) 
										echo "Email eslesmiyor!<br>"; 
							?>			

							<div class="form-group">
						    	<label for="pw">Password:</label>
						    	<input type="password" class="form-control" id="pw" placeholder="" name="reg_password">
						    </div> 

						    <div class="form-group">
						    	<label for="pw2">Password:</label>
						    	<input type="password" class="form-control" id="pw2" placeholder="" name="reg_password2">
						    </div> 
							<?php 	if(in_array("Sifreler eslesmiyor.", $error_array)) 
										echo "Sifreler eslesmiyor.<br>";
									else if(in_array("Sifrendeki karakterler hatali.", $error_array)) 
										echo "Sifrendeki karakterler hatali.<br>";	
									else if(in_array("Sifre 30 ile 5 karakter arasi olmalidir.", $error_array)) 
										echo "Sifre 30 ile 5 karakter arasi olmalidir.<br>"; 
							?>

							<input class="btn btn-primary btn-lg btn-block" type="submit" name="reg_button" value="Register">
							<br>
							<?php if(in_array("<span style='color:#14C800;'>islem basarili.Giris yapabilirsin</span><br>", $error_array)) 
									echo "<div class='alert alert-success' role='alert'>islem basarili.Giris yapabilirsin</div>"; 
							?>
							<a href="#" id="signin" class="signin">Buradan giris yapabilirsin</a>

						</form>

					</div>

				</div>
			</div>

		</div>
		<!-- SAG LOGIN ve REGISTER FORM -->

	</div>

</div>

</body>
</html>