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
		<title>Moje trasy - EKARD VTC</title>
	</head>
	<body>
		<?php require_once("header.php"); ?>
		<nav>
			<div class="nav_section">
				<h1><span class="fas fa-road fa-sm" aria-hidden="true"></span>TRASY</h1>
				<ul>
					<a href="new_route" title="Dodaj nową trasę" class="nav_item"><li>Dodaj nową</li></a>
					<a href="my_routes" title="Moje trasy" class="nav_item nav_active"><li>Moje trasy</li></a>
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
			<div class="my_routes">
				<h1>Moje trasy</h1>
				<div class="block_content">
					<div class="my_routes_table">
						<?php
							$joinedDate = mysqli_fetch_row(mysqli_query($conn, "SELECT Joined FROM users WHERE Login = '$login';"));
							$statsFrom = $joinedDate[0];
							$myRoutes = mysqli_query($conn, "SELECT * FROM routes WHERE (Id < 3 OR Id > 43) AND Login = '$login' AND DATE(DateAndTime) >= \"$statsFrom\" ORDER BY DateAndTime DESC;");

							if(mysqli_num_rows($myRoutes) >= 1){
								echo "<div class='check_route_table'><table><thead><tr><th>ID</th><th>Dodano</th><th>Miejsce rozpoczęcia</th><th>Miejsce zakończenia</th><th>Ładunek</th><th>Dystans</th><th>Status</th><th>Sprawdzał</th><th></th></thead><tbody>";
								while($route = mysqli_fetch_array($myRoutes)){
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
								echo "<div style='font-size: 25px;' class='center'>Nie przejechałeś jeszcze ani jednej trasy.</div>";
							}
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
		<?php mysqli_close($conn); ?>
	</body>
</html>