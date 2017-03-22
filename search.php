<!DOCTYPE HTML>
<?php session_start();  include 'helpers.php';?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>
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

<?php
	if(isset($_POST['query'])&& strlen($_POST['query'])>0){
		echo "Searching for '".$_POST['query']."'...<br>";
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
		$sql = "SELECT media.id, media.id as mid, LCASE(media.title), media.title, media.views, media.thumb, media.description, users.username, media.userID, users.id FROM media, users WHERE media.userID = users.id AND ( media.title LIKE '%".strtolower($_POST['query'])."' OR media.title LIKE '".strtolower($_POST['query'])."%' OR media.title LIKE '%".strtolower($_POST['query'])."%' ) ORDER BY views ";
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
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
			echo "0 results";
		}
		$conn->close();
	}else{
		echo "Searching for empty string returned no results.";
	}
?>

</body>
</html>