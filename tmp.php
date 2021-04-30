<?php
	$pass = password_hash("test", PASSWORD_BCRYPT);
	echo $pass."<br /><br />";
	// require_once("db_conn.php");
	// $handle = fopen("Miasta.txt", "r");
	// if ($handle) {
	//     while (($line = fgets($handle)) !== false) {
	//     	$line=str_replace("\n", "", $line);
	//     	$line=str_replace("\r", "", $line);
	//         mysqli_query($conn, "INSERT INTO cities (CityName, Game) VALUES ('$line', 'ets2');");
	//         echo "Wrzucono ".$line."<br />";
	//     }

	//     fclose($handle);
	// }
	// $handle = fopen("Ladunki-ETS2.txt", "r");
	// if ($handle) {
	//     while (($line = fgets($handle)) !== false) {
	//         echo $line."<br />";
	//     }

	//     fclose($handle);
	// }
	// $login = "DrunkinDonut";
	// $index = 1;
	// echo $login.$index;

	// $distance = "2550";
	// $damage = "45.7";

	// $earned = intval($distance) * 3;
	// $damage_value = (floatval($damage) / 100) * intval($earned);
	// $earnedMoney = intval($earned) - floatval($damage_value);

	// echo $earned."<br />";
	// echo $damage_value."<br />";
	// echo $earnedMoney;

	// $dzisiaj = date("Y-m-d", strtotime("-1 months"));
	// echo $dzisiaj."<br />";
	// $data = mysqli_query($conn, "SELECT DateAndTime FROM routes WHERE DATE(DateAndTime) <= \"$dzisiaj\";");
	// while($wiersz = mysqli_fetch_array($data)){
	// 	echo $wiersz['DateAndTime']."<br />";
	// }

	// $url0 = "http://ekard-vs.pl/tachograf/api?data=drivers_number";
	// $get0 = get_object_vars(json_decode(file_get_contents($url0)));
	// echo "Liczba kierowców: ".$get0['drivers_number']."/30<br /><br />";

	// $url = "http://ekard-vs.pl/tachograf/api?data=company_stats";
	// $get = get_object_vars(json_decode(file_get_contents($url)));
	// echo "Dystans: ".$get["dystans"]."<br />";
	// echo "Trasy: ".$get["trasy"]."<br />";
	// echo "Tonaż: ".$get["tonaz"]."<br />";
	// echo "Gotówka: ".$get["gotowka"]."<br />";
	// echo "Paliwo: ".$get["paliwo"]."<br />";
	// echo "Spalanie: ".$get["spalanie"]."<br /><br />";

	// echo "TOP 10 TYDZIEŃ<br />";
	// $url2 = "http://ekard-vs.pl/tachograf/api?data=top_week&type=drivers";
	// $get2 = get_object_vars(json_decode(file_get_contents($url2)));
	// $url3 = "http://ekard-vs.pl/tachograf/api?data=top_week&type=distance";
	// $get3 = get_object_vars(json_decode(file_get_contents($url3)));
	// echo "1. ".$get2[1]." - ".$get3[1]." km<br />";
	// echo "2. ".$get2[2]." - ".$get3[2]." km<br />";
	// echo "3. ".$get2[3]." - ".$get3[3]." km<br />";
	// echo "4. ".$get2[4]." - ".$get3[4]." km<br />";
	// echo "5. ".$get2[5]." - ".$get3[5]." km<br />";
	// echo "6. ".$get2[6]." - ".$get3[6]." km<br />";
	// echo "7. ".$get2[7]." - ".$get3[7]." km<br />";
	// echo "8. ".$get2[8]." - ".$get3[8]." km<br />";
	// echo "9. ".$get2[9]." - ".$get3[9]." km<br />";
	// echo "10. ".$get2[10]." - ".$get3[10]." km<br /><br />";

	// echo "TOP 10 MIESIĄC<br />";
	// $url4 = "http://ekard-vs.pl/tachograf/api?data=top_month&type=drivers";
	// $get4 = get_object_vars(json_decode(file_get_contents($url4)));
	// $url5 = "http://ekard-vs.pl/tachograf/api?data=top_month&type=distance";
	// $get5 = get_object_vars(json_decode(file_get_contents($url5)));
	// echo "1. ".$get4[1]." - ".$get5[1]." km<br />";
	// echo "2. ".$get4[2]." - ".$get5[2]." km<br />";
	// echo "3. ".$get4[3]." - ".$get5[3]." km<br />";
	// echo "4. ".$get4[4]." - ".$get5[4]." km<br />";
	// echo "5. ".$get4[5]." - ".$get5[5]." km<br />";
	// echo "6. ".$get4[6]." - ".$get5[6]." km<br />";
	// echo "7. ".$get4[7]." - ".$get5[7]." km<br />";
	// echo "8. ".$get4[8]." - ".$get5[8]." km<br />";
	// echo "9. ".$get4[9]." - ".$get5[9]." km<br />";
	// echo "10. ".$get4[10]." - ".$get5[10]." km<br />";
?>