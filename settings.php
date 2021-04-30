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
		<title>Ustawienia - EKARD VTC</title>
	</head>
	<body>
		<?php require_once("header.php"); ?>
		<script type="text/javascript">
			var profil = document.querySelector("#settings");
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
				$avatarSrc = mysqli_fetch_row(mysqli_query($conn, "SELECT ImgUrl FROM users WHERE Login = '$login';"));
				if($_SERVER["REQUEST_METHOD"] == "POST"){
					if(test_input($_POST['operation_type']) == "change_avatar"){
						$avatarFile = $_FILES["avatar_img"]["name"];
						$imageFileType = strtolower(pathinfo($avatarFile,PATHINFO_EXTENSION));

						if(!empty($avatarFile)){
							if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg"){
								if($_FILES["avatar_img"]["error"] != 1){
									move_uploaded_file($_FILES["avatar_img"]["tmp_name"],"img/avatars/".$login.".jpg");
									mysqli_query($conn, "UPDATE users SET ImgUrl = '$login.jpg' WHERE Login = '$login';");
									$successMsgAvatar = '<div class="if_success">Twój avatar został zmieniony.</div>';
								}
								else{
									$errorMsgAvatar = '<div class="if_error">Zbyt duży plik. Maksymalny rozmiar: 4 MB.</div>';
								}
							}
							else{
								$errorMsgAvatar = '<div class="if_error">Nieobsługiwany format pliku. Obsługiwane formaty: jpg, jpeg, png.</div>';
							}
						}
						else{
							$errorMsgAvatar = '<div class="if_error">Wybierz plik z dysku, aby zmienić avatar.</div>';
						}
					}
					if(test_input($_POST['operation_type']) == "links"){
						$truckersmpLink = test_input($_POST['truckersmp']);
						$steamLink = test_input($_POST['steam']);

						mysqli_query($conn, "UPDATE users SET TruckersMPUrl = '$truckersmpLink' WHERE Login = '$login';");
						mysqli_query($conn, "UPDATE users SET SteamUrl = '$steamLink' WHERE Login = '$login';");
						$successMsgLinks = '<div class="if_success">Linki zostały zaktualizowane.</div>';
					}
					if(test_input($_POST['operation_type']) == "change_pass"){
						$newPassword = test_input($_POST["pass1"]);
						$repeatedNewPassword = test_input($_POST["pass2"]);
						$newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);

						if($newPassword != $repeatedNewPassword){
							$errorMsgPass = '<div class="if_error">Podane hasła nie pasują do siebie.</div>';
						}
						else{
							mysqli_query($conn, "UPDATE users SET Password = '$newPasswordHashed' WHERE Login = '$login';");
							$successMsgPass = '<div class="if_success">Twoje hasło zostało zmienione.</div>';
						}
					}
				}
			?>
			<div class="block settings">
				<h1>Zmiana avatara</h1>
				<div class="block_content">
					<?php if(isSet($errorMsgAvatar)) echo $errorMsgAvatar; ?>
					<?php if(isSet($successMsgAvatar)) echo $successMsgAvatar; ?>
					<form action="settings" method="POST" enctype="multipart/form-data">
						<input type="text" name="operation_type" value="change_avatar" hidden />
						<img class="avatar_img" src="img/avatars/<?php echo $avatarSrc[0]; ?>" alt="Avatar" />
						<input type="file" name="avatar_img" accept=".jpg, .jpeg, .png" title="Wybierz plik" />
						<button type="submit" title="Zmień avatar"><span class="fas fa-sync-alt" aria-hidden="true"></span>Zmień avatar</button>
					</form>
				</div>
			</div>
			<div class="block settings">
				<h1>Twoje odnośniki</h1>
				<div class="block_content">
					<?php if(isSet($errorMsgLinks)) echo $errorMsgLinks; ?>
					<?php if(isSet($successMsgLinks)) echo $successMsgLinks; ?>
					<?php
						$truckersMPActualLink = mysqli_fetch_row(mysqli_query($conn, "SELECT TruckersMPUrl FROM users WHERE Login = '$login';"));
						$steamActualLink = mysqli_fetch_row(mysqli_query($conn, "SELECT SteamUrl FROM users WHERE Login = '$login';"));
					?>
					<form action="settings" method="POST">
						<input type="text" name="operation_type" value="links" hidden />
						<div class="form-group">
							<label for="truckersmp"><img class="truckersmp_icon" src="img/truckersmp.png" alt="TruckersMP" />TruckersMP</label>
							<input type="url" name="truckersmp" title="Konto TruckersMP" value="<?php echo $truckersMPActualLink[0]; ?>"/>
						</div>
						<div class="form-group">
							<label for="steam"><span class="fab fa-steam fa-2x steam_icon"></span>Steam</label>
							<input type="url" name="steam" title="Konto Steam" value="<?php echo $steamActualLink[0]; ?>"/>
						</div>
						<button type="submit" title="Zapisz"><span class="fas fa-save" aria-hidden="true"></span>Zapisz</button>
					</form>
				</div>
			</div>
			<div class="block settings">
				<h1>Zmiana hasła</h1>
				<div class="block_content">
					<?php if(isSet($errorMsgPass)) echo $errorMsgPass; ?>
					<?php if(isSet($successMsgPass)) echo $successMsgPass; ?>
					<form action="settings" method="POST">
						<input type="text" name="operation_type" value="change_pass" hidden />
						<div class="form-group">
							<label for="pass1">Nowe hasło</label>
							<input type="password" name="pass1" title="Nowe hasło" required />
						</div>
						<div class="form-group">
							<label for="pass2">Powtórz hasło</label>
							<input type="password" name="pass2" title="Powtórz hasło" required />
						</div>
						<button type="submit" title="Zmień hasło"><span class="fas fa-unlock" aria-hidden="true"></span>Zmień hasło</button>
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