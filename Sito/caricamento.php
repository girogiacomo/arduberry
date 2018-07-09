<html>
<head>
<link href="pulsante.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="caricamento.css">
<script src="caricamento.js"></script>
<title>Pagina protetta da password</title>
</head>
<body>
<div class="loader">LOADING...</div>
<?php 
// -------------------------
$password = 'accesso';
// -------------------------
$pwd = isset($_GET['pwd']) ? $_GET['pwd'] : false;
if (!$pwd || $pwd != $password) {
  ?>
<form method="get" action="<?php echo $_SERVER['PHPSELF']; ?>">
<table border="0" cellspacing="0" cellpadding="10" style="margin: 50px auto; border: 1px solid #DDD; background: #EEE;">
<?php if ($pwd !== false): ?><tr class="errore"><td colspan="3">La password inserita non e corretta!</td></tr><?php endif; ?>
<center>
<tr style="text-align: center;">
  <td style="font-family: verdana, arial, tahoma; font-size: 16px; color: #333;">Password</td>
  <td><input type="password" name="pwd" style="width: 180px;" style=" width: 180px; font-family: verdana, arial, tahoma; font-size: 16px; color: #333;  border: 1px solid #DDD;"/></td>
  <td><input type="submit" value="Entra" style="font-family: verdana, arial, tahoma; font-size: 16px; color: #333;"/></td>
</tr>
</table>
</form>
</center>
  <?php													  	// pagina protetta fuori dai tag
}else{
  ?>

<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
define("PORT","/dev/ttyACM0");
 
 
 
$pin12="";
$pin13="";

$stringa [14]="";

 
    include "php_serial.class.php";				// importo classe seriale
		$serial = new phpSerial;
		$serial->deviceSet(PORT);
        $serial->confBaudRate(115200);
        $serial->confParity("none");
        $serial->confCharacterLength(8);
        $serial->confStopBits(1);
        $serial->confFlowControl("none");
        $serial->deviceOpen();

		
	$serial->sendMessage("?");					// stampo a video lo stato dei led
	usleep(50000);
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
				
		
if (isset($_GET['action'])) { 					// prendo in input i dati
	switch ($_GET['action']) { 
		
		case "off12":  
				$serial->sendMessage("s12");
			break;
		case "on12":  
				$serial->sendMessage("a12");
			break;
		case "off13":  
				$serial->sendMessage("s13");
			break;
		case "on13":  
				$serial->sendMessage("a13");
			break;
		case "toggle12":  
				if($pin12==""){
					$serial->sendMessage("a12");
					$pin12="checked";
				}else if($pin12=="checked"){
					$serial->sendMessage("s12");
					$pin12="";
				}
			break;
		case "toggle13": 
				if($pin13==""){
					$serial->sendMessage("a13");
					$pin13="checked";
				}else if($pin13=="checked"){
					$serial->sendMessage("s13");
					$pin13="";
				}				
			break;
		case "reset!": 
				$serial->sendMessage("r");	
			break;
	}
}

//	usleep(50000); 
//	$accesi = $serial->sendMessage("?");


//	$serial->deviceClose();

?>
<br />
	<table border="0px" height="70" align="left">  
		<tr>
			<td rowspan="2" width=120><center>PIN<br />ARDUINO</center></td>
			<td width=125><center>PIN 12<br /><br /><div class="onoffswitch"><form>
												<input type="checkbox" name="pin12" class="onoffswitch-checkbox" id="pin12" onClick="location.href = 'caricamento.php?action=toggle12&pwd=<?php echo $password; ?>';" <?php echo $pin12; ?>>
												<label class="onoffswitch-label" for="pin12">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</form></div></center></td>
			<td width=125><center>PIN 13<br /><br /><div class="onoffswitch"><form>
												<input type="checkbox" name="pin13" class="onoffswitch-checkbox" id="pin13" onClick="location.href = 'caricamento.php?action=toggle13&pwd=<?php echo $password; ?>';" <?php echo $pin13; ?>>
												<label class="onoffswitch-label" for="pin13">
													<span class="onoffswitch-inner"></span>
													<span class="onoffswitch-switch"></span>
												</label>
											</form></div></center></td>		
											
			<td rowspan="2" width=30><a href="caricamento.php?pwd=<?php echo $password; ?>"><img width=35 src=icon-refresh-128.png></a></td>
			<td rowspan="2" width=95 align=right><a href="<?=$_SERVER['PHP_SELF'] . "?action=reset!&pwd=" . $password; ?>"><button style="height:30px";><b>RESET!</b></button></a></td>
		</tr>
		
	</table>
<br /><br /><br /><br />
</body> 
</html>

  <?php 
}
?>
</body>
</html>
