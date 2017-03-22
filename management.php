<!DOCTYPE HTML>
<?php session_start(); include 'helpers.php';?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>DataBrew</title>
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
<div id="body" class="mediaCenter">
	<?php
		if(!isset($_POST['checkChanges']) && !isset($_POST['finalizeChanges'])){ 
			echo "New user information (retype the password if you don't want to change it):<br>";
			echo '<form action="management.php" method="post">
				<input class="registrationForm" value="'.$_SESSION['username'].'"  type="text" name="username"><br>
				
				<input  class="registrationForm" placeholder="Password"  type="password" name="password"> REQUIRED <br>
				
				<textarea  class="registrationForm" value="'.$_SESSION['description'].'" name="description" rows="5" cols="50" maxlength="1024" >'.$_SESSION['description'].'</textarea><br>
				
				<input  class="registrationForm" value="'.$_SESSION["image"].'" type="text" name="image"><br>
				
				<input type="submit"  class="registrationForm"  name="checkChanges" value="Check validity">
				</form>';		
		}else if(isset($_POST['checkChanges']) || (isset($_SESSION['ok']) && $_SESSION['ok'] == 0)){
			$conn = DBINIT();
			echo '<form action="management.php" method="post">';
			$_SESSION['ok']=1;
			//USERNAME CHECK
			if(isset($_POST['username'])){
				echo '<input value="'.$_SESSION['username'].'" class="registrationForm"  type="text" name="username" value="'.$_POST['username'].'" ';
				if($_SESSION['username']!=$_POST['username']){
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
					echo "disabled>OK.<br>";
				}
			}else{
				echo '<input class="registrationForm" value="'.$_SESSION['username'].'"  type="text" name="username"><br>';
			}
			//PASSWORD CHECK
			if(isset($_POST['password'])){
				echo '<input  class="registrationForm" placeholder="password"  type="password" name="password" value="'.$_POST['password'].'" ';
				if(strlen($_POST['password'])<5){
					echo ">Password must be at least 5 characters long!<br>";
					$_SESSION['ok']=0;
				}else{
					echo "disabled>OK.<br>";
				}
			}else{
				$_SESSION['ok']=0;
				echo '<br><input  class="registrationForm" placeholder="Password" type="password" name="password"><br>
				<input  class="registrationForm" placeholder="password" type="password" name="password"><br>';
			}
			if(isset($_POST['description'])){
				echo '<br><textarea class="registrationForm" name="description" value="'.$_SESSION["description"].'" rows="5" cols="50" maxlength="1024" value="'.$_POST['description'].'" disabled></textarea> OK.<br>';
			}else{
				echo '<br><textarea  class="registrationForm" value="" name="description" rows="5" cols="50" maxlength="1024"> '.$_SESSION["description"].' </textarea> OK.<br>';
			}
			if(isset($_POST['image'])){
				echo '<br><input  class="registrationForm" value="'.$_SESSION["image"].'" type="text" name="image" value="'.$_POST['image'].'" disabled>OK.<br>';
			}else{
				echo '<br><input  class="registrationForm" value="'.$_SESSION["image"].'" type="text" name="image">OK.<br>';
			}
			if($_SESSION['ok'] == 0){
				echo '<br><input  class="registrationForm"  type="submit"  name="checkChanges" value="Check validity">
				</form>';
			}else{
				echo '<br><input  class="registrationForm"  type="submit" name="finalizeChanges" value="Done">
				</form>';
			}
			if(!isset($_SESSION['newusername']) && isset($_POST['username'])){
				$_SESSION['newusername'] = $_POST['username'];
			}
			if(!isset($_SESSION['newpassword']) && isset($_POST['password'])){
				$_SESSION['newpassword'] = $_POST['password'];
			}
			if(!isset($_SESSION['newdescription']) && isset($_POST['description'])){
				$_SESSION['newdescription'] = $_POST['description'];
			}
			if(!isset($_SESSION['newimage']) && isset($_POST['image'])){
				$_SESSION['newimage'] = $_POST['image'];
			}
		}else if(isset($_POST['finalizeChanges'])){
			$conn = DBINIT();
			$sql = "
			UPDATE users 
			SET
			username = '".$_SESSION['newusername']."' , 
			password = '".md5($_SESSION['newpassword'])."' ,
			description = '".$_SESSION['newdescription']."' , 
			image = '".$_SESSION['newimage']."' 
			WHERE id = '".$_SESSION['id']."' ;";
			$result = $conn->query($sql);
			echo "Changes successfuly applied! Click <a href=".'index.php'.">here</a> to return to main page.";
			session_unset(); 
			session_destroy(); 
			header("Location:index.php");
		}
	?>
</div>
</body>
</html>