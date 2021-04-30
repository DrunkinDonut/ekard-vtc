<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?php
			require_once("head.html");
		?>
		<title>Statystyki - EKARD VTC</title>
	</head>
	<body>
		<?php require_once("header.php"); ?>
		<nav>
			<div class="nav_section">
				<h1><span class="fas fa-road fa-sm" aria-hidden="true"></span>TRASY</h1>
				<ul>
					<a href="new_route" title="Dodaj nową trasę" class="nav_item"><li>Dodaj nową</li></a>
					<a href="my_routes" title="Moje trasy" class="nav_item"><li>Moje trasy</li></a>
					<a href="all_routes" title="Wszystkie trasy" class="nav_item"><li>Wszystkie trasy</li></a>
				</ul>
			</div>
			<div class="nav_section">
				<h1><span class="fas fa-truck fa-xs" aria-hidden="true"></span>FIRMA</h1>
				<ul>
					<a href="index" title="Panel kierowcy" class="nav_item"><li>Panel kierowcy</li></a>
					<a href="drivers" title="Lista kierowców" class="nav_item"><li>Kierowcy</li></a>
					<a href="stats" title="Statystyki" class="nav_item nav_active"><li>Statystyki</li></a>
				</ul>
			</div>
			<?php
				$login = $_SESSION["Login"];
				$userRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Login = '$login';"));
				$numberRoutesToCheck = mysqli_num_rows(mysqli_query($conn, "SELECT Status FROM routes WHERE Status = 'Oczekuje';"));
				
				if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel" OR $userRank[0] == "Prezes" OR $userRank[0] == "Dyspozytor" OR $userRank[0] == "VIP"){
					echo '<div class="nav_section">
				<h1><span class="fas fa-check-square fa-sm" aria-hidden="true"></span>DYSPOZYTORKA</h1>
				<ul>
					<a href="check_route" title="Sprawdzanie tras" class="nav_item"><li>Sprawdzanie tras';if($numberRoutesToCheck >= 1){echo '<span class="number_routes_to_check">'.$numberRoutesToCheck.'</span>';}echo '</li></a>
				</ul>
			</div>';
				}

				if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel" OR $userRank[0] == "Prezes" OR $userRank[0] == "Rekruter" OR $userRank[0] == "VIP"){
					echo '<div class="nav_section">
				<h1><span class="fas fa-user-plus fa-xs" aria-hidden="true"></span>REKRUTACJA</h1>
				<ul>
					<a href="add_user" title="Dodaj użytkownika" class="nav_item"><li>Dodaj użytkownika</li></a>
				</ul>
			</div>';
				}

				if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel" OR $userRank[0] == "Prezes"){
					echo '<div class="nav_section">
				<h1><span class="fas fa-cogs fa-xs" aria-hidden="true"></span>ADMINISTRACJA</h1>
				<ul>
					<a href="edit_users" title="Edytuj użytkowników" class="nav_item"><li>Użytkownicy</li></a>
					<a href="tachograph_logs" title="Logi tachografu" class="nav_item"><li>Logi tachografu</li></a>
					<a href="cities_loads" title="Miasta i ładunki" class="nav_item"><li>Miasta i ładunki</li></a>
				</ul>
			</div>';
				}
			?>
			<div class="nav-section">
				<div class="nav_copy">&copy;Copyright <?php echo date("Y"); ?> by <a href="http://steamcommunity.com/id/drunkin_donut" target="_blank">Natan Ryl</a>.<br />Wszelkie prawa zastrzeżone.</div>
			</div>
		</nav>
		<section>
			<?php
				if(isset($_GET['type'])){
					if($_GET['type'] == 'general' OR $_GET['type'] == 'detailed'){
						$type = $_GET['type'];
					}
				}
				else{
					$_GET['type'] = "general";
					$type = $_GET['type'];
				}

				if(isset($_GET['game'])){
					if($_GET['game'] == 'all' OR $_GET['game'] == 'ets2' OR $_GET['game'] == 'ats'){
						$game = $_GET['game'];
					}
				}
				else{
					$_GET['game'] = "all";
					$game = $_GET['game'];
				}

				$lastSunday = date("Y-m-d", strtotime("sunday last week"));
				if(date("Y-m-d") == $lastSunday){
					$thisMonday = date("Y-m-d", strtotime("monday last week"));
					$thisSunday = date("Y-m-d", strtotime("sunday last week"));
				}
				else {
					$thisMonday = date("Y-m-d", strtotime("monday this week"));
					$thisSunday = date("Y-m-d", strtotime("sunday this week"));
				}

				if(isset($_GET['date_from'])){
					if($_GET['date_from'] != ""){
						if(strtotime($_GET['date_from'])){
							$dateFrom = $_GET['date_from'];
						}
						else{
							$_GET['date_from'] = $thisMonday;
							$dateFrom = $_GET['date_from'];
						}
					}
					else{
						$_GET['date_from'] = $thisMonday;
						$dateFrom = $_GET['date_from'];
					}
				}
				else{
					$_GET['date_from'] = $thisMonday;
					$dateFrom = $_GET['date_from'];
				}

				if(isset($_GET['date_to'])){
					if($_GET['date_to'] != ""){
						if(strtotime($_GET['date_to'])){
							$dateTo = $_GET['date_to'];
						}
						else{
							$_GET['date_to'] = $thisSunday;
							$dateTo = $_GET['date_to'];
						}
					}
					else{
						$_GET['date_to'] = $thisSunday;
						$dateTo = $_GET['date_to'];
					}
				}
				else{
					$_GET['date_to'] = $thisSunday;
					$dateTo = $_GET['date_to'];
				}
			?>
			<div class="stats_filter_block">
				<form action='stats' method='GET'>
					<div class="form-group">
						<label for="type">Rodzaj statystyk:</label>
						<select name="type">
							<option value="general" <?php if(isset($_GET['type'])){ if($_GET['type'] == 'general'){ echo "selected"; }} ?>>Ogólne</option>
							<option value="detailed" <?php if(isset($_GET['type'])){ if($_GET['type'] == 'detailed'){ echo "selected"; }} ?>>Szczegółowe</option>
						</select>
					</div>
					<div class="form-group">
						<label for="game">Gra:</label>
						<select name="game">
							<option value="all" <?php if(isset($_GET['game'])){ if($_GET['game'] == 'all'){ echo "selected"; }} ?>>Wszystkie</option>
							<option value="ets2" <?php if(isset($_GET['game'])){ if($_GET['game'] == 'ets2'){ echo "selected"; }} ?>>ETS2</option>
							<option value="ats" <?php if(isset($_GET['game'])){ if($_GET['game'] == 'ats'){ echo "selected"; }} ?>>ATS</option>
						</select>
					</div>
					<div class="form-group">
						<label for="date_from">Data od:</label>
						<input type="date" name="date_from" <?php if(isset($_GET['date_from'])){ echo "value=\"".$_GET['date_from']."\""; } ?> />
					</div>
					<div class="form-group">
						<label for="date_to">Data do:</label>
						<input type="date" name="date_to" <?php if(isset($_GET['date_to'])){ echo "value=\"".$_GET['date_to']."\""; } ?> />
					</div>
					<button type="submit" title="Filtruj"><span class='fas fa-search fa-sm'></span>Filtruj</button>
				</form>
			</div>
			<?php
				if($type == 'general'){
					if($game == 'all'){
						$statsData = mysqli_query($conn, "SELECT * FROM routes WHERE DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\";");
						$distanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$roadsSum = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$tonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$moneySum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$fuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						if(intval($distanceSum[0]) >= 1){
							$fuelAvg =  (floatval($fuelSum[0]) / intval($distanceSum[0])) * 100;
						}
						else{
							$fuelAvg =  0.0;
						}
						$distanceSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Status = 'Zatwierdzona';"));
						$roadsSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT SUM(AddedRoutesEts2)+SUM(AddedRoutesAts) FROM users) FROM routes WHERE Status = 'Zatwierdzona';"));
						$tonnageSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Status = 'Zatwierdzona';"));
						$moneySumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Status = 'Zatwierdzona';"));
						$fuelSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Status = 'Zatwierdzona';"));
						if(intval($distanceSumAll[0]) >= 1){
							$fuelAvgAll =  (floatval($fuelSumAll[0]) / intval($distanceSumAll[0])) * 100;
						}
						else{
							$fuelAvgAll =  0.0;
						}
						$longestRoad = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance, Login FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\"  AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
						$longestRoadProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$longestRoad[1]\""));
						$mostDistanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(Distance) AS Dystans FROM routes WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\"  AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Dystans DESC LIMIT 1;"));
						$mostDistanceSumProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostDistanceSum[0]\""));
						$mostTonnage = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, Tonnage FROM routes WHERE (Id < 3 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\"  AND Status = 'Zatwierdzona' GROUP BY Tonnage ORDER BY Tonnage DESC LIMIT 1;"));
						$mostTonnageProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostTonnage[0]\""));
						$leastFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT DISTINCT Login, ((SELECT SUM(Fuel) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') / (SELECT SUM(Distance) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') * 100) AS Spalanie FROM routes AS r1 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Spalanie ASC LIMIT 1;"));
						$leastFuelConsumptionProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$leastFuelConsumption[0]\""));
						$mostMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(EarnedMoney) AS Pieniądze FROM routes WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\"  AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Pieniądze DESC LIMIT 1;"));
						$mostMoneyProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostMoney[0]\""));
						if($dateFrom < "2018-03-19"){
							$mostRoadsAdd = "+((SELECT AddedRoutesEts2 FROM users AS u WHERE r.Login = u.Login)+(SELECT AddedRoutesAts FROM users AS u WHERE r.Login = u.Login))";
						}
						else {
							$mostRoadsAdd = "";
						}
						$mostRoads = mysqli_fetch_row(mysqli_query($conn, "SELECT r.Login, COUNT(*)".$mostRoadsAdd." AS Trasy FROM routes AS r WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Trasy DESC LIMIT 1;"));

						$mostRoadsProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostRoads[0]\""));
					}
					if($game == 'ets2'){
						$statsData = mysqli_query($conn, "SELECT * FROM routes WHERE Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\";");
						$distanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$roadsSum = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$tonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$moneySum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$fuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						if(intval($distanceSum[0]) >= 1){
							$fuelAvg =  (floatval($fuelSum[0]) / intval($distanceSum[0])) * 100;
						}
						else{
							$fuelAvg =  0.0;
						}
						$distanceSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Game = \"ets2\" AND Status = 'Zatwierdzona';"));
						$roadsSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT SUM(AddedRoutesEts2) FROM users) FROM routes WHERE Game = \"ets2\" AND Status = 'Zatwierdzona';"));
						$tonnageSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Game = \"ets2\" AND Status = 'Zatwierdzona';"));
						$moneySumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Game = \"ets2\" AND Status = 'Zatwierdzona';"));
						$fuelSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Game = \"ets2\" AND Status = 'Zatwierdzona';"));
						if(intval($distanceSumAll[0]) >= 1){
							$fuelAvgAll =  (floatval($fuelSumAll[0]) / intval($distanceSumAll[0])) * 100;
						}
						else{
							$fuelAvgAll =  0.0;
						}
						$longestRoad = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance, Login FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
						$longestRoadProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$longestRoad[1]\""));
						$mostDistanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(Distance) AS Dystans FROM routes WHERE (Id < 42 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Dystans DESC LIMIT 1;"));
						$mostDistanceSumProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostDistanceSum[0]\""));
						$mostTonnage = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, Tonnage FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Tonnage ORDER BY Tonnage DESC LIMIT 1;"));
						$mostTonnageProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostTonnage[0]\""));
						$leastFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT DISTINCT Login, ((SELECT SUM(Fuel) FROM routes AS r2 WHERE Game = \"ets2\" AND r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') / (SELECT SUM(Distance) FROM routes AS r2 WHERE Game = \"ets2\" AND r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') * 100) AS Spalanie FROM routes AS r1 WHERE (Id < 42 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Spalanie ASC LIMIT 1;"));
						$leastFuelConsumptionProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$leastFuelConsumption[0]\""));
						$mostMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(EarnedMoney) AS Pieniądze FROM routes WHERE (Id < 42 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Pieniądze DESC LIMIT 1;"));
						$mostMoneyProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostMoney[0]\""));
						if($dateFrom < "2018-03-19"){
							$mostRoadsAdd = "+(SELECT AddedRoutesEts2 FROM users AS u WHERE r.Login = u.Login)";
						}
						else {
							$mostRoadsAdd = "";
						}
						$mostRoads = mysqli_fetch_row(mysqli_query($conn, "SELECT r.Login, COUNT(*)".$mostRoadsAdd." AS Trasy FROM routes AS r WHERE (Id < 42 OR Id > 43) AND Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Trasy DESC LIMIT 1;"));
						$mostRoadsProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostRoads[0]\""));
					}
					if($game == 'ats'){
						$statsData = mysqli_query($conn, "SELECT * FROM routes WHERE Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\";");
						$distanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$roadsSum = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$tonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$moneySum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						$fuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
						if(intval($distanceSum[0]) >= 1){
							$fuelAvg =  (floatval($fuelSum[0]) / intval($distanceSum[0])) * 100;
						}
						else{
							$fuelAvg =  0.0;
						}
						$distanceSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Game = \"ats\" AND Status = 'Zatwierdzona';"));
						$roadsSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT SUM(AddedRoutesAts) FROM users) FROM routes WHERE Game = \"ats\" AND Status = 'Zatwierdzona';"));
						$tonnageSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Game = \"ats\" AND Status = 'Zatwierdzona';"));
						$moneySumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Game = \"ats\" AND Status = 'Zatwierdzona';"));
						$fuelSumAll = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Game = \"ats\" AND Status = 'Zatwierdzona';"));
						if(intval($distanceSumAll[0]) >= 1){
							$fuelAvgAll =  (floatval($fuelSumAll[0]) / intval($distanceSumAll[0])) * 100;
						}
						else{
							$fuelAvgAll =  0.0;
						}
						$longestRoad = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance, Login FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
						$longestRoadProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$longestRoad[1]\""));
						$mostDistanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(Distance) AS Dystans FROM routes WHERE (Id < 42 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Dystans DESC LIMIT 1;"));
						$mostDistanceSumProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostDistanceSum[0]\""));
						$mostTonnage = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, Tonnage FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Tonnage ORDER BY Tonnage DESC LIMIT 1;"));
						$mostTonnageProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostTonnage[0]\""));
						$leastFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT DISTINCT Login, ((SELECT SUM(Fuel) FROM routes AS r2 WHERE Game = \"ats\" AND r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') / (SELECT SUM(Distance) FROM routes AS r2 WHERE Game = \"ats\" AND r1.Login = r2.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') * 100) AS Spalanie FROM routes AS r1 WHERE (Id < 42 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Spalanie ASC LIMIT 1;"));
						$leastFuelConsumptionProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$leastFuelConsumption[0]\""));
						$mostMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT Login, SUM(EarnedMoney) AS Pieniądze FROM routes WHERE (Id < 42 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Pieniądze DESC LIMIT 1;"));
						$mostMoneyProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostMoney[0]\""));
						if($dateFrom < "2018-03-19"){
							$mostRoadsAdd = "+(SELECT AddedRoutesAts FROM users AS u WHERE r.Login = u.Login)";
						}
						else {
							$mostRoadsAdd = "";
						}
						$mostRoads = mysqli_fetch_row(mysqli_query($conn, "SELECT r.Login, COUNT(*)".$mostRoadsAdd." AS Trasy FROM routes AS r WHERE (Id < 42 OR Id > 43) AND Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' GROUP BY Login ORDER BY Trasy DESC LIMIT 1;"));
						$mostRoadsProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = \"$mostRoads[0]\""));
					}

					echo "<div class='general_stats_block'>
							<h1><span class='far fa-calendar-alt fa-fw'></span> $dateFrom - <span class='far fa-calendar-alt fa-fw'></span> $dateTo</h1>
							<div class='general_stats_grid'>
								<div class='general_stats_company_grid'>
									<div class='general_stats_company'>
										<h2><span class='fas fa-truck fa-xs'></span>FIRMA</h2>
										<div class='general_stats_company_block'>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-road'></span>
												<h3>PRZEJECHALIŚMY</h3>
												<span class='general_stats_data'>".number_format($distanceSum[0], 0, ',', ' ')." km</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-clipboard'></span>
												<h3>WYKONALIŚMY</h3>
												<span class='general_stats_data'>".number_format($roadsSum[0], 0, ',', ' ')." zleceń</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-truck'></span>
												<h3>PRZEWIEŹLIŚMY</h3>
												<span class='general_stats_data'>".number_format($tonnageSum[0], 0, ',', ' ')." ton</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon far fa-money-bill-alt'></span>
												<h3>ZAROBILIŚMY</h3>
												<span class='general_stats_data'>".number_format($moneySum[0], 2, ',', ' ')." zł</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon far fas fa-fire'></span>
												<h3>ZUŻYLIŚMY</h3>
												<span class='general_stats_data'>".number_format($fuelSum[0], 1, '.', ' ')." l ropy</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-chart-line'></span>
												<h3>ŚREDNIE SPALANIE</h3>
												<span class='general_stats_data'>".number_format($fuelAvg, 1, '.', ' ')." l/100km</span>
											</div>
										</div>
									</div>
									<div class='general_stats_company_together'>
										<h2><span class='fas fa-truck fa-xs'></span>FIRMA ŁĄCZNIE</h2>
										<div class='general_stats_company_block'>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-road'></span>
												<h3>PRZEJECHALIŚMY</h3>
												<span class='general_stats_data'>".number_format($distanceSumAll[0], 0, ',', ' ')." km</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-clipboard'></span>
												<h3>WYKONALIŚMY</h3>
												<span class='general_stats_data'>".number_format($roadsSumAll[0], 0, ',', ' ')." zleceń</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-truck'></span>
												<h3>PRZEWIEŹLIŚMY</h3>
												<span class='general_stats_data'>".number_format($tonnageSumAll[0], 0, ',', ' ')." ton</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon far fa-money-bill-alt'></span>
												<h3>ZAROBILIŚMY</h3>
												<span class='general_stats_data'>".number_format($moneySumAll[0], 2, ',', ' ')." zł</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon far fas fa-fire'></span>
												<h3>ZUŻYLIŚMY</h3>
												<span class='general_stats_data'>".number_format($fuelSumAll[0], 1, '.', ' ')." l ropy</span>
											</div>
											<div class='general_stats_company_block_single'>
												<span class='general_stats_icon fas fa-chart-line'></span>
												<h3>ŚREDNIE SPALANIE</h3>
												<span class='general_stats_data'>".number_format($fuelAvgAll, 1, '.', ' ')." l/100km</span>
											</div>
										</div>
									</div>
								</div>
								<div class='general_stats_driver'>
									<h2><span class='fas fa-users fa-fw'></span>KIEROWCY</h2>
									<div class='general_stats_driver_block'>
										<div class='general_stats_driver_block_single'>
											<h3>NAJDŁUŻSZA TRASA</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($longestRoadProfile[0]){echo $longestRoadProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$longestRoad[1]</span><br />
											<span class='general_stats_data'>".number_format($longestRoad[0], 0, ',', ' ')." km</span>
										</div>
										<div class='general_stats_driver_block_single'>
											<h3>NAJWIĘKSZY PRZEBIEG</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($mostDistanceSumProfile[0]){echo $mostDistanceSumProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$mostDistanceSum[0]</span><br />
											<span class='general_stats_data'>".number_format($mostDistanceSum[1], 0, ',', ' ')." km</span>
										</div>
										<div class='general_stats_driver_block_single'>
											<h3>NAJWIĘCEJ TRAS</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($mostRoadsProfile[0]){echo $mostRoadsProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$mostRoads[0]</span><br />
											<span class='general_stats_data'>".number_format($mostRoads[1], 0, ',', ' ')."</span>
										</div>
										<div class='general_stats_driver_block_single'>
											<h3>NAJCIĘŻSZY TOWAR</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($mostTonnageProfile[0]){echo $mostTonnageProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$mostTonnage[0]</span><br />
											<span class='general_stats_data'>".number_format($mostTonnage[1], 0, ',', ' ')." t</span>
										</div>
										<div class='general_stats_driver_block_single'>
											<h3>NAJNIŻSZE SPALANIE</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($leastFuelConsumptionProfile[0]){echo $leastFuelConsumptionProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$leastFuelConsumption[0]</span><br />
											<span class='general_stats_data'>".number_format($leastFuelConsumption[1], 1, '.', ' ')." l/100km</span>
										</div>
										<div class='general_stats_driver_block_single'>
											<h3>NAJWIĘKSZY ZAROBEK</h3>
											<span class='general_stats_profile_img'><img src='img/avatars/";if($mostMoneyProfile[0]){echo $mostMoneyProfile[0];}else{echo "no_avatar.jpg";}echo "' /></span><br />
											<span class='general_stats_profile_name'>$mostMoney[0]</span><br />
											<span class='general_stats_data'>".number_format($mostMoney[1], 2, ',', ' ')." zł</span>
										</div>
									</div>
								</div>
							</div>
						</div>";
				}
				if($type == 'detailed'){
					echo "<div class='detailed_stats_block'>
							<h1><span class='far fa-calendar-alt fa-fw'></span> $dateFrom - <span class='far fa-calendar-alt fa-fw'></span> $dateTo</h1>
							<div class='detailed_stats_grid'>
								<table class='sortable'>
									<thead>
										<tr>
											<th>Nazwa użytkownika</th>
											<th>Stanowisko</th>
											<th onclick='sortTable(0)'>Przebieg (km)</th>
											<th onclick='sortTable(1)'>Liczba tras</th>
											<th onclick='sortTable(2)'>Najdłuższa trasa (km)</th>
											<th onclick='sortTable(3)'>Spalanie</th>
											<th onclick='sortTable(4)'>Masa towarów (t)</th>
											<th onclick='sortTable(5)'>Zużyte paliwo (l)</th>
											<th onclick='sortTable(6)'>Zarobek (zł)</th>
										</tr>
									</thead>
									<tbody>";
										if($game == 'all'){
											$statsUsers = mysqli_query($conn, "SELECT *, (SELECT SUM(r.Distance) FROM routes AS r WHERE u.Login = r.Login AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona') AS 'MaxKm' FROM users AS u WHERE Id != 35 AND Rank != \"Programista\" ORDER BY MaxKm DESC;");
											while($user = mysqli_fetch_array($statsUsers)){

												$userDistance = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if($dateFrom < "2018-03-19"){
													$userRoutesAdd = "+((SELECT AddedRoutesEts2 FROM users AS u WHERE r.Login = u.Login)+(SELECT AddedRoutesAts FROM users AS u WHERE r.Login = u.Login))";
												}
												else {
													$userRoutesAdd = "";
												}
												$userRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)".$userRoutesAdd." FROM routes AS r WHERE Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												$userLongestRoute = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance FROM routes WHERE (Id < 3 OR Id > 43) AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
												$userTonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												$userFuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if(intval($userDistance[0]) >= 1){
													$userAvgFuelConsumption = (floatval($userFuelSum[0]) / intval($userDistance[0])) * 100;
												}
												else{
													$userAvgFuelConsumption = 0.0;
												}
												$userMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));

												switch ($user['Rank']) {
													case 'Programista':
														$rankClass = "programista";
														break;
													case 'Właściciel':
														$rankClass = "wlasciciel";
														break;
													case 'Prezes':
														$rankClass = "prezes";
														break;
													case 'Dyspozytor':
														$rankClass = "dyspozytor";
														break;
													case 'Rekruter':
														$rankClass = "rekruter";
														break;
													case 'VIP':
														$rankClass = "vip";
														break;
													case 'Spedytor':
														$rankClass = "spedytor";
														break;
													case 'Kierowca':
														$rankClass = "kierowca";
														break;
													case 'Okres próbny':
														$rankClass = "okres_probny";
														break;
												}

												echo "<tr>
														<td style='float: left;'><img class='drivers_profile_img' src='img/avatars/".$user['ImgUrl']."' />".$user['Login']."</td>
														<td><span class='rank_class ".$rankClass."'>".$user['Rank']."</span></td>
														<td>".number_format($userDistance[0], 0, ',', ' ')."</td>
														<td>".number_format($userRoutes[0], 0, ',', ' ')."</td>
														<td>".number_format($userLongestRoute[0], 0, ',', ' ')."</td>
														<td>".number_format($userAvgFuelConsumption, 1, '.', ' ')." l/100km</td>
														<td>".number_format($userTonnageSum[0], 0, ',', ' ')."</td>
														<td>".number_format($userFuelSum[0], 1, '.', ' ')."</td>
														<td>".number_format($userMoney[0], 2, ',', ' ')."</td>
													</tr>";
											}
										}
										if($game == 'ets2'){
											$statsUsers = mysqli_query($conn, "SELECT *, (SELECT SUM(r.Distance) FROM routes AS r WHERE Game = \"ets2\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' AND u.Login = r.Login) AS 'MaxKm' FROM users AS u WHERE Id != 35 AND Rank != \"Programista\" ORDER BY MaxKm DESC;");
											while($user = mysqli_fetch_array($statsUsers)){

												$userDistance = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Game = \"ets2\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if($dateFrom < "2018-03-19"){
													$userRoutesAdd = "+(SELECT AddedRoutesEts2 FROM users AS u WHERE u.Login = r.Login)";
												}
												else {
													$userRoutesAdd = "";
												}
												$userRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT (SELECT COUNT(*) FROM routes AS r WHERE Status = 'Zatwierdzona' AND Game = 'ets2' AND Login = '".$user['Login']."')".$userRoutesAdd." FROM routes AS r WHERE Game = \"ets2\" AND Status = 'Zatwierdzona' AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\";"));
												$userLongestRoute = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ets2\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
												$userTonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Game = \"ets2\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												$userFuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Game = \"ets2\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if(intval($userDistance[0]) >= 1){
													$userAvgFuelConsumption = (floatval($userFuelSum[0]) / intval($userDistance[0])) * 100;
												}
												else{
													$userAvgFuelConsumption = 0.0;
												}
												$userMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Game = \"ets2\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));

												switch ($user['Rank']) {
													case 'Programista':
														$rankClass = "programista";
														break;
													case 'Właściciel':
														$rankClass = "wlasciciel";
														break;
													case 'Prezes':
														$rankClass = "prezes";
														break;
													case 'Dyspozytor':
														$rankClass = "dyspozytor";
														break;
													case 'Rekruter':
														$rankClass = "rekruter";
														break;
													case 'VIP':
														$rankClass = "vip";
														break;
													case 'Spedytor':
														$rankClass = "spedytor";
														break;
													case 'Kierowca':
														$rankClass = "kierowca";
														break;
													case 'Okres próbny':
														$rankClass = "okres_probny";
														break;
												}

												echo "<tr>
														<td style='float: left;'><img class='drivers_profile_img' src='img/avatars/".$user['ImgUrl']."' />".$user['Login']."</td>
														<td><span class='rank_class ".$rankClass."'>".$user['Rank']."</span></td>
														<td>".number_format($userDistance[0], 0, ',', ' ')."</td>
														<td>".number_format($userRoutes[0], 0, ',', ' ')."</td>
														<td>".number_format($userLongestRoute[0], 0, ',', ' ')."</td>
														<td>".number_format($userAvgFuelConsumption, 1, '.', ' ')." l/100km</td>
														<td>".number_format($userTonnageSum[0], 0, ',', ' ')."</td>
														<td>".number_format($userFuelSum[0], 1, '.', ' ')."</td>
														<td>".number_format($userMoney[0], 2, ',', ' ')."</td>
													</tr>";
											}
										}
										if($game == 'ats'){
											$statsUsers = mysqli_query($conn, "SELECT *, (SELECT SUM(r.Distance) FROM routes AS r WHERE Game = \"ats\" AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' AND u.Login = r.Login) AS 'MaxKm' FROM users AS u WHERE Id != 35 AND Rank != \"Programista\" ORDER BY MaxKm DESC;");
											while($user = mysqli_fetch_array($statsUsers)){

												$userDistance = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Game = \"ats\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if($dateFrom < "2018-03-19"){
													$userRoutesAdd = "+(SELECT AddedRoutesAts FROM users AS u WHERE u.Login = r.Login)";
												}
												else {
													$userRoutesAdd = "";
												}
												$userRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT (SELECT COUNT(*) FROM routes AS r WHERE Game = 'ats' AND Login = '".$user['Login']."')".$userRoutesAdd." FROM routes AS r WHERE Game = \"ats\" AND Login = '".$user['Login']."' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\";"));
												$userLongestRoute = mysqli_fetch_row(mysqli_query($conn, "SELECT Distance FROM routes WHERE (Id < 3 OR Id > 43) AND Game = \"ats\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona' ORDER BY Distance DESC LIMIT 1;"));
												$userTonnageSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Tonnage) FROM routes WHERE Game = \"ats\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												$userFuelSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Game = \"ats\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));
												if(intval($userDistance[0]) >= 1){
													$userAvgFuelConsumption = (floatval($userFuelSum[0]) / intval($userDistance[0])) * 100;
												}
												else{
													$userAvgFuelConsumption = 0.0;
												}
												$userMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Game = \"ats\" AND Login = '".$user['Login']."' AND DATE(DateAndTime) >= \"$dateFrom\" AND DATE(DateAndTime) <= \"$dateTo\" AND Status = 'Zatwierdzona';"));

												switch ($user['Rank']) {
													case 'Programista':
														$rankClass = "programista";
														break;
													case 'Właściciel':
														$rankClass = "wlasciciel";
														break;
													case 'Prezes':
														$rankClass = "prezes";
														break;
													case 'Dyspozytor':
														$rankClass = "dyspozytor";
														break;
													case 'Rekruter':
														$rankClass = "rekruter";
														break;
													case 'VIP':
														$rankClass = "vip";
														break;
													case 'Spedytor':
														$rankClass = "spedytor";
														break;
													case 'Kierowca':
														$rankClass = "kierowca";
														break;
													case 'Okres próbny':
														$rankClass = "okres_probny";
														break;
												}

												echo "<tr>
														<td style='float: left;'><img class='drivers_profile_img' src='img/avatars/".$user['ImgUrl']."' />".$user['Login']."</td>
														<td><span class='rank_class ".$rankClass."'>".$user['Rank']."</span></td>
														<td>".number_format($userDistance[0], 0, ',', ' ')."</td>
														<td>".number_format($userRoutes[0], 0, ',', ' ')."</td>
														<td>".number_format($userLongestRoute[0], 0, ',', ' ')."</td>
														<td>".number_format($userAvgFuelConsumption, 1, '.', ' ')." l/100km</td>
														<td>".number_format($userTonnageSum[0], 0, ',', ' ')."</td>
														<td>".number_format($userFuelSum[0], 1, '.', ' ')."</td>
														<td>".number_format($userMoney[0], 2, ',', ' ')."</td>
													</tr>";
											}
										}
								echo "</tbody>
								</table>
							</div>
						</div>";
				}
			?>
		</section>
		<script type="text/javascript">
			if(document.querySelector(".number_routes_to_check") != null){
				var numberRoutesToCheck = document.querySelector(".number_routes_to_check");
				numberRoutesToCheck = parseInt(numberRoutesToCheck.innerHTML);
				if(numberRoutesToCheck > 9){
					document.querySelector(".number_routes_to_check").className += " two_digits";
				}
			}
		</script>
		<script src="js/sorttable.js"></script>
		<?php mysqli_close($conn); ?>
	</body>
</html>