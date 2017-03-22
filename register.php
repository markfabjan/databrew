<!DOCTYPE HTML>
<?php session_start(); include 'helpers.php';?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>DataBrew - User registration</title>
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
	<div id="body" class="regCenter">
	<?php
	if(isset($_POST['register'])){
		session_unset(); 
		session_destroy(); 
		session_start();
		echo "Please fill out the registration form below:<br>";
		echo '<form action="register.php" method="post">
			<input class="registrationForm" placeholder="Username"  type="text" name="username"> REQUIRED <br>
			<input  class="registrationForm" placeholder="Password"  type="password" name="password"> REQUIRED <br>
			<input class="registrationForm" placeholder="Confirm password"  type="password" name="password2"> REQUIRED <br>
			<textarea  class="registrationForm" placeholder="Description" name="description" rows="5" cols="50" maxlength="1024" ></textarea><br>
			<input  class="registrationForm" placeholder="AvatarURL" type="text" name="image"><br>
			<input type="submit"  class="registrationForm"  name="checkRegistration" value="Check validity">
			</form>';		
	}else if(isset($_POST['checkRegistration']) || (isset($_SESSION['ok']) && $_SESSION['ok'] == 0)){
		session_unset(); 
		session_destroy(); 
		session_start();
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
		echo '<form action="register.php" method="post">';
		$_SESSION['ok']=1;
		//USERNAME CHECK
		if(isset($_POST['username'])){
			echo '<input placeholder="Username" class="registrationForm"  type="text" name="username" value="'.$_POST['username'].'" ';
			if(strlen($_POST['username'])>0 && strlen($_POST['username'])<51){
				//echo $sql;
				$sql = 'SELECT users.username FROM users WHERE users.username = '."'".$_POST['username']."'";
				//echo $sql;
				$result = $conn->query($sql);
				$numRows = $result->num_rows;
				if ($numRows > 0) {
					echo ">User with this name already exists.<br>";
					$_SESSION['ok']=0;
				}else{
					echo "disabled>OK.<br>";
				}
			}else{
				echo ">Username should be between 1 and 50 characters long.<br>";
				$_SESSION['ok']=0;
			}
		}else{
			echo 'Username: <input  class="registrationForm"  type="text" name="username">';
		}
		//PASSWORD CHECK
		if(isset($_POST['password']) && isset($_POST['password2'])){
			echo '<input  class="registrationForm" placeholder="Password"  type="password" name="password" value="'.$_POST['password'].'"';
			if(($_POST['password'] != $_POST['password2']) && strlen($_POST['password'])>5){
				echo "><br>";
				$_SESSION['ok']=0;
			}else{
				echo "disabled>OK.<br>";
			}
			echo '<input  class="registrationForm" placeholder="Confirm password"  type="password" name="password2" value="'.$_POST['password2'].'"';
			if(($_POST['password'] != $_POST['password2']) && strlen($_POST['password'])>5){
				echo "Passwords must be the same and at least 5 characters long<br>";
				$_SESSION['ok']=0;
			}else{
				echo "disabled>OK.<br>";
			}
		}else{
			echo '<input  class="registrationForm" placeholder="Password" type="password" name="password"><br>
			<input  class="registrationForm" placeholder="Confirm password" type="password" name="password2"><br>';
		}
		if(isset($_POST['description'])){
			echo '<textarea name="description" placeholder="Description" rows="5" cols="50" maxlength="1024" value="'.$_POST['description'].'" disabled></textarea> OK.<br>';
		}else{
			echo '<textarea  class="registrationForm" placeholder="Description" name="description" rows="5" cols="50" maxlength="1024"> </textarea> OK.<br>';
		}
		if(isset($_POST['image'])){
			echo '<input  class="registrationForm" placeholder="AvatarURL" type="text" name="image" value="'.$_POST['image'].'" disabled>OK.<br>';
		}else{
			echo '<input  class="registrationForm" placeholder="AvatarURL" type="text" name="image">OK.<br>';
		}
		if($_SESSION['ok'] == 0){
			echo '<input  class="registrationForm"  type="submit"  name="checkRegistration" value="Check validity">
			</form>';
		}else{
			echo '<input  class="registrationForm"  type="submit" name="finalizeRegistration" value="Done">
			</form>';
		}
		if(!isset($_SESSION['username']) && isset($_POST['username'])){
			$_SESSION['username'] = $_POST['username'];
		}
		if(!isset($_SESSION['password']) && isset($_POST['password'])){
			$_SESSION['password'] = $_POST['password'];
		}
		if(!isset($_SESSION['description']) && isset($_POST['description'])){
			$_SESSION['description'] = $_POST['description'];
		}
		if(!isset($_SESSION['image']) && isset($_POST['image'])){
			$_SESSION['image'] = $_POST['image'];
		}
	}else if(isset($_POST['finalizeRegistration'])){
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
		$sql = "INSERT INTO users (username, password, description, image) VALUES ('".$_SESSION['username']."', "."'".md5($_SESSION['password'])."', "."'".$_SESSION['description']."', "."'".$_SESSION['image']."')";
		$result = $conn->query($sql);
		echo "Registration successful! Click <a href=".'index.php'.">here</a> to return to main page.";
		header("Location:index.php");
	}

?>
</div>
</body>
</html>