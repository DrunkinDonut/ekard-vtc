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
		<title>Strona główna - EKARD VTC</title>
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
					<a href="index" title="Panel kierowcy" class="nav_item nav_active"><li>Panel kierowcy</li></a>
					<a href="drivers" title="Lista kierowców" class="nav_item"><li>Kierowcy</li></a>
					<a href="stats" title="Statystyki" class="nav_item"><li>Statystyki</li></a>
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
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					$opType = test_input($_POST['type']);
					$opId = test_input($_POST['id']);

					if($opType == 'discard'){
						mysqli_query($conn, "UPDATE routes SET UserAccept = 1 WHERE Id = '$opId';");
					}
					else{
						if($opType == 'correct'){
							$addrId = $opId;
							header("Location: correct_route?id=$addrId");
						}
					}
				}

				$toCorrect = mysqli_query($conn, "SELECT * FROM routes WHERE Login = '$login' AND Status = 'Do poprawy';");
				$Discarded = mysqli_query($conn, "SELECT * FROM routes WHERE Login = '$login' AND Status = 'Odrzucona' AND UserAccept = 0;");

				while($correct = mysqli_fetch_array($toCorrect)){
					$dyspozytorId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = '".$correct['CheckedBy']."';"));
					echo "<form action='index' method='POST'><input type='text' name='type' value='correct' hidden /><input type='text' name='id' value='".$correct['Id']."' hidden /><div class='index_information_correct'>Dyspozytor <a href='profile?id=".$dyspozytorId['Id']."' class='profile_link' style='color: #fff !important; font-weight: normal; font-family: \"Roboto-medium\", sans-serif;'>".$correct['CheckedBy']."</a> wysłał Twoją trasę do poprawy. Powód:<br />
						<span class='index_information_reason'>".$correct['Reason']."</span><br />
						<button type='submit' title='Popraw trasę'><span class='far fa-edit'></span>Popraw trasę</button></div></form>";
				}
				while($discard = mysqli_fetch_array($Discarded)){
					$dyspozytorId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = '".$discard['CheckedBy']."';"));
					echo "<form action='index' method='POST'><input type='text' name='type' value='discard' hidden /><input type='text' name='id' value='".$discard['Id']."' hidden /><div class='index_information_discard'>Dyspozytor <a href='profile?id=".$dyspozytorId['Id']."' class='profile_link' style='color: #fff !important; font-weight: normal; font-family: \"Roboto-medium\", sans-serif;'>".$discard['CheckedBy']."</a> odrzucił Twoją trasę. Powód:<br />
						<span class='index_information_reason'>".$discard['Reason']."</span><br />
						<a href='route?id=".$discard['Id']."' class='route_btn' title='Zobacz trasę'><span class='fas fa-search fa-fw'></span><span>Zobacz trasę</span></a>
						<button type='submit' title='Akceptuj'><span class='fas fa-check fa-fw'></span>Akceptuj</button></div></form>";
				}
			?>
			<div class="block logs">
				<h1>Aktualności</h1>
				<div class="block_content">
					<?php
						$logs = mysqli_query($conn, "SELECT * FROM (SELECT * FROM logs ORDER BY Id DESC LIMIT 5) sub ORDER BY Id DESC;");
						while($wiersz = mysqli_fetch_row($logs)){
							$userId = mysqli_fetch_row(mysqli_query($conn, "SELECT Id FROM users WHERE Login = '$wiersz[2]';"));
							$dateTime = explode(" ", $wiersz[1]);
							echo "<span class='far fa-calendar-alt fa-fw'></span> $dateTime[0] | <span class='far fa-clock fa-sm'></span>&nbsp;$dateTime[1] - Użytkownik <a class='profile_link' href=\"profile?id=".$userId[0]."\"><b>$wiersz[2]</b></a> $wiersz[3]<br />";
						}
					?>
				</div>
			</div>
			<?php
				$joinedDate = mysqli_fetch_row(mysqli_query($conn, "SELECT Joined FROM users WHERE Login = '$login';"));
				$statsFrom = $joinedDate[0];
				$earnedMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Login = '$login' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$joinedDate = date_create(date("Y-m-d", strtotime($joinedDate[0])));
				$today = date_create(date("Y-m-d"));
				$timeDiff = date_diff($today, $joinedDate);
				$numberDays = $timeDiff->days;
				$allDistance = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$login' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$login' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$avgFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$login' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesEts2 + AddedRoutesAts FROM users WHERE Login = '$login') FROM routes WHERE Login = '$login' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allDistanceEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$login' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allFuelConsumptionEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$login' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$avgFuelConsumptionEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$login' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allRoutesEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesEts2 FROM users WHERE Login = '$login') FROM routes WHERE Login = '$login' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allDistanceAts = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$login' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allFuelConsumptionAts = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$login' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$avgFuelConsumptionAts = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$login' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));
				$allRoutesAts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesAts FROM users WHERE Login = '$login') FROM routes WHERE Login = '$login' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";;"));

				$lastSunday = date("Y-m-d", strtotime("sunday last week"));
				if(date("Y-m-d") == $lastSunday){
					$thisMonday = date("Y-m-d", strtotime("monday last week"));
					$thisSunday = date("Y-m-d", strtotime("sunday last week"));
				}
				else {
					$thisMonday = date("Y-m-d", strtotime("monday this week"));
					$thisSunday = date("Y-m-d", strtotime("sunday this week"));
				}
			?>
			<div class="index_stats_top_group">
				<div class="index_stats_top_block">
					<h2>TOP 3 NAJNIŻSZE SPALANIE | TYDZIEŃ</h2>
					<div class="index_stats_top_table">
						<?php
							$lowestFuelConsumption = mysqli_query($conn, "SELECT DISTINCT Login, ((SELECT SUM(Fuel) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND Status = 'Zatwierdzona') / (SELECT SUM(Distance) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND Status = 'Zatwierdzona') * 100) AS Spalanie FROM routes AS r1 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND Status = 'Zatwierdzona' AND (SELECT SUM(Distance) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND Status = 'Zatwierdzona') >= 5000 GROUP BY Login ORDER BY Spalanie ASC LIMIT 3;");

							if(mysqli_num_rows($lowestFuelConsumption) >= 1){
								$i = 1;
								while($driverlowestFuelConsumption = mysqli_fetch_array($lowestFuelConsumption)){
									$lowestFuelConsumptionProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = '".$driverlowestFuelConsumption["Login"]."';"));
									echo '<div class="index_stats_top_row">
											<div class="index_stats_top_login">
												'.$i.'.';if($lowestFuelConsumptionProfile[0] != ""){echo "<img class='index_profile_img' src='img/avatars/".$lowestFuelConsumptionProfile[0]."' />";}else{echo "<img class='index_profile_img' src='img/avatars/no_avatar.jpg' />";} echo $driverlowestFuelConsumption["Login"].'
											</div>
											<div class="index_stats_top_value">
												'.number_format($driverlowestFuelConsumption["Spalanie"], 1, '.', ' ').' l/100km
											</div>
										</div>';
									$i++;
								}
							}
							else{
								echo "<div class='index_stats_top_msg'>Żaden kierowca nie przejechał w tym tygodniu ponad 5&nbsp;000km.</div>";
							}
						?>
						<h3>Dla przebiegu powyżej 5&nbsp;000km</h3>
					</div>
				</div>
				<div class="index_stats_top_block">
					<h2>TOP 3 PRZEBIEGI | TYDZIEŃ</h2>
					<div class="index_stats_top_table">
						<?php
							$topDistance = mysqli_query($conn, "SELECT DISTINCT Login, (SELECT SUM(Distance) FROM routes AS r1 WHERE r1.Login = r2.Login AND Status = 'Zatwierdzona' AND (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday') AS Dystans FROM routes AS r2 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$thisMonday' AND DATE(DateAndTime) <= '$thisSunday' AND (SELECT SUM(Distance) FROM routes AS r1 WHERE r1.Login = r2.Login AND Status = 'Zatwierdzona') >= 5000 AND Status = 'Zatwierdzona' ORDER BY Dystans DESC LIMIT 3");

							if(mysqli_num_rows($topDistance) >= 1){
								$i = 1;
								while($drivertopDistance = mysqli_fetch_array($topDistance)){
									$drivertopDistanceProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = '".$drivertopDistance["Login"]."';"));
									echo '<div class="index_stats_top_row">
											<div class="index_stats_top_login">
												'.$i.'.';if($drivertopDistanceProfile[0] != ""){echo "<img class='index_profile_img' src='img/avatars/".$drivertopDistanceProfile[0]."' />";}else{echo "<img class='index_profile_img' src='img/avatars/no_avatar.jpg' />";} echo $drivertopDistance["Login"].'
											</div>
											<div class="index_stats_top_value">
												'.number_format($drivertopDistance["Dystans"], 0, ',', ' ').' km
											</div>
										</div>';
									$i++;
								}
							}
							else{
								echo "<div class='index_stats_top_msg'>Żaden kierowca nie przejechał w tym tygodniu ponad 5&nbsp;000km.</div>";
							}
						?>
						<h3>&nbsp;</h3>
					</div>
				</div>
				<div class="index_stats_top_block">
					<h2>TOP 3 NAJNIŻSZE SPALANIE | MIESIĄC</h2>
					<div class="index_stats_top_table">
						<?php
							$firstDayOfMonth = date("Y-m-d", strtotime("first day of this Month"));
							$lastDayOfMonth = date("Y-m-d", strtotime("last day of this Month"));
							$lowestFuelConsumptionMonth = mysqli_query($conn, "SELECT DISTINCT Login, ((SELECT SUM(Fuel) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona') / (SELECT SUM(Distance) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona') * 100) AS Spalanie FROM routes AS r1 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona' AND (SELECT SUM(Distance) FROM routes AS r2 WHERE r1.Login = r2.Login AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona') >= 20000 GROUP BY Login ORDER BY Spalanie ASC LIMIT 3;");

							if(mysqli_num_rows($lowestFuelConsumptionMonth) >= 1){
								$i = 1;
								while($driverlowestFuelConsumptionMonth = mysqli_fetch_array($lowestFuelConsumptionMonth)){
									$lowestFuelConsumptionMonthProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = '".$driverlowestFuelConsumptionMonth["Login"]."';"));
									echo '<div class="index_stats_top_row">
											<div class="index_stats_top_login">
												'.$i.'.';if($lowestFuelConsumptionMonthProfile[0] != ""){echo "<img class='index_profile_img' src='img/avatars/".$lowestFuelConsumptionMonthProfile[0]."' />";}else{echo "<img class='index_profile_img' src='img/avatars/no_avatar.jpg' />";} echo $driverlowestFuelConsumptionMonth["Login"].'
											</div>
											<div class="index_stats_top_value">
												'.number_format($driverlowestFuelConsumptionMonth["Spalanie"], 1, '.', ' ').' l/100km
											</div>
										</div>';
									$i++;
								}
							}
							else{
								echo "<div class='index_stats_top_msg'>Żaden kierowca nie przejechał w tym miesiącu ponad 20&nbsp;000km.</div>";
							}
						?>
						<h3>Dla przebiegu powyżej 20&nbsp;000km</h3>
					</div>
				</div>
				<div class="index_stats_top_block">
					<h2>TOP 3 PRZEBIEGI | MIESIĄC</h2>
					<div class="index_stats_top_table">
						<?php
							$topDistanceMonth = mysqli_query($conn, "SELECT DISTINCT Login, (SELECT SUM(Distance) FROM routes AS r1 WHERE r1.Login = r2.Login AND Status = 'Zatwierdzona' AND (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth') AS Dystans FROM routes AS r2 WHERE (Id < 42 OR Id > 43) AND DATE(DateAndTime) >= '$firstDayOfMonth' AND DATE(DateAndTime) <= '$lastDayOfMonth' AND Status = 'Zatwierdzona' ORDER BY Dystans DESC LIMIT 3");

							if(mysqli_num_rows($topDistanceMonth) >= 1){
								$i = 1;
								while($drivertopDistanceMonth = mysqli_fetch_array($topDistanceMonth)){
									$drivertopDistanceMonthProfile = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = '".$drivertopDistanceMonth["Login"]."';"));
									echo '<div class="index_stats_top_row">
											<div class="index_stats_top_login">
												'.$i.'.';if($drivertopDistanceMonthProfile[0] != ""){echo "<img class='index_profile_img' src='img/avatars/".$drivertopDistanceMonthProfile[0]."' />";}else{echo "<img class='index_profile_img' src='img/avatars/no_avatar.jpg' />";} echo $drivertopDistanceMonth["Login"].'
											</div>
											<div class="index_stats_top_value">
												'.number_format($drivertopDistanceMonth["Dystans"], 0, ',', ' ').' km
											</div>
										</div>';
									$i++;
								}
							}
							else{
								echo "<div class='index_stats_top_msg'>Żaden kierowca nie przejechał w tym miesiącu ponad 20&nbsp;000km.</div>";
							}
						?>
						<h3>&nbsp;</h3>
					</div>
				</div>
			</div>
			<h1 class="index_mystats">MOJE STATYSTYKI</h1>
			<div class="index_stats_group_two">
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($earnedMoney[0] >= 0.1){ echo number_format($earnedMoney[0], 2, ',', ' ')." zł"; }else{ echo "0 zł"; } ?>
					</div>
					<div class="index_stats_label">
						ZAROBIONA GOTÓWKA
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($numberDays >= 1){ echo number_format($numberDays, 0, ',', ' ')." dni"; }else{ echo "0 dni"; } ?>
					</div>
					<div class="index_stats_label">
						STAŻ W FIRMIE
					</div>
				</div>
			</div>
			<div class="index_stats_group">
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allDistance[0] >= 1){ echo number_format($allDistance[0], 0, ',', ' ')." km"; }else{ echo "0 km"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANY DYSTANS | ŁĄCZNIE
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allFuelConsumption[0] >= 0.1){ echo number_format($allFuelConsumption[0], 1, '.', ' ')." l"; }else{ echo "0 l"; } ?>
					</div>
					<div class="index_stats_label">
						ZUŻYTE PALIWO | ŁĄCZNIE
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($avgFuelConsumption[0] >= 0.1){ echo number_format($avgFuelConsumption[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; } ?>
					</div>
					<div class="index_stats_label">
						ŚREDNIE ZUŻYCIE PALIWA | ŁĄCZNIE
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allRoutes[0] >= 1){ echo number_format($allRoutes[0], 0, ',', ' ').""; }else{ echo "0"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANE TRASY | ŁĄCZNIE
					</div>
				</div>
			</div>
			<div class="index_stats_group">
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allDistanceEts2[0] >= 1){ echo number_format($allDistanceEts2[0], 0, ',', ' ')." km"; }else{ echo "0 km"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANY DYSTANS | EURO TRUCK SIMULATOR 2
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allFuelConsumptionEts2[0] >= 0.1){ echo number_format($allFuelConsumptionEts2[0], 1, '.', ' ')." l"; }else{ echo "0 l"; } ?>
					</div>
					<div class="index_stats_label">
						ZUŻYTE PALIWO | EURO TRUCK SIMULATOR 2
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($avgFuelConsumptionEts2[0] >= 0.1){ echo number_format($avgFuelConsumptionEts2[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; } ?>
					</div>
					<div class="index_stats_label">
						ŚREDNIE ZUŻYCIE PALIWA | EURO TRUCK SIMULATOR 2
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allRoutesEts2[0] >= 1){ echo number_format($allRoutesEts2[0], 0, ',', ' ').""; }else{ echo "0"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANE TRASY | EURO TRUCK SIMULATOR 2
					</div>
				</div>
			</div>
			<div class="index_stats_group">
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allDistanceAts[0] >= 1){ echo number_format($allDistanceAts[0], 0, ',', ' ')." km"; }else{ echo "0 km"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANY DYSTANS | AMERICAN TRUCK SIMULATOR
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allFuelConsumptionAts[0] >= 0.1){ echo number_format($allFuelConsumptionAts[0], 1, '.', ' ')." l"; }else{ echo "0 l"; } ?>
					</div>
					<div class="index_stats_label">
						ZUŻYTE PALIWO | AMERICAN TRUCK SIMULATOR
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($avgFuelConsumptionAts[0] >= 0.1){ echo number_format($avgFuelConsumptionAts[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; } ?>
					</div>
					<div class="index_stats_label">
						ŚREDNIE ZUŻYCIE PALIWA | AMERICAN TRUCK SIMULATOR
					</div>
				</div>
				<div class="index_stats_block">
					<div class="index_stats_value">
						<?php if($allRoutesAts[0] >= 1){ echo number_format($allRoutesAts[0], 0, ',', ' ').""; }else{ echo "0"; } ?>
					</div>
					<div class="index_stats_label">
						PRZEJECHANE TRASY | AMERICAN TRUCK SIMULATOR
					</div>
				</div>
			</div>
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
		<?php mysqli_close($conn); ?>
	</body>
</html>