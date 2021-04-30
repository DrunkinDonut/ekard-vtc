<?php
	session_start();
	session_regenerate_id();

	if($_SESSION["loggedIn"] != TRUE){
		header("Location: ../login");
	}
	else{
		if($_SERVER["REMOTE_ADDR"] != $_SESSION["IPAddr"]){
			header("Location: ../login");
		}
	}

	$login = $_SESSION["Login"];
?>
<!DOCTYPE html>
<html lang="pl">
	<head>
		<meta name="description" content="Cyfrowy tachograf dla kierowców firmy EKARD." />
		<meta name="author" content="Natan Ryl" />
		<link rel="shortcut icon" href="../favicon.ico"/>
		<link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
		<link rel="icon" type="image/png" sizes="96x96" href="../favicon-96x96.png" />
		<link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
		<meta name="viewport" content="width=1007px" />
		<meta name="theme-color" content="#039BE5" />
		<link href="css/style.css" rel="stylesheet" type="text/css" />

		<title>Cyfrowy tachograf - EKARD VTC</title>

		<script src="https://code.jquery.com/jquery-1.8.0.min.js"></script>

		<script type="text/javascript">
			var actualTime = "00:00";
			var actualDate = "00.00.0000";
			var connection = "";
			var gameOn = "false";
			var speed = 0;
			var odometer = 0;
			var engine = "off";
			var driveTime = "00:00";
			var timeToPause = "00:00";
			var pauseTime = "00:00";
			function get_data(){
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
			        if (this.readyState == 4 && this.status == 200) {
			        	var dane = JSON.parse(this.responseText);
			        	actualTime = dane.actualTime;
			        	actualDate = dane.actualDate;
			        	connection = dane.connection;
			        	gameOn = dane.gameOn;
			        	speed = dane.speed;
			        	odometer = dane.odometer;
			        	engine = dane.engine.toString();
			        	driveTime = dane.driveTime;
			        	timeToPause = dane.timeToPause;
			        	pauseTime = dane.pauseTime;
			        }
			    };
				xmlhttp.open("POST", "get_data.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			    xmlhttp.send("username=<?php echo $login; ?>");
			}
			get_data();
			setInterval(get_data, 1000);
		</script>
	</head>
	<body>
		<div class="tacho_body">
			<div id="screen" class="off">
				<div id="one">
					<div class="first_line">
						<div class="actual_time">
							<span id="actualTime"></span>
							<img id="time_icon" src="img/time.svg" alt="Czas." />
						</div>
						<div class="drive_status">
							<img id="drive_status" src="img/drive.svg" alt="Jazda." />
						</div>
						<div class="speed">
							<span id="speed"></span>
						</div>
					</div>
					<div class="second_line">
						<div id="connection_status">
							<img id="pause" src="img/pause.svg" alt="Pauza." />
							<img id="drive" src="img/drive.svg" alt="Jazda." />
							<img class="driver_card" src="img/driver_card.svg" alt="Karta kierowcy włożona." />
						</div>
						<div class="odometer">
							<span id="odometer"></span>
						</div>
					</div>
				</div>
				<div id="two">
					<div class="first_line">
						<div class="drive_status">
							<div class="driveTime">
								1<img class="driveTime_icon" src="img/drive.svg" alt="Jazda." /><span id="driveTime"></span>
							</div>
						</div>
						<div class="next_pause">
							<img class="next_pause_icon" src="img/next_pause.svg" alt="Następna pauza." />
							<span id="timeToPause"></span>
						</div>
					</div>
					<div class="second_line"></div>
				</div>
				<div id="three">
					<div class="first_line">
						<span>VDO</span>
						<div class="next_pause_time">
							<img class="driveTime_icon" src="img/drive.svg" alt="Jazda." />
							<img class="next_stop_icon" src="img/next_stop.svg" alt="Następna pauza." />
							<span id="timeToPause2"></span>
						</div>
					</div>
					<div class="second_line">
						<div class="pause_time">
							<img id="pause" src="img/pause.svg" alt="Pauza." />
							<img class="next_stop_icon" src="img/next_stop.svg" alt="Następna pauza." />
							<span id="pauseTime"></span>
						</div>
						<div class="next_drive_time">
							<img id="pause" src="img/pause.svg" alt="Pauza." />
							<img class="driveTime_icon" src="img/drive.svg" alt="Jazda." />
							<img id="next" src="img/next.svg" alt="Następny." />
							<span id="nextDriveTime">00h45</span>
						</div>
					</div>
				</div>
				<div id="four">
					<div class="first_line">
						<div class="timezone">
							UTC<img id="clock" src="img/clock.svg" alt="Zegar." />
						</div>
						<span id="actualDate"></span>
					</div>
					<div class="second_line">
						<span id="actualTime2"></span>
						<div class="timezone_diff">
							+02h00
						</div>
					</div>
				</div>
				<div id="alert">
					<div id="alert_first_line">
						
					</div>
					<div id="alert_second_line">

					</div>
				</div>
			</div>
			<button class="previous_screen" onclick="previous_screen()"></button>
			<button class="next_screen" onclick="next_screen();"></button>
			<button class="mute" onclick="mute();"><img id="unmuted" src="img/volume.svg"><img id="muted" style="display: none;" src="img/volume_muted.svg"></button>
		</div>
		<script type="text/javascript">
			function data_status(){
				var screen = document.getElementById("screen");
				var screenClass = document.getElementById("screen").classList;
				if(screenClass != engine){
				    screen.classList = engine;
				}

				var connectionStatus = document.getElementById("connection_status");
				if(connection == "connected"){
					connectionStatus.style.visibility = "visible";
				}
				if(connection == "false"){
					connectionStatus.style.visibility = "hidden";
				}

				if(engine == "on"){
					document.getElementById("pause").style.display = "none";
					document.getElementById("drive").style.display = "block";
					document.getElementById("drive_status").style.display = "block";
				}
				else {
					document.getElementById("pause").style.display = "block";
					document.getElementById("drive").style.display = "none";
					document.getElementById("drive_status").style.display = "none";
				}

				if(actualTime != "00:00"){
					document.getElementById("time_icon").style.display = "block";
				}

				var driveTimeHTML = document.getElementById("driveTime");
				var driveTimeSplitted = driveTime.split(":");
				driveTimeHTML.innerHTML = driveTimeSplitted[0]+"h"+driveTimeSplitted[1];

				var timeToPauseHTML = document.getElementById("timeToPause");
				var timeToPauseSplitted = timeToPause.split(":");
				timeToPauseHTML.innerHTML = timeToPauseSplitted[0]+"h"+timeToPauseSplitted[1];

				var timeToPause2HTML = document.getElementById("timeToPause2");
				var timeToPause2Splitted = timeToPause.split(":");
				timeToPause2HTML.innerHTML = timeToPause2Splitted[0]+"h"+timeToPause2Splitted[1];

				var pauseTimeHTML = document.getElementById("pauseTime");
				if(gameOn == "true"){
					var pauseTimeSplitted = pauseTime.split(":");
					pauseTimeHTML.innerHTML = pauseTimeSplitted[0]+"m"+pauseTimeSplitted[1];
				}
				else{
					pauseTimeHTML.innerHTML = "00m00";
				}

				var odometerHTML = document.getElementById("odometer");
				if(odometer == null){
					odometerHTML.innerHTML = "0km";
				}
				else{
					odometerHTML.innerHTML = odometer+"km";
				}

				var actualTimeHTML = document.getElementById("actualTime");
				actualTimeHTML.innerHTML = actualTime;

				var actualTime2HTML = document.getElementById("actualTime2");
				actualTime2HTML.innerHTML = actualTime;

				var actualDateHTML = document.getElementById("actualDate");
				var actualDateSplitted = actualDate.split("-");
				actualDateHTML.innerHTML = actualDateSplitted[2]+"."+actualDateSplitted[1]+"."+actualDateSplitted[0];

				var speedHTML = document.getElementById("speed");
				if(speed == null){
					speedHTML.innerHTML = "0km/h";
				}
				else{
					speedHTML.innerHTML = speed+"km/h";
				}
			}
			setInterval(data_status, 1000);

			var activeScreen = 1;

			function previous_screen(){
				switch(activeScreen){
					case 1:
						return activeScreen = 4;
						break;
					case 2:
						return activeScreen = 1;
						break;
					case 3:
						return activeScreen = 2;
						break;
					case 4:
						return activeScreen = 3;
						break;
				}
			}
			function next_screen(){
				switch(activeScreen){
					case 1:
						activeScreen = 2;
						break;
					case 2:
						activeScreen = 3;
						break;
					case 3:
						activeScreen = 4;
						break;
					case 4:
						activeScreen = 1;
						break;
				}
			}

			function show_screen(){
				if(activeScreen == 1){
					document.getElementById("one").style.display = "block";
					document.getElementById("two").style.display = "none";
					document.getElementById("three").style.display = "none";
					document.getElementById("four").style.display = "none";
				}
				if(activeScreen == 2){
					document.getElementById("one").style.display = "none";
					document.getElementById("two").style.display = "block";
					document.getElementById("three").style.display = "none";
					document.getElementById("four").style.display = "none";
				}
				if(activeScreen == 3){
					document.getElementById("one").style.display = "none";
					document.getElementById("two").style.display = "none";
					document.getElementById("three").style.display = "block";
					document.getElementById("four").style.display = "none";
				}
				if(activeScreen == 4){
					document.getElementById("one").style.display = "none";
					document.getElementById("two").style.display = "none";
					document.getElementById("three").style.display = "none";
					document.getElementById("four").style.display = "block";
				}
			}
			var show_screen_interval = setInterval(show_screen, 1);

			beep_volume = 0.1;

			function beep(){
				var beep = new Audio('mp3/beep.mp3');
				beep.volume = beep_volume;
				beep.play();
			}
			setTimeout(beep, 1000);

			function show_alert(message1, message2, beeps, time){
				clearInterval(show_screen_interval);

				$('#one').css('display', 'none');
				document.getElementById("two").style.display = "none";
				document.getElementById("three").style.display = "none";
				document.getElementById("four").style.display = "none";

				document.getElementById("alert").style.display = "block";
				document.getElementById("alert_first_line").innerHTML = message1;
				document.getElementById("alert_second_line").innerHTML = message2;
				
				var beep_interval = setInterval(beep, 2000);

				beep();
				var beep_timeout = (beeps-1) * 2000;
				setTimeout(clear_interval, beep_timeout);
				function clear_interval(){
					clearInterval(beep_interval);
				}

				var off_timeout = time * 1000;

				function off_alert(){
					document.getElementById("alert").style.display = "none";
					show_screen_interval = setInterval(show_screen, 1);
				}
				setTimeout(off_alert, off_timeout);
			}

			function pauseAlert5Min(){
				var timeToPauseSplitted = timeToPause.split(":");
				if(parseInt(timeToPauseSplitted[1]) == 5){
					show_alert('PAUZA ZA', '5 MINUT', 3, 10);
					clearInterval(pauseAlert5MinInterval);
					setTimeout(pauseAlert5MinIntervalSet, 60000);
					function pauseAlert5MinIntervalSet(){
						pauseAlert5MinInterval = setInterval(pauseAlert5Min, 10);
					}
				}
			}
			var pauseAlert5MinInterval = setInterval(pauseAlert5Min, 10);

			function pauseAlert(){
				var timeToPauseSplitted = timeToPause.split(":");
				if(parseInt(timeToPauseSplitted[1]) == 0 && engine == "on"){
					show_alert('ZATRZYMAJ SIE', 'NA PRZERWE', 3, 10);
					clearInterval(pauseAlertInterval);
					setTimeout(pauseAlertIntervalSet, 60000);
					function pauseAlertIntervalSet(){
						pauseAlertInterval = setInterval(pauseAlert, 10);
					}
				}
			}
			var pauseAlertInterval = setInterval(pauseAlert, 10);

			function mute(){
				var x = document.getElementById("muted");
				var y = document.getElementById("unmuted");
			    if (x.style.display === "none") {
			        x.style.display = "block";
			        y.style.display = "none";
			        beep_volume = 0;
			    } else {
			        x.style.display = "none";
			        y.style.display = "block";
			        beep_volume = 0.1;
			    }
			}
		</script>
	</body>
</html>