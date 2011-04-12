<?php
/** Provides utilitary functions to log to a file */
class MyLogUtils
{
	// Log $msg into a file located at $filepath
	public static function mylog($filepath, $msg)
	{
		$fh = fopen($filepath, 'a');
		if (FALSE !==  $fh)
		{
			fwrite($fh, date("d/m/y-H:i:s\t", time()));
			fwrite($fh, $msg . "\r\n");
			fclose($fh);
		}
	}
	
	// get a string of a value for Log purposes
	public static function logValue($mvalue, $lvl=0) {
		
		if (is_array($mvalue))
		{
			$indent="";
			for ($i=0; $i < $lvl; $i++)
				$indent = $indent."\t";
			$buffer = "";
			$buffer=$buffer. "[]={\r\n";
			foreach ($mvalue as $akey=>$avalue) {
				$buffer=$buffer . $indent . "\t" . $akey ."=>". MyLogUtils::logValue($avalue,$lvl+1) . "\r\n";
			}
			$buffer=$buffer.$indent."}";
			return ($buffer);	
		} 
		else if (is_object($mvalue))
		{
			$indent="";
			for ($i=0; $i < $lvl; $i++)
				$indent = $indent."\t";
			$buffer = "?" . "=" . get_class($mvalue) . "{\r\n";
			foreach ($mvalue as $akey=>$avalue) {
				$buffer=$buffer . $indent . "\t" . $akey ."=". MyLogUtils::logValue($avalue,$lvl+1) . "\r\n";
			}
			$buffer=$buffer.$indent."}";
			return($buffer);				
		}
		else
			return ($mvalue);	
	}
}

