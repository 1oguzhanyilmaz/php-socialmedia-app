<?php 

include 'includes/header.php';

if (isset($_GET['q'])) {
	$query = $_GET['q'];
}else{
	$query = "";
}

if (isset($_GET['type'])) {
	$type = $_GET['type'];
}else{
	$type = "name";
}

?>

<div class="container" style="background-color:#fff;">

	<?php 

		if ($query == "") {
			echo "Arama yapmadiniz.";
		}else{



			if ($type == "username") {
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
			}else{

				$names = explode(" ", $query);

				if (count($names) == 3) {
					$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') 
													AND user_closed='no'");
				}else if (count($names) == 2) {
					$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') 
													AND user_closed='no'");
				}else{
					$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') 
														AND user_closed='no'");
				}

			}

			// sonuclar bulunduysa kontrol et
			if (mysqli_num_rows($usersReturnedQuery) == 0) {
				echo "We cant find anyone with a". $type ." like : ". $query;
			}else{
				echo mysqli_num_rows($usersReturnedQuery) . " sonuc bulundu : <br> <br>";
			}

			echo "<p>Try searching for : </p>";
			echo "<a href='search.php?q=". $query ."&type=name'>Names</a>, <a href='search.php?q=". $query ."&type=username'>Usernames</a><br><hr>";

			while ($row = mysqli_fetch_array($usersReturnedQuery)) {
				$user_obj = new User($con, $user['username']);

				$button = "";
				$ortak_arkadaslar = "";

				if ($user['username'] != $row['username']) {
					
					// arkadaslik durumuna bagli olarak buton olustur
					if ($user_obj->isFriend($row['username'])) {
						$button = "<input type='submit' name='". $row['username'] ."' class='btn btn-outline-danger' value='Arkadasi Sil'>";
					}elseif ($user_obj->didReceiveRequest($row['username'])) {
						$button = "<input type='submit' name='". $row['username'] ."' class='btn btn-outline-warning' value='Kabul Et'>";
					}elseif ($user_obj->didSendRequest($row['username'])) {
						$button = "<input type='submit' class='btn btn-outline-default' value='istek Gonderildi'>";
					}else{
						$button = "<input type='submit' name='". $row['username'] ."' class='btn btn-outline-success' value='Arkadasi Ekle'>";
					}

					$ortak_arkadaslar = $user_obj->getOrtakFriends($row['username']) . " ortak arkadas";

					// button forms
					if (isset($_POST[$row['username']])) {

						if ($user_obj->isFriend($row['username'])) {
							$user_obj->removeFriend($row['username']);
							header("Location:http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); // SAYFAYA refresh yapar
						}elseif ($user_obj->didReceiveRequest($row['username'])) {
							header("Location:request.php");
						}elseif ($user_obj->didSendRequest($row['username'])) {
							
						}else{
							$user_obj->sendRequest($row['username']);
							header("Location:http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
						}

					}

				}

				echo "<div class='row'>

							<div class='col-sm-8'>
								<div class='media border p-3 border-0'>
									<a href='". $row['username'] ."'>
										<img src='". $row['profile_pic'] ."' class='img-fluid mr-3  rounded-circle' height='75' width='75'>
									</a>
									
									<div class='media-body'>
										<a href='". $row['username'] ."'>". $row['first_name'] ." ". $row['last_name'] ."
											<p id='grey'>". $row['username'] ."</p>
										</a>
										<br>
										". $ortak_arkadaslar ."<br>
									</div>
								</div>
							</div>

							<div class='col-sm-4'>
								<div class='searchPageFriendButtons'>
									<form action='' method='POST'>
										". $button ."<br>
									</form>
								</div>
							</div>	

						</div>		

					<hr style='border:1px solid #ddd;'>";

			} // END WHILE
			
		}

	 ?>

</div>