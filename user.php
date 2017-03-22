<!DOCTYPE HTML>
<?php session_start();  include 'helpers.php';?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>
			<?php
				if(isset($_GET['id'])){
					$_SESSION['vuid']=$_GET['id'];
				}
			?>
			DataBrew - Search
		</title>
	</head> 
<body id="html" class="container">
	<div id="body" class="logoAndSearch">
		<a href="index.php">
			<img src="images/DataBrew.png" width="390">
		</a>
		<?php 
			renderSearchBox();
		?>
	</div>
	<div id="body" class="loginOrUser">
		<?php
			if(isset($_GET['deleteFileAt'])){
				delete($_GET['deleteFileAt']);
			}
			if(isset($_POST['login'])){
				login();
			}else if (isset($_POST['logout'])&&isset($_SESSION['sessionID'])){
				session_unset(); 
				session_destroy(); 
				session_start();
				renderLoginForm();
			}else if(isset($_SESSION['sessionID'])){
				loggedIn();
			}else{
				renderLoginForm();
			}
		?>
		<form action="register.php" method="post">
		<input type="submit" class="loginLogoutRegisterFW" name="register" value="Create new accout">
		</form>

		<?php
			if(isset($_SESSION['sessionID'])){
				echo '<form action="upload.php">
				<input class="loginLogoutRegisterFW" type="submit" name="jump" value="Upload">
				</form>';
			}
		?>
	</div>
	</div>
	<div id="body" class="center">
		User info:
	</div>
	<div id="body" class="mediaCenter">
		<?php 
			$result = getUserInfo($_SESSION['vuid']);
			$numRows = $result->num_rows;
			if ($numRows > 0) {
				while($row = $result->fetch_assoc()) {
					echo '<div id="img" class="textWrap">';
					echo '<img src="'.$row['image'].'" width="100" height="100"/>';
					echo '</div>';
					echo '<h3>'.$row['username'].'</h3>';
					echo $row['description'];
				}
			} else {
				echo "User does not exist";
			}
			
		?>
	</div>
	<div id="body" class="mediaCenter">
		<?php
			if($_SESSION['id']==$_SESSION['vuid']){
				echo '<form action="management.php">
				<input class="loginLogoutRegister" type="submit" name="jump" value="Modify profile">
				</form>';
			}
		?>
	</div>
	<div id="body" class="center">
		<?php
			$server_address = $_SERVER['SERVER_ADDR'];
			if($server_address == "::1"){
				if(!isset($_SERVER['LOCAL_ADDR'])){
					$server_address = gethostbyname(gethostname());
				}else{
					$server_address = $_SERVER['LOCAL_ADDR'];
				}
			}
			$conn = new mysqli($server_address, "databrewuser", "", "databrew");
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			$sql = "SELECT media.id, media.id as mid, LCASE(media.title), media.title, media.views, media.thumb, media.description, users.username, media.userID, users.id 
			FROM media, users 
			WHERE media.userID = users.id
			AND media.userID = ".$_SESSION['vuid']." 
			ORDER BY views ";
			$result = $conn->query($sql);
			$numRows = $result->num_rows;
			echo "User's uploaded content:<br>";
			if ($numRows > 0) {
				// output data of each row
				while($row = $result->fetch_assoc()) {
					echo '<div id="body" class="mediaItem">';
					$_SESSION['mediaId']=$row['mid'];
					echo '<a href="media.php?mid='.$row['mid'].'">';
					echo '<div id="img" class="textWrap">';
					echo '<img src="'.$row['thumb'].'">';
					echo '</div>';
					echo '<div id="body" class="mediaItemData">';
					echo $row['title'];
					echo '</a>';
					echo '<br>';
					echo "Uploaded by";
					echo '<br>';
					echo '<a href="user.php?id='.$row['userID'].'">';
					echo $row['username'];
					echo '</a>';
					if(isset($_SESSION['sessionID']) && $_SESSION['id']==$row['userID']){
						echo '<a href="index.php?deleteFileAt='.$row['mid'].'">  [DELETE] </a>';
					}
					echo '</div>';
					echo '</div>';
				}
			} else {
				echo "User didn't upload anything.";
			}
			$conn->close();
		?>
	</div>
	<div id="body" class="center">
		Profile comments:<br>
		<?php
			processProfileComment();
		?>
	</div>
	<div id="body" class="center">
		<?php
			if(isset($_SESSION['sessionID'])){
				renderProfileCommentBox();
			}else{
				echo "Only registered users may post comments.";
			}
		?>
	</div>
	
	<div id="body" class="center">
		<?php
			$result = getProfileComments($_SESSION['vuid']);
			$numRows = $result->num_rows;
			if ($numRows > 0) {
				while($row = $result->fetch_assoc()) {
					echo '<div id="body" class="comment">';
					$result2 = getUserInfo($row['uid']);
					$numRows2 = $result2->num_rows;
					if ($numRows2 > 0) {
						while($row2 = $result2->fetch_assoc()) {
							echo '<div id="img" class="textWrap">';
							echo '<img src="'.$row2['image'].'" width="100" height="100"/>';
							echo '</div>';
							echo '<h3>'.$row2['username'].'</h3>';
						}
					}
					echo $row['txt'];
					echo '</div>';
				}
			}else{
				echo "No comments have been posted yet.";
			}
		?>
	</div>
</body>
</html>














