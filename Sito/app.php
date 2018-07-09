<!DOCTYPE html>
<?php 
// -------------------------
$password = 'apppwd';
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
 
 
 
$pin12="";
$pin13="";
$pulse="";

$stringa [14]="";

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
		
	$serial->sendMessage("?*");					// ricevo stati da arduino
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
	}
}

$pin12 = $pin13 = $red = $green = $blue = $duty = $pulse = "0";
$serial->sendMessage("?*");					// ri-ricevo stati da arduino
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

<?php
	echo "Pin12;" . $pin12 . ";"; 
	echo "Pin13;" . $pin13 . ";";
	echo "Red;" . $red . ";";
	echo "Green" . $green . ";";
	echo "Blue;" . $blue . ";";
	echo "Duty;" . $duty . ";";
	echo "Pulse;";
	if ($pulse){
		echo 1;
	}else{ echo 0; }
	echo ";";
?> 

</body> 
</html>

  <?php 
}
?>