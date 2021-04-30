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
		<title>Poprawianie trasy - EKARD VTC</title>
		<script src="https://code.jquery.com/jquery-1.8.0.min.js"></script>
		<script src="js/jquery.elevateZoom.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="loader_box"><div class="loader"></div></div>
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
				if(!isset($_GET['id'])){
					header("Location: index");
				}
				else{
					$getRouteId = test_input($_GET['id']);

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
							$screenshot1 = test_input($_POST["screenshot1_ets2"]);
							$screenshot2 = test_input($_POST["screenshot2_ets2"]);
							$screenshot3 = test_input($_POST["screenshot3_ets2"]);
							$note = test_input($_POST["note_ets2"]);

							if($damage == ""){
								$damage = 0;
							}

							$earned = intval($distance) * 3;
							$damage_value = (floatval($damage) / 100) * intval($earned);
							$earnedMoney = intval($earned) - floatval($damage_value);
							$earnedMoney = round($earnedMoney, 2);

							$avgFuelConsumption = (floatval($fuel) / intval($distance)) * 100;
							$avgFuelConsumption = round($avgFuelConsumption, 1);

							mysqli_query($conn, "INSERT INTO routes (DateAndTime, Login, Game, CityFrom, CityTo, LoadName, Distance, Fuel, Tonnage, Damage, AvgFuelConsumption, Screenshot1Url, Screenshot2Url, Screenshot3Url, Note, EarnedMoney, Status) VALUES (NOW(), '$login', '$game', '$city_from', '$city_to', '$load', '$distance', '$fuel', '$tonnage', '$damage', '$avgFuelConsumption', '$screenshot1', '$screenshot2', '$screenshot3', '$note', '$earnedMoney', 'Oczekuje');");
							mysqli_query($conn, "DELETE FROM routes WHERE Id = '$getRouteId';");
							$successMsg = '<div class="if_success" style="text-align: center;">Trasa została poprawiona.</div>';
							header("Location: index");
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
							$screenshot1 = test_input($_POST["screenshot1_ats"]);
							$screenshot2 = test_input($_POST["screenshot2_ats"]);
							$screenshot3 = test_input($_POST["screenshot3_ats"]);
							$note = test_input($_POST["note_ats"]);

							if($damage == ""){
								$damage = 0;
							}

							$earned = intval($distance) * 3;
							$damage_value = (floatval($damage) / 100) * intval($earned);
							$earnedMoney = intval($earned) - floatval($damage_value);
							$earnedMoney = round($earnedMoney, 2);

							$avgFuelConsumption = (floatval($fuel) / intval($distance)) * 100;
							$avgFuelConsumption = round($avgFuelConsumption, 2);

							mysqli_query($conn, "INSERT INTO routes (DateAndTime, Login, Game, CityFrom, CityTo, LoadName, Distance, Fuel, Tonnage, Damage, AvgFuelConsumption, Screenshot1Url, Screenshot2Url, Screenshot3Url, Note, EarnedMoney, Status) VALUES (NOW(), '$login', '$game', '$city_from', '$city_to', '$load', '$distance', '$fuel', '$tonnage', '$damage', '$avgFuelConsumption', '$screenshot1', '$screenshot2', '$screenshot3', '$note', '$earnedMoney', 'Oczekuje');");
							mysqli_query($conn, "DELETE FROM routes WHERE Id = '$getRouteId';");
							$successMsg = '<div class="if_success" style="text-align: center;">Trasa została poprawiona.</div>';
							header("Location: index");
						}
					}

					$getRouteInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM routes WHERE Id = $getRouteId;"));

					if($getRouteInfo['Status'] != 'Do poprawy'){
						echo "<div style='font-size: 25px; color: #fff; font-family: \"Roboto-light\", sans-serif; margin-top: 40px;' class='center'>Trasa o podanym id nie jest do poprawy.</div>";
					}
					else{
						if($getRouteInfo['Login'] != $login){
							echo "<div style='font-size: 25px; color: #fff; font-family: \"Roboto-light\", sans-serif; margin-top: 40px;' class='center'>Nie możesz poprawić nie swojej trasy!</div>";
						}
						else{
							if($getRouteInfo['Game'] == 'ets2'){
								echo "<div class='block new_route'>
									<h1>Poprawianie trasy #EK"; echo test_input($_GET['id']); echo "</h1>
									<div class='block_content'>
										<div class='new_route_tab_ets2 tab_active'>";
								if(isSet($errorMsg)) echo $errorMsg;
								if(isSet($successMsg)) echo $successMsg;
								echo "<form action='correct_route?id=$getRouteId' method='POST' id='form_ets2' style='align-items: center;' enctype='multipart/form-data'>
										<div class='form-group' id='form-group_from_ets2'>
											<label for='city_from_ets2'>Z</label>
												<input type='text' value='".$getRouteInfo['CityFrom']."' list='cities_from_ets2' id='cities_from_ets2_input' name='city_from_ets2' title='Miasto startowe' required/>
											<datalist id='cities_from_ets2'>";
								$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ets2' ORDER BY CityName ASC;");

								while($wiersz = mysqli_fetch_row($citiesListEts)){
									echo "<option value=\"".$wiersz[0]."\">";
								}
								echo "</datalist>
										</div>
										<div class='form-group' id='form-group_to_ets2'>
											<label for='city_to_ets2'>Do</label>
											<input type='text' value='".$getRouteInfo['CityTo']."' list='cities_to_ets2' id='cities_to_ets2_input' name='city_to_ets2' title='Miasto docelowe' required/>
											<datalist id='cities_to_ets2'>";
								$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ets2' ORDER BY CityName ASC;");

								while($wiersz = mysqli_fetch_row($citiesListEts)){
									echo "<option value=\"".$wiersz[0]."\">";
								}
								echo "</select>
										</div>
										<input type='text' name='game' value='ets2' hidden>
										<div class='form-group' id='form-group_load_ets2'>
											<label for='load_ets2'>Ładunek</label>
											<input type='text' value='".$getRouteInfo['LoadName']."' list='load_ets2' id='load_ets2_input' name='load_ets2' title='Ładunek' required/>
											<datalist id='load_ets2'>";
								$loadsListEts = mysqli_query($conn, "SELECT LoadName FROM loads WHERE Game = 'ets2' ORDER BY LoadName ASC;");

								while($wiersz = mysqli_fetch_row($loadsListEts)){
									echo "<option value=\"".$wiersz[0]."\">";
								}
								echo "</select>
										</div>
										<div class='form-group'>
											<label for='tonnage_ets2'>Tonaż ładunku (t)</label>
											<input type='number' value='".$getRouteInfo['Tonnage']."' min='0' max='999' name='tonnage_ets2' title='Tonaż' required />
										</div>
										<div class='form-group'>
											<label for='distance_ets2'>Przebyty dystans (km)</label>
											<input type='number' value='".$getRouteInfo['Distance']."' min='0' max='999999' name='distance_ets2' title='Dystans' required />
										</div>
										<div class='form-group'>
											<label for='fuel_ets2'>Zużyte paliwo (l)</label>
											<input type='number' value='".$getRouteInfo['Fuel']."' min='0' max='999999' step='0.1' name='fuel_ets2' title='Zużyte paliwo' required />
										</div>
										<div class='form-group'>
											<label for='damage_ets2'>Uszkodzenia (%)</label>
											<input type='number' value='".$getRouteInfo['Damage']."' min='0' max='100' step='0.1' name='damage_ets2' title='Uszkodzenia'/>
										</div>
										<input type='text' name='screenshot1_ets2' value='".$getRouteInfo['Screenshot1Url']."' hidden />
										<input type='text' name='screenshot2_ets2' value='".$getRouteInfo['Screenshot2Url']."' hidden />
										<input type='text' name='screenshot3_ets2' value='".$getRouteInfo['Screenshot3Url']."' hidden />
										<div class='form-group'>
											<label for='note_ets2'>Notatka:</label>
											<input type='textarea' value='".$getRouteInfo['Note']."' maxlenght='500' name='note_ets2' title='Notatka'/>
										</div>";
										echo "<div class ='check_route_screenshots'><h2>Screenshoty:</h2>";
										if($getRouteInfo['Screenshot1Url'] == 'brak' AND $getRouteInfo['Screenshot2Url'] == 'brak' AND $getRouteInfo['Screenshot3Url'] == 'brak'){
											echo "Brak screenshot'ów.";
											echo "</div>";
										}
										else{
											echo '<div class="slideshow-container">';
											if($getRouteInfo['Screenshot1Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr01" src="img/screenshots/'.$getRouteInfo['Screenshot1Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot1Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											if($getRouteInfo['Screenshot2Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr02" src="img/screenshots/'.$getRouteInfo['Screenshot2Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot2Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											if($getRouteInfo['Screenshot3Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr03" src="img/screenshots/'.$getRouteInfo['Screenshot3Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot3Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											echo '<a class="prev" onclick="plusSlides(-1)">&#10094;</a>';
											echo '<a class="next" onclick="plusSlides(1)">&#10095;</a>';
											echo '</div></div>';
										}
										echo "<div class='correct_route_button'>
											<button type='submit' title='Popraw trasę' id='ets2_submit_button'><span class='far fa-edit' aria-hidden='true'></span>Popraw trasę</button>
										</div>
									</form>
								</div>
							</div>
						</div>";
							}
							else{
								if($getRouteInfo['Game'] == 'ats'){
									echo "<div class='block new_route'>
										<h1>Poprawianie trasy #EK"; echo test_input($_GET['id']); echo "</h1>
										<div class='block_content'>
											<div class='new_route_tab_ats tab_active'>";
									if(isSet($errorMsg)) echo $errorMsg;
									if(isSet($successMsg)) echo $successMsg;
									echo "<form action='correct_route?id=$getRouteId' method='POST' id='form_ats' style='align-items: center;' enctype='multipart/form-data'>
										<div class='form-group'  id='form-group_from_ats'>
											<label for='city_from_ats'>Z</label>
											<input type='text' value='".$getRouteInfo['CityFrom']."' list='cities_from_ats' id='cities_from_ats_input' name='city_from_ats' title='Miasto startowe' required/>
											<datalist id='cities_from_ats'>";
									$citiesListAts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ats' ORDER BY CityName ASC;");

									while($wiersz = mysqli_fetch_row($citiesListAts)){
										echo "<option value=\"".$wiersz[0]."\">";
									}
									echo "</datalist>
										</div>
										<div class='form-group'  id='form-group_to_ats'>
											<label for='city_to_ats'>Do</label>
											<input type='text' value='".$getRouteInfo['CityTo']."' list='cities_to_ats' id='cities_to_ats_input' name='city_to_ats' title='Miasto docelowe' required/>
											<datalist id='cities_to_ats'>";
									$citiesListEts = mysqli_query($conn, "SELECT CityName FROM cities WHERE Game = 'ats' ORDER BY CityName ASC;");

									while($wiersz = mysqli_fetch_row($citiesListEts)){
										echo "<option value=\"".$wiersz[0]."\">";
									}
									echo "</select>
										</div>
										<input type='text' name='game' value='ats' hidden>
										<div class='form-group'  id='form-group_load_ats'>
											<label for='load_ats'>Ładunek</label>
											<input type='text' value='".$getRouteInfo['LoadName']."' list='load_ats' id='load_ats_input' name='load_ats' title='Ładunek' required/>
											<datalist id='load_ats'>";
									$loadsListAts = mysqli_query($conn, "SELECT LoadName FROM loads WHERE Game = 'ats' ORDER BY LoadName ASC;");

									while($wiersz = mysqli_fetch_row($loadsListAts)){
										echo "<option value=\"".$wiersz[0]."\">";
									}
									echo "</select>
										</div>
										<div class='form-group'>
											<label for='tonnage_ats'>Tonaż ładunku (t)</label>
											<input type='number' value='".$getRouteInfo['Tonnage']."' min='0' max='999' name='tonnage_ats' title='Tonaż' required/>
										</div>
										<div class='form-group'>
											<label for='distance_ats'>Przebyty dystans (km)</label>
											<input type='number' value='".$getRouteInfo['Distance']."' min='0' max='999999' name='distance_ats' title='Dystans' required/>
										</div>
										<div class='form-group'>
											<label for='fuel_ats'>Zużyte paliwo (l)</label>
											<input type='number' value='".$getRouteInfo['Fuel']."' min='0' max='999999' step='0.1' name='fuel_ats' title='Zużyte paliwo' required/>
										</div>
										<div class='form-group'>
											<label for='damage_ats'>Uszkodzenia (%)</label>
											<input type='number' value='".$getRouteInfo['Damage']."' min='0' max='100' step='0.1' name='damage_ats' title='Uszkodzenia'/>
										</div>
										<input type='text' name='screenshot1_ats' value='".$getRouteInfo['Screenshot1Url']."' hidden />
										<input type='text' name='screenshot2_ats' value='".$getRouteInfo['Screenshot2Url']."' hidden />
										<input type='text' name='screenshot3_ats' value='".$getRouteInfo['Screenshot3Url']."' hidden />
										<div class='form-group'>
											<label for='note_ats'>Notatka:</label>
											<input type='textarea' value='".$getRouteInfo['Note']."' maxlenght='500' name='note_ats' title='Notatka'/>
										</div>";
										echo "<div class ='check_route_screenshots'><h2>Screenshoty:</h2>";
										if($getRouteInfo['Screenshot1Url'] == 'brak' AND $getRouteInfo['Screenshot2Url'] == 'brak' AND $getRouteInfo['Screenshot3Url'] == 'brak'){
											echo "Brak screenshot'ów.";
											echo "</div>";
										}
										else{
											echo '<div class="slideshow-container">';
											if($getRouteInfo['Screenshot1Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr01" src="img/screenshots/'.$getRouteInfo['Screenshot1Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot1Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											if($getRouteInfo['Screenshot2Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr02" src="img/screenshots/'.$getRouteInfo['Screenshot2Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot2Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											if($getRouteInfo['Screenshot3Url'] != 'brak'){
												echo '<div class="mySlides fade">';
												echo '<img class="scr03" src="img/screenshots/'.$getRouteInfo['Screenshot3Url'].'" data-zoom-image="img/screenshots/'.$getRouteInfo['Screenshot3Url'].'" style="width: 100%;" />';
												echo '</div>';
											}
											echo '<a class="prev" onclick="plusSlides(-1)">&#10094;</a>';
											echo '<a class="next" onclick="plusSlides(1)">&#10095;</a>';
											echo '</div></div>';
										}
										echo "<div class='correct_route_button'>
											<button type='submit' title='Popraw trasę' id='ats_submit_button'><span class='far fa-edit' aria-hidden='true'></span>Popraw trasę</button>
										</div>
									</form>
								</div>
							</div>
						</div>";
								}
							}
						}
					}
				}
			?>
		</section>
		<script type="text/javascript">
			var slideIndex = 1;
			showSlides(slideIndex);

			// Next/previous controls
			function plusSlides(n) {
			  showSlides(slideIndex += n);
			}

			// Thumbnail image controls
			function currentSlide(n) {
			  showSlides(slideIndex = n);
			}

			function showSlides(n) {
			  var i;
			  var slides = document.getElementsByClassName("mySlides");
			  if (n > slides.length) {slideIndex = 1} 
			  if (n < 1) {slideIndex = slides.length}
			  for (i = 0; i < slides.length; i++) {
			      slides[i].style.display = "none"; 
			  }
			  slides[slideIndex-1].style.display = "block"; 
			  $(".scr01").elevateZoom({zoomType: "inner", scrollZoom : true, cursor: "crosshair", zoomWindowFadeIn: 500, zoomWindowFadeOut: 500, lensFadeIn: 500, lensFadeOut: 500});
				$(".scr02").elevateZoom({zoomType: "inner", scrollZoom : true, cursor: "crosshair", zoomWindowFadeIn: 500, zoomWindowFadeOut: 500, lensFadeIn: 500, lensFadeOut: 500});
				$(".scr03").elevateZoom({zoomType: "inner", scrollZoom : true, cursor: "crosshair", zoomWindowFadeIn: 500, zoomWindowFadeOut: 500, lensFadeIn: 500, lensFadeOut: 500});
			}
		</script>
		<script type="text/javascript">
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