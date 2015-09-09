<html>
	<body>
		<h1>LOGIN</h1></br>
		<form action="login.php" method="post">
			<b>ID </b><input type="text" name="id"></br>
			<b>PW</b><input type="password" name="pw"></br></br>
			<input type="submit" value="login"></br>
		</form>
	</body>
</html>
<?php
	@session_start();
	require_once "functions.php";

	if(isset($_POST['id']) && isset($_POST['pw']))
	{
		$data = data_escape($_POST);
		$res = mysql_query("select id from member where id='$data[id]' and pw='$data[pw]'");
		$arr = mysql_fetch_array($res);
		if($arr['id'])
		{
			$_SESSION['id'] = $data['id'];
			echo "<script>document.location='./index.php';</script>";
			
		}
		else
			echo "<script>alert('user not found');</script>";
		
	}
?>
