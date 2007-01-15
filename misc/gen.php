<?php

if (!extension_loaded("unicode") || !class_exists("ResourceBundle", false)) {
	die("Need ext/unicode with ResourceBundle support!\n");
}

define(TPL, <<<TPL
<?php
/**
 * \$Id$
 */
\$this->codes = %s;
?>

TPL
);

function ch($s) {
	$utf8 = "";
	$unicode = hexdec($s[1]);
    if ( $unicode < 128 ) {
        $utf8.= chr( $unicode );
    } elseif ( $unicode < 2048 ) {
        $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
        $utf8.= chr( 128 + ( $unicode % 64 ) );
    } else {
        $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
        $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
        $utf8.= chr( 128 + ( $unicode % 64 ) );
    }
    return $utf8;
}

function Languages($la) {
	foreach ($la as $k => $v) {
		if (strlen($k) != 2) {
			unset($la[$k]);
		}
	}
	return $la;
}

function Countries($co) {
	foreach ($co as $k => $v) {
		if (!is_string($k) || strlen($k) != 2) {
			unset($co[$k]);
		}
	}
	return $co;
}

function Currencies(&$cu) {
	foreach ($cu as $k => $v) {
		$cu[$k] = $v[1];
	}
	return $cu;
}

function s($a, $b) {
	var_dump($a, $b);
}

$root = new ResourceBundle();
foreach (Locale::getAvailable() as $id) {
	switch (strlen($id)) {
		case 2:
			foreach (array("Language" => "Languages", "Country" => "Countries", "Currency" => "Currencies") as $dn => $kn) {
				echo "Processing $id: $kn";
				
				$lo = new LocaleData($id);
				$ar = $kn(array_merge((array) $root->getByKey($kn), (array) $lo->getResourceBundle()->getByKey($kn))); 
				
				try {
					uasort($ar, array(new Collator($id), "compare"));
				} catch (Exception $x) {
				}
				
				file_put_contents("$dn/$id.php", sprintf(TPL, preg_replace_callback("/\\\\u([a-fA-F0-9]{4})/", "ch", var_export($kn($ar), true))));
				echo "\n";
			}
			break;
	}
}

?>
