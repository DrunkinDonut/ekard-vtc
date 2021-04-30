<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);

	$login = $_SESSION["Login"];
	$profileId = $_GET["id"];
	$profileName = mysqli_fetch_row(mysqli_query($conn, "SELECT Login FROM users WHERE Id = '$profileId';"));
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?php
			require_once("head.html");
		?>
		<title><?php echo $profileName[0]; ?> - Profil użytkownika - EKARD VTC</title>

	</head>
	<body>
		<?php require_once("header.php"); ?>
		<script type="text/javascript">
			var profil = document.querySelector("#profile");
			profil.className += " user_nav_icon_active";
		</script>
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
					<a href="stats" title="Statystyki" class="nav_item"><li>Statystyki</li></a>
				</ul>
			</div>
			<?php
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
				if($profileId == ""){
					header("Location: index");
				}
				else{
					$userLogin = mysqli_fetch_row(mysqli_query($conn, "SELECT Login FROM users WHERE Id = '$profileId';"));
					$userLogin = $userLogin[0];
					$joinedDate = mysqli_fetch_row(mysqli_query($conn, "SELECT Joined FROM users WHERE Login = '$userLogin';"));
					$statsFrom = $joinedDate[0];
					$earnedMoney = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(EarnedMoney) FROM routes WHERE Login = '$userLogin' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$joinedDate = date_create(date("Y-m-d", strtotime($joinedDate[0])));
					$today = date_create(date("Y-m-d"));
					$timeDiff = date_diff($today, $joinedDate);
					$numberDays = $timeDiff->days;
					$allDistance = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$userLogin' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$userLogin' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$avgFuelConsumption = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$userLogin' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesEts2 + AddedRoutesAts FROM users WHERE Login = '$userLogin') FROM routes WHERE Login = '$userLogin' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allDistanceEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$userLogin' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allFuelConsumptionEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$userLogin' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$avgFuelConsumptionEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$userLogin' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allRoutesEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesEts2 FROM users WHERE Login = '$userLogin') FROM routes WHERE Login = '$userLogin' AND Game = 'ets2' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allDistanceAts = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '$userLogin' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allFuelConsumptionAts = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Fuel) FROM routes WHERE Login = '$userLogin' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$avgFuelConsumptionAts = mysqli_fetch_row(mysqli_query($conn, "SELECT (SUM(Fuel) / SUM(Distance)) * 100 FROM routes WHERE Login = '$userLogin' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
					$allRoutesAts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*)+(SELECT AddedRoutesAts FROM users WHERE Login = '$userLogin') FROM routes WHERE Login = '$userLogin' AND Game = 'ats' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
				}

				$userRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Id = '$profileId';"));
				$userRank = $userRank[0];

				if($userRank == "Programista"){
					echo '<div style="background-color: #7f8c8d; margin: auto; text-shadow: 0 1px 2px rgba(0,0,0,0.20); text-align: center; width: 400px; height: 100px; display: flex; justify-content: center; align-items: center; border-radius: 10px;"><img src="img/programmer.png" alt="Programista" style="vertical-align: bottom; height: 70px; width: 70px;" /><span style="font-family: Roboto-light, sans-serif; font-size: 50px; color: #fff; line-height: 70px; margin-left: 20px;">Programista</span></div>';
				}
				else {
					echo '<div class="index_stats_group_two">
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($earnedMoney[0] >= 0.1){ echo number_format($earnedMoney[0], 2, ',', ' ')." zł"; }else{ echo "0 zł"; }
								echo '</div>
								<div class="index_stats_label">
									ZAROBIONA GOTÓWKA | ŁĄCZNIE
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($numberDays >= 1){ echo number_format($numberDays, 0, ',', ' ')." dni"; }else{ echo "0 dni"; }
								echo '</div>
								<div class="index_stats_label">
									STAŻ W FIRMIE
								</div>
							</div>
						</div>
						<div class="index_stats_group">
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allDistance[0] >= 1){ echo number_format($allDistance[0], 0, ',', ' ')." km"; }else{ echo "0 km"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANY DYSTANS | ŁĄCZNIE
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allFuelConsumption[0] >= 0.1){ echo number_format($allFuelConsumption[0], 1, '.', ' ')." l"; }else{ echo "0 l"; }
								echo '</div>
								<div class="index_stats_label">
									ZUŻYTE PALIWO | ŁĄCZNIE
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($avgFuelConsumption[0] >= 0.1){ echo number_format($avgFuelConsumption[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; }
								echo '</div>
								<div class="index_stats_label">
									ŚREDNIE ZUŻYCIE PALIWA | ŁĄCZNIE
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allRoutes[0] >= 1){ echo number_format($allRoutes[0], 0, ',', ' ').""; }else{ echo "0"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANE TRASY | ŁĄCZNIE
								</div>
							</div>
						</div>
						<div class="index_stats_group">
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allDistanceEts2[0] >= 1){ echo number_format($allDistanceEts2[0], 0, ',', ' ')." km"; }else{ echo "0 km"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANY DYSTANS | EURO TRUCK SIMULATOR 2
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allFuelConsumptionEts2[0] >= 0.1){ echo number_format($allFuelConsumptionEts2[0], 1, '.', ' ')." l"; }else{ echo "0 l"; }
								echo '</div>
								<div class="index_stats_label">
									ZUŻYTE PALIWO | EURO TRUCK SIMULATOR 2
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($avgFuelConsumptionEts2[0] >= 0.1){ echo number_format($avgFuelConsumptionEts2[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; }
								echo '</div>
								<div class="index_stats_label">
									ŚREDNIE ZUŻYCIE PALIWA | EURO TRUCK SIMULATOR 2
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allRoutesEts2[0] >= 1){ echo number_format($allRoutesEts2[0], 0, ',', ' ').""; }else{ echo "0"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANE TRASY | EURO TRUCK SIMULATOR 2
								</div>
							</div>
						</div>
						<div class="index_stats_group">
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allDistanceAts[0] >= 1){ echo number_format($allDistanceAts[0], 0, ',', ' ')." km"; }else{ echo "0 km"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANY DYSTANS | AMERICAN TRUCK SIMULATOR
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allFuelConsumptionAts[0] >= 0.1){ echo number_format($allFuelConsumptionAts[0], 1, '.', ' ')." l"; }else{ echo "0 l"; }
								echo '</div>
								<div class="index_stats_label">
									ZUŻYTE PALIWO | AMERICAN TRUCK SIMULATOR
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($avgFuelConsumptionAts[0] >= 0.1){ echo number_format($avgFuelConsumptionAts[0], 1, '.', ' ')." l/100km"; }else{ echo "0 l/100km"; }
								echo '</div>
								<div class="index_stats_label">
									ŚREDNIE ZUŻYCIE PALIWA | AMERICAN TRUCK SIMULATOR
								</div>
							</div>
							<div class="index_stats_block">
								<div class="index_stats_value">
									'; if($allRoutesAts[0] >= 1){ echo number_format($allRoutesAts[0], 0, ',', ' ').""; }else{ echo "0"; }
								echo '</div>
								<div class="index_stats_label">
									PRZEJECHANE TRASY | AMERICAN TRUCK SIMULATOR
								</div>
							</div>
						</div>
						<div class="user_routes">
							<h1>Trasy użytkownika</h1>
							<div class="block_content">
								<div class="profile_routes_table">
										'; $userRoutes = mysqli_query($conn, "SELECT * FROM routes WHERE (Id < 3 OR Id > 43) AND Login = '$userLogin'  AND DATE(DateAndTime) >= \"$statsFrom\" ORDER BY DateAndTime DESC;");

										if(mysqli_num_rows($userRoutes) >= 1){
											echo "<div class='check_route_table'><table><thead><tr><th>ID</th><th>Dodano</th><th>Miejsce rozpoczęcia</th><th>Miejsce zakończenia</th><th>Ładunek</th><th>Dystans</th><th>Status</th><th>Sprawdzał</th><th></th></thead><tbody>";
											while($route = mysqli_fetch_array($userRoutes)){
												$routeDriverId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = \"".$route['Login']."\";"));
												$routeInfoCheckedId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = \"".$route['CheckedBy']."\";"));

												switch ($route['Status']) {
													case 'Zatwierdzona':
														$statusClass = "confirmed";
														break;
													case 'Oczekuje':
														$statusClass = "to_check";
														break;
													case 'Do poprawy':
														$statusClass = "to_correct";
														break;
													case 'Odrzucona':
														$statusClass = "rejected";
														break;
												}

												echo "<tr><td>#EK".$route['Id']."</td><td>"; $addedDate = explode(" ", $route['DateAndTime']); echo $addedDate[0]."</td><td>".$route['CityFrom']."</td><td>".$route['CityTo']."</td><td>".$route['LoadName']." (".$route['Tonnage']." t)</td><td>".number_format($route['Distance'], 0, ',', ' ')." km</td><td><span class='status_class ".$statusClass."'>".$route['Status']."</span></td><td><a class='profile_link' href='profile?id=".$routeInfoCheckedId['Id']."' title='".$route['CheckedBy']."'><span style='color: #e1a01a;'>".$route['CheckedBy']."</span></a></td><td style='width: 180px;'><a class='check_route_button' href='route?id=".$route['Id']."' title='Przejrzyj trasę #EK".$route['Id']."'><span class='fas fa-search fa-sm'></span>Podgląd</a></td></tr>";
											}
											echo "</tbody></table></div>";
										}
										else{
											echo "<div style='font-size: 25px;' class='center'>Użytkownik nie przejechał jeszcze ani jednej trasy.</div>";
										}
								echo '</div>
							</div>
						</div>';
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
		<?php mysqli_close($conn); ?>
	</body>
</html>