<?php
	require_once("../db_conn.php");
	$username = $_POST["username"];
	header('Content-type: application/json');

	// Delete data if lastUpdate > 5 minutes
	mysqli_query($conn, "DELETE FROM telemetry WHERE user = '$username' AND lastUpdate <= SUBDATE(NOW(), INTERVAL 5 MINUTE)");

	// Check if telemetry server sends data
	$sql1 = mysqli_num_rows(mysqli_query($conn, "SELECT lastUpdate FROM telemetry WHERE user = '$username' AND lastUpdate >= SUBDATE(NOW(), INTERVAL 5 SECOND)"));
	if($sql1 == 1){
		$connection = "connected";
	}
	else{
		$connection = "false";
	}

	// Check if game is on
	$sql5 = mysqli_fetch_row(mysqli_query($conn, "SELECT connected FROM telemetry WHERE user = '$username'"));
	if($sql5[0] == 1){
		$gameOn = "true";
	}
	else{
		$gameOn = "false";
	}

	// Get actual time
	$actualTimeSql = mysqli_fetch_row(mysqli_query($conn, "SELECT LEFT(RIGHT(NOW(), 8),5)"));
	$actualTime = $actualTimeSql[0];

	// Get actual date
	$actualDateSql = mysqli_fetch_row(mysqli_query($conn, "SELECT LEFT(NOW(), 10)"));
	$actualDate = $actualDateSql[0];

	// Get speed
	$sql2 = mysqli_fetch_row(mysqli_query($conn, "SELECT speed FROM telemetry WHERE user = '$username'"));
	$speed = $sql2[0];

	// Get odometer
	$sql3 = mysqli_fetch_row(mysqli_query($conn, "SELECT odometer FROM telemetry WHERE user = '$username'"));
	$odometer = $sql3[0];

	// Get engine status
	$sql4 = mysqli_fetch_row(mysqli_query($conn, "SELECT engine FROM telemetry WHERE user = '$username'"));
	$engine = $sql4[0];

	// Get drive time
	$getFirstEngineOn = mysqli_fetch_row(mysqli_query($conn, "SELECT firstEngineOn FROM telemetry WHERE user = '$username'"));
	if($getFirstEngineOn[0] == "0000-00-00 00:00:00"){
		$driveTime = "00:00";
	}
	else{
		$now = new DateTime(date("H:i"));
		$firstEngineOn = date_create($getFirstEngineOn[0]);
		$driveTimeDiff = date_diff($firstEngineOn, $now);
		$driveTime = $driveTimeDiff->format('%H').":".$driveTimeDiff->format('%I');
	}

	// Get time to pause
	$getControlEngineOn = mysqli_fetch_row(mysqli_query($conn, "SELECT controlEngineOn FROM telemetry WHERE user = '$username'"));
	if($getControlEngineOn[0] == "0000-00-00 00:00:00"){
		$timeToPause = "00:00";
	}
	else{
		$controlEngineOnSecondsSql2 = mysqli_fetch_row(mysqli_query($conn, "SELECT TIMESTAMPDIFF(SECOND, NOW(), ADDDATE(controlEngineOn, INTERVAL 45 MINUTE)) FROM telemetry WHERE user = '$username'"));
		$secondsToPause = intval($controlEngineOnSecondsSql2[0]);
		$now = new DateTime(date("H:i"));
		$nextPauseTime = new DateTime(date("H:i", strtotime("$secondsToPause second")));
		$timeToPauseDiff = date_diff($now, $nextPauseTime);
		if($now > $nextPauseTime){
			$timeToPause = "00:00";
		}
		else{
			$timeToPause = $timeToPauseDiff->format('%H').":".$timeToPauseDiff->format('%I');
		}
	}

	// Get pause time
	$getControlEngineOff = mysqli_fetch_row(mysqli_query($conn, "SELECT controlEngineOff FROM telemetry WHERE user = '$username'"));
	if($getControlEngineOff[0] == "0000-00-00 00:00:00"){
		$pauseTime = "00:00";
	}
	else{
		$controlEngineOffSeconds = mysqli_fetch_row(mysqli_query($conn, "SELECT TIMESTAMPDIFF(SECOND, NOW(), ADDDATE(controlEngineOff, INTERVAL 5 MINUTE)) FROM telemetry WHERE user = '$username'"));
		$pauseSeconds = intval($controlEngineOffSeconds[0]);
		$now = new DateTime(date("H:i:s"));
		$pauseDoneTime = new DateTime(date("H:i:s", strtotime("$pauseSeconds second")));
		$pauseDoneDiff = date_diff($now, $pauseDoneTime);
		if($now > $pauseDoneTime){
			$pauseTime = "00:00";
		}
		else{
			if($engine == "on"){
				$pauseTime = "00:00";
			}
			else{
				$pauseTime = $pauseDoneDiff->format('%I').":".$pauseDoneDiff->format('%S');
			}
		}
	}

	$data = array();
	$data['actualTime'] = $actualTime;
	$data['actualDate'] = $actualDate;
	$data['connection'] = $connection;
	$data['gameOn'] = $gameOn;
	$data['speed'] = $speed;
	$data['odometer'] = $odometer;
	$data['engine'] = $engine;
	$data['driveTime'] = $driveTime;
	$data['timeToPause'] = $timeToPause;
	$data['pauseTime'] = $pauseTime;

	echo json_encode($data, JSON_UNESCAPED_UNICODE);

	mysqli_close($conn);
?>