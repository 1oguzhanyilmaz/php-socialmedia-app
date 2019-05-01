<?php

include 'includes/header.php';


if (isset($_POST['post_button'])) {

	$uploadOk = 1;
	$imageName = $_FILES['fileUpload']['name'];
	$errorMessage = "";

	if ($imageName != "") {
		$hedef = "assets/images/posts/";
		$imageName = $hedef . uniqid(). basename($imageName); // assets/images/posts/12753399resim.png
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if ($_FILES['fileUpload']['size'] > 10000000) {
			$errorMessage = "Dosya boyutu buyuk";
			$uploadOk = 0;
		}

		if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Dosya boyutu buyuk";
			$uploadOk = 0;
		}

		if ($uploadOk) {
			if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $imageName)) {
				// image uploaded
			}else{
				// image did no upload
				$uploadOk = 0;
			}
		}

	}

	if ($uploadOk) {
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], 'none', $imageName);
	}else{
		echo "<div style='text-align:center;color:red;' class=''>
			.". $errorMessage ."
			</div>";
	}
	
}

?>
		
<div class="container" style="margin-top:30px">

	<div class="row">

		<!-- SOL -->
		<div class="col-sm-3 mr-4">

			<!-- =========================== SOL USER DETAILS =========================== -->
			<div class="row">
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

			<!-- =========================== SOL POPULER KELimeler =========================== -->
			<div class="row mt-4">
				<div class="col-sm-12 bg-white">
					
					<h3>Populer</h3>

					<?php 

					$query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

					echo '<ul class="nav nav-pills flex-column">';

						foreach ($query as $row) {
							$word = $row['title'];
							$word_dot = strlen($word) >= 14 ? "..." : "";

							$trimmed_word = str_split($word, 14);
							$trimmed_word = $trimmed_word[0];

							echo '<li class="nav-item"> '. $trimmed_word . $word_dot .' </li>';
						}

					echo '</ul>';

					 ?>

				</div>
			</div>

		</div>


		<!-- =========================== SAG POST =========================== -->
		<div class="col-sm-7 ml-4 bg-white p-0" style="">
			<form action="index.php" method="POST" enctype="multipart/form-data">
				<input type="file" name="fileUpload" id="fileUpload_id"> <!-- Resim Upload -->
				<div class="input-group mb-3 ml-0">
				    <textarea class="form-control" name="post_text" id="exampleFormControlTextarea1" rows="3" placeholder="Birşeyler paylaş"></textarea>
				    <div class="input-group-append">
				      	<button class="btn btn-outline-primary border-0 anasayfa-buton" name="post_button">Post</button>
				      	<button class="btn btn-outline-danger border-0 anasayfa-buton" type="button" name="sil">Sil</button> 
				    </div>
				</div>
			</form>
			
			<hr> 

			<!-- =========================== SAG SCROLL =========================== -->
			<div class="posts_area"></div>
			<i id="loading" class="fa fa-spinner fa-spin" style="font-size:24px"></i>

		</div>

		<!-- =========================== SAG SCROLL SCRIPT =========================== -->
		<script>
			var userLoggedIn = '<?php echo $userLoggedIn; ?>';
			$(document).ready(function(){

				$('#loading').show();

				$.ajax({
					url:"includes/handlers/ajax_load_posts.php",
					type:"POST",
					data:"page=1&userLoggedIn="+userLoggedIn,
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
											url:"includes/handlers/ajax_load_posts.php",
											type:"POST",
											data:"page="+page+"&userLoggedIn="+userLoggedIn,
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

	</div>

</div>	

