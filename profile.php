<?php

include 'includes/header.php';

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$friend_sayisi = (substr_count($user_array['friend_array'], ',')) - 1;
}

if (isset($_POST['remove_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}

if (isset($_POST['add_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}

if (isset($_POST['respond_request'])) {
	header("Location:requests.php");
}

if (isset($_POST['post_message'])) {
	if (isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($username, $body, $date);
	}

	$link = '#profileTabs a[href="#message_div"]';
	echo "
		<script>
			$(function(){
				$('" . $link . "').tab('show');
			});
		</script>
	";
}

?>


<div class="container-fluid">

	<div class="row">

		<div class="col-sm-3">

			<div class="row">
				<div class="col-sm-12">
					<img src="<?php echo $user_array['profile_pic']; ?>" class="rounded" alt="Cinque Terre" width="80%" height="250">
					<hr>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<p> <b> <?php echo $user_array['first_name'] . " " . $user_array['last_name']; ?> </b> </p>
					<p> <?php echo "Posts : " . $user_array['num_posts']; ?> </p>
					<p> <?php echo "Likes : " . $user_array['num_likes']; ?> </p>
					<p> <?php echo "Friends : " . $friend_sayisi; ?> </p>
					<hr>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<form action="<?php echo $username; ?>" method="POST">
						<?php 

						$profile_user_obj = new User($con, $username);
						if ($profile_user_obj->isClosed()) {
							header("Location:user_closed.php");
						}

						$logged_in_user_obj = new User($con, $userLoggedIn);

						if ($userLoggedIn != $username) {
							
							if ($logged_in_user_obj->isFriend($username)) {
								echo '<input type="submit" name="remove_friend" class="btn btn-outline-danger" value="Arkadaşı Sil"><br>';
							}elseif ($logged_in_user_obj->didReceiveRequest($username)) {
								echo '<input type="submit" name="respond_request" class="btn btn-outline-warning" value="Respond to Request"><br>';
							}elseif ($logged_in_user_obj->didSendRequest($username)) {
								echo '<input type="submit" name="" class="btn btn-outline-default" value="istek Gonderildi"><br>';
							}else{
								echo '<input type="submit" name="add_friend" class="btn btn-outline-success" value="Add Friend"><br>';
							}

						}

						?>
					</form>
					<hr>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<input type="submit" class="btn btn-info" data-toggle="modal" data-target="#myModal" value="POST">
				</div>
			</div>

			<!-- The Modal -->
			<div class="modal" id="myModal">
				<div class="modal-dialog">
					<div class="modal-content">

						<!-- Modal Header -->
						<div class="modal-header">
							<h4 class="modal-title">POST</h4>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>

						<!-- Modal body -->
						<div class="modal-body">

							<p>...</p>

							<form class="profile_post" action="" method="POST">
								<div class="form-group">
									<textarea class="form-control" name="post_body"></textarea>
									<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
									<input type="hidden" name="user_to" value="<?php echo $username; ?>">
								</div>
							</form>

						</div>

						<!-- Modal footer -->
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-info" name="post_button" id="submit_profile_post">Post</button>
						</div>

					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<p>

						<?php 

						if ($userLoggedIn != $username) {
							echo '<div class="">';
							echo $logged_in_user_obj->getOrtakFriends($username). " ortak arkadas";
							echo '</div>';
						}

						?>

					</p>
					<hr>
				</div>
			</div>

		</div>

		<!--/// SAG TARAF NAV TABS \\\-->
		<div class="col-sm-7">
				<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist" id='profileTabs'>
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" href="#home">POSTS</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#about">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#message_div">Message</a>
				</li>
			</ul>

			<!-- Tab panel -->
			<div class="tab-content">
				<div id="home" class="container tab-pane active"><br>
					<h3>Posts</h3>
					<p>
						<div class="posts_area"></div>
						<i id="loading" class="fa fa-spinner fa-spin" style="font-size:24px"></i>
					</p>
				</div>
				<div id="about" class="container tab-pane fade"><br>
					<h3>About</h3>
					<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
				</div>
				<div id="message_div" class="container tab-pane fade"><br>
					
					<div class="row">
						<div class="col-sm-12" id=''>
							<?php
							
								echo "<h4> <span class='text-success'>You</span> and <a href='". $username ."'>" . $profile_user_obj->getFirstAndLastName() ."</a></h4>";
								echo "<div class='loaded_messages' id='scroll_messages'>";
								echo $message_obj->getMessages($username);
								echo "</div>";

							?>
								<script>

								var div = document.getElementById("scroll_messages");
								div.scrollTop = div.scrollHeight;

								</script>
						</div>
					</div>

					<div class="row">
						<div class="col-sm-12">
							<form action="" method="POST">
								<textarea name='message_body' class='form-control' rows='3' id='message_textarea' placeholder='mesajini buraya yaz ...'></textarea><br>
								<input type='submit' name='post_message' class='btn btn-info' id='message_submit' value='Gonder'>
							</form>
						</div>
					</div>	

				</div>
			</div>
		</div>

	</div>

</div>

		<!-- SCROLL -->
		<script>
			var userLoggedIn = '<?php echo $userLoggedIn; ?>';
			var profileUsername = '<?php echo $username; ?>';

			$(document).ready(function(){

				$('#loading').show();

				$.ajax({
					url:"includes/handlers/ajax_load_profile_posts.php",
					type:"POST",
					data:"page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
					cache:false,
					success:function(data){
						$('#loading').hide();
						$('.posts_area').html(data);
					}
				});

				$(window).scroll(function(){
					var height = $('.posts_area').height();
					var scroll_top = $(this).scrollTop();
					var page = $('.posts_area').find('.nextPage').val();
					var noMorePosts = $('.posts_area').find('.noMorePosts').val();

					// alert(document.body.scrollHeight+"__"+document.body.scrollTop+"__"+window.innerHeight+" Toplami:"+(document.body.scrollTop+window.innerHeight));

					if ( ((document.body.scrollHeight-1) <= (document.body.scrollTop + window.innerHeight)) && (noMorePosts == 'false') ) {
						$('#loading').show();
						// alert("Hello!!!");
						var ajaxReq = $.ajax({
											url:"includes/handlers/ajax_load_profile_posts.php",
											type:"POST",
											data:"page="+page+"&userLoggedIn="+userLoggedIn + "&profileUsername=" + profileUsername,
											cache:false,

											success:function(response){
												$('.posts_area').find('.nextPage').remove();
												$('.posts_area').find('.noMorePosts').remove();

												$('#loading').hide();
												$('.posts_area').append(response);
											}
										});
					} // END IF

					return false;

				}); // (window).scroll(function()

			});
		</script>

