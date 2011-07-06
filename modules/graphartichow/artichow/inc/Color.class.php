<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Graph.class.php";

/**
 * Create your colors
 *
 * @package Artichow
 */
class awColor {
	
	public $red;
	public $green;
	public $blue;
	public $alpha;

	/**
	 * Build your color
	 *
	 * @var int $red Red intensity (from 0 to 255)
	 * @var int $green Green intensity (from 0 to 255)
	 * @var int $blue Blue intensity (from 0 to 255)
	 * @var int $alpha Alpha channel (from 0 to 100)
	 */
	public function __construct($red, $green, $blue, $alpha = 0) {
	
		$this->red = (int)$red;
		$this->green = (int)$green;
		$this->blue = (int)$blue;
		$this->alpha = (int)round($alpha * 127 / 100);
		
	}
	
	/**
	 * Get RGB and alpha values of your color
	 *
	 * @return array
	 */
	public function getColor() {		
		return $this->rgba();
	}
	
	/**
	 * Change color brightness
	 *
	 * @param int $brightness Add this intensity to the color (betweeen -255 and +255)
	 */
	public function brightness($brightness) {
	
		$brightness = (int)$brightness;
	
		$this->red = min(255, max(0, $this->red + $brightness));
		$this->green = min(255, max(0, $this->green + $brightness));
		$this->blue = min(255, max(0, $this->blue + $brightness));
	
	}

	/**
	 * Get RGB and alpha values of your color
	 *
	 * @return array
	 */
	public function rgba() {
	
		return array($this->red, $this->green, $this->blue, $this->alpha);
	
	}

}

registerClass('Color');

$colors = array(
	'Black' => array(0, 0, 0),
	'AlmostBlack' => array(48, 48, 48),
	'VeryDarkGray' => array(88, 88, 88),
	'DarkGray' => array(128, 128, 128),
	'MidGray' => array(160, 160, 160),
	'LightGray' => array(195, 195, 195),
	'VeryLightGray' => array(220, 220, 220),
	'White' => array(255, 255, 255),
	'VeryDarkRed' => array(64, 0, 0),
	'DarkRed' => array(128, 0, 0),
	'MidRed' => array(192, 0, 0),
	'Red' => array(255, 0, 0),
	'LightRed' => array(255, 192, 192),
	'VeryDarkGreen' => array(0, 64, 0),
	'DarkGreen' => array(0, 128, 0),
	'MidGreen' => array(0, 192, 0),
	'Green' => array(0, 255, 0),
	'LightGreen' => array(192, 255, 192),
	'VeryDarkBlue' => array(0, 0, 64),
	'DarkBlue' => array(0, 0, 128),
	'MidBlue' => array(0, 0, 192),
	'Blue' => array(0, 0, 255),
	'LightBlue' => array(192, 192, 255),
	'VeryDarkYellow' => array(64, 64, 0),
	'DarkYellow' => array(128, 128, 0),
	'MidYellow' => array(192, 192, 0),
	'Yellow' => array(255, 255, 2),
	'LightYellow' => array(255, 255, 192),
	'VeryDarkCyan' => array(0, 64, 64),
	'DarkCyan' => array(0, 128, 128),
	'MidCyan' => array(0, 192, 192),
	'Cyan' => array(0, 255, 255),
	'LightCyan' => array(192, 255, 255),
	'VeryDarkMagenta' => array(64, 0, 64),
	'DarkMagenta' => array(128, 0, 128),
	'MidMagenta' => array(192, 0, 192),
	'Magenta' => array(255, 0, 255),
	'LightMagenta' => array(255, 192, 255),
	'DarkOrange' => array(192, 88, 0),
	'Orange' => array(255, 128, 0),
	'LightOrange' => array(255, 168, 88),
	'VeryLightOrange' => array(255, 220, 168),
	'DarkPink' => array(192, 0, 88),
	'Pink' => array(255, 0, 128),
	'LightPink' => array(255, 88, 168),
	'VeryLightPink' => array(255, 168, 220),
	'DarkPurple' => array(88, 0, 192),
	'Purple' => array(128, 0, 255),
	'LightPurple' => array(168, 88, 255),
	'VeryLightPurple' => array(220, 168, 255),
);



$php = '';

foreach($colors as $name => $color) {

	list($red, $green, $blue) = $color;

	$php .= '
	class aw'.$name.' extends awColor {
	
		public function __construct($alpha = 0) {
			parent::__construct('.$red.', '.$green.', '.$blue.', $alpha);
		}
	
	}
	';
	
	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.$name.' extends aw'.$name.' {
		
		}
		';
	}

}

eval($php);




