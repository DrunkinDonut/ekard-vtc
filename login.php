<?php
	session_start();
	session_regenerate_id();
	require_once("db_conn.php");
	error_reporting(0);
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<meta charset="UTF-8" />
		<meta name="description" content="System VTC firmy EKARD." />
		<meta name="author" content="Natan Ryl" />
		<link rel="shortcut icon" href="favicon.ico"/>
		<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
		<link rel="icon" type="image/png" sizes="96x96" href="favicon-96x96.png" />
		<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<meta name="theme-color" content="#039BE5" />
		<link rel="stylesheet" href="css/fontawesome-all.css" />

		<title>EKARD VTC - Logowanie</title>

		<style>
			* {
				margin: 0;
				padding: 0;
			}

			@font-face {
				font-family: 'Roboto';
				src: url('webfonts/Roboto-Regular.eot');
				src: url('webfonts/Roboto-Regular.eot?#iefix') format('embedded-opentype'),
					url('webfonts/Roboto-Regular.woff') format('woff'),
					url('webfonts/Roboto-Regular.ttf') format('truetype');
				font-weight: normal;
				font-style: normal;
			}

			@font-face {
				font-family: 'Roboto-light';
				src: url('webfonts/Roboto-Light.eot');
				src: url('webfonts/Roboto-Light.eot?#iefix') format('embedded-opentype'),
					url('webfonts/Roboto-Light.woff') format('woff'),
					url('webfonts/Roboto-Light.ttf') format('truetype');
				font-weight: 300;
				font-style: normal;
			}

			body {
				background-color: #3a4144;
				display: flex;
				justify-content: center;
				align-items: center;
				flex-direction: column;
				height: 100vh;
			}

			.login_header {
				color: #039BE5;
				font-family: 'Roboto', sans-serif;
				font-size: 25px;
			}

			.login_header sup {
				font-family: 'Roboto-light', sans-serif;
				font-size: 18px;
			}

			.login_block {
				background-color: #4b5357;
				font-family: 'Roboto-light', sans-serif;
				font-size: 14px;
				line-height: 25px;
				padding: 10px 15px;
				margin: 20px;
				max-width: 250px;
				width: 100%;
				border-top: 2px solid #313639;
				border-bottom: 1px solid #313639;
				border-left: 1px solid #313639;
				border-right: 1px solid #313639;
			}

			.login_form {
				display: flex;
				flex-direction: column;
			}

			.if_error {
				color: #F44336;
				text-align: center;
			}

			.form-group {
				margin-bottom: 15px;
				display: flex;
				flex-direction: column;
				width: 100%;
			}

			label {
				color: #ffffff;
			}

			input {
				padding: 10px 15px;
				background-color: #43494B;
				border: 1px solid #313639;
				border-radius: 3px;
				color: #fff;
			}

			span {
				color: #868e91;
				font-size: 12px;
				line-height: 15px;
				margin-top: 5px;
			}

			button {
				border: 0;
				color: #ffffff;
				background-color: #039BE5;
				padding: 10px 15px;
				align-self: center;
				cursor: pointer;
				border-radius: 3px;
				text-shadow: 0 1px 2px rgba(0,0,0,0.20);
				transition: all 0.2s ease-in-out;
			}

			button:hover {
				background-color: #038AC8;
			}

			button span {
				color: #fff;
				margin-right: 5px;
				margin-top: 0px;
				font-size: 14px;
			}

			.login_footer {
				color: #ffffff;
				font-family: 'Roboto-light', sans-serif;
				font-size: 14px;
				line-height: 15px;
				text-align: center;
			}

			.login_footer a {
				color: #ffffff;
				text-decoration: none;
			}
		</style>
	</head>
	<body>
		<div class="login_header">
			EKARD <sup>VTC</sup>
		</div>
		<?php
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$login = test_input($_POST["login"]);
				$password = test_input($_POST["password"]);

				$findUser = mysqli_query($conn, "SELECT Password FROM users WHERE BINARY Login = '$login';");
				$user = mysqli_fetch_row($findUser);
				if(password_verify($password, $user[0])){
					$_SESSION["loggedIn"] = TRUE;
					$_SESSION["IPAddr"] = $_SERVER["REMOTE_ADDR"];
					$_SESSION["Login"] = $login;
					header("Location: index");
				}
				else{
					$error = "Błędny login lub hasło!";
				}
			}
		?>
		<div class="login_block">
			<form class="login_form" action="login" method="POST">
				<div class="if_error">
					<?php if(isSet($error)) echo $error; ?>
				</div>
				<div class="form-group">
					<label for="login">Login</label>
					<input type="text" name="login" title="Wprowadź swój login." required autofocus />
				</div>
				<div class="form-group">
					<label for="password">Hasło</label>
					<input type="password" name="password" title="Wprowadź swoje hasło." required/>
					<span>Nie pamiętasz hasła?<br />Skontaktuj się z zarządem firmy.</span>
				</div>
				<button type="submit"><span class="fas fa-sign-in-alt"></span>Zaloguj</button>
			</form>
		</div>
		<div class="login_footer">
			Created by <a href="http://steamcommunity.com/id/drunkin_donut" target="_blank">Natan Ryl</a>.<br /><br />
			&copy;Copyright <?php echo date("Y"); ?>.<br />
			Wszelkie prawa zastrzeżone.<br />
		</div>
		<div class="login_block" style="color: white; text-align: center;">
			<p>
				System rang - konta (Login - hasło):<br>
				Okres probny - okres_probny<br>
				Kierowca - kierowca<br>
				Spedytor - spedytor<br>
				Rekrutacja - rekrutacja<br>
				Dyspozytor - dyspozytor<br>
				VIP - vip<br>
				Prezes - prezes<br>
				Wlasciciel - wlasciciel<br>
			</p>
		</div>
		<?php mysqli_close($conn); ?>
	</body>
</html>