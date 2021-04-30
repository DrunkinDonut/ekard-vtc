<?php
	$host = "sql313.epizy.com";
	$user = "epiz_28509342";
	$pass = "snl8t680XpAE";
	$db_name = "epiz_28509342_ekard_vtc";

	$conn = mysqli_connect($host, $user, $pass, $db_name);
	if(!$conn){
		header("Location: db_error.html");
	}

	mysqli_query($conn, "SET NAMES utf8");
  	mysqli_query($conn, "SET CHARACTER SET utf8");
  	mysqli_query($conn, "SET collation_connection = utf8_general_ci");

  	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	function compress_image($source, $destination, $quality) {

		$info = getimagesize($source);

		if ($info['mime'] == 'image/jpeg') 
			$image = imagecreatefromjpeg($source);

		elseif ($info['mime'] == 'image/png') 
			$image = imagecreatefrompng($source);

		imagejpeg($image, $destination, $quality);

		return $destination;
	}
?>