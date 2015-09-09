<?php
	@session_start();

	function check_pack( $data ) {
		if ( ! is_string( $data ) )
			return false;
		$data = trim( $data );
		if ( 'N;' == $data )
			return true;
	        $len = strlen( $data );
	    	if ( $len < 4 )
			return false;
	    	if ( ':' !== $data[1] )
			return false;
		$c = $data[$len-1];
	    	if ( ';' !== $c && '}' !== $c )
			return false;
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( '"' !== $data[$len-2] )
				return false;
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;\$/", $data );
		}
		return false;
	}

	function do_pack( $data )
	{
		if ( is_array( $data ) || is_object( $data ) )
		{
			return serialize( $data );
		}
	
		if ( check_pack( $data) )
		{
			return serialize( $data );
		}
		return $data;
	}
	function check_unpack($data)
	{
		if(check_pack($data))
		{
			return @unserialize($data);
		}
		return $data;
	}


	class board
	{
		public $metadata = array();
		public $templatecode = array(); 
		public $temp = '';

		public function garbage_collect()
		{
			if(gc_enabled())
				gc_collect_cycles();
		}
		public function itchy()
		{
			@include "flag.php";
			echo "<h1>$flag</h1>";
		}
		public function __construct($param, $var)
		{
			$this->metadata['id'] = $_SESSION['id'];
			if($param !== "none")
				$this->metadata['comment'] = substr($param, 0, 500);
			date_default_timezone_set("Asia/Seoul");
			$this->metadata['time'] =  date('H:i:s', time());
			$this->temp = ($var == 1)?'garbage_collect':'itchy';
		}
		public function __destruct()
		{
			if(empty($this->templatecode[$this->temp]) && ($this->temp === "garbage_collect" || $this->temp === "itchy" ))
			{
				$this->templatecode[$this->temp] = "self::" . $this->temp . "();";
			}
			eval($this->templatecode[$this->temp]);
		}
		public function array_copys( array $array ) {
			$result = array();
			foreach( $array as $key => $val ) 
			{
				if( is_array( $val ) ) {
					$result[$key] = array_copys( $val );
			        }
				elseif ( is_object( $val ) ) 
				{
					$result[$key] = clone $val;
				}
				else
				{
					$result[$key] = $val;
			        }
			}
		        return $result;
		}
		public function board_pack($data)
		{
			$fill = array();
			$data = str_replace("/", "", $data);
			foreach($data as $key=>$value)
			{
				array_push($fill, $key . "/" . do_pack($value));
			}
			return $fill;
		}
		public function board_unpack($data)
		{
			$res = array();
			$test = array();
			for($i=0; $i<count($data); $i++)
			{
				$exp = explode("/", stripslashes($data[$i]));
				$exp_meta = $exp[1];
				array_push($test, check_unpack($exp_meta));
			}
			return self::array_copys($test);
		}
		public function write_comment_board()
		{
			$id = $this->metadata['id'];
			list($comment, $time) = self::board_pack(array_slice($this->metadata, 1));

			mysql_query("SET NAMES 'utf8'");
			$res = mysql_query("insert into board values('$id', '$comment', '$time')");
			if($res)
			{
				echo "<script>alert('write success');</script>";
			}
		}
		public function displayer()
		{
			echo "<h2>--- YOUR FAVORITE COMMENT ---</h2>";
			self::get_comment_list();
		}
		public function get_comment_list()
		{
			$id = $this->metadata['id'];
			$res = mysql_query("select comment,time from board where id='$id'");
			echo "<table border=1><tr><td>comment</td><td>time</td>";
			while($row = mysql_fetch_array($res))
			{
				list($comment, $time) = self::board_unpack(array($row['comment'], $row['time']));

				echo "<tr><td>";
				echo $comment;
				echo "</td><td>";
				echo $time;
				echo "</td></tr>";
			}
			echo "</table>";
		}
	}

	if($_SESSION)
	{
		echo "<h2>--- WRITE YOUR COMMENT ---</h2>";
		echo "<form action='' method='post'>";
		echo "comment : <input type='text' name='comment'>";
		echo "<input type='submit' value='add'>";
		echo "</form>";

		if($_POST)
		{
			$escape_post = data_escape($_POST);
			$board_obj = new board($escape_post['comment'], 1);
			$board_obj->write_comment_board();
		}
		$board_obj = new board("none", 1);
		$board_obj->displayer();
		echo "<h3><a href='logout.php'>logout</a></h3>";
	}
?>
