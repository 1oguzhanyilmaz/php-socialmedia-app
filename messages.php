<?php

include 'includes/header.php';

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u'])) {
	$user_to = $_GET['u'];
}else{
	$user_to = $message_obj->getMostRecentUser();
	if ($user_to == false) {
		$user_to = 'new';
	}
}

if ($user_to != "new") {
	$user_to_obj = new User($con, $user_to);
}

if (isset($_POST['post_message'])) {
	
	if (isset($_POST['message_body'])) {

		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date);

	}

}

?>

<div class="container" style="margin-top:30px">

	<div class="row">

		<!-- SOL -->
		<div class="col-sm-3 mr-4 bg-white">

			<!-- SOL USER DETAILS -->
			<div class="row">
				<div class="col-sm-12">
					<div class="card" style="width:100%">
						<a href="<?php echo $userLoggedIn; ?>">
							<img class="card-img-top" src="<?php echo $user['profile_pic']; ?>" alt="Profile image" style="width:50%">
						</a>
						<div class="card-body">
							<h4 class="card-title"> <?php echo $user['first_name'] . " " . $user['last_name']; ?> </h4>
							<p class="card-text"> 
								<p> <?php echo "Posts : " . $user['num_posts']; ?> </p>
								<p> <?php echo "Likes : " . $user['num_likes']; ?> </p> 
							</p>
							<a href="<?php echo $userLoggedIn; ?>" class="btn btn-outline-primary">Profile Git</a>
						</div>
					</div>
				</div>
			</div>

			<hr>
			<hr>
			<hr>

			<div class="row">

				<div class="col-sm-12">

					<h4>Sohbetler</h4>

					<div class="row">
						<div class="col-sm-12 sol-sohbet">
							<div style="width:100%;max-height:300px;overflow-y:auto;">
								<?php echo $message_obj->getSohbet(); ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<a href="messages.php?u=new">New Message</a>
						</div>
					</div>

				</div>

			</div>
			<!-- SOL USER DETAILS -->

		</div>

		<!-- SAG -->
		<div class="col-sm-7 ml-4 bg-white p-0">

			<div class="row">

				<div class="col-sm-12">

					<?php

						if ($user_to != "new") {
							echo "<h4> <span class='text-success'>You</span> and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() ."</a></h4>";
							echo "<div class='loaded_messages' id='scroll_messages'>";
							echo $message_obj->getMessages($user_to);
							echo "</div>";

					?>
							<script>

							var div = document.getElementById("scroll_messages");
							div.scrollTop = div.scrollHeight;

							</script>
					<?php	
						}else{
							echo "<p style='font-size:30px;'>Yeni Mesaj</p>";
						}

					?>

				</div>

			</div>

			<div class="row">

				<div class="col-sm-12">

					<form action="" method="POST">

					<?php

					if ($user_to == "new") {
						echo "Mesaj gondermek istedigin arkadasi sec : <br><br>";
						?> 

						Kime : <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Search name' autocomplete='off'  id='search_text_input'>
					
						<?php
						echo "<div class='results'></div>";
					}else{
						echo "<textarea name='message_body' class='form-control' rows='3' id='message_textarea' placeholder='mesajini buraya yaz ...'></textarea><br>";
						echo "<input type='submit' name='post_message' class='btn btn-info' id='message_submit' value='Gonder'>";
					}

					?>

					</form>

				</div>

			</div>	
			

		</div>

	</div>	

</div> <!-- End container -->