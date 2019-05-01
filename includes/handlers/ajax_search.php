<?php

include '../../config/config.php';
include '../../includes/Classes/User.php';

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

if (strpos($query, '_') !== false) {
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
}else if (count($names) == 2) {
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') 
										AND user_closed='no' LIMIT 8");
}else {
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') 
										AND user_closed='no' LIMIT 8");
}


if ($query != "") {

	while ($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($con, $userLoggedIn);

		if ($row['username'] != $userLoggedIn) {
			$ortak_arkadaslar = $user->getOrtakFriends($row['username']) . " ortak arkadas";
		}else{
			$ortak_arkadaslar = "";
		}

		echo "
			<div class='row p-0 m-0' style=''>
				<div class='col-sm-12 p-0'>
					<a href='". $row['username'] ."' style='color:#78a5ed;font-size:12px;text-decoration:none;'>

						<div class='media border p-0 m-0 border-0'>
							<img src='". $row['profile_pic'] ."' class='mr-3 mt-3 rounded-circle' style='width:40px;'>
						
							<div class='media-body p-0'>
								<span style='font-size:14px;'>". $row['first_name'] ." ". $row['last_name'] ."</span>
					      		<p style=''>". $row['username'] ."</p>
								<p style=''>". $ortak_arkadaslar ."</p>
							</div>
						</div>

					</a>
				</div>
			</div>
			<hr class='m-0 p-0'>
		";
	}

}

?>