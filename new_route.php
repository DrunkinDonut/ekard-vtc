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
		<title>Dodawanie nowej trasy - EKARD VTC</title>
		<script src="https://code.jquery.com/jquery-1.8.0.min.js"></script>
	</head>
	<body>
		<div class="loader_box"><div class="loader"></div></div>
		<?php require_once("header.php"); ?>
		<nav>
			<div class="nav_section">
				<h1><span class="fas fa-road fa-sm" aria-hidden="true"></span>TRASY</h1>
				<ul>
					<a href="new_route" title="Dodaj nową trasę" class="nav_item nav_active"><li>Dodaj nową</li></a>
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
					$game = test_input($_POST["game"]);

					if($game == "ets2"){
						$login;
						$city_from = test_input($_POST["city_from_ets2"]);
						$city_to = test_input($_POST["city_to_ets2"]);
						$load = test_input($_POST["load_ets2"]);
						$distance = test_input($_POST["distance_ets2"]);
						$fuel = test_input($_POST["fuel_ets2"]);
						$tonnage = test_input($_POST["tonnage_ets2"]);
						$damage = test_input($_POST["damage_ets2"]);
						$screenshot1 = $_FILES["screenshot1_ets2"]["name"];
						$screenshot2 = $_FILES["screenshot2_ets2"]["name"];
						$screenshot3 = $_FILES["screenshot3_ets2"]["name"];
						$note = test_input($_POST["note_ets2"]);

						if(!empty($screenshot1)){
							// $extension = end(explode(".", $_FILES["file"]["name"]));
							if(($_FILES["screenshot1_ets2"]["type"] == "image/jpeg") || ($_FILES["screenshot1_ets2"]["type"] == "image/JPG") || ($_FILES["screenshot1_ets2"]["type"] == "image/png")){
								if(($_FILES["screenshot1_ets2"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot1_url = "img/screenshots/".$login.$newImgIndex.".jpg";
									// move_uploaded_file($_FILES["screenshot1_ets2"]["tmp_name"],"img/screenshots/".$login.$newImgIndex.".".$extension);

        							$filename = compress_image($_FILES["screenshot1_ets2"]["tmp_name"], $screenshot1_url, 80);

        							$screenshot1_filename = $login.$newImgIndex.".jpg";

									$scr1_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 1 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr1_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 1 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr1_valid = FALSE;
							}
						}
						else{
							$screenshot1_filename = "brak";
							$scr1_valid = TRUE;
						}
						if(!empty($screenshot2)){
							if(($_FILES["screenshot2_ets2"]["type"] == "image/jpeg") || ($_FILES["screenshot2_ets2"]["type"] == "image/JPG") || ($_FILES["screenshot2_ets2"]["type"] == "image/png")){
								if(($_FILES["screenshot2_ets2"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot2_url = "img/screenshots/".$login.$newImgIndex.".jpg";

        							$filename = compress_image($_FILES["screenshot2_ets2"]["tmp_name"], $screenshot2_url, 80);

        							$screenshot2_filename = $login.$newImgIndex.".jpg";

									$scr2_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 2 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr2_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 2 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr2_valid = FALSE;
							}
						}
						else{
							$screenshot2_filename = "brak";
							$scr2_valid = TRUE;
						}
						if(!empty($screenshot3)){
							if(($_FILES["screenshot3_ets2"]["type"] == "image/jpeg") || ($_FILES["screenshot3_ets2"]["type"] == "image/JPG") || ($_FILES["screenshot3_ets2"]["type"] == "image/png")){
								if(($_FILES["screenshot3_ets2"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot3_url = "img/screenshots/".$login.$newImgIndex.".jpg";

        							$filename = compress_image($_FILES["screenshot3_ets2"]["tmp_name"], $screenshot3_url, 80);

        							$screenshot3_filename = $login.$newImgIndex.".jpg";

									$scr3_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 3 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr3_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 3 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr3_valid = FALSE;
							}
						}
						else{
							$screenshot3_filename = "brak";
							$scr3_valid = TRUE;
						}

						if($damage == ""){
							$damage = 0;
						}

						$earned = intval($distance) * 3;
						$damage_value = (floatval($damage) / 100) * intval($earned);
						$earnedMoney = intval($earned) - floatval($damage_value);
						$earnedMoney = round($earnedMoney, 2);

						$avgFuelConsumption = (floatval($fuel) / intval($distance)) * 100;
						$avgFuelConsumption = round($avgFuelConsumption, 1);

						if($scr1_valid == TRUE && $scr2_valid == TRUE && $scr3_valid == TRUE){
							mysqli_query($conn, "INSERT INTO routes (DateAndTime, Login, Game, CityFrom, CityTo, LoadName, Distance, Fuel, Tonnage, Damage, AvgFuelConsumption, Screenshot1Url, Screenshot2Url, Screenshot3Url, Note, EarnedMoney, Status) VALUES (NOW(), '$login', '$game', '$city_from', '$city_to', '$load', '$distance', '$fuel', '$tonnage', '$damage', '$avgFuelConsumption', '$screenshot1_filename', '$screenshot2_filename', '$screenshot3_filename', '$note', '$earnedMoney', 'Oczekuje');");
							$successMsg = '<div class="if_success" style="text-align: center;">Dodano nową trasę.</div>';
						}
					}
					if($game == "ats"){
						$login;
						$city_from = test_input($_POST["city_from_ats"]);
						$city_to = test_input($_POST["city_to_ats"]);
						$load = test_input($_POST["load_ats"]);
						$distance = test_input($_POST["distance_ats"]);
						$fuel = test_input($_POST["fuel_ats"]);
						$tonnage = test_input($_POST["tonnage_ats"]);
						$damage = test_input($_POST["damage_ats"]);
						$screenshot1 = $_FILES["screenshot1_ats"]["name"];
						$screenshot2 = $_FILES["screenshot2_ats"]["name"];
						$screenshot3 = $_FILES["screenshot3_ats"]["name"];
						$note = test_input($_POST["note_ats"]);

						if(!empty($screenshot1)){
							if(($_FILES["screenshot1_ats"]["type"] == "image/jpeg") || ($_FILES["screenshot1_ats"]["type"] == "image/JPG") || ($_FILES["screenshot1_ats"]["type"] == "image/png")){
								if(($_FILES["screenshot1_ats"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot1_url = "img/screenshots/".$login.$newImgIndex.".jpg";

        							$filename = compress_image($_FILES["screenshot1_ats"]["tmp_name"], $screenshot1_url, 80);

        							$screenshot1_filename = $login.$newImgIndex.".jpg";

									$scr1_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 1 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr1_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 1 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr1_valid = FALSE;
							}
						}
						else{
							$screenshot1_filename = "brak";
							$scr1_valid = TRUE;
						}
						if(!empty($screenshot2)){
							if(($_FILES["screenshot2_ats"]["type"] == "image/jpeg") || ($_FILES["screenshot2_ats"]["type"] == "image/JPG") || ($_FILES["screenshot2_ats"]["type"] == "image/png")){
								if(($_FILES["screenshot2_ats"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot2_url = "img/screenshots/".$login.$newImgIndex.".jpg";

        							$filename = compress_image($_FILES["screenshot2_ats"]["tmp_name"], $screenshot2_url, 80);

        							$screenshot2_filename = $login.$newImgIndex.".jpg";

									$scr2_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 2 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr2_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 2 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr2_valid = FALSE;
							}
						}
						else{
							$screenshot2_filename = "brak";
							$scr2_valid = TRUE;
						}
						if(!empty($screenshot3)){
							if(($_FILES["screenshot3_ats"]["type"] == "image/jpeg") || ($_FILES["screenshot3_ats"]["type"] == "image/JPG") || ($_FILES["screenshot3_ats"]["type"] == "image/png")){
								if(($_FILES["screenshot3_ats"]["size"] < 4000000)){
									$lastImgIndex = mysqli_fetch_row(mysqli_query($conn, "SELECT LastScreenshotIndex FROM users WHERE Login = '$login';"));
									$newImgIndex = intval($lastImgIndex[0]) + 1;
									mysqli_query($conn, "UPDATE users SET LastScreenshotIndex = '$newImgIndex' WHERE Login = '$login';");
									$screenshot3_url = "img/screenshots/".$login.$newImgIndex.".jpg";

        							$filename = compress_image($_FILES["screenshot3_ats"]["tmp_name"], $screenshot3_url, 80);

        							$screenshot3_filename = $login.$newImgIndex.".jpg";

									$scr3_valid = TRUE;
								}
								else{
									$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 3 - Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
									$scr3_valid = FALSE;
								}
							}
							else{
								$errorMsg = '<div class="if_error" style="text-align: center;">Screenshot 3 - Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
								$scr3_valid = FALSE;
							}
						}
						else{
							$screenshot3_filename = "brak";
							$scr3_valid = TRUE;
						}

						if($damage == ""){
							$damage = 0;
						}

						$earned = intval($distance) * 3;
						$damage_value = (floatval($damage) / 100) * intval($earned);
						$earnedMoney = intval($earned) - floatval($damage_value);
						$earnedMoney = round($earnedMoney, 2);

						$avgFuelConsumption = (floatval($fuel) / intval($distance)) * 100;
						$avgFuelConsumption = round($avgFuelConsumption, 2);

						if($scr1_valid == TRUE && $scr2_valid == TRUE && $scr3_valid == TRUE){
							mysqli_query($conn, "INSERT INTO routes (DateAndTime, Login, Game, CityFrom, CityTo, LoadName, Distance, Fuel, Tonnage, Damage, AvgFuelConsumption, Screenshot1Url, Screenshot2Url, Screenshot3Url, Note, EarnedMoney, Status) VALUES (NOW(), '$login', '$game', '$city_from', '$city_to', '$load', '$distance', '$fuel', '$tonnage', '$damage', '$avgFuelConsumption', '$screenshot1_filename', '$screenshot2_filename', '$screenshot3_filename', '$note', '$earnedMoney', 'Oczekuje');");
							$successMsg = '<div class="if_success" style="text-align: center;">Dodano nową trasę.</div>';
						}
						else{
							$errorMsg = '<div class="if_error" style="text-align: center;">Coś poszło nie tak.</div>';
						}
					}
				}
			?>
			<div class="block new_route">
				<h1>Dodawanie trasy</h1>
				<div class="block_content">
					<div class="gamechange_new_route_tab">
						<div class="gamechange_new_route_ets2 gamechange_active">
							Euro Truck Simulator 2
						</div>
						<div class="gamechange_new_route_ats">
							American Truck Simulator
						</div>
					</div>
					<div class="new_route_tab_ets2 tab_active">
						<?php if(isSet($errorMsg)) echo $errorMsg; ?>
						<?php if(isSet($successMsg)) echo $successMsg; ?>
						<form action="new_route" method="POST" id="form_ets2" enctype="multipart/form-data">
							<div class="form-group" id="form-group_from_ets2">
								<label for="city_from_ets2">Z</label>
								<input type="text" list="cities_from_ets2" id="cities_from_ets2_input" name="city_from_ets2" title="Miasto startowe" required/>
								<datalist id="cities_from_ets2">
									<?php
										$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ets2' ORDER BY CityName ASC;");

										while($wiersz = mysqli_fetch_row($citiesListEts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</datalist>
							</div>
							<div class="form-group" id="form-group_to_ets2">
								<label for="city_to_ets2">Do</label>
								<input type="text" list="cities_to_ets2" id="cities_to_ets2_input" name="city_to_ets2" title="Miasto docelowe" required/>
								<datalist id="cities_to_ets2">
									<?php
										$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ets2' ORDER BY CityName ASC;");

										while($wiersz = mysqli_fetch_row($citiesListEts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</select>
							</div>
							<input type="text" name="game" value="ets2" hidden>
							<div class="form-group" id="form-group_load_ets2">
								<label for="load_ets2">Ładunek</label>
								<input type="text" list="load_ets2" id="load_ets2_input" name="load_ets2" title="Ładunek" required/>
								<datalist id="load_ets2">
									<?php
										$loadsListEts = mysqli_query($conn, "SELECT LoadName FROM loads WHERE Game = 'ets2' ORDER BY LoadName ASC;");

										while($wiersz = mysqli_fetch_row($loadsListEts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="tonnage_ets2">Tonaż ładunku (t)</label>
								<input type="number" min="0" max="999" name="tonnage_ets2" title="Tonaż" required />
							</div>
							<div class="form-group">
								<label for="distance_ets2">Przebyty dystans (km)</label>
								<input type="number" min="0" max="999999" name="distance_ets2" title="Dystans" required />
							</div>
							<div class="form-group">
								<label for="fuel_ets2">Zużyte paliwo (l)</label>
								<input type="number" min="0" max="999999" step="0.1" name="fuel_ets2" title="Zużyte paliwo" required />
							</div>
							<div class="form-group">
								<label for="damage_ets2">Uszkodzenia (%)</label>
								<input type="number" min="0" max="100" step="0.1" name="damage_ets2" title="Uszkodzenia"/>
							</div>
							<div class="form-group">
								<label for="screenshot1_ets2">Screenshot 1</label>
								<input type="file" name="screenshot1_ets2" accept=".jpg, .jpeg, .png" title="Screenshot 1"/>
							</div>
							<div class="form-group">
								<label for="screenshot2_ets2">Screenshot 2</label>
								<input type="file" name="screenshot2_ets2" accept=".jpg, .jpeg, .png" title="Screenshot 2"/>
							</div>
							<div class="form-group">
								<label for="screenshot3_ets2">Screenshot 3</label>
								<input type="file" name="screenshot3_ets2" accept=".jpg, .jpeg, .png" title="Screenshot 3"/>
							</div>
							<div class="form-group">
								<label for="note_ets2">Notatka:</label>
								<input type="textarea" maxlenght="500" name="note_ets2" title="Notatka"/>
							</div>
							<button type="submit" title="Dodaj trasę" id="ets2_submit_button"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj trasę</button>
						</form>
					</div>
					<div class="new_route_tab_ats">
						<?php if(isSet($errorMsg)) echo $errorMsg; ?>
						<?php if(isSet($successMsg)) echo $successMsg; ?>
						<form action="new_route" method="POST" id="form_ats" enctype="multipart/form-data">
							<div class="form-group"  id="form-group_from_ats">
								<label for="city_from_ats">Z</label>
								<input type="text" list="cities_from_ats" id="cities_from_ats_input" name="city_from_ats" title="Miasto startowe" required/>
								<datalist id="cities_from_ats">
									<?php
										$citiesListAts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ats' ORDER BY CityName ASC;");

										while($wiersz = mysqli_fetch_row($citiesListAts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</datalist>
							</div>
							<div class="form-group"  id="form-group_to_ats">
								<label for="city_to_ats">Do</label>
								<input type="text" list="cities_to_ats" id="cities_to_ats_input" name="city_to_ats" title="Miasto docelowe" required/>
								<datalist id="cities_to_ats">
									<?php
										$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ats' ORDER BY CityName ASC;");

										while($wiersz = mysqli_fetch_row($citiesListEts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</select>
							</div>
							<input type="text" name="game" value="ats" hidden>
							<div class="form-group"  id="form-group_load_ats">
								<label for="load_ats">Ładunek</label>
								<input type="text" list="load_ats" id="load_ats_input" name="load_ats" title="Ładunek" required/>
								<datalist id="load_ats">
									<?php
										$loadsListAts = mysqli_query($conn, "SELECT LoadName FROM loads WHERE Game = 'ats' ORDER BY LoadName ASC;");

										while($wiersz = mysqli_fetch_row($loadsListAts)){
											echo "<option value=\"".$wiersz[0]."\">";
										}
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="tonnage_ats">Tonaż ładunku (t)</label>
								<input type="number" min="0" max="999" name="tonnage_ats" title="Tonaż" required/>
							</div>
							<div class="form-group">
								<label for="distance_ats">Przebyty dystans (km)</label>
								<input type="number" min="0" max="999999" name="distance_ats" title="Dystans" required/>
							</div>
							<div class="form-group">
								<label for="fuel_ats">Zużyte paliwo (l)</label>
								<input type="number" min="0" max="999999" step="0.1" name="fuel_ats" title="Zużyte paliwo" required/>
							</div>
							<div class="form-group">
								<label for="damage_ats">Uszkodzenia (%)</label>
								<input type="number" min="0" max="100" step="0.1" name="damage_ats" title="Uszkodzenia"/>
							</div>
							<div class="form-group">
								<label for="screenshot1_ats">Screenshot 1</label>
								<input type="file" name="screenshot1_ats" accept=".jpg, .jpeg, .png" title="Screenshot 1"/>
							</div>
							<div class="form-group">
								<label for="screenshot2_ats">Screenshot 2</label>
								<input type="file" name="screenshot2_ats" accept=".jpg, .jpeg, .png" title="Screenshot 2"/>
							</div>
							<div class="form-group">
								<label for="screenshot3_ats">Screenshot 3</label>
								<input type="file" name="screenshot3_ats" accept=".jpg, .jpeg, .png" title="Screenshot 3"/>
							</div>
							<div class="form-group">
								<label for="note_ats">Notatka:</label>
								<input type="textarea" maxlenght="500" name="note_ats" title="Notatka"/>
							</div>
							<button type="submit" title="Dodaj trasę" id="ats_submit_button"><span class="fas fa-plus" aria-hidden="true"></span>Dodaj trasę</button>
						</form>
					</div>
				</div>
			</div>
		</section>
		<script type="text/javascript">
			var new_route_ets2 = document.querySelector('.gamechange_new_route_ets2');
			var new_route_ats = document.querySelector('.gamechange_new_route_ats');
			var new_route_tab_ets2 = document.querySelector('.new_route_tab_ets2');
			var new_route_tab_ats = document.querySelector('.new_route_tab_ats');

			new_route_ets2.addEventListener("click", function(){
				new_route_ets2.className += " gamechange_active";
				new_route_ats.className = "gamechange_new_route_ets2";
				new_route_tab_ets2.className += " tab_active";
				new_route_tab_ats.className = "new_route_tab_ats";
			});
			new_route_ats.addEventListener("click", function(){
				new_route_ats.className += " gamechange_active";
				new_route_ets2.className = "gamechange_new_route_ats";
				new_route_tab_ats.className += " tab_active";
				new_route_tab_ets2.className = "new_route_tab_ets2";
			});

			$('#form_ats').submit(function() {
			    $('.loader_box').css('display', 'flex');
			});
			$('#form_ets2').submit(function() {
			    $('.loader_box').css('display', 'flex');
			});

			var initialArray_from_ets2 = [];
	        initialArray_from_ets2 = $('#cities_from_ets2 option');
	        $('#form-group_from_ets2 #cities_from_ets2_input').on('input', function() {
	          	var inputVal = $('#cities_from_ets2_input').val();
	          	var first = [];
	          	first = $('#cities_from_ets2 option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('cities_from_ets2').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_from_ets2.length; i++) {
	              		options += '<option value="' + initialArray_from_ets2[i].value + '" />';
	            	}
	            document.getElementById('cities_from_ets2').innerHTML = options;
	        	}
	    	});

	    	var initialArray_to_ets2 = [];
	        initialArray_to_ets2 = $('#cities_to_ets2 option');
	        $('#form-group_to_ets2 #cities_to_ets2_input').on('input', function() {
	          	var inputVal = $('#cities_to_ets2_input').val();
	          	var first = [];
	          	first = $('#cities_to_ets2 option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('cities_to_ets2').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_to_ets2.length; i++) {
	              		options += '<option value="' + initialArray_to_ets2[i].value + '" />';
	            	}
	            document.getElementById('cities_to_ets2').innerHTML = options;
	        	}
	    	});

	    	var initialArray_from_ats = [];
	        initialArray_from_ats = $('#cities_from_ats option');
	        $('#form-group_from_ats #cities_from_ats_input').on('input', function() {
	          	var inputVal = $('#cities_from_ats_input').val();
	          	var first = [];
	          	first = $('#cities_from_ats option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('cities_from_ats').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_from_ats.length; i++) {
	              		options += '<option value="' + initialArray_from_ats[i].value + '" />';
	            	}
	            document.getElementById('cities_from_ats').innerHTML = options;
	        	}
	    	});

	    	var initialArray_to_ats = [];
	        initialArray_to_ats = $('#cities_to_ats option');
	        $('#form-group_to_ats #cities_to_ats_input').on('input', function() {
	          	var inputVal = $('#cities_to_ats_input').val();
	          	var first = [];
	          	first = $('#cities_to_ats option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('cities_to_ats').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_to_ats.length; i++) {
	              		options += '<option value="' + initialArray_to_ats[i].value + '" />';
	            	}
	            document.getElementById('cities_to_ats').innerHTML = options;
	        	}
	    	});

	    	var initialArray_load_ets2 = [];
	        initialArray_load_ets2 = $('#load_ets2 option');
	        $('#form-group_load_ets2 #load_ets2_input').on('input', function() {
	          	var inputVal = $('#load_ets2_input').val();
	          	var first = [];
	          	first = $('#load_ets2 option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('load_ets2').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_load_ets2.length; i++) {
	              		options += '<option value="' + initialArray_load_ets2[i].value + '" />';
	            	}
	            document.getElementById('load_ets2').innerHTML = options;
	        	}
	    	});

	    	var initialArray_load_ats = [];
	        initialArray_load_ats = $('#load_ats option');
	        $('#form-group_load_ats #load_ats_input').on('input', function() {
	          	var inputVal = $('#load_ats_input').val();
	          	var first = [];
	          	first = $('#load_ats option');
	          	if (inputVal != '' && inputVal != 'undefined') {
	            	var options = '';
	            	for (var i = 0; i < first.length; i++) {
		            	if (first[i].value.toLowerCase().startsWith(inputVal.toLowerCase())) {
	       	       			options += '<option value="' + first[i].value + '" />';
	              		}
	            	}
	            	document.getElementById('load_ats').innerHTML = options;
	         	} else {
	            	var options = '';
	            	for (var i = 0; i < initialArray_load_ats.length; i++) {
	              		options += '<option value="' + initialArray_load_ats[i].value + '" />';
	            	}
	            document.getElementById('load_ats').innerHTML = options;
	        	}
	    	});
		</script>
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