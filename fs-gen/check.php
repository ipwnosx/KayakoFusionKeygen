<?php
	function dump(&$var, $info = FALSE)
	{
		$scope = false;
		$prefix = 'unique';
		$suffix = 'value';
	 
		if($scope) $vals = $scope;
		else $vals = $GLOBALS;

		$old = $var;
		$var = $new = $prefix.rand().$suffix; $vname = FALSE;
		foreach($vals as $key => $val) if($val === $new) $vname = $key;
		$var = $old;

		echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px;'>";
		if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
		do_dump($var, '$'.$vname);
		echo "</pre>";
	}

	function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
	{
		$do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
		$reference = $reference.$var_name;
		$keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';

		if (is_array($var) && isset($var[$keyvar]))
		{
			$real_var = &$var[$keyvar];
			$real_name = &$var[$keyname];
			$type = ucfirst(gettype($real_var));
			echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
		}
		else
		{
			$var = array($keyvar => $var, $keyname => $reference);
			$avar = &$var[$keyvar];
	   
			$type = ucfirst(gettype($avar));
			if($type == "String") $type_color = "<span style='color:green'>";
			elseif($type == "Integer") $type_color = "<span style='color:red'>";
			elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
			elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
			elseif($type == "NULL") $type_color = "<span style='color:black'>";
	   
			if(is_array($avar))
			{
				$count = count($avar);
				echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
				$keys = array_keys($avar);
				foreach($keys as $name)
				{
					$value = &$avar[$name];
					do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
				}
				echo "$indent)<br>";
			}
			elseif(is_object($avar))
			{
				echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
				foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
				echo "$indent)<br>";
			}
			elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
			elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>";
			elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
			elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
			elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
			else echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>";

			$var = $var[$keyvar];
		}
	}

	function DecodeKey( $_keyContents )
	{
		if ( trim( $_keyContents ) == "" )
		{
			echo "key.php is empty.";
			exit( );
		}
		$_finalKeyContents = "";
		$_matches = array( );
		if ( !preg_match( "@\\[([^\\[\\]]*)\\]@", $_keyContents, $_matches ) )
		{
			echo "Invalid key.php (3).";
			exit( );
		}
		$_finalKeyContents = $_matches[1];
		$_keyChunksContainer = explode( ";", $_finalKeyContents );
		if ( count( $_keyChunksContainer ) != 4 )
		{
			echo "Invalid key.php (2).";
			exit( );
		}
		$_md5Base64 = $_keyChunksContainer[0];
		$_md5Decrypted = $_keyChunksContainer[1];
		$_extendedKey = $_keyChunksContainer[2];
		$_base64Data = $_keyChunksContainer[3];
		if ( md5( $_base64Data ) != $_md5Base64 )
		{
			echo "Invalid key.php (INVALID BASE64HASH).";
			exit( );
		}
		if ( empty( $_extendedKey ) )
		{
			echo "Invalid key.php (INVALID EXTENDEDKEY).";
			exit( );
		}
		$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
		$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		$b64d = base64_decode( $_base64Data );
		$mcrpt = mcrypt_decrypt( MCRYPT_RIJNDAEL_256, "A376545AD82A8B695A60".$_extendedKey, $b64d, MCRYPT_MODE_ECB, $iv );
		$_decryptedData = str_replace( "\x00", "", $mcrpt);
		$_licenseContainer = unserialize( $_decryptedData );
		if ( empty( $_decryptedData ) || md5( $_decryptedData ) != $_md5Decrypted || !is_array( $_licenseContainer ) )
		{
			echo "Invalid key.php (INVALID DECRYPT).";
			exit( );
		}
		return $_licenseContainer;
	}	
	
	if ( !file_exists( "./key.php" ) )
	{
		echo "Unable to locate key.php.";
		exit( );
	}
	$_keyContents = file_get_contents( "./key.php" );
	$_licenseContainer = decodekey( $_keyContents );
	if ( empty($_licenseContainer) )
	{
		echo "Invalid key.php.";
		exit( );
	}
	dump($_keyContents);
	dump($_licenseContainer);

	echo "<br><br>";
	echo "Av0id // REVENGE";
?>