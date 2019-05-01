<?php

include 'includes/header.php';

if (isset($_GET['id'])) {
	$id = $_GET['id'];
}else{
	$id = 0;
}

?>

<div class="container">

	<div class="row">

		<div class="col-sm-3 mr-4">

			<a href="<?php echo $userLoggedIn; ?>">
			<img src="<?php echo $user['profile_pic']; ?>" style="width:50%">
			</a>
			<p> <?php echo $userLoggedIn; ?> </p>
			<p> <?php echo $user['first_name'] . " " . $user['last_name']; ?> </p>

		</div>


		<div class="col-sm-7">

			<?php 

			$post = new Post($con, $userLoggedIn);
			$post->getSinglePost($id);

			 ?>

		</div>

	</div>

	
</div>