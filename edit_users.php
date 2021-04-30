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
		<title>Edytowanie użytkowników - EKARD VTC</title>
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
					<a href="edit_users" title="Edytuj użytkowników" class="nav_item nav_active"><li>Użytkownicy</li></a>
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
		<section class="edit_users">
			<?php
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					$operation_type = $_POST["operation_type"];

					if($operation_type == "change_rank"){
						$changeUser = $_POST["user_change_rank"];
						$oldRank = mysqli_fetch_row(mysqli_query($conn, "SELECT Rank FROM users WHERE Login = '$changeUser';"));
						$newRank = $_POST["rank"];

						if($oldRank[0] == "Okres próbny"){
							$oldRankNumber = 1;
						}
						if($oldRank[0] == "Kierowca"){
							$oldRankNumber = 2;
						}
						if($oldRank[0] == "Spedytor"){
							$oldRankNumber = 3;
						}
						if($oldRank[0] == "Rekruter"){
							$oldRankNumber = 4;
						}
						if($oldRank[0] == "Dyspozytor"){
							$oldRankNumber = 4;
						}
						if($oldRank[0] == "VIP"){
							$oldRankNumber = 5;
						}
						if($oldRank[0] == "Prezes"){
							$oldRankNumber = 6;
						}
						if($oldRank[0] == "Właściciel"){
							$oldRankNumber = 7;
						}

						if($newRank == "Okres próbny"){
							$newRankNumber = 1;
						}
						if($newRank == "Kierowca"){
							$newRankNumber = 2;
						}
						if($newRank == "Spedytor"){
							$newRankNumber = 3;
						}
						if($newRank == "Rekruter"){
							$newRankNumber = 4;
						}
						if($newRank == "Dyspozytor"){
							$newRankNumber = 4;
						}
						if($newRank == "VIP"){
							$newRankNumber = 5;
						}
						if($newRank == "Prezes"){
							$newRankNumber = 6;
						}
						if($newRank == "Właściciel"){
							$newRankNumber = 7;
						}

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

						$isInDB = mysqli_query($conn, "SELECT Login FROM users WHERE Login = '$changeUser';");
						if(mysqli_num_rows($isInDB) == 1){
							if($oldRank[0] != $newRank){
								mysqli_query($conn, "UPDATE users SET Rank = '$newRank' WHERE Login = '$changeUser';");
								
								if($newRankNumber >= $oldRankNumber){
									$logMsg = "<b>awansował</b> na ".$logMsgRank.".";
								}
								else{
									$logMsg = "<b>został zdegradowany</b> na ".$logMsgRank.".";
								}
								mysqli_query($conn, "INSERT INTO logs (User, Message, DateTime) VALUES ('$changeUser', '$logMsg', NOW());");
								$successRankMsg = '<div class="if_success">Pomyślnie zmieniono rangę użytkownikowi '.$changeUser.'.</div>';
							}
							else{
								$errorRankMsg = '<div class="if_error">Użytkownik już posiada tę rangę.</div>';
							}
						}
					}
					if($operation_type == "change_password"){
						$userToChangePass = $_POST["user_change_password"];
						$newPassword = test_input($_POST["new_password"]);
						$newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);

						mysqli_query($conn, "UPDATE users SET Password = '$newPasswordHashed' WHERE Login = '$userToChangePass';");
						$successMsg = '<div class="if_success">Hasło zostało zmienione.</div>';
					}
					if($operation_type == "delete"){
						$removedUser = $_POST["user_delete"];
						$isInDB = mysqli_query($conn, "SELECT Login FROM users WHERE Login = '$removedUser';");
						if(mysqli_num_rows($isInDB) == 1){
							$addedEts2 = mysqli_fetch_row(mysqli_query($conn, "SELECT AddedRoutesEts2 FROM users WHERE Login = '$removedUser';"));
							$addedAts = mysqli_fetch_row(mysqli_query($conn, "SELECT AddedRoutesAts FROM users WHERE Login = '$removedUser';"));
							mysqli_query($conn, "UPDATE users SET AddedRoutesEts2 = AddedRoutesEts2 + ".$addedEts2[0].", AddedRoutesAts = AddedRoutesAts + ".$addedAts[0]." WHERE Id = 35;");
							mysqli_query($conn, "DELETE FROM users WHERE Login = '$removedUser';");
							$logMsg = "został <b>zwolniony</b> z firmy.";
							mysqli_query($conn, "INSERT INTO logs (User, Message, DateTime) VALUES ('$removedUser', '$logMsg', NOW());");
							$successDeleteMsg = '<div class="if_success">Pomyślnie usunięto użytkownika.</div>';
						}
						else{
							$errorDeleteMsg = '<div class="if_error">Ten użytkownik już został usunięty.</div>';
						}
					}
				}
			?>
			<div class="block change_rank">
				<h1>Zmiana rangi</h1>
				<div class="block_content">
					<?php if(isSet($successRankMsg)) echo $successRankMsg; ?>
					<?php if(isSet($errorRankMsg)) echo $errorRankMsg; ?>
					<form action="edit_users" method="POST">
						<div class="form-group">
							<label for="user_change_rank">Użytkownik</label>
							<select name="user_change_rank" title="Użytkownik" required>
								<option value="" disabled selected hidden>Wybierz użytkownika</option>
								<?php
									if($userRank[0] == "Prezes"){
										$usersList = mysqli_query($conn, "SELECT Login, Rank FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Prezes\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].' - '.$wiersz[1].'</option>';
										}
									}

									if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel"){
										$usersList = mysqli_query($conn, "SELECT Login, Rank FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].' - '.$wiersz[1].'</option>';
										}
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="rank">Nowa ranga</label>
							<select name="rank" title="Ranga" required>
								<option value="" disabled selected hidden>Wybierz rangę</option>
								<option value="Okres próbny">Okres próbny</option>
								<option value="Kierowca">Kierowca</option>
								<option value="Spedytor">Spedytor</option>
								<option value="Rekruter">Osoba ds. Rekrutacji</option>
								<option value="Dyspozytor">Dyspozytor</option>
								<option value="VIP">VIP</option>
								<?php
									if($userRank[0] == "Właściciel" OR $userRank[0] == "Programista"){
										echo '<option value="Prezes">Prezes</option>';
										echo '<option value="Właściciel">Właściciel</option>';
									}
								?>
							</select>
						</div>
						<input type="text" name="operation_type" value="change_rank" hidden />
						<button type="submit" title="Zmień rangę"><span class="fas fa-sync-alt" aria-hidden="true"></span>Zmień rangę</button>
					</form>
				</div>
			</div>
			<div class="block change_pass">
				<h1>Zmiana hasła</h1>
				<div class="block_content">
					<?php if(isSet($successMsg)) echo $successMsg; ?>
					<form action="edit_users" method="POST">
						<div class="form-group">
							<label for="user_change_password">Użytkownik</label>
							<select name="user_change_password" title="Użytkownik" required>
								<option value="" disabled selected hidden>Wybierz użytkownika</option>
								<?php
									if($userRank[0] == "Prezes"){
										$usersList = mysqli_query($conn, "SELECT Login FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Prezes\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].'</option>';
										}
									}

									if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel"){
										$usersList = mysqli_query($conn, "SELECT Login FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].'</option>';
										}
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="new_password">Nowe hasło</label>
							<input type="password" name="new_password" title="Nowe hasło" />
						</div>
						<input type="text" name="operation_type" value="change_password" hidden />
						<button type="submit" title="Zmień haśło"><span class="fas fa-unlock" aria-hidden="true"></span>Zmień hasło</button>
					</form>
				</div>
			</div>
			<div class="block del_user">
				<h1>Usuwanie użytkownika</h1>
				<div class="block_content">
					<?php if(isSet($errorDeleteMsg)) echo $errorDeleteMsg; ?>
					<?php if(isSet($successDeleteMsg)) echo $successDeleteMsg; ?>
					<form action="edit_users" method="POST">
						<div class="form-group">
							<label for="user_delete">Użytkownik</label>
							<select name="user_delete" title="Użytkownik" required>
								<option value="" disabled selected hidden>Wybierz użytkownika</option>
								<?php
									if($userRank[0] == "Prezes"){
										$usersList = mysqli_query($conn, "SELECT Login FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Prezes\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].'</option>';
										}
									}

									if($userRank[0] == "Programista" OR $userRank[0] == "Właściciel"){
										$usersList = mysqli_query($conn, "SELECT Login FROM users WHERE Id != 35 AND Rank != \"Właściciel\" AND Rank != \"Programista\";");
										while ($wiersz = mysqli_fetch_row($usersList)){
											echo '<option value="'.$wiersz[0].'">'.$wiersz[0].'</option>';
										}
									}
								?>
							</select>
						</div>
						<input type="text" name="operation_type" value="delete" hidden />
						<button type="submit" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?');" title="Usuń użytkownika"><span class="fas fa-user-times" aria-hidden="true"></span>Usuń użytkownika</button>
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