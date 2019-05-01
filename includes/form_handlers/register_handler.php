<?php

$fname = "";
$lname = "";
$em = "";
$em2 = "";
$password = "";
$password2 = "";
$date = ""; // register tarihi
$error_array = array(); // hatalari tut yazdir

if (isset($_POST['reg_button'])) {
	// first name
	$fname = strip_tags($_POST['reg_fname']); // taglari kaldir
	$fname = str_replace(' ', '', $fname); // search, replace, subject *bosluk kaldir
	$fname = ucfirst(strtolower($fname)); // ilk harfi buyuk yap
	$_SESSION['reg_fname'] = $fname; // first name tut sessionda

	// last name
	$lname = strip_tags($_POST['reg_lname']); // taglari kaldir
	$lname = str_replace(' ', '', $lname); // search, replace, subject *bosluk kaldir
	$lname = ucfirst(strtolower($lname)); // ilk harfi buyuk yap
	$_SESSION['reg_lname'] = $lname; // last name tut sessionda

	// email
	$em = strip_tags($_POST['reg_email']); // taglari kaldir
	$em = str_replace(' ', '', $em); // search, replace, subject *bosluk kaldir
	$em = ucfirst(strtolower($em)); // ilk harfi buyuk yap
	$_SESSION['reg_email'] = $em; // email tut sessionda

	// email2
	$em2 = strip_tags($_POST['reg_email2']); // taglari kaldir
	$em2 = str_replace(' ', '', $em2); // search, replace, subject *bosluk kaldir
	$em2 = ucfirst(strtolower($em2)); // ilk harfi buyuk yap
	$_SESSION['reg_email2'] = $em2; // email tut sessionda

	// password
	$password = strip_tags($_POST['reg_password']); // taglari kaldir
	$password2 = strip_tags($_POST['reg_password2']); // taglari kaldir

	$date = date("Y-m-d"); 

	if ($em == $em2) {
		
		if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
			$em = filter_var($em, FILTER_VALIDATE_EMAIL);
			
			// email kontrolu DB
			$em_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");
			$num_rows = mysqli_num_rows($em_check); // geri donen satir sayisi

			if ($num_rows > 0) {
				array_push($error_array, "Email kullanilmaktadir.");
			}

		}else{
			array_push($error_array, "Email Format hatasi yaptiniz.");
		}

	}else{
		array_push($error_array, "Email eslesmiyor!");
	}

	if (strlen($fname) > 25 || strlen($fname) < 2 ) {
		array_push($error_array, "isim 25 ile 2 karakter arasi olmalidir.");
	}

	if (strlen($lname) > 25 || strlen($lname) < 2 ) {
		array_push($error_array, "soyad 25 ile 2 karakter arasi olmalidir.");
	}

	if ($password != $password2) {
		array_push($error_array, "Sifreler eslesmiyor.");
	}else{
		if (preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($error_array, "Sifrendeki karakterler hatali.");
		}
	}
	
	if (strlen($password) > 30 || strlen($password) < 5 ) {
		array_push($error_array, "Sifre 30 ile 5 karakter arasi olmalidir.");
	}

	if (empty($error_array)) { // array de eleman yoksa INSERT yap
		$password = md5($password); // DB ye gondermeden once sifreleme yap

		//username uret
		$username = strtolower($fname . "_" . $lname);
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");

		$i =  0;
		while (mysqli_num_rows($check_username_query) != 0) {
			$i++;
			$username = $username . "_" . $i;
			$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		}

		// default olarak profil resmi ver
		$rand = rand(1, 2);

		if ($rand == 1) {
			$profile_pic =  "assets/images/profile_pics/defaults/profil2.png";
		} else {
			$profile_pic =  "assets/images/profile_pics/defaults/profil3.png";
		}

		// INSERT
		$query = mysqli_query($con, "INSERT INTO users VALUES 
						('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0' ,'0', 'no', ',')");

		array_push($error_array, "<span style='color:#14C800;'>islem basarili.Giris yapabilirsin</span><br>");

		// session degerlerini temizle
		$_SESSION['reg_fname'] 	= "";
		$_SESSION['reg_lname'] 	= "";
		$_SESSION['reg_email'] 	= "";
		$_SESSION['reg_email2'] = "";
		
	}

}

?>