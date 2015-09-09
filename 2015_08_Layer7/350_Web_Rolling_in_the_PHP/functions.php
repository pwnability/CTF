<?php
	require_once "mysql.php";

	function data_escape($method)
	{
		$keys = array();
		foreach($_POST as $key=>$value)
		{
			$value = str_replace("\\", "", $value);
			$value = str_replace("'", "", $value);
			$keys[$key] = addslashes(substr($value, 0, 500));
		}
		return $keys;
	}


?>
