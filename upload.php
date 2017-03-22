<!DOCTYPE HTML>
<?php session_start();  include 'helpers.php';?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>
			DataBrew - Upload
		</title>
	</head> 
<body>
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
<div id="html" class="container">
<div id="body" class="regCenter">
<?php
	if(isset($_POST['upload']) && isset($_SESSION['sessionID'])){
		unset($_POST['upload']);
		$ok = 1;
		if(strlen($_POST['title'])<1){
			$ok = 0;
			echo "Title cannot be empty! ";
		}
		if($ok == 1){
			$uploadOk = 1;
			$target_dir = "uploads/";
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$type = $_FILES["fileToUpload"]["type"];
			$filename = $_FILES["fileToUpload"]["name"];
			$thumbNail = "uploadsThumbs/binary.jpg";
			
			if($type == "video/mp4"){
				$thumbNail = "uploadsThumbs/mp4.jpg";
			}else if($type == "image/jpeg"){
				$thumbNail = "uploadsThumbs/jpeg.jpg";
			}else if($type == "video/webm"){
				$thumbNail = "uploadsThumbs/webm.jpg";
			}else if($type == "audio/mpeg"){
				$thumbNail = "uploadsThumbs/mp3.jpg";
			}else if($type == "image/png"){
				$thumbNail = "uploadsThumbs/png.jpg";
			}else if($type == "text/plain"){
				$thumbNail = "uploadsThumbs/text.jpg";
			}else if($type == "application/pdf"){
				$thumbNail = "uploadsThumbs/pdf.jpg";
				
			}else{
				echo $type." not currently supported by this service.<br><br>";
				$uploadOk = 0;
			}
			$ext = pathinfo($target_file, PATHINFO_EXTENSION);
			$size = formatFileSize($_FILES["fileToUpload"]["size"]);
			$target_file = $target_dir . $filename . generateRandomString() .".".$ext;
			while(file_exists($target_file)) {
				$target_file = $target_dir . $filename . generateRandomString() .".".$ext;
			} 
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
			/*if(isset($_POST["submit"])) {
				$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
				if($check !== false) {
					echo "File is an image - " . $check["mime"] . ".";
					$uploadOk = 1;
				} else {
					echo "File is not an image.";
					$uploadOk = 0;
				}
			}*/
			if ($uploadOk == 0) {
				echo "Sorry, your file was not uploaded.";
			} else {
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded. Click <a href=".'index.php'.">here</a> to return to main page.";
					header("Location:index.php");
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
			}
			$server_address = $_SERVER['SERVER_ADDR'];
			if($server_address == "::1"){
				if(!isset($_SERVER['LOCAL_ADDR'])){
					$server_address = gethostbyname(gethostname());
				}else{
					$server_address = $_SERVER['LOCAL_ADDR'];
				}
			}
			$conn = new mysqli($server_address, "databrewuser", "", "databrew");
			$sql = "INSERT INTO media (userID, Title, Type, Description, Views, Upvotes, Downvotes, filepath, thumb) 
			VALUES ('".
			$_SESSION['id']."', "."'".
			$_POST['title']."', "."'".
			$type."', "."'".
			$_POST['description']."', "."'".
			"0"."', "."'".
			"0"."', "."'".
			"0"."', "."'".
			$target_file."', "."'".
			$thumbNail
			."')";
			$result = $conn->query($sql);
			unset($_POST);
		}
	}else if(isset($_SESSION['sessionID'])){
		echo '<form action="upload.php" method="post" enctype="multipart/form-data">
		<input type="file" class="registrationForm" name="fileToUpload" id="fileToUpload"><br>
		<input type="text" class="registrationForm" placeholder="Title" name="title" maxlength="128"><br>
		<textarea  class="registrationForm" placeholder="Description" name="description" rows="5" cols="200" maxlength="1024"> </textarea><br>
		<input class="registrationForm" type="submit" value="Upload File" name="upload"><br>';
	}else{
		echo "You must be logged in to upload a file.";
	}
	
?>
</div>
</div>

</form>
</body>
</html>