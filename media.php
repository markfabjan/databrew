
<!DOCTYPE html>
<?php session_start(); include 'helpers.php';?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>
			<?php
				if(isset($_GET['mid'])){
					echo getContentTitleAtId($_GET['mid'])." - DataBrew";
					$_SESSION['vmid']=$_GET['mid'];
				}else{
					echo getContentTitleAtId($_SESSION['vmid'])." - DataBrew";
				}
				increaseViewCount($_SESSION['vmid']);
			?>
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
	<div id="body" class="mediaCenter">

<?php
	echo '<h3 class="videoTitle">'.getContentTitleAtId($_SESSION['vmid'])."</h3>";
	$result = getContentAtId($_SESSION['vmid']);
	$numRows = $result->num_rows;
	if ($numRows > 0) {
		while($row = $result->fetch_assoc()) {
			if($row['Type']=="video/webm"){
				echo '<video id="player" width="798" controls>';
				echo '<source src="'.$row['filepath'].'" type="video/webm">';
				echo '</video>';
			}else if($row['Type']=="video/mp4"){
				echo '<video id="player" width="798" controls>';
				echo '<source src="'.$row['filepath'].'" type="video/mp4">';
				echo '</video>';
			}else if($row['Type']=="image/jpeg" || $row['Type']=="image/png"){
				echo '<a href="'.$row['filepath'].'">
				<img width="798" src="'.$row['filepath'].'">
				</a>';
			}else if($row['Type']=="audio/mpeg"){
				echo '<audio id="player" width="798" height="20" controls>';
				echo '<source src="'.$row['filepath'].'" type="audio/mpeg">';
				echo '</audio>';
			}else if($row['Type']=="text/plain"){
				echo '<div id="body" class="textContent">';
				$myfile = fopen($row['filepath'], "r") or die("Unable to open file!");
				while(!feof($myfile)) {
					echo fgets($myfile) . "<br>";
				}
				fclose($myfile);
				echo '</div>';
			}else if($row['Type']=="application/pdf"){
				echo '<div id="body" class="textContent">';
				echo '<object data="pdfFiles/interfaces.pdf" type="application/pdf">
                <embed width="100%" height="800" src="'.$row['filepath'].'" type="application/pdf"></embed>
                </object>';
				echo '</div>';
			}else{
				echo $row['Type'];
			}
			echo "<br><br>".$row['Description']."<br>";
		}
	} else {
		echo "This file no longer exists.";
	}
?>
</div>
	<?php
		/*if(isset($_POST['like'])){
			addOneLike($_SESSION['vmid']);
			unset($_POST);
			header("location:media.php");
		}else if(isset($_POST['dislike'])){
			addOneDisike($_SESSION['vmid']);
			unset($_POST);
			header("location:media.php");
		}*/
	?>

<!--<div id="body" class="mediaCenter">
	
	<form action="media.php" method="post">
		<input class="loginLogoutRegisterFW" type="submit" name="like" value="I liked this">
	</form>
	<form action="media.php" method="post">
		<input class="loginLogoutRegisterFW" type="submit" name="dislike" value="I didn't like this">
	</form>
	
</div>-->
<div id="body" class="mediaCenter">
	Views: <?php getViews($_SESSION['vmid']); ?><br>
	<!--Votes: </*?php getVotes($_SESSION['vmid']); */?>-->
</div>
<div id="body" class="center">
	Media comments:<br>
	<?php
		processMediaComment();
	?>
</div>
<div id="body" class="center">
	<?php
		if(isset($_SESSION['sessionID'])){
			renderMediaCommentBox();
		}else{
			echo "Only registered users may post comments.";
		}
	?>
</div>

<div id="body" class="center">
	<?php
		$result = getMediaComments($_SESSION['vmid']);
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