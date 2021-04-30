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
		<title>Kierowcy - EKARD VTC</title>
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
					<a href="drivers" title="Lista kierowców" class="nav_item nav_active"><li>Kierowcy</li></a>
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
			<div class="drivers">
				<h1>Kierowcy - <?php $driversNumber = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE Id != 35 AND Rank != 'Programista';")); echo $driversNumber; ?>/30</h1>
				<div class="block_content">
					<div class="drivers_table_block">
						<?php
							$driversList = mysqli_query($conn, "SELECT * FROM users WHERE Id != 35 ORDER BY Joined ASC;");
							$routesData = mysqli_query($conn, "SELECT * FROM routes;");

							echo "<div class='drivers_table'><table class='sortable'><thead><tr><th style='width: 200px;' onclick='sortTable(0)'>Nazwa użytkownika</th><th onclick='sortTable(1)'>Stanowisko</th><th onclick='sortTable(2)'>Dołączył</th><th onclick='sortTable(3)'>Staż (dni)</th><th onclick='sortTable(4)'>Przebieg (km)</th><th onclick='sortTable(5)'>Ilość tras</th><th></th></tr></thead><tbody>";
							while($drivers = mysqli_fetch_array($driversList)){
								$routesUserData = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM routes WHERE Login = '$login';"));
								$joinedDate = mysqli_fetch_row(mysqli_query($conn, "SELECT Joined FROM users WHERE Login = '".$drivers['Login']."';"));
								$statsFrom = $joinedDate[0];
								$driverDistanceSum = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(Distance) FROM routes WHERE Login = '".$drivers['Login']."' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
								$distanceSum = number_format($driverDistanceSum[0], 0, ',', ' ');
								$driverAddedRoutes = mysqli_fetch_row(mysqli_query($conn, "SELECT AddedRoutesEts2 + AddedRoutesAts FROM users WHERE Login = '".$drivers['Login']."';"));
								$driverRoutesNumber = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(Id) FROM routes WHERE Login = '".$drivers['Login']."' AND Status = 'Zatwierdzona' AND DATE(DateAndTime) >= \"$statsFrom\";"));
								$routesNumber = number_format($driverRoutesNumber[0], 0, ',', ' ') + $driverAddedRoutes[0];

								$joinedDate = date_create(date("Y-m-d", strtotime($joinedDate[0])));
								$today = date_create(date("Y-m-d"));
								$timeDiff = date_diff($today, $joinedDate);
								$numberDays = $timeDiff->days;

								switch ($drivers['Rank']) {
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

								echo "<tr><td style='float: left;'>";if($drivers['ImgUrl'] != ""){echo "<img class='drivers_profile_img' src='img/avatars/".$drivers['ImgUrl']."' />";}else{echo "<img class='drivers_profile_img' src='img/avatars/no_avatar.jpg' />";} echo $drivers['Login']."</td><td><span class='rank_class ".$rankClass."'>".$drivers['Rank']."</span></td><td><span class='far fa-clock fa-sm'></span> ".$drivers['Joined']."</td><td>"; if($numberDays >= 1){ echo number_format($numberDays, 0, ',', ' '); }else{ echo "0"; } echo "</td><td>";if($drivers['Rank'] == 'Programista'){echo "-";}else{echo $distanceSum;}echo "</td><td>";if($drivers['Rank'] == 'Programista'){echo "-";}else{echo $routesNumber;} echo "</td><td style='width: 180px;'><a class='check_route_button' href='profile?id=".$drivers['Id']."' title='Zobacz profil ".$drivers['Login']."'><span class='fas fa-user fa-sm'></span>Zobacz profil</a></td></tr>";
							}
							echo "</table></div>";
						?>
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
		<script src="js/sorttable.js"></script>
		<?php mysqli_close($conn); ?>
	</body>
</html>