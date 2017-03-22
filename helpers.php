<?php
	
	function delete($id){
		$delreq = $id;
		$currentuserid = $_SESSION['id'];
		$conn = DBINIT();
		$sql = "SELECT media.id as mid, userID 
		FROM media 
		WHERE userID = '".$currentuserid."' 
		AND media.id = '".$delreq."'";
		$result = $conn->query($sql);
		if ($result == false){
			echo "That file is not yours to delete!";
		}
		else if ($result->num_rows > 0) {
			$sql = "DELETE FROM media
			WHERE id = '".$delreq."'";
			$result = $conn->query($sql);
		}
	}
	
	function renderLoginForm(){
		echo '<form action="index.php" method="post">
					<input class="loginLogoutRegister" placeholder="Username" type="text" name="username">
					<input class="loginLogoutRegister" placeholder="Password" type="password" name="password">
					<input class="loginLogoutRegister" type="submit" name="login" value="Login">
					</form>';
	}
	
	function login(){
		session_unset(); 
		session_destroy(); 
		session_start();
		$pass = md5($_POST['password']);
		$conn = DBINIT();
		$sql = "SELECT id, username, password, description, image FROM users where username like '".$_POST['username']."' and password like '".$pass."';";
		$result = $conn->query($sql);
		if ($result == false){
			echo "Incorrect username/password!";
			renderLoginForm();
		}
		else if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$_SESSION["id"] = $row["id"];
			$_SESSION["username"] = $row["username"];
			$_SESSION["image"] = $row["image"];
			$_SESSION["sessionID"] = md5($row["password"]);
			$_SESSION["description"] = $row["description"];
			loggedIn();
		} else {
			echo "Incorrect username/password!";
			renderLoginForm();
		}
		
		$conn->close();
	}

	function renderSearchBox(){
		echo '<form   action="search.php" method="post">
			<input class="search" type="text" name="query" placeholder="Search">
			<input class="search" type="submit" name="search" value="Search">
			</form>';
	}
	
	function getContentAtId($mid){
		$conn = DBINIT();
		$sql = 'SELECT * FROM media WHERE id='.$mid.' ;';
		$result = $conn->query($sql);
		return $result;
	}
	
	function getContentTitleAtId($mid){
		$conn = DBINIT();
		$sql = 'SELECT * FROM media WHERE id='.$mid.' ;';
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				return $row['Title'];
			}
		} else {
			return "This file no longer exists.";
		}
	}
	
	function loggedIn(){
		echo '<div id="body" class="userInfo">';
			echo '<a href="user.php?id='.$_SESSION['id'].'">';
			echo '<div id="img" class="textWrap">';
			echo '<img src="'.$_SESSION['image'].'" width="100" height="100"/>';
			echo '</div>';
			echo $_SESSION['username'];
			echo "<br>Uploads: ".getNumUploads($_SESSION['id']);
			echo "<br>Views: ".getNumViewsUser($_SESSION['id']);
			//OTHER USER INFO LATER
			//NUMBER OF UPLOADED VIDEOS?
			//COLUMNATED NUMBER OF LIKES?
			echo '</a>';
			echo '</div>';
			echo '<div id="body" class="userFunctions">';
			echo '<form action="index.php" method="post">
					<input class="loginLogoutRegisterFW" type="submit" name="logout" value="Logout">
					</form>';
	}
	
	function generateRandomString() {
		$length = 30;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	function formatFileSize($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }
        else{
            $bytes = '0 bytes';
        }
        return $bytes;
	}
	
	function getUserInfo($uid){
		$conn = DBINIT();
		$sql = 'SELECT * FROM users WHERE id='.$uid.' ;';
		$result = $conn->query($sql);
		$conn->close();
		return $result;
	}
	
	function renderProfileCommentBox(){
		echo '<form action="user.php" method="post">
		<textarea  class="commentForm" placeholder="Comment" name="comment" rows="5" cols="200" maxlength="2048"> </textarea> <br>
		<input class="loginLogoutRegister" type="submit" name="postComment" value="Post comment">
		</form>';
	}
	
	function processProfileComment(){
		if(isset($_POST['postComment'])){
			$conn = DBINIT();
			$sql = "INSERT INTO profilecomments (uid, pid, txt) VALUES ('".
			$_SESSION['id']."', '".
			$_SESSION['vuid']."', '".
			$_POST['comment']."')";
			$result = $conn->query($sql);
			//echo "Comment sucessfuly posted.";
			unset($_POST);
			header("Location:user.php");
			$conn->close();
		}
	}
	
	function getProfileComments($uid){
		$conn = DBINIT();
		$sql = 'SELECT * FROM profilecomments WHERE pid='.$uid.' ;';
		$result = $conn->query($sql);
		return $result;
	}
	
	function renderMediaCommentBox(){
		echo '<form action="media.php" method="post">
		<textarea  class="commentForm" placeholder="Comment" name="comment" rows="5" cols="200" maxlength="2048"> </textarea> <br>
		<input class="loginLogoutRegister" type="submit" name="postComment" value="Post comment">
		</form>';
	}
	
	function processMediaComment(){
		if(isset($_POST['postComment'])){
			$conn = DBINIT();
			$sql = "INSERT INTO mediacomments (mid, uid, txt) VALUES ('".
			$_SESSION['vmid']."', '".
			$_SESSION['id']."', '".
			$_POST['comment']."')";
			$result = $conn->query($sql);
			//echo "Comment sucessfuly posted.";
			unset($_POST);
			//echo $sql;
			header("Location:media.php");
			$conn->close();
		}
	}
	
	function getMediaComments($mid){
		$conn = DBINIT();
		$sql = "SELECT * FROM mediacomments WHERE mid='".$mid."' ;";
		//echo $sql;
		$result = $conn->query($sql);
		return $result;
	}
	
	function increaseViewCount($mid){
		if(isset($_SESSION['id'])){
			$uid=$_SESSION['id'];
			//check if view already exists
			//echo "view exists = ".getView($uid, $mid);
			if(getView($uid, $mid)==0){
				//echo "adding view";
				$result=getContentAtId($mid);
				if ($result->num_rows > 0) {
					//add view to views table
					addView($uid, $mid);
					//set views
					refreshViewCount($mid);
				}
			}else{
				refreshViewCount($mid);
			}
		}
	}
	
	function getView($uid, $mid){
		$conn = DBINIT();
		$sql = "
			SELECT COUNT(vid) as num 
			FROM views
			WHERE mid = '".$mid. "'
			AND uid = '".$uid. "'
			;";
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
			while($row = $result->fetch_assoc()) {
				return $row['num'];
			}
		} else {
			return 0;
		}
	}
	
	function addView($uid, $mid){
		$conn = DBINIT();
		$sql = "
			INSERT INTO views
			(mid, uid)
			VALUES
			( '".$mid. "' , '".$uid. "' );";
		$result = $conn->query($sql);
	}
	
	function refreshViewCount($mid){
		//get number of views
		$views = 0;
		$conn = DBINIT();
		$sql = "
			SELECT COUNT(vid) as num 
			FROM views
			WHERE mid = '".$mid."'
			;";
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
			//echo $sql;
			while($row = $result->fetch_assoc()) {
				$views=$row['num'];
			}
		}
		echo "got views: ".$views;
		//write them to media
		$conn = DBINIT();
		$sql = "
			UPDATE media 
			SET Views = '".$views."'
			WHERE ID = '".$mid. "';";
		$result = $conn->query($sql);
	}
	
	function getViews($mid){
		$result=getContentAtId($mid);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$numViews = $row['Views'];
			echo $numViews;
		}
	}
	
	function getVotes($mid){
		$result=getContentAtId($mid);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$numLikes = $row['Upvotes'];
			$numDisikes = $row['Downvotes'];
			$score = ($numLikes - $numDisikes);
			echo $score;
		}
	}
	
	function DBINIT(){
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
		return $conn;
	}
	
	function getNumUploads($uid){
		$count = 0;
		$conn = DBINIT();
		$sql = 'SELECT COUNT(ID) as num
				FROM media
				WHERE media.userID = '.$uid.' ;';
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$count = $row['num'];
			}
		} else {
			$count = 0;
		}
		return $count;
	}
	
	function getNumViewsUser($uid){
		$count = 0;
		$conn = DBINIT();
		$sql = 'SELECT *
				FROM media
				WHERE media.userID = '.$uid.' ;';
		$result = $conn->query($sql);
		$numRows = $result->num_rows;
		if ($numRows > 0) {
			while($row = $result->fetch_assoc()) {
				$count = $count + $row['Views'];
			}
		} else {
			$count = 0;
		}
		return $count;
	}
	
	function addOneLike($mid){
		$result=getContentAtId($mid);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$numlikes = $row['Upvotes'];
			$conn = DBINIT();
			$numlikes = $numlikes + 1;
			$sql = "
			UPDATE media 
			SET Upvotes = '".$numlikes."'
			WHERE ID = '".$mid. "';";
			$result = $conn->query($sql);
		}
	}
	
	function addOneDisike($mid){
		$result=getContentAtId($mid);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$numdislikes = $row['Downvotes'];
			$conn = DBINIT();
			$numdislikes = $numdislikes + 1;
			$sql = "
			UPDATE media 
			SET Downvotes = '".$numdislikes."'
			WHERE ID = '".$mid. "';";
			$result = $conn->query($sql);
		}
	}
	
	
?>



	
	
	
	
	
	