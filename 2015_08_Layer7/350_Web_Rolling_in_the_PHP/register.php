<html>
	<body>
		<h1>REGISTER</h1></br>
		<form action="register.php" method="post">
			<b>ID </b><input type="text" name="id"></br>
			<b>PW</b><input type="password" name="pw"></br></br>
			<input type="submit" value="register"></br>
		</form>
	</body>
</html>


<?php
	@session_start();
	require_once "functions.php";


	if(isset($_POST['id']) && isset($_POST['pw']))
	{
		$data = data_escape($_POST);
		$res = mysql_query("select id from member where id='$data[id]'");
		$arr = mysql_fetch_array($res);
                if($arr['id'])
		{
			echo "<script>alert('". $data['id']. " exist');</script>";
			exit();
		}
		else
		{
			$res = mysql_query("insert into member values('$data[id]', '$data[pw]')");
			if($res)
			{
				echo "<script>alert('". $data['id']. " sign up');location.href='index.php';</script>";
			}
		}
			
		
	}
?>
