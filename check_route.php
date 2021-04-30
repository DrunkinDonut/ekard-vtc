<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);

	$login = $_SESSION["Login"];
	$userRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Login = '$login';"));
				
	if($userRank[0] == "Rekruter" OR $userRank[0] == "Spedytor" OR $userRank[0] == "Kierowca" OR $userRank[0] == "Okres próbny"){
		header("Location: index");
	}
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?php
			require_once("head.html");
		?>
		<title>Sprawdzanie tras - EKARD VTC</title>
		
		<script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
		<script src="js/jquery.elevateZoom.js" type="text/javascript"></script>
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
					<a href="check_route" title="Sprawdzanie tras" class="nav_item nav_active"><li>Sprawdzanie tras';if($numberRoutesToCheck >= 1){echo '<span class="number_routes_to_check">'.$numberRoutesToCheck.'</span>';}echo '</li></a>
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
		<section><?php if(isset($_GET["operation"]) AND isset($_GET["route"]) AND isset($_GET["status"])){echo " class='section_check_route'";} ?>>
			<?php
				if(isset($_GET["operation"]) AND isset($_GET["status"])){
					if($_GET["operation"] == "confirm" AND $_GET["status"] == "success"){
						echo "<div class='operation_status'>Trasa #".$_GET["route"]." została zatwierdzona.</div>";
					}
					if($_GET["operation"] == "correct" AND $_GET["status"] == "success"){
						echo "<div class='operation_status'>Trasa #".$_GET["route"]." została wysłana do poprawy.</div>";
					}
					if($_GET["operation"] == "discard" AND $_GET["status"] == "success"){
						echo "<div class='operation_status'>Trasa #".$_GET["route"]." została odrzucona.</div>";
					}
				}

				if($_SERVER["REQUEST_METHOD"] == "POST"){
					$decision = test_input($_POST["decision"]);
					$routeId = test_input($_GET["id"]);

					if($decision == "confirm"){
						mysqli_query($conn, "UPDATE routes SET Status = 'Zatwierdzona' WHERE Id = '$routeId'");
						mysqli_query($conn, "UPDATE routes SET CheckedBy = '$login' WHERE Id = '$routeId'");
						header("Location: check_route?operation=confirm&route=EK".$routeId."&status=success");
						$logRouteInfo = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM routes WHERE Id = '$routeId';"));
						$logMsg = "przewiózł <b>".$logRouteInfo["LoadName"]." (".$logRouteInfo['Tonnage']." t)</b> z <b>".$logRouteInfo["CityFrom"]."</b> do <b>".$logRouteInfo["CityTo"]."</b>.";
						mysqli_query($conn, "INSERT INTO logs (User, Message, DateTime) VALUES (\"".$logRouteInfo["Login"]."\", '$logMsg', \"".$logRouteInfo["DateAndTime"]."\");");
					}

					if($decision == "to_correct"){
						$correctReason = test_input($_POST["to_correct_reason"]);
						mysqli_query($conn, "UPDATE routes SET Status = 'Do poprawy', Reason = '$correctReason' WHERE Id = '$routeId'");
						mysqli_query($conn, "UPDATE routes SET CheckedBy = '$login' WHERE Id = '$routeId'");
						header("Location: check_route?operation=correct&route=EK".$routeId."&status=success");
					}

					if($decision == "discard"){
						$discardReason = test_input($_POST["discard_reason"]);
						mysqli_query($conn, "UPDATE routes SET Status = 'Odrzucona', Reason = '$discardReason' WHERE Id = '$routeId'");
						mysqli_query($conn, "UPDATE routes SET CheckedBy = '$login' WHERE Id = '$routeId'");
						header("Location: check_route?operation=discard&route=EK".$routeId."&status=success");
					}
				}

				if(isset($_GET["id"])){
					$routeId = test_input($_GET["id"]);
					$checkIfChecked = mysqli_fetch_array(mysqli_query($conn, "SELECT Status FROM routes WHERE Id = '$routeId';"));

					if($checkIfChecked['Status'] != "Oczekuje"){
						echo "<div class='block'><h1>Sprawdzanie trasy</h1><div class='block_content center'>Nie znaleziono podanej trasy do sprawdzenia.</div></div>";
					}
					else{
						$getRouteInfo = mysqli_query($conn, "SELECT * FROM routes WHERE Id = '$routeId';");
						$routeInfo = mysqli_fetch_array($getRouteInfo);
						$routeInfoUserId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = \"".$routeInfo['Login']."\";"));
						echo "<div class='block check_single_route'><h1>Sprawdzanie trasy #EK".$routeId."</h1><div class='block_content'><table>";
						echo "<tr><td>ID Zlecenia:</td><td>#EK".$routeInfo['Id']."</td></tr>";
						echo "<tr><td>Data i godzina:</td><td>".$routeInfo['DateAndTime']."</td></tr>";
						echo "<tr><td>Kierowca:</td><td><a class='profile_link' href='profile?id=".$routeInfoUserId['Id']."' title='".$routeInfo['Login']."'>".$routeInfo['Login']."</a></td></tr>";
						echo "<tr><td>Gra:</td><td>";if($routeInfo['Game'] == 'ats'){echo "American Truck Simulator";}else{echo "Euro Truck Simulator 2";}echo "</td></tr>";
						echo "<tr><td>Miejsce rozpoczęcia:</td><td>".$routeInfo['CityFrom']."</td></tr>";
						echo "<tr><td>Miejsce zakończenia:</td><td>".$routeInfo['CityTo']."</td></tr>";
						echo "<tr><td>Ładunek:</td><td>".$routeInfo['LoadName']." (".$routeInfo['Tonnage']." t)</td></tr>";
						echo "<tr><td>Przebyty dystans:</td><td>".number_format($routeInfo['Distance'], 0, ',', ' ')." km</td></tr>";
						echo "<tr><td>Zużyte paliwo:</td><td>".number_format($routeInfo['Fuel'], 1, '.', ' ')." l</td></tr>";
						echo "<tr><td>Średnie spalanie:</td><td>".number_format($routeInfo['AvgFuelConsumption'], 1, '.', ' ')." l/100km</td></tr>";
						echo "<tr><td>Uszkodzenia:</td><td>".$routeInfo['Damage']."%</td></tr>";
						echo "<tr><td>Zarobiona gotówka:</td><td>".number_format($routeInfo['EarnedMoney'], 2, ',', ' ')."zł</td></tr>";
						echo "<tr><td>Dodatkowe informacje:</td><td>"; if($routeInfo['Note'] == ''){echo "brak";}else{echo $routeInfo['Note'];} echo "</td></tr>";
						echo "</table>";
						echo "<div class ='check_route_screenshots'><h2>Screenshoty:</h2>";
						if($routeInfo['Screenshot1Url'] == 'brak' AND $routeInfo['Screenshot2Url'] == 'brak' AND $routeInfo['Screenshot3Url'] == 'brak'){
							echo "Brak screenshot'ów.";
						}
						else{
							echo '<div class="slideshow-container">';
							if($routeInfo['Screenshot1Url'] != 'brak'){
								echo '<div class="mySlides fade">';
								echo '<img class="scr01" src="img/screenshots/'.$routeInfo['Screenshot1Url'].'" data-zoom-image="img/screenshots/'.$routeInfo['Screenshot1Url'].'" style="width: 100%;" />';
								echo '</div>';
							}
							if($routeInfo['Screenshot2Url'] != 'brak'){
								echo '<div class="mySlides fade">';
								echo '<img class="scr02" src="img/screenshots/'.$routeInfo['Screenshot2Url'].'" data-zoom-image="img/screenshots/'.$routeInfo['Screenshot2Url'].'" style="width: 100%;" />';
								echo '</div>';
							}
							if($routeInfo['Screenshot3Url'] != 'brak'){
								echo '<div class="mySlides fade">';
								echo '<img class="scr03" src="img/screenshots/'.$routeInfo['Screenshot3Url'].'" data-zoom-image="img/screenshots/'.$routeInfo['Screenshot3Url'].'" style="width: 100%;" />';
								echo '</div>';
							}
							echo '<a class="prev" onclick="plusSlides(-1)">&#10094;</a>';
							echo '<a class="next" onclick="plusSlides(1)">&#10095;</a>';
							echo '</div></div>';
						}
						echo "<div class='decision_block'>";
						echo "<div class='decision_confirm'><form action='check_route?id=".$routeInfo["Id"]."' method='POST'><input type='text' name='decision' value='confirm' hidden /><button type='submit' title='Zatwierdź trasę'><span class='fas fa-check'></span>Zatwierdź</button></form></div>";
						echo "<div class='decision_to_correct'><form action='check_route?id=".$routeInfo["Id"]."' method='POST'><input type='text' name='decision' value='to_correct' hidden /><label for='to_correct_reason'>Powód</label><input type='text' name='to_correct_reason' required /><button type='submit' title='Wyślij trasę do poprawy'><span class='far fa-edit'></span>Do poprawy</button></form></div>";
						echo "<div class='decision_discard'><form action='check_route?id=".$routeInfo["Id"]."' method='POST'><input type='text' name='decision' value='discard' hidden /><label for='discard_reason'>Powód</label><input type='text' name='discard_reason' required /><button type='submit' title='Odrzuć trasę'><span class='fas fa-times'></span>Odrzuć</button></form></div>";
						echo "</div></div></div>";
					}
				}
				else{
					echo "<div class='check_route'>
						<h1>Trasy do sprawdzenia</h1>
						<div class='block_content'>";
							$routesToCheck = mysqli_query($conn, "SELECT * FROM routes WHERE Status = 'Oczekuje' ORDER BY DateAndTime;");
							if(mysqli_num_rows($routesToCheck) >= 1){
								echo "<div class='check_route_table'><table><thead><tr><th>Gra</th><th>Dodano</th><th>Kierowca</th><th>Miejsce rozpoczęcia</th><th>Miejsce zakończenia</th><th>Ładunek</th><th>Dystans</th><th></th></thead><tbody>";
								while($route = mysqli_fetch_array($routesToCheck)){
									$routeDriverId = mysqli_fetch_array(mysqli_query($conn, "SELECT Id FROM users WHERE Login = \"".$route['Login']."\";"));
									echo "<tr><td>";if($route['Game'] == 'ats'){echo "ATS";}else{echo "ETS2";} echo "</td><td>".$route['DateAndTime']."</td><td><a class='profile_link' href='profile?id=".$routeDriverId['Id']."' title='".$route['Login']."'>".$route['Login']."</a></td><td>".$route['CityFrom']."</td><td>".$route['CityTo']."</td><td>".$route['LoadName']." (".$route['Tonnage']." t)</td><td>".number_format($route['Distance'], 0, ',', ' ')." km</td><td style='width: 180px;'><a class='check_route_button' href='check_route?id=".$route['Id']."' title='Sprawdź trasę #EK".$route['Id']."'><span class='far fa-square fa-sm'></span><span class='far fa-check-square fa-sm'></span>Sprawdź</a></td></tr>";
								}
								echo "</tbody></table></div>";
							}
							else{
								echo "<div style='font-size: 25px;' class='center'>Wszystkie trasy zostały sprawdzone.</div>";
							}
					echo"</div></div>";
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