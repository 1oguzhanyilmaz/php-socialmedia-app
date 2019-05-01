<?php

include 'includes/header.php';

?>

<div class="container">

	<div class="col-sm-12">

		<h4>Arkadaslik istekleri</h4>


		<?php


		$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");

		if (mysqli_num_rows($query) == 0) {
			echo "Su anda arkadaslik istegi bulunmamaktadir.";
		} else {
			
			while ($row = mysqli_fetch_array($query)) {
				$user_from = $row['user_from'];
				$user_from_obj = new User($con, $user_from);

				echo $user_from_obj->getFirstAndLastName() . " sana bir arkadaslik istegi gonderdi.";

				$user_from_friend_array = $user_from_obj->getFriendArray();

				if (isset($_POST['accept_request'.$user_from])) {
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					echo "Artik arkadas oldunuz.";
					header("Location:index.php");
				}

				if (isset($_POST['ignore_request'.$user_from])) {
					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					echo "istek reddedildi.";
					header("Location:requests.php");
				}

		?>

				<form action="requests.php" method="POST">
					<input type="submit" name="accept_request<?php echo $user_from; ?>" id="" class="btn btn-success" value="Kabul Et">
					<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="" class="btn btn-danger" value="Reddet">
				</form>

		<?php

			}

		}
		
		?>

	</div>
</div>