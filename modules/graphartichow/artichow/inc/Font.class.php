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
 * Common font characteristics and methods.
 * Declared abstract only so that it can't be instanciated.
 * Users have to call 'new awPHPFont' or 'new awFileFont',
 * or any of their inherited classes (awFont1, awTuffy, awTTFFont, etc.)
 *
 * @package Artichow
 */
abstract class awFont {

	/**
	 * Build the font
	 *
	 */
	public function __construct() {
		
	}

	/**
	 * Draw a text
	 *
	 * @param awDriver $driver
	 * @param awPoint $p Draw text at this point
	 * @param awText $text The text
	 * @param int $width Text box width
	 */
	public function draw(awDriver $driver, awPoint $point, awText $text, $width = NULL) {
		
		$driver->string($this, $text, $point, $width);
		
	}

}

registerClass('Font', TRUE);

/**
 * Class for fonts that cannot be transformed,
 * like the built-in PHP fonts for example.
 * 
 * @package Artichow
 */
class awPHPFont extends awFont {
	
	/**
	 * The used font identifier
	 * 
	 * @var int
	 */
	public $font;
	
	public function __construct($font = NULL) {
		parent::__construct();
		
		if($font !== NULL) {
			$this->font = (int)$font;
		}
	}
	
}

registerClass('PHPFont');

/**
 * Class for fonts that can be transformed (rotated, skewed, etc.),
 * like TTF or FDB fonts for example.
 *
 * @package Artichow
 */
class awFileFont extends awFont {
	
	/**
	 * The name of the font, without the extension
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 * The size of the font
	 *
	 * @var int
	 */
	protected $size;
	
	/**
	 * The font filename extension
	 * 
	 * @var string
	 */
	protected $extension;
	
	public function __construct($name, $size) {
		parent::__construct();
		
		$this->setName($name);
		$this->setSize($size);
	}
	
	/**
	 * Set the name of the font. The $name variable can contain the full path,
	 * or just the filename. Artichow will try to do The Right Thing,
	 * as well as set the extension property correctly if possible.
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$fontInfo = pathinfo((string)$name);
		
		if(strpos($fontInfo['dirname'], '/') !== 0) {
			// Path is not absolute, use ARTICHOW_FONT
			$name = ARTICHOW_FONT.DIRECTORY_SEPARATOR.$fontInfo['basename'];
			$fontInfo = pathinfo($name);
		}
		
		$this->name = $fontInfo['dirname'].DIRECTORY_SEPARATOR.$fontInfo['basename'];
		
		if(array_key_exists('extension', $fontInfo) and $fontInfo['extension'] !== '') {
			$this->setExtension($fontInfo['extension']);
		}
	}
	
	/**
	 * Return the name of the font, i.e. the absolute path and the filename, without the extension.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Set the size of the font, in pixels
	 *
	 * @param int $size
	 */
	public function setSize($size) {
		$this->size = (int)$size;
	}
	
	/**
	 * Return the size of the font, in pixels
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}
	
	/**
	 * Set the extension, without the dot
	 *
	 * @param string $extension
	 */
	public function setExtension($extension) {
		$this->extension = (string)$extension;
	}
	
	/**
	 * Get the filename extension for that font
	 * 
	 * @return string
	 */
	public function getExtension() {
		return $this->extension;
	}

}

registerClass('FileFont');

/**
 * Class representing TTF fonts
 * 
 * @package Artichow
 */
class awTTFFont extends awFileFont {
	
	public function __construct($name, $size) {
		parent::__construct($name, $size);
		
		if($this->getExtension() === NULL) {
			$this->setExtension('ttf');
		}
	}

}

registerClass('TTFFont');



$php = '';

for($i = 1; $i <= 5; $i++) {

	$php .= '
	class awFont'.$i.' extends awPHPFont {

		public function __construct() {
			parent::__construct('.$i.');
		}

	}
	';

	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.'Font'.$i.' extends awFont'.$i.' {
		}
		';
	}

}

eval($php);

$php = '';

foreach($fonts as $font) {

	$php .= '
	class aw'.$font.' extends awFileFont {

		public function __construct($size) {
			parent::__construct(\''.$font.'\', $size);
		}

	}
	';

	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.$font.' extends aw'.$font.' {
		}
		';
	}

}

eval($php);



/*
 * Environment modification for GD2 and TTF fonts
 */
if(function_exists('putenv')) {
	putenv('GDFONTPATH='.ARTICHOW_FONT);
}

