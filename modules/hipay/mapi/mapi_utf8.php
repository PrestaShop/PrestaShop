<?php
class HIPAY_MAPI_UTF8 
{
	/**
	 * Test si une chaine est en UTF-8
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function is_utf8($string) {
	    // From http://w3.org/International/questions/qa-forms-utf-8.html
	    return preg_match('%^(?:
          	[\x09\x0A\x0D\x20-\x7E]            # ASCII
        	| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        	|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        	| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        	|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        	|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        	| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        	|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    	)*$%xs', $string);
	}


	/**
	 * Encode une chaine en UTF-8 si elle ne l'est pas déjà
	 *
	 * @param string $string
	 * @return string
	 */
	public static function forceUTF8($string) {
		if (!self::is_utf8($string))
			return utf8_encode($string);
		else 
			return $string;
	}


	/**
	 * Longueur d'une chaine UTF-8
	 *
	 * @param string $str
	 * @return int longueur de la chaine
	 */
	public static function strlen_utf8 ($str) {
	    $i = 0;
	    $count = 0;
	    $len = strlen ($str);
		
	    while ($i < $len)
	    {
			$chr = ord($str[$i]);
			$count++;
			$i++;
			if ($i >= $len)
				break;

			if ($chr & 0x80)
			{
				$chr <<= 1;
				while ($chr & 0x80)
				{
					$i++;
					$chr <<= 1;
				}
			}
	    }
	    return $count;
	}
}