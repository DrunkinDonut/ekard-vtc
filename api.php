<?php
	// Database connect
	require_once("db_conn.php");

	// Correct week display
	$lastSunday = date("Y-m-d", strtotime("sunday last week"));
	if(date("Y-m-d") == $lastSunday){
		$thisMonday = date("Y-m-d", strtotime("monday last week"));
		$thisSunday = date("Y-m-d", strtotime("sunday last week"));
	}
	else {
		$thisMonday = date("Y-m-d", strtotime("monday this week"));
		$thisSunday = date("Y-m-d", strtotime("sunday this week"));
	}

	// Get this month value
	$firstDayOfMonth = date("Y-m-d", strtotime("first day of this Month"));
	$lastDayOfMonth = date("Y-m-d", strtotime("last day of this Month"));

	// Drivers number
	if($_GET['data'] == "drivers_number"){
		$driversNumber = array();

		$driversNumberSql = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Id != 35 AND Rank != 'Programista';"));
		
		$driversNumber['drivers_number'] = $driversNumberSql;

		echo json_encode($driversNumber);
	}

	// Top 10 of this week
	if($_GET['data'] == "top_week"){
		$topDistance = mysqli_query($conn, "SELECT DISTINCT Login, (SELECT SUM(Distance) FROM routes AS r1 WHERE r1.Login = r2.Login AND Status = 'Zatwierdzona' AND (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday') AS Dystans FROM routes AS r2 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND Status = 'Zatwierdzona' ORDER BY Dystans DESC LIMIT 10");

		// Drivers list
		if($_GET['type'] == "drivers"){
			$topWeekDrivers = array();
			$i = 1;

			if(mysqli_num_rows($topDistance) >= 1){
				while($value = mysqli_fetch_array($topDistance)){
					$userLogin = $value['Login'];
					$topWeekDrivers[$i] = $userLogin;
					$i++;
				}
			}
			else{
				while($i <= 10){
					$topWeekDrivers[$i] = "-";
					$i++;
				}
			}

			echo json_encode($topWeekDrivers);
		}

		// Distance list
		if($_GET['type'] == "distance"){
			$topWeekDistance = array();
			$i = 1;

			if(mysqli_num_rows($topDistance) >= 1){
				while($value = mysqli_fetch_array($topDistance)){
					$userDistance = number_format($value['Dystans'], 0, ',', '');
					$topWeekDistance[$i] = $userDistance;
					$i++;
				}
			}
			else{
				while($i <= 10){
					$topWeekDistance[$i] = "0";
					$i++;
				}
			}

			echo json_encode($topWeekDistance);
		}
	}

	// Top 10 of this month
	if($_GET['data'] == "top_month"){
		$topDistanceMonth = mysqli_query($conn, "SELECT DISTINCT Login, (SELECT SUM(Distance) FROM routes AS r1 WHERE r1.Login = r2.Login AND Status = 'Zatwierdzona' AND (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth') AS Dystans FROM routes AS r2 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona' ORDER BY Dystans DESC LIMIT 10");

		// Drivers list
		if($_GET['type'] == "drivers"){
			$topMonthDrivers = array();
			$i = 1;

			if(mysqli_num_rows($topDistanceMonth) >= 1){
				while($value = mysqli_fetch_array($topDistanceMonth)){
					$userLogin = $value['Login'];
					$topMonthDrivers[$i] = $userLogin;
					$i++;
				}
			}
			else{
				while($i <= 10){
					$topMonthDrivers[$i] = "-";
					$i++;
				}
			}

			echo json_encode($topMonthDrivers);
		}

		// Distance list
		if($_GET['type'] == "distance"){
			$topMonthDistance = array();
			$i = 1;

			if(mysqli_num_rows($topDistanceMonth) >= 1){
				while($value = mysqli_fetch_array($topDistanceMonth)){
					$userDistance = number_format($value['Dystans'], 0, ',', '');
					$topMonthDistance[$i] = $userDistance;
					$i++;
				}
			}
			else{
				while($i <= 10){
					$topMonthDistance[$i] = "0";
					$i++;
				}
			}

			echo json_encode($topMonthDistance);
		}
	}

	// Company stats
	if($_GET['data'] == "company_stats"){
		$company = array();

		$distanceSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Status = 'Zatwierdzona';"));
		$company['dystans'] = number_format($distanceSumAll[0], 0, ',', '');
		$roadsSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT SUM(AddedRoutesEts2)+SUM(AddedRoutesAts) FROM users) FROM routes WHERE Status = 'Zatwierdzona';"));
		$company['trasy'] = number_format($roadsSumAll[0], 0, ',', '');
		$tonnageSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Status = 'Zatwierdzona';"));
		$company['tonaz'] = number_format($tonnageSumAll[0], 0, ',', '');
		$moneySumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Status = 'Zatwierdzona';"));
		$company['gotowka'] = number_format($moneySumAll[0], 2, ',', '');
		$fuelSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Status = 'Zatwierdzona';"));
		$company['paliwo'] = number_format($fuelSumAll[0], 1, '.', '');
		if(intval($distanceSumAll[0]) >= 1){
			$fuelAvgAll =  (floatval($fuelSumAll[0]) / intval($distanceSumAll[0])) * 100;
			$company['spalanie'] = number_format($fuelAvgAll, 1, '.', '');
		}
		else{
			$fuelAvgAll =  0.0;
			$company['spalanie'] = number_format($fuelAvgAll, 1, '.', '');
		}

		echo json_encode($company, JSON_UNESCAPED_UNICODE);
	}

	// Close connection with database
	mysqli_close($conn);
?>