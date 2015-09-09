<?php
	@session_start();
	require_once "functions.php";

?>

<html>
	<body>
		<h1>Add your favorite comment</h1></br>
		<img src="a.jpg"></br></br>
		<?php if(!$_SESSION)
			echo "<h2><a href='login.php'>login</a></br><a href='register.php'>register</a></h2>";
			else
				require_once "content.php";
		?>
	</body>
</html>
