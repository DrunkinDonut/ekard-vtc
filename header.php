<?php 
	if($_SESSION["loggedIn"] != TRUE){
		header("Location: login");
	}
	else{
		if($_SERVER["REMOTE_ADDR"] != $_SESSION["IPAddr"]){
			header("Location: login");
		}
	}

	$login = $_SESSION["Login"];
	$userId = mysqli_fetch_row(mysqli_query($conn, "SELECT Id FROM users WHERE Login = '$login';"));
?>
<header>
			<div class="mobile_nav_icon">
				<button onclick="toggleMenu();"><i id="mobile_menu_icon" class="fas fa-bars"></i></button>
			</div>
			<a href="index" title="Strona główna" class="header_logo">
				<span>EKARD <sup>VTC</sup></span>
			</a>
			<div class="user_nav_username">
				WITAJ, <?php echo strtoupper($_SESSION["Login"]); ?>
			</div>
			<div class="user_nav_icons">
				<a href="profile?id=<?php echo $userId[0]; ?>" title="Profil" class="user_nav_icon" id="profile"><span class="fas fa-user" aria-hidden="true"></span></a>
				<a href="settings" title="Ustawienia" class="user_nav_icon" id="settings"><span class="fas fa-cog" aria-hidden="true"></span></a>
				<a href="logout" title="Wyloguj" class="user_nav_icon fas fa-sign-out-alt"></a>
			</div>
		</header>
