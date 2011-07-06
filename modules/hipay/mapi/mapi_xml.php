<?php
class HIPAY_MAPI_XML {

	/**
	 * Cré le flux XML de cet objet.
	 * Les membres commençants par "_" sont ignorées
	 *
	 * @param int $t
	 * @return string
	 */
	public function getXML($t = 0, $noshow = true) {
		$xml = '';
		$xml .= str_repeat(chr(9), $t)."<".get_class($this).">\n";
		
		foreach($this as $name => $value) {
			if ($noshow && substr($name, 0, 1) == '_')
				continue;

			if (!is_array($this->$name) && !is_object($this->$name) && !is_bool($this->$name)) {
				$xml .= str_repeat(chr(9), $t + 1)."<$name>$value</$name>\n";
			} else if (is_bool($this->$name)) {
				if ($value === true)
					$xml .= str_repeat(chr(9), $t + 1)."<$name>true</$name>\n";
				else
					$xml .= str_repeat(chr(9), $t + 1)."<$name>false</$name>\n";
			} else if (is_object($this->$name) && method_exists($this->$name, 'getXML')) {
				$xml .= $this->$name->getXml($t + 1);
			} else if (is_array($this->$name)) {
				$xml .= str_repeat(chr(9), $t + 1)."<$name>\n";
				$xml .= self::getXMLArray($this->$name, $t + 1, $noshow);
				$xml .= str_repeat(chr(9), $t + 1)."</$name>\n";
			}
			// else : no getXML method available
		}
		
		$xml .= str_repeat(chr(9), $t)."</".get_class($this).">\n";		
		return $xml;
	}

	/**
	 * Cré le flux XML d'un tableau 
	 *
	 * @param array $array
	 * @param int $t
	 * @return string
	 */
	protected function getXMLArray($array, $t = 0, $noshow = true) {
		$xml = '';
		
		foreach($array as $name => $value) {
			if (substr($name, 0, 1) == '_')
				continue;

			if (!is_array($array[$name]) && !is_object($array[$name]) && !is_bool($array[$name])) {
				$xml .= str_repeat(chr(9), $t + 1)."<_aKey_$name>$value</_aKey_$name>\n";
			} else if (is_bool($array[$name])) {
				if ($value === true)
					$xml .= str_repeat(chr(9), $t + 1)."<$name>true</$name>\n";
				else
					$xml .= str_repeat(chr(9), $t + 1)."<$name>false</$name>\n";
			} else if (is_object($array[$name]) && method_exists($array[$name], 'getXML')) {
				$xml .= $array[$name]->getXml($t + 1);
			} else if (is_array($array[$name])){
				$xml .= str_repeat(chr(9), $t + 1)."<$name>\n";
				$xml .= self::getXMLArray($array[$name], $t + 1, $noshow);
				$xml .= str_repeat(chr(9), $t + 1)."</$name>\n";
			}
		}
		
		return $xml;
	}
}