<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);

	$login = $_SESSION["Login"];
	$userRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Login = '$login';"));
				
	if($userRank[0] == "Dyspozytor" OR $userRank[0] == "Rekruter" OR $userRank[0] == "VIP" OR $userRank[0] == "Spedytor" OR $userRank[0] == "Kierowca" OR $userRank[0] == "Okres próbny"){
		header("Location: index");
	}
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?php
			require_once("head.html");
		?>
		<title>Miasta i ładunki - EKARD VTC</title>
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
					<a href="cities_loads" title="Miasta i ładunki" class="nav_item nav_active"><li>Miasta i ładunki</li></a>
				</ul>
			</div>';
				}
			?>
			<div class="nav-section">
				<div class="nav_copy">&copy;Copyright <?php echo date("Y"); ?> by <a href="http://steamcommunity.com/id/drunkin_donut" target="_blank">Natan Ryl</a>.<br />Wszelkie prawa zastrzeżone.</div>
			</div>
		</nav>
		<section class="cities_loads">
			<?php
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					if(isset($_POST["city_id"])){
						$cityId = $_POST["city_id"];
						$isInDB = mysqli_query($conn, "SELECT CityName FROM cities WHERE Id = '$cityId';");

						if(mysqli_num_rows($isInDB) == 1){
							mysqli_query($conn, "DELETE FROM cities WHERE Id = '$cityId';");
							$citySuccess = "<div class='if_success' style='text-align: center;'>Miasto zostało usunięte z bazy.</div>";
						}
						else{
							$cityError = "<div class='if_error' style='text-align: center;'>To miasto już jest usunięte.</div>";
						}
					}
					if(isset($_POST["city_name"])){
						$cityName = test_input($_POST["city_name"]);
						$gameName = $_POST["game"];
						$isInDB = mysqli_query($conn, "SELECT CityName FROM cities WHERE CityName = '$cityName' AND Game = '$gameName';");

						if(mysqli_num_rows($isInDB) == 0){
							mysqli_query($conn, "INSERT INTO cities (CityName, Game) VALUES ('$cityName', '$gameName');");
							$citySuccess = "<div class='if_success' style='text-align: center;'>Miasto zostało dodane do bazy.</div>";
						}
						else{
							$cityError = "<div class='if_error' style='text-align: center;'>To miasto już istnieje w bazie.</div>";
						}
					}
					if(isset($_POST["load_id"])){
						$loadId = $_POST["load_id"];
						$isInDB = mysqli_query($conn, "SELECT LoadName FROM loads WHERE Id = '$loadId';");

						if(mysqli_num_rows($isInDB) == 1){
							mysqli_query($conn, "DELETE FROM loads WHERE Id = '$loadId';");
							$loadsSuccess = "<div class='if_success' style='text-align: center;'>Ładunek został usunięty z bazy.</div>";
						}
						else{
							$loadsError = "<div class='if_error' style='text-align: center;'>Ten ładunek już jest usunięty.</div>";
						}
					}
					if(isset($_POST["load_name"])){
						$loadName = test_input($_POST["load_name"]);
						$gameName = $_POST["game"];
						$isInDB = mysqli_query($conn, "SELECT LoadName FROM loads WHERE LoadName = '$loadName' AND Game = '$gameName';");

						if(mysqli_num_rows($isInDB) == 0){
							mysqli_query($conn, "INSERT INTO loads (LoadName, Game) VALUES ('$loadName', '$gameName');");
							$loadsSuccess = "<div class='if_success' style='text-align: center;'>Ładunek został dodany do bazy.</div>";
						}
						else{
							$loadsError = "<div class='if_error' style='text-align: center;'>Ten ładunek już istnieje w bazie.</div>";
						}
					}
				}
			?>
			<div class="block cities">
				<h1>Miasta</h1>
				<div class="block_content">
					<div class="gamechange_cities_tab">
						<div class="gamechange_cities_ets2 gamechange_active">
							Euro Truck Simulator 2
						</div>
						<div class="gamechange_cities_ats">
							American Truck Simulator
						</div>
					</div>
					<div class="cities_tab_ets2 tab_active">
						<?php if(isset($citySuccess)) echo $citySuccess; ?>
						<?php if(isset($cityError)) echo $cityError; ?>
						<div class="cities_table">
							<table>
								<thead>
									<tr>
										<th>Lp.</th>
										<th>Nazwa miasta</th>
										<th>Usuwanie</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$ets2CitiesList = mysqli_query($conn, "SELECT * FROM cities WHERE Game = 'ets2' ORDER BY CityName;");
										$i = 1;

										while($wiersz = mysqli_fetch_row($ets2CitiesList)){
											echo "<tr><td>$i</td><td>$wiersz[1]</td><td class='del_city_button'><form action='cities_loads' method='POST'><input type='text' name='city_id' value='$wiersz[0]' hidden /><button type='submit' title='Usuń $wiersz[1]'><span class='fas fa-times fa-lg' onclick='return confirm(\"Czy na pewno chcesz usunąć to miasto z bazy?\");'></span></button></form></td></tr>";
											$i++;
										}
									?>
								</tbody>
							</table>
						</div>
						<div class="add_city_ets2">
							<form action="cities_loads" method="POST">
								<div class="form-group">
									<label for="city_name">Nazwa</label>
									<input type="text" name="city_name" title="Nazwa miasta" required />
								</div>
								<input type="text" name="game" value="ets2" hidden />
								<button type="submit" title="Dodaj miasto"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj miasto</button>
							</form>
						</div>
					</div>
					<div class="cities_tab_ats">
						<?php if(isset($citySuccess)) echo $citySuccess; ?>
						<?php if(isset($cityError)) echo $cityError; ?>
						<div class="cities_table">
							<table>
								<thead>
									<tr>
										<th>Lp.</th>
										<th>Nazwa miasta</th>
										<th>Usuwanie</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$atsCitiesList = mysqli_query($conn, "SELECT * FROM cities WHERE Game = 'ats' ORDER BY CityName;");
										$i = 1;

										while($wiersz = mysqli_fetch_row($atsCitiesList)){
											echo "<tr><td>$i</td><td>$wiersz[1]</td><td class='del_city_button'><form action='cities_loads' method='POST'><input type='text' name='city_id' value='$wiersz[0]' hidden /><button type='submit' title='Usuń $wiersz[1]'><span class='fas fa-times fa-lg' onclick='return confirm(\"Czy na pewno chcesz usunąć to miasto z bazy?\");'></span></button></form></td></tr>";
											$i++;
										}
									?>
								</tbody>
							</table>
						</div>
						<div class="add_city_ats">
							<form action="cities_loads" method="POST">
								<div class="form-group">
									<label for="city_name">Nazwa</label>
									<input type="text" name="city_name" title="Nazwa miasta" required />
								</div>
								<input type="text" name="game" value="ats" hidden />
								<button type="submit" title="Dodaj miasto"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj miasto</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="block loads">
				<h1>Ładunki</h1>
				<div class="block_content">
					<div class="gamechange_loads_tab">
						<div class="gamechange_loads_ets2 gamechange_active">
							Euro Truck Simulator 2
						</div>
						<div class="gamechange_loads_ats">
							American Truck Simulator
						</div>
					</div>
					<div class="loads_tab_ets2 tab_active">
						<?php if(isset($loadsSuccess)) echo $loadsSuccess; ?>
						<?php if(isset($loadsError)) echo $loadsError; ?>
						<div class="loads_table">
							<table>
								<thead>
									<tr>
										<th>Lp.</th>
										<th>Nazwa ładunku</th>
										<th>Usuwanie</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$ets2LoadsList = mysqli_query($conn, "SELECT * FROM loads WHERE Game = 'ets2' ORDER BY LoadName;");
										$i = 1;

										while($wiersz = mysqli_fetch_row($ets2LoadsList)){
											echo "<tr><td>$i</td><td>$wiersz[1]</td><td class='del_load_button'><form action='cities_loads' method='POST'><input type='text' name='load_id' value='$wiersz[0]' hidden /><button type='submit' title='Usuń $wiersz[1]'><span class='fas fa-times fa-lg' onclick='return confirm(\"Czy na pewno chcesz usunąć ten ładunek z bazy?\");'></span></button></form></td></tr>";
											$i++;
										}
									?>
								</tbody>
							</table>
						</div>
						<div class="add_load_ets2">
							<form action="cities_loads" method="POST">
								<div class="form-group">
									<label for="load_name">Nazwa</label>
									<input type="text" name="load_name" title="Nazwa ładunku" required />
								</div>
								<input type="text" name="game" value="ets2" hidden />
								<button type="submit" title="Dodaj ładunek"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj ładunek</button>
							</form>
						</div>
					</div>
					<div class="loads_tab_ats">
						<?php if(isset($loadsSuccess)) echo $loadsSuccess; ?>
						<?php if(isset($loadsError)) echo $loadsError; ?>
						<div class="loads_table">
							<table>
								<thead>
									<tr>
										<th>Lp.</th>
										<th>Nazwa ładunku</th>
										<th>Usuwanie</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$atsLoadsList = mysqli_query($conn, "SELECT * FROM loads WHERE Game = 'ats' ORDER BY LoadName;");
										$i = 1;

										while($wiersz = mysqli_fetch_row($atsLoadsList)){
											echo "<tr><td>$i</td><td>$wiersz[1]</td><td class='del_load_button'><form action='cities_loads' method='POST'><input type='text' name='load_id' value='$wiersz[0]' hidden /><button type='submit' title='Usuń $wiersz[1]'><span class='fas fa-times fa-lg' onclick='return confirm(\"Czy na pewno chcesz usunąć ten ładunek z bazy?\");'></span></button></form></td></tr>";
											$i++;
										}
									?>
								</tbody>
							</table>
						</div>
						<div class="add_load_ats">
							<form action="cities_loads" method="POST">
								<div class="form-group">
									<label for="load_name">Nazwa</label>
									<input type="text" name="load_name" title="Nazwa ładunku" required />
								</div>
								<input type="text" name="game" value="ats" hidden />
								<button type="submit" title="Dodaj ładunek"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj ładunek</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				var cities_ets2 = document.querySelector('.gamechange_cities_ets2');
				var cities_ats = document.querySelector('.gamechange_cities_ats');
				var cities_tab_ets2 = document.querySelector('.cities_tab_ets2');
				var cities_tab_ats = document.querySelector('.cities_tab_ats');

				var loads_ets2 = document.querySelector('.gamechange_loads_ets2');
				var loads_ats = document.querySelector('.gamechange_loads_ats');
				var loads_tab_ets2 = document.querySelector('.loads_tab_ets2');
				var loads_tab_ats = document.querySelector('.loads_tab_ats');

				cities_ets2.addEventListener("click", function(){
					cities_ets2.className += " gamechange_active";
					cities_ats.className = "gamechange_cities_ets2";
					cities_tab_ets2.className += " tab_active";
					cities_tab_ats.className = "cities_tab_ats";
				});
				cities_ats.addEventListener("click", function(){
					cities_ats.className += " gamechange_active";
					cities_ets2.className = "gamechange_cities_ats";
					cities_tab_ats.className += " tab_active";
					cities_tab_ets2.className = "cities_tab_ets2";
				});

				loads_ets2.addEventListener("click", function(){
					loads_ets2.className += " gamechange_active";
					loads_ats.className = "gamechange_loads_ets2";
					loads_tab_ets2.className += " tab_active";
					loads_tab_ats.className = "loads_tab_ats";
				});
				loads_ats.addEventListener("click", function(){
					loads_ats.className += " gamechange_active";
					loads_ets2.className = "gamechange_loads_ats";
					loads_tab_ats.className += " tab_active";
					loads_tab_ets2.className = "loads_tab_ets2";
				});
			</script>
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