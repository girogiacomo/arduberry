<?php 
// -------------------------
$password = 'password';
// -------------------------
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : false;
if (!$pwd || $pwd != $password) {
  ?>
<html>
<head>
  <title>Pagina protetta da password</title>
</head>
<body>
<center>
<form method="get" action="<?php echo $_SERVER['PHPSELF']; ?>">
<table border="0" cellspacing="0" cellpadding="10" style="margin: 50px auto; border: 1px solid #DDD; background: #EEE;">
<?php if ($pwd !== false): ?><tr class="errore"><td colspan="3">La password inserita non e corretta!</td></tr><?php endif; ?>
<tr style="text-align: center;">
  <td style="font-family: verdana, arial, tahoma; font-size: 16px; color: #333;">Password</td>
  <td><input value="password" type="password" name="pwd" style="width: 180px;" style=" width: 180px; font-family: verdana, arial, tahoma; font-size: 16px; color: #333;  border: 1px solid #DDD;"/></td>
  <td><input type="submit" value="Entra" style="font-family: verdana, arial, tahoma; font-size: 16px; color: #333;"/></td>
</tr>
</table>
</form>
</center>
</body>
</html>
  <?php													  	// pagina protetta fuori dai tag
}else{
  ?>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
define("PORT","/dev/ttyACM0");
//define("PORT","/dev/ttyUSB0");

$stringa [14]="";
$pin12 = "";
$pin13 = "";
$pulse = "";
$airtime = 2000000;

    shell_exec("/usr/bin/python /var/www/html/www3/arduino.py");                              // iniziallizzo la seriale con python 
	
    include "php_serial.class.php";				// importo classe seriale
	$serial = new phpSerial;
	$serial->deviceSet(PORT);
        $serial->confBaudRate(9600);
        $serial->confParity("none");
        $serial->confCharacterLength(8);
        $serial->confStopBits(1);
        $serial->confFlowControl("none");
        $serial->deviceOpen();
	usleep(10000);
	if (!isset($_GET['api'])){
		$serial->sendMessage("?*");					// ricevo stati da arduino
		usleep($airtime);
		$stringa = explode("|", $serial->readPort(), -1);
		for($i=0; $i<count($stringa); $i++){
				switch ($stringa [$i]){
					case 12:
							$pin12="checked";
						break;
					case 13:
							$pin13="checked";
						break;
					case 'r':
							$i = $i + 1;
							$red = $stringa[$i];
						break;
					case 'g':
							$i = $i + 1;
							$green = $stringa[$i];
						break;
					case 'b':
							$i = $i + 1;
							$blue = $stringa[$i];
						break;
					case 'd':
							$i = $i + 1;
							$duty = $stringa[$i];
						break;	
					case 'p':
							$pulse = "checked";
						break;
				}
			}
	}
				
if (isset($_GET['red'])){ 					// Dò comandi ad arduino
  $serial->sendMessage("r" . (string)$_GET['red'] . "*");
}
if (isset($_GET['green'])){
  $serial->sendMessage("g" . (string)$_GET['green'] . "*");
}
if (isset($_GET['blue'])){
  $serial->sendMessage("b" . (string)$_GET['blue'] . "*");
}
if (isset($_GET['duty'])){
  $serial->sendMessage("d" . (string)$_GET['duty'] . "*");
}
				
if (isset($_GET['action'])) {
	switch ($_GET['action']) { 
		
		case "off12":  
				$serial->sendMessage("s12*");
			break;
		case "on12":  
				$serial->sendMessage("a12*");
			break;
		case "off13":  
				$serial->sendMessage("s13*");
			break;
		case "on13":  
				$serial->sendMessage("a13*");
			break;
		case "toggle12":  
				if($pin12==""){
					$serial->sendMessage("a12*");
					$pin12=="checked";
				}else if($pin12=="checked"){
					$serial->sendMessage("s12*");
					$pin12=="";
				}
			break;
		case "toggle13": 
				if($pin13==""){
					$serial->sendMessage("a13*");
					$pin13=="checked";
				}else if($pin13=="checked"){
					$serial->sendMessage("s13*");
					$pin13=="";
				}				
			break;
		case "reset!": 
				$serial->sendMessage("R*");
				usleep(2000000);
				$pin12 = $pin13 = "";
			break;
		case "shutdownforever":
			system("sudo shutdown -hP now");
			break;
		case "togglepulse":  
				if($pulse==""){
					$serial->sendMessage("pa");
					$pulse=="checked";
				}else if($pulse=="checked"){
					$serial->sendMessage("ps");
					$pulse=="";
				}
			break;
		case "onp":  
				$serial->sendMessage("pa");
				$pulse=="checked";
			break;
		case "offp":  
				$serial->sendMessage("ps");
				$pulse=="";
			break;
	}
}

$pin12 = $pin13 = $red = $green = $blue = $duty = $pulse = "0";
$serial->sendMessage("?*");					// ri-ricevo stati da arduino
	usleep($airtime);
	$stringa = explode("|", $serial->readPort(), -1);
	for($i=0; $i<count($stringa); $i++){
			switch ($stringa [$i]){
				case 12:
						$pin12="checked";
					break;
				case 13:
						$pin13="checked";
					break;
				case 'r':
						$i = $i + 1;
						$red = $stringa[$i];
					break;
				case 'g':
						$i = $i + 1;
						$green = $stringa[$i];
					break;
				case 'b':
						$i = $i + 1;
						$blue = $stringa[$i];
					break;
				case 'd':
						$i = $i + 1;
						$duty = $stringa[$i];
					break;
				case 'p':
						$pulse = "checked";
					break;
			}
		}

/*												// stampo a video lo stato dei led (vecchio debug)
$serial->sendMessage("?*");					
	usleep(10000);
	$stringa = explode("|", $serial->readPort(), -1);
	for($i=0; $i<count($stringa); $i++){
			switch ($stringa [$i]){
				case 12:
						$pin12="checked";
					break;
				case 13:
						$pin13="checked";
					break;
			}
		}

//	usleep(100000); 
//	$accesi = $serial->sendMessage("?*");


	$serial->deviceClose();
//	echo(exec("/home/pi/Script/singolatemp.sh"));	
*/

if (!isset($_GET['api'])){
	?>
	<html> 
	<head>
		<title>Arduberry Domotic Platform®</title>
		<link href="pulsante.css" rel="stylesheet" type="text/css">
	<!--	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">  -->	
	 <style>
	   .no-spin::-webkit-inner-spin-button, .no-spin::-webkit-outer-spin-button {
		-webkit-appearance: none !important;
		margin: 0 !important;
		-moz-appearance:textfield !important;
	   }
	 </style>
	</head>
	<body>
	<?php //echo $red; echo $green; echo $blue; echo $duty; ?> 
		<table height="70" align="left" style="margin-top: 0; border: 0;"> 
			<tr>
				<td rowspan="1" width="130"><center>PIN<br />ARDUINO</center></td>
				<td width="125"><center>PIN 12<br /><br /><div class="onoffswitch"><form>
													<input type="checkbox" name="pin12" class="onoffswitch-checkbox" id="pin12" onClick="location.href = 'index.php?action=toggle12&pwd=<?php echo $password; ?>';" <?php echo $pin12; ?>>
													<label class="onoffswitch-label" for="pin12">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</form></div></center></td>
				<td width="125"><center>PIN 13<br /><br /><div class="onoffswitch"><form>
													<input type="checkbox" name="pin13" class="onoffswitch-checkbox" id="pin13" onClick="location.href = 'index.php?action=toggle13&pwd=<?php echo $password; ?>';" <?php echo $pin13; ?>>
													<label class="onoffswitch-label" for="pin13">
														<span class="onoffswitch-inner"></span>
														<span class="onoffswitch-switch"></span>
													</label>
												</form></div></center></td>		
												
				<td rowspan="2" width="50" align="center"><a href="index.php?pwd=<?php echo $password; ?>"><img width=35 src=icon-refresh-128.png></a></td>
				<td rowspan="2" width="75" align="right"><a href="<?=$_SERVER['PHP_SELF'] . "?action=reset!&pwd=" . $password; ?>"><button style="height:30px";><b>RESET!</b></button></a></td>
			</tr>
			<tr>
				<td height="150"><center>PULSANTING<br /><br />LED</center></td>
				<td colspan="2">
					<form name="formpuls" method="GET" action="<?=$_SERVER['PHP_SELF'] . "?pwd=" . $password; ?>">
						<center>
							R: <input required class="no-spin" type="number" name="red" min="0" max="255" value="<?php echo $red; ?>" style="font-weight: bold; color: white; width: 50px; height: 35px; background-color: red; margin-right: 10px;">
							G: <input required class="no-spin" type="number" name="green" min="0" max="255" value="<?php echo $green; ?>" style="font-weight: bold; color: white; width: 50px; height: 35px; background-color: #00e600; margin-right: 10px;">
							B: <input required class="no-spin" type="number" name="blue" min="0" max="255" value="<?php echo $blue; ?>" style="font-weight: bold; color: white; width: 50px; height: 35px; background-color: #0066ff;">
							<br />
							<br />
							P: <input required class="no-spin" type="number" name="duty" min="0" max="255" value="<?php echo $duty; ?>" style="font-weight: bold; color: white; width: 50px; height: 35px; background-color: black; margin-right: 10px;">
							Pulse: <input type="checkbox" name="pulse" onClick="location.href = 'index.php?action=togglepulse&pwd=<?php echo $password; ?>';" <?php echo $pulse; ?>>
							<input type="submit" name="invia" value="Invia!" style="margin-right: 10px;">
							
							<input type="hidden" name="pwd" value="<?php echo $password; ?>">						
						</center>
					</form>
				</td>
			</tr>
		</table>
	</body> 
	</html>

    <?php
}else if ($_GET['api']=='Android'){
	echo "Pin12;"; if ($pin12){ echo 1; }else{ echo 0; } echo ";";
	echo "Pin13;"; if ($pin13){ echo 1; }else{ echo 0; } echo ";";
	echo "Red;"; if ($red){ echo $red; }else{ echo 0; } echo ";";
	echo "Green;"; if ($green){ echo $green; }else{ echo 0; } echo ";";
	echo "Blue;"; if ($blue){ echo $blue; }else{ echo 0; } echo ";";
	echo "Duty;"; if ($duty){ echo $duty; }else{ echo 0; } echo ";";
	echo "Pulse;"; if ($pulse){ echo 1; }else{ echo 0; } echo ";";
}
}
?>