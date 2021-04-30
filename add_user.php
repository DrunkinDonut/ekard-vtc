<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);

	$login = $_SESSION["Login"];
	$userRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Login = '$login';"));
				
	if($userRank[0] == "Dyspozytor" OR $userRank[0] == "Spedytor" OR $userRank[0] == "Kierowca" OR $userRank[0] == "Okres próbny"){
		header("Location: index");
	}
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<?php
			require_once("head.html");
		?>
		<title>Dodawanie użytkownika - EKARD VTC</title>
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
					<a href="add_user" title="Dodaj użytkownika" class="nav_item nav_active"><li>Dodaj użytkownika</li></a>
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
					$newLogin = test_input($_POST["login"]);
					$newPassword = test_input($_POST["password"]);
					$newRank = test_input($_POST["rank"]);

					$isLoginInDB = mysqli_num_rows(mysqli_query($conn, "SELECT Login FROM users WHERE Login = '$newLogin';"));

					if($isLoginInDB == 0){
						$userNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
						mysqli_query($conn, "INSERT INTO users (Login, Password, Rank, Joined) VALUES ('$newLogin', '$userNewPassword', '$newRank', DATE(NOW()));");
						$successMsg = '<div class="if_success">Dodano nowego użytkownika.</div>';

						switch ($newRank) {
							case 'Okres próbny':
								$logMsgRank = "<b>Okres próbny</b>";
								break;
							case 'Kierowca':
								$logMsgRank = "stanowisko <b>Kierowcy</b>";
								break;
							case 'Spedytor':
								$logMsgRank = "stanowisko <b>Spedytora</b>";
								break;
							case 'Rekruter':
								$logMsgRank = "stanowisko <b>Osoby ds. Rekrutacji</b>";
								break;
							case 'Dyspozytor':
								$logMsgRank = "stanowisko <b>Dyspozytora</b>";
								break;
							case 'VIP':
								$logMsgRank = "stanowisko <b>VIPa</b>";
								break;
							case 'Prezes':
								$logMsgRank = "stanowisko <b>Prezesa</b>";
								break;
							case 'Właściciel':
								$logMsgRank = "stanowisko <b>Właściciela</b>";
								break;
						}
						$logMsg = 'został zatrudniony na '.$logMsgRank.'.';
						mysqli_query($conn, "INSERT INTO logs (User, Message, DateTime) VALUES ('$newLogin', '$logMsg', NOW());");
					}
					else{
						$errorMsg = '<div class="if_error">Podany login już istnieje.</div>';
					}
				}
			?>
			<div class="block add_user">
				<h1>Dodawanie użytkownika</h1>
				<div class="block_content">
					<?php if(isSet($errorMsg)) echo $errorMsg; ?>
					<?php if(isSet($successMsg)) echo $successMsg; ?>
					<form action="add_user" method="POST">
						<div class="form-group">
							<label for="login">Login</label>
							<input type="text" name="login" title="Login użytkownika" required />
						</div>
						<div class="form-group">
							<label for="password">Hasło</label>
							<input type="password" name="password" title="Hasło użytkownika" required />
						</div>
						<div class="form-group">
							<label for="rank">Ranga</label>
							<select name="rank" title="Ranga użytkownika" required>
								<option value="" disabled selected hidden>Wybierz rangę</option>
								<option value="Okres próbny">Okres próbny</option>
								<option value="Kierowca">Kierowca</option>
								<?php
									if($userRank[0] == "VIP" OR $userRank[0] == "Prezes" OR $userRank[0] == "Właściciel" OR $userRank[0] == "Programista"){
										echo '<option value="Spedytor">Spedytor</option>';
										echo '<option value="Rekruter">Osoba ds. Rekrutacji</option>';
										echo '<option value="Dyspozytor">Dyspozytor</option>';
										if($userRank[0] == "Prezes" OR $userRank[0] == "Właściciel" OR $userRank[0] == "Programista"){
											echo '<option value="VIP">VIP</option>';
										}
										if($userRank[0] == "Właściciel" OR $userRank[0] == "Programista"){
											echo '<option value="Prezes">Prezes</option>';
											echo '<option value="Właściciel">Właściciel</option>';
										}
									}
								?>
							</select>
						</div>
						<button type="submit" title="Dodaj użytkownika"><span class="fas fa-user-plus" aria-hidden="true"></span>Dodaj użytkownika</button>
					</form>
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