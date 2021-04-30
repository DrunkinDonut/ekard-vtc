<?php
	require_once("../db_conn.php");
	error_reporting(0);
	$data = json_decode(file_get_contents('php://input'), true);

	$headerUser = base64_decode($_SERVER['HTTP_X_USERID']);
	$userPassword = base64_decode($_SERVER['HTTP_X_USERPASSWORD']);

	$pwdHash = mysqli_fetch_row(mysqli_query($conn, "SELECT Password FROM users WHERE Login = '$headerUser'"));
	if(!password_verify($userPassword, $pwdHash[0])){
		mysqli_close($conn);
		exit();
	}

	$connected = $data["Game"]["Connected"];
	if($connected == "true"){
		$connected = 1;
	}
	else {
		$connected = 0;
	}
	$speed = $data["Truck"]["Speed"];
	$odometer = $data["Truck"]["Odometer"];
	$engineData = $data["Truck"]["EngineOn"];
	if($engineData == "true"){
		$engine = "on";
	}
	else {
		$engine = "off";
	}
	$positionX = $data["Truck"]["Placement"]["X"];
	$positionY = $data["Truck"]["Placement"]["Y"];
	$positionZ = $data["Truck"]["Placement"]["Z"];

	if($engine == "on"){
		$newControlEngineOnSqlHeader = ", controlEngineOn";
		$newControlEngineOnSqlValue = ", NOW()";
		$firstEngineOnSqlHeader = ", firstEngineOn";
		$firstEngineOnSqlValue = ", NOW()";
	}
	else {
		$newControlEngineOffSqlHeader = ", controlEngineOff";
		$newControlEngineOffSqlValue = ", NOW()";
	}

	$getLastUpdateValue = mysqli_fetch_row(mysqli_query($conn, "SELECT lastUpdate FROM telemetry WHERE user = '$headerUser'"));
	mysqli_query($conn, "DELETE FROM telemetry WHERE user = '$headerUser' AND lastUpdate <= SUBDATE(NOW(), INTERVAL 5 MINUTE)");

	$getControlEngineOn = mysqli_fetch_row(mysqli_query($conn, "SELECT controlEngineOn FROM telemetry WHERE user = '$headerUser'"));
	$getControlEngineOff = mysqli_fetch_row(mysqli_query($conn, "SELECT controlEngineOff FROM telemetry WHERE user = '$headerUser'"));

	if($engine == "on" && $getControlEngineOn[0] == "0000-00-00 00:00:00"){
		mysqli_query($conn, "UPDATE telemetry SET controlEngineOn = NOW() WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET firstEngineOn = NOW() WHERE user = '$headerUser'");
	}
	if($engine == "off" && $getControlEngineOff[0] == "0000-00-00 00:00:00"){
		mysqli_query($conn, "UPDATE telemetry SET controlEngineOff = NOW() WHERE user = '$headerUser'");
	}

	$getDbEngineStatus = mysqli_fetch_row(mysqli_query($conn, "SELECT engine FROM telemetry WHERE user = '$headerUser'"));
	if($getDbEngineStatus[0] == "on"){
		if($engine == "off"){
			mysqli_query($conn, "UPDATE telemetry SET controlEngineOff = NOW() WHERE user = '$headerUser'");
		}
	}
	if($getDbEngineStatus[0] == "off"){
		if($engine == "on"){
			$ifPauseCompletedSql = mysqli_num_rows(mysqli_query($conn, "SELECT controlEngineOff FROM telemetry WHERE user = '$headerUser' AND NOW() >= ADDDATE(controlEngineOff, INTERVAL 5 MINUTE)"));
			if($ifPauseCompletedSql == 1){
				mysqli_query($conn, "UPDATE telemetry SET controlEngineOn = NOW() WHERE user = '$headerUser'");
			}
		}
	}

	$isInDB = mysqli_num_rows(mysqli_query($conn, "SELECT user FROM telemetry WHERE user = '$headerUser'"));
	$newEntrySql = "INSERT INTO telemetry (user, connected, speed, odometer, engine, positionX, positionY, positionZ, firstUpdate, lastUpdate".$firstEngineOnSqlHeader."".$newControlEngineOnSqlHeader."".$newControlEngineOffSqlHeader.") VALUES ('$headerUser', '$connected', '$speed', '$odometer', '$engine', '$positionX', '$positionY', '$positionZ', NOW(), NOW()".$firstEngineOnSqlValue."".$newControlEngineOnSqlValue."".$newControlEngineOffSqlValue.")";
	if($isInDB == 0){
		mysqli_query($conn, $newEntrySql);
	}
	else{
		mysqli_query($conn, "UPDATE telemetry SET speed = '$speed' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET connected = '$connected' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET odometer = '$odometer' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET engine = '$engine' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET positionX = '$positionX' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET positionY = '$positionY' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET positionZ = '$positionZ' WHERE user = '$headerUser'");
		mysqli_query($conn, "UPDATE telemetry SET lastUpdate = NOW() WHERE user = '$headerUser'");
	}
	mysqli_close($conn);
?>