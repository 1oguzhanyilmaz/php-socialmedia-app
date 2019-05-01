<?php

class Post{
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to, $imageName){
		$body = strip_tags($body); // html taglari kaldir.
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); // pattern,replacement,subject => bosluklari sil

		if ($check_empty != "") {

			// Youtube post
			$body_array = preg_split("/\s+/", $body);
			foreach ($body_array as $key => $value) {
				if (strpos($value, "www.youtube.com/watch?v=") !== false) {

					// videolisti onlemek icin ambersende gore ayir
					$link = preg_split("!&!", $value);

					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'". $value ."\'></iframe><br>";
					$body_array[$key] = $value;
				}
			}
			$body = implode(" ", $body_array);
			
			$date_added = date("Y-m-d H:i:s");

			$added_by = $this->user_obj->getUsername();

			// kendine post atarsa
			if ($user_to == $added_by) {
				$user_to = "none";
			}

			// INSERT post
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
			$returned_id = mysqli_insert_id($this->con);

			// insert notification
			if ($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

			// kullanici icin post sayisini guncelle
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'"); 



			// **************** populer kelimeler ****************
			$kelimeler = "bir bu ne ve mi icin cok ben o sen evet hayir var ama tamam bitti burada neden sadece sana ona her 
			sonra daha hadi guzel oyle istiyorum selam merhaba dogru olarak tek efendim biri 
			haydi peki bilmiyorum biliyorum fazla az cok yeni gel git yap et ile oh tesekkürler tesekkür ederim 
			iki dostum al tabii hala asla izin kimse baba anne dayi teyze asla senin benim ad soyad numara no 
			para ask is oluyor ayni bakalim dakika saniye saat onlar orada oradan hep her zaman bir iki yedi telefon bilgisayar 
			masa sandalye kapi dolap fare pil gozluk tel beyaz sari kirmizi adam adamim de da iyi bekliyorum geliyorum gidecegim 
			oguz ahmet ronaldo olivia barth simpson naber nasilsin iyimisin trend";

			$kelimeler = preg_split("/[\s,]+/", $kelimeler); // bosluklar icin
			$no_noktalama = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);

			if (strpos($no_noktalama, "height") === false && strpos($no_noktalama, "width") === false && strpos($no_noktalama, "http") === false) {
				
				$no_noktalama = preg_split("/[\s,]+/", $no_noktalama);

				foreach ($kelimeler as $value1) {
					foreach ($no_noktalama as $key => $value2) {
						
						if ( strtolower($value1) == strtolower($value2) ) {
							$no_noktalama[$key] = "";
						}

					}
				}

				foreach ($no_noktalama as $value) {
					$this->calculateTrend(ucfirst($value));
				}

			}

		}
	}

	public function calculateTrend($term){

		if ($term != "") {
			$query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

			if (mysqli_num_rows($query) == 0) {
				$insert_query = mysqli_query($this->con, "INSERT INTO trends(title, hits) VALUES('$term', '1')");
			}else{
				$insert_query = mysqli_query($this->con, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
			}
		}

	}

	public function loadPostsFriends($data, $limit){

		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1) {
			$start = 0;
		}else{
			$start = ($page - 1) * $limit;
		}

		$str = "";
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY post_id DESC");

		if (mysqli_num_rows($data_query) > 0) {

			$num_iterations = 0;
			$count = 1;

			while ($row = mysqli_fetch_array($data_query)) {
				$post_id 	= $row['post_id'];
				$body 		= $row['body'];
				$added_by 	= $row['added_by'];
				$date_time 	= $row['date_added'];
				$imagePath 	= $row['image'];

				// user_to hazirla 
				if ($row['user_to'] == "none") {
					$user_to = "";
				}else{
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
				}

				// postu gonderenin closed olma durumunu kontrol et 
				$added_by_obj = new User($this->con, $added_by);
				if ($added_by_obj->isClosed()) {
					continue;
				}

				// Arkadas postlarini listele
				$user_logged_obj = new User($this->con, $userLoggedIn);
				if ($user_logged_obj->isFriend($added_by)) {
					
					if($num_iterations++ < $start)
						continue;

					// 10 tane post yuklendikten sonra break
					if($count > $limit){
						break;
					}else{
						$count++;
					}

					// postu Sil butonu
					if ($userLoggedIn == $added_by) {
						$delete_button = "<button style='float:right;' class='rounded-circle btn-danger delete_button' id='post$post_id'>X</button>";
					}else{
						$delete_button="";
					}

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

		?>

				<!-- Yorumlar Toggle -->
				<script>
				function toggle<?php echo $post_id; ?>(){

					var target = $(event.target); // isime tiklandiginda sadece profil sayfasina git -> yorumlar acilmasin
					if (!target.is("a")) {
						var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
						if (element.style.display == "block") {
							element.style.display = "none";
						}else{
							element.style.display = "block";
						}
					}
				}
				</script>


		<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$post_id'");
					$comments_check_num = mysqli_num_rows($comments_check);

					// Time of posts
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); // Time of post
					$end_date = new DateTime($date_time_now); // Current Time
					$interval = $start_date->diff($end_date); // zaman farki
					if ($interval->y >= 1) {
						if ($interval == 1) {
							$time_message = $interval->y . " year ago."; // 1 yil once
						}else{
							$time_message = $interval->y . " years ago."; // 1+ yil once
						}
					}else if($interval->m >= 1){
						if ($interval->d == 0) {
							$days = " ago";
						}else if($interval->d == 1){
							$days = $interval->d . " day ago";
						}else{
							$days = $interval->d . " days ago";
						}

						if ($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}else{
							$time_message = $interval->m . " months". $days;
						}
					}else if($interval->d >= 1){
						if($interval->d == 1){
							$time_message = "Yesterday";
						}else{
							$time_message = $interval->d . " days ago";
						}
					}else if($interval->h >= 1){
						if($interval->h == 1){
							$time_message = $interval->h . " hour ago";
						}else{
							$time_message = $interval->h . " hours ago";
						}
					}else if($interval->i >= 1){
						if($interval->i == 1){
							$time_message = $interval->i . " minute ago";
						}else{
							$time_message = $interval->i . " minutes ago";
						}
					}else{
						if($interval->s < 30){
							$time_message = "Just now";
						}else{
							$time_message = $interval->s . " seconds ago";
						}
					}

					if ($imagePath != "") {
						$imageDiv = "<div class='postedImage'>
										<img src='". $imagePath ."' class='img-fluid rounded mx-auto d-block' alt='...''>
									</div>";
					}else{
						$imageDiv = "";
					}

					$str .= "
					<!-- POSTLAR -->

					<div class='row'>
						<div class='col-sm-12'>
							<div class='media border p-4' onClick='javascript:toggle$post_id()' style='cursor:pointer;'>
								<img src='$profile_pic' alt='$added_by' class='mr-3 mt-3 rounded-circle' style='width:60px;'>
								<div class='media-body border-left pl-3'>
									<p> <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp; <small><i>Posted on $time_message </i></small> $delete_button</p>
									<p> $body </p> 
									<p> $imageDiv </p>     
								</div>
							</div>
						</div>
					</div>
							
							
					<!-- YORUMLAR, Like -->
					
					<div class='row text-info'>
						<div class='col-sm-2'>
							<span class='mt-0'>Yorumlar($comments_check_num) </span>	
						</div>
						<div class='col-sm-2'>
							<iframe class='iframe-like' src='like.php?post_id=$post_id' scrolling='no' style='height:35px;width:150px;margin:px;' frameborder='0'></iframe>
						</div>
					</div>
							

					<!-- YORUM EKLEME BOLUMU -->
					
					<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
						<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe' style='width:100%;max-height:350px;height:200px;' frameborder='0'></iframe>
					</div>

					<hr>

					";
				}

?>

				<script>

					$(document).ready(function(){

						$('#post<?php echo $post_id; ?>').on('click', function(){

							bootbox.confirm("postu silmek istedigine emin misin ?", function(result){

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $post_id; ?>", {result:result});

								if (result) {
									location.reload();
								}

							});
						});

					});

				</script>

<?php

			} // END WHILE

			if($count > $limit){
				$str .= "<input type='hidden' class='nextPage' value='" . ($page+1) . "'>
						<input type='hidden' class='noMorePosts' value='false'>";
			}else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'>
						<p class='text-muted text-center'>Gosterilecek Post yok.</p>";
			}

		}

		echo $str;

	} // END loadPostsFriends

	public function loadProfilePosts($data, $limit){

		$page = $data['page'];
		$profileUser = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if ($page == 1) {
			$start = 0;
		}else{
			$start = ($page - 1) * $limit;
		}

		$str = "";
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY post_id DESC");

		if (mysqli_num_rows($data_query) > 0) {

			$num_iterations = 0;
			$count = 1;

			while ($row = mysqli_fetch_array($data_query)) {
				$post_id 	= $row['post_id'];
				$body 		= $row['body'];
				$added_by 	= $row['added_by'];
				$date_time 	= $row['date_added'];
					
					if($num_iterations++ < $start)
						continue;

					// 10 tane post yuklendikten sonra break
					if($count > $limit){
						break;
					}else{
						$count++;
					}

					// postu Sil butonu
					if ($userLoggedIn == $added_by) {
						$delete_button = "<button style='float:right;' class='rounded-circle btn-danger delete_button' id='post$post_id'>X</button>";
					}else{
						$delete_button="";
					}

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

		?>

				<!-- Yorumlar Toggle -->
				<script>
				function toggle<?php echo $post_id; ?>(){

					var target = $(event.target); // isime tiklandiginda sadece profil sayfasina git -> yorumlar acilmasin
					if (!target.is("a")) {
						var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
						if (element.style.display == "block") {
							element.style.display = "none";
						}else{
							element.style.display = "block";
						}
					}
				}
				</script>


		<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$post_id'");
					$comments_check_num = mysqli_num_rows($comments_check);

					// Time of posts
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); // Time of post
					$end_date = new DateTime($date_time_now); // Current Time
					$interval = $start_date->diff($end_date); // zaman farki
					if ($interval->y >= 1) {
						if ($interval == 1) {
							$time_message = $interval->y . " year ago."; // 1 yil once
						}else{
							$time_message = $interval->y . " years ago."; // 1+ yil once
						}
					}else if($interval->m >= 1){
						if ($interval->d == 0) {
							$days = " ago";
						}else if($interval->d == 1){
							$days = $interval->d . " day ago";
						}else{
							$days = $interval->d . " days ago";
						}

						if ($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}else{
							$time_message = $interval->m . " months". $days;
						}
					}else if($interval->d >= 1){
						if($interval->d == 1){
							$time_message = "Yesterday";
						}else{
							$time_message = $interval->d . " days ago";
						}
					}else if($interval->h >= 1){
						if($interval->h == 1){
							$time_message = $interval->h . " hour ago";
						}else{
							$time_message = $interval->h . " hours ago";
						}
					}else if($interval->i >= 1){
						if($interval->i == 1){
							$time_message = $interval->i . " minute ago";
						}else{
							$time_message = $interval->i . " minutes ago";
						}
					}else{
						if($interval->s < 30){
							$time_message = "Just now";
						}else{
							$time_message = $interval->s . " seconds ago";
						}
					}

					$str .= "
					<!-- POSTLAR -->

					<div class='row'>
						<div class='col-sm-12'>
							<div class='media border p-4' onClick='javascript:toggle$post_id()' style='cursor:pointer;'>
								<img src='$profile_pic' alt='$added_by' class='mr-3 mt-3 rounded-circle' style='width:60px;'>
								<div class='media-body border-left pl-3'>
									<p> <a href='$added_by'>$first_name $last_name</a> &nbsp;&nbsp;&nbsp; <small><i>Posted on $time_message </i></small> $delete_button</p>
									<p> $body </p>      
								</div>
							</div>
						</div>
					</div>
							
							
					<!-- YORUMLAR, Like -->
					
					<div class='row text-info'>
						<div class='col-sm-2'>
							<span class='mt-0'>Yorumlar($comments_check_num) </span>	
						</div>
						<div class='col-sm-2'>
							<iframe class='iframe-like' src='like.php?post_id=$post_id' scrolling='no' style='height:35px;width:150px;margin:px;' frameborder='0'></iframe>
						</div>
					</div>
							

					<!-- YORUM EKLEME BOLUMU -->
					
					<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
						<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe' style='width:100%;max-height:350px;height:200px;' frameborder='0'></iframe>
					</div>

					<hr>

					";

?>

				<script>

					$(document).ready(function(){

						$('#post<?php echo $post_id; ?>').on('click', function(){

							bootbox.confirm("postu silmek istedigine emin misin ?", function(result){

								$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $post_id; ?>", {result:result});

								if (result) {
									location.reload();
								}

							});
						});

					});

				</script>

<?php

			} // END WHILE

			if($count > $limit){
				$str .= "<input type='hidden' class='nextPage' value='" . ($page+1) . "'>
						<input type='hidden' class='noMorePosts' value='false'>";
			}else{
				$str .= "<input type='hidden' class='noMorePosts' value='true'>
						<p class='text-muted text-center'>Gosterilecek Post yok.</p>";
			}

		}

		echo $str;

	}

	public function getSinglePost($post_id){

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = "";
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND post_id='$post_id'");

		if (mysqli_num_rows($data_query) > 0) {

			$row = mysqli_fetch_array($data_query);
				$post_id 	= $row['post_id'];
				$body 		= $row['body'];
				$added_by 	= $row['added_by'];
				$date_time 	= $row['date_added'];

				// user_to hazirla 
				if ($row['user_to'] == "none") {
					$user_to = "";
				}else{
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
				}

				// postu gonderenin closed olma durumunu kontrol et 
				$added_by_obj = new User($this->con, $added_by);
				if ($added_by_obj->isClosed()) {
					return;
				}

				// Arkadas postlarini listele
				$user_logged_obj = new User($this->con, $userLoggedIn);
				if ($user_logged_obj->isFriend($added_by)) {

					// postu Sil butonu
					if ($userLoggedIn == $added_by) {
						$delete_button = "<button style='float:right;' class='rounded-circle btn-danger delete_button' id='post$post_id'>X</button>";
					}else{
						$delete_button="";
					}

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];

		?>

				<!-- Yorumlar Toggle -->
				<script>
				function toggle<?php echo $post_id; ?>(){

					var target = $(event.target); // isime tiklandiginda sadece profil sayfasina git -> yorumlar acilmasin
					if (!target.is("a")) {
						var element = document.getElementById("toggleComment<?php echo $post_id; ?>");
						if (element.style.display == "block") {
							element.style.display = "none";
						}else{
							element.style.display = "block";
						}
					}
				}
				</script>


		<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$post_id'");
					$comments_check_num = mysqli_num_rows($comments_check);

					// Time of posts
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); // Time of post
					$end_date = new DateTime($date_time_now); // Current Time
					$interval = $start_date->diff($end_date); // zaman farki
					if ($interval->y >= 1) {
						if ($interval == 1) {
							$time_message = $interval->y . " year ago."; // 1 yil once
						}else{
							$time_message = $interval->y . " years ago."; // 1+ yil once
						}
					}else if($interval->m >= 1){
						if ($interval->d == 0) {
							$days = " ago";
						}else if($interval->d == 1){
							$days = $interval->d . " day ago";
						}else{
							$days = $interval->d . " days ago";
						}

						if ($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}else{
							$time_message = $interval->m . " months". $days;
						}
					}else if($interval->d >= 1){
						if($interval->d == 1){
							$time_message = "Yesterday";
						}else{
							$time_message = $interval->d . " days ago";
						}
					}else if($interval->h >= 1){
						if($interval->h == 1){
							$time_message = $interval->h . " hour ago";
						}else{
							$time_message = $interval->h . " hours ago";
						}
					}else if($interval->i >= 1){
						if($interval->i == 1){
							$time_message = $interval->i . " minute ago";
						}else{
							$time_message = $interval->i . " minutes ago";
						}
					}else{
						if($interval->s < 30){
							$time_message = "Just now";
						}else{
							$time_message = $interval->s . " seconds ago";
						}
					}

					$str .= "
					<!-- POSTLAR -->

					<div class='row'>
						<div class='col-sm-12'>
							<div class='media border p-4' onClick='javascript:toggle$post_id()' style='cursor:pointer;'>
								<img src='$profile_pic' alt='$added_by' class='mr-3 mt-3 rounded-circle' style='width:60px;'>
								<div class='media-body border-left pl-3'>
									<p> <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp; <small><i>Posted on $time_message </i></small> $delete_button</p>
									<p> $body </p>      
								</div>
							</div>
						</div>
					</div>
							
							
					<!-- YORUMLAR, Like -->
					
					<div class='row text-info'>
						<div class='col-sm-2'>
							<span class='mt-0'>Yorumlar($comments_check_num) </span>	
						</div>
						<div class='col-sm-2'>
							<iframe class='iframe-like' src='like.php?post_id=$post_id' scrolling='no' style='height:35px;width:150px;margin:px;' frameborder='0'></iframe>
						</div>
					</div>
							

					<!-- YORUM EKLEME BOLUMU -->
					
					<div class='post_comment' id='toggleComment$post_id' style='display:none;'>
						<iframe src='comment_frame.php?post_id=$post_id' id='comment_iframe' style='width:100%;max-height:350px;height:200px;' frameborder='0'></iframe>
					</div>

					<hr>

					";

				?>
					<script>
						$(document).ready(function(){
							$('#post<?php echo $post_id; ?>').on('click', function(){
								bootbox.confirm("postu silmek istedigine emin misin ?", function(result){
									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $post_id; ?>", {result:result});
									if (result) {
										location.reload();
									}
								});
							});
						});
					</script>
				<?php

				}else{
					echo "<p>Arkadas olmadigin icin postlari goremezsin.</p>";
					return;
				}

		}else{
			echo "<p>Post bulunamadi...</p>";
			return;
		}

		echo $str;
	}

}

?>