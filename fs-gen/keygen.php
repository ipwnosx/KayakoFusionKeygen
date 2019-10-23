<?php
	function GenRandomString($length = 10)
	{
		$characters = "0123456789ABCDEF";
		$string = "";
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0,15)];
		}
		return $string;
	}
	
	$_licenseContainer = array();


/**
* ###############################################
* MODIFY LICENSE DETAILS HERE
* ###############################################
*/

	$_licenseContainer['expiry']			= strtotime("2050-12-31 23:59:59");
	$_licenseContainer['fullname']			= "Name Surname";
	$_licenseContainer['product']			= "Fusion";
	$_licenseContainer['licensedstaff']		= 0;
	$_licenseContainer['domains']['0']		= "localhost";
	$_licenseContainer['domains']['1']		= "your-host-here.com";
	$_licenseContainer['package']			= "Fusion";
	$_licenseContainer['organization']		= "Unknown";
	$_licenseContainer['uniqueid']			= GenRandomString(32);
	$_licenseContainer['istrial']['0']		= 0;
	
	// | md5_base64 | md5_decrypted | extended key | base64_data |
	
	$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
	$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
	
	$_decryptedData = serialize( $_licenseContainer );
	$_extendedKey = GenRandomString(12);
	$b64d = mcrypt_encrypt( MCRYPT_RIJNDAEL_256, "A376545AD82A8B695A60".$_extendedKey, $_decryptedData, MCRYPT_MODE_ECB, $iv );
	$_base64Data = base64_encode ( $b64d );
	
	$_keyChunksContainer[0] = md5( $_base64Data );
	$_keyChunksContainer[1] = md5( $_decryptedData );
	$_keyChunksContainer[2] = $_extendedKey;
	$_keyChunksContainer[3] = $_base64Data;
	$_resultKey = "<?php /* [" . $_keyChunksContainer[0] . ";" . $_keyChunksContainer[1] . ";" . $_keyChunksContainer[2] . ";" . $_keyChunksContainer[3] . "] */ ?>" . $_licenseContainer['uniqueid'];
	
 	echo "<textarea rows=20 cols=100>". $_resultKey . "</textarea>";
	echo "<br><br>";
	echo "Av0id // REVENGE";
?>
