<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 

if(is_file(dirname(__FILE__)."/Artichow.cfg.php")) { // For PHP 4+5 version
	require_once dirname(__FILE__)."/Artichow.cfg.php";
}




/*
 * Register a class with the prefix in configuration file
 */
function registerClass($class, $abstract = FALSE) {

	if(ARTICHOW_PREFIX === 'aw') {
		return;
	}
	
	
	if($abstract) {
		$abstract = 'abstract';
	} else {
		$abstract = '';
	}
	
	
	eval($abstract." class ".ARTICHOW_PREFIX.$class." extends aw".$class." { }");

}

/*
 * Register an interface with the prefix in configuration file
 */
function registerInterface($interface) {

	if(ARTICHOW_PREFIX === 'aw') {
		return;
	}

	
	eval("interface ".ARTICHOW_PREFIX.$interface." extends aw".$interface." { }");
	

}

// Some useful files
require_once ARTICHOW."/Component.class.php";

require_once ARTICHOW."/inc/Grid.class.php";
require_once ARTICHOW."/inc/Tools.class.php";
require_once ARTICHOW."/inc/Driver.class.php";
require_once ARTICHOW."/inc/Math.class.php";
require_once ARTICHOW."/inc/Tick.class.php";
require_once ARTICHOW."/inc/Axis.class.php";
require_once ARTICHOW."/inc/Legend.class.php";
require_once ARTICHOW."/inc/Mark.class.php";
require_once ARTICHOW."/inc/Label.class.php";
require_once ARTICHOW."/inc/Text.class.php";
require_once ARTICHOW."/inc/Color.class.php";
require_once ARTICHOW."/inc/Font.class.php";
require_once ARTICHOW."/inc/Gradient.class.php";
require_once ARTICHOW."/inc/Shadow.class.php";
require_once ARTICHOW."/inc/Border.class.php";

require_once ARTICHOW."/common.php";
 
/**
 * An image for a graph
 *
 * @package Artichow
 */
class awImage {

	/**
	 * Graph width
	 *
	 * @var int
	 */
	public $width;

	/**
	 * Graph height
	 *
	 * @var int
	 */
	public $height;
	
	/**
	 * Use anti-aliasing ?
	 *
	 * @var bool
	 */
	protected $antiAliasing = FALSE;
	
	/**
	 * Image format
	 *
	 * @var int
	 */
	protected $format = awImage::PNG;
	
	/**
	 * Image background color
	 *
	 * @var Color
	 */
	protected $background;
	
	/**
	 * GD resource
	 *
	 * @var resource
	 */
	protected $resource;
	
	/**
	 * A Driver object
	 *
	 * @var Driver
	 */
	protected $driver;
	
	/**
	 * Driver string
	 * 
	 * @var string
	 */
	protected $driverString;
		
	/**
	 * Shadow
	 *
	 * @var Shadow
	 */
	public $shadow;
	
	/**
	 * Image border
	 *
	 * @var Border
	 */
	public $border;
	
	/**
	 * Use JPEG for image
	 *
	 * @var int
	 */
	const JPEG = IMG_JPG;
	
	/**
	 * Use PNG for image
	 *
	 * @var int
	 */
	const PNG = IMG_PNG;
	
	/**
	 * Use GIF for image
	 *
	 * @var int
	 */
	const GIF = IMG_GIF;
	
	/**
	 * Build the image
	 */
	public function __construct() {
		
		$this->background = new awColor(255, 255, 255);
		$this->shadow = new awShadow(awShadow::RIGHT_BOTTOM);
		$this->border = new awBorder;
		
	}
	
	/**
	 * Get driver of the image
	 *
	 * @param int $w Driver width (from 0 to 1) (default to 1)
	 * @param int $h Driver height (from 0 to 1) (default to 1)
	 * @param float $x Position on X axis of the center of the driver (default to 0.5)
	 * @param float $y Position on Y axis of the center of the driver (default to 0.5)
	 * @return Driver
	 */
	public function getDriver($w = 1, $h = 1, $x = 0.5, $y = 0.5) {
		$this->create();
		$this->driver->setSize($w, $h);
		$this->driver->setPosition($x, $y);
		return $this->driver;
	}
	
	/**
	 * Sets the driver that will be used to draw the graph
	 * 
	 * @param string $driverString
	 */
	public function setDriver($driverString) {
		$this->driver = $this->selectDriver($driverString);
		
		$this->driver->init($this);
	}
	
	/**
	 * Change the image size
	 *
	 * @var int $width Image width
	 * @var int $height Image height
	 */
	public function setSize($width, $height) {
	
		if($width !== NULL) {
			$this->width = (int)$width;
		}
		if($height !== NULL) {
			$this->height = (int)$height;
		}
	
	}
	
	/**
	 * Change image background
	 * 
	 * @param mixed $background
	 */
	public function setBackground($background) {
		if($background instanceof awColor) {
			$this->setBackgroundColor($background);
		} elseif($background instanceof awGradient) {
			$this->setBackgroundGradient($background);
		}
	}
	
	/**
	 * Change image background color
	 *
	 * @param awColor $color
	 */
	public function setBackgroundColor(awColor $color) {
		$this->background = $color;
	}
	
	/**
	 * Change image background gradient
	 *
	 * @param awGradient $gradient
	 */
	public function setBackgroundGradient(awGradient $gradient) {
		$this->background = $gradient;
	}
	
	/**
	 * Return image background, whether a Color or a Gradient
	 * 
	 * @return mixed
	 */
	public function getBackground() {
		return $this->background;
	}
	
	/**
	 * Turn antialiasing on or off
	 *
	 * @var bool $bool
	 */
	public function setAntiAliasing($bool) {
		$this->antiAliasing = (bool)$bool;
	}
	
	/**
	 * Return the antialiasing setting
	 *
	 * @return bool
	 */
	public function getAntiAliasing() {
		return $this->antiAliasing;
	}
	
	/**
	 * Change image format
	 *
	 * @var int $format New image format
	 */
	public function setFormat($format) {
		if($format === awImage::JPEG or $format === awImage::PNG or $format === awImage::GIF) {
			$this->format = $format;
		}
	}
	
	/**
	 * Returns the image format as an integer
	 *
	 * @return unknown
	 */
	public function getFormat() {
		return $this->format;
	}
	
	/**
	 * Returns the image format as a string
	 *
	 * @return string
	 */
	public function getFormatString() {
		
		switch($this->format) {
			case awImage::JPEG :
				return 'jpeg';
			case awImage::PNG :
				return 'png';
			case awImage::GIF :
				return 'gif';
		}
		
	}

	/**
	 * Create a new awimage
	 */
	public function create() {

		if($this->driver === NULL) {
			$driver = $this->selectDriver($this->driverString);

			$driver->init($this);
			
			$this->driver = $driver;
		}

	}
	
	/**
	 * Select the correct driver
	 *
	 * @param string $driver The desired driver
	 * @return mixed
	 */
	protected function selectDriver($driver) {
		$drivers = array('gd');
		$driver = strtolower((string)$driver);

		if(in_array($driver, $drivers, TRUE)) {
			$string = $driver;
		} else {
			$string = ARTICHOW_DRIVER;
		}

		switch ($string) {
				case 'gd':
					require_once ARTICHOW.'/inc/drivers/gd.class.php';
					$this->driverString = $string;
					return new awGDDriver();
					
				default:
					// We should never get here, unless the wrong string is used AND the ARTICHOW_DRIVER
					// global has been messed with.
					awImage::drawError('Class Image: Unknown driver type (\''.$string.'\')');
					break;
			}
	}
	
	/**
	 * Draw a component on the image
	 *
	 * @var awComponent $component A component
	 */
	public function drawComponent(awComponent $component) {
		
		$shadow = $this->shadow->getSpace(); // Image shadow
		$border = $this->border->visible() ? 1 : 0; // Image border size
	
		$driver = clone $this->driver;
		$driver->setImageSize(
			$this->width - $shadow->left - $shadow->right - $border * 2,
			$this->height - $shadow->top - $shadow->bottom - $border * 2
		);
	
		// No absolute size specified
		if($component->w === NULL and $component->h === NULL) {
		
			list($width, $height) = $driver->setSize($component->width, $component->height);
	
			// Set component size in pixels
			$component->setAbsSize($width, $height);
			
		} else {
		
			$driver->setAbsSize($component->w, $component->h);
		
		}
		
		if($component->top !== NULL and $component->left !== NULL) {
			$driver->setAbsPosition(
				$border + $shadow->left + $component->left,
				$border + $shadow->top + $component->top
			);
		} else {
			$driver->setPosition($component->x, $component->y);
		}
		
		$driver->movePosition($border + $shadow->left, $border + $shadow->top);
		
		list($x1, $y1, $x2, $y2) = $component->getPosition();
		
		$component->init($driver);
		
		$component->drawComponent($driver, $x1, $y1, $x2, $y2, $this->antiAliasing);
		$component->drawEnvelope($driver, $x1, $y1, $x2, $y2);
		
		$component->finalize($driver);
	
	}
	
	protected function drawShadow() {
	
		$driver = $this->getDriver();
		
		$this->shadow->draw(
			$driver,
			new awPoint(0, 0),
			new awPoint($this->width, $this->height),
			awShadow::IN
		);
	
	}
	
	/**
	 * Send the image into a file or to the user browser
	 *
	 */
	public function send() {
		$this->driver->send($this);
	}
	
	/**
	 * Return the image content as binary data
	 *
	 */	
	public function get() {
		return $this->driver->get($this);
	}
	
	/**
	 * Send the correct HTTP header according to the image type
	 *
	 */
	public function sendHeaders() {

		if(headers_sent() === FALSE) {
			
			switch ($this->driverString) {
				case 'gd' :
					header('Content-type: image/'.$this->getFormatString());
					break;
				
			}

		}
	
	}
	
	
	private static $errorWriting = FALSE;
	

	/*
	 * Display an error image and exit
	 *
	 * @param string $message Error message
	 */
	public static function drawError($message) {
	
			
		if(self::$errorWriting) {
			return;
		}
	
		self::$errorWriting = TRUE;
	
		$message = wordwrap($message, 40, "\n", TRUE);
		
		$width = 400;
		$height = max(100, 40 + 22.5 * (substr_count($message, "\n") + 1));
		
		$image = new awImage();
		$image->setSize($width, $height);
		$image->setDriver('gd');
		
		$driver = $image->getDriver();
		$driver->init($image);
		
		// Display title
		$driver->filledRectangle(
			new awWhite,
			new awLine(
				new awPoint(0, 0),
				new awPoint($width, $height)
			)
		);
		
		$driver->filledRectangle(
			new awRed,
			new awLine(
				new awPoint(0, 0),
				new awPoint(110, 25)
			)
		);
		
		$text = new awText(
			"Artichow error",
			new awFont3,
			new awWhite,
			0
		);
		
		$driver->string($text, new awPoint(5, 6));
		
		// Display red box
		$driver->rectangle(
			new awRed,
			new awLine(
				new awPoint(0, 25),
				new awPoint($width - 90, $height - 1)
			)
		);
		
		// Display error image
		$file = ARTICHOW_IMAGE.DIRECTORY_SEPARATOR.'error.png';
		
		$imageError = new awFileImage($file);
		$driver->copyImage(
			$imageError,
			new awPoint($width - 81, $height - 81),
			new awPoint($width - 1, $height - 1)
		);
		
		// Draw message
		$text = new awText(
			strip_tags($message),
			new awFont2,
			new awBlack,
			0
		);
		
		$driver->string($text, new awPoint(10, 40));
		
		$image->send();
		
		exit;
	
	}
	
	/*
	 * Display an error image located in a file and exit
	 *
	 * @param string $error Error name
	 */
	public static function drawErrorFile($error) {
	
		$file = ARTICHOW_IMAGE.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error.'.png';
		
		header("Content-Type: image/png");
		readfile($file);
		exit;
	
	}

}

registerClass('Image');

 
/**
 * Load an image from a file
 *
 * @package Artichow
 */
class awFileImage extends awImage {

	/**
	 * Build a new awimage
	 *
	 * @param string $file Image file name
	 */
	public function __construct($file) {
	
		$driver = $this->selectDriver($this->driverString);
		
		$driver->initFromFile($this, $file);
		
		$this->driver = $driver;
	
	}

}

registerClass('FileImage');


