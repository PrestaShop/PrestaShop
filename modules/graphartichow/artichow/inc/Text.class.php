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
 * To handle text
 *
 * @package Artichow
 */
class awText {

	/**
	 * Your text
	 *
	 * @var string
	 */
	private $text;

	/**
	 * Text font
	 *
	 * @var Font
	 */
	private $font;

	/**
	 * Text angle
	 * Can be 0 or 90
	 *
	 * @var int
	 */
	private $angle;

	/**
	 * Text color
	 *
	 * @var Color
	 */
	private $color;

	/**
	 * Text background
	 *
	 * @var Color, Gradient
	 */
	private $background;

	/**
	 * Padding
	 *
	 * @var array Array for left, right, top and bottom paddings
	 */
	private $padding;

	/**
	 * Text border
	 *
	 * @var Border
	 */
	public $border;
	
	/**
	 * Build a new awtext
	 *
	 * @param string $text Your text
	 */
	public function __construct($text, $font = NULL, $color = NULL, $angle = 0) {
	
		if(is_null($font)) {
			$font = new awFont2;
		}
		
		$this->setText($text);
		$this->setFont($font);
		
		// Set default color to black
		if($color === NULL) {
			$color = new awColor(0, 0, 0);
		}
		
		$this->setColor($color);
		$this->setAngle($angle);
		
		$this->border = new awBorder;
		$this->border->hide();
	
	}
	
	/**
	 * Get text
	 *
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Change text
	 *
	 * @param string $text New text
	 */
	public function setText($text) {
		$this->text = (string)$text;
		$this->text = str_replace("\r", "", $text);
	}

	/**
	 * Change text font
	 *
	 * @param Font
	 */
	public function setFont(awFont $font) {
		$this->font = $font;
	}
	
	/**
	 * Get text font
	 *
	 * @return int
	 */
	public function getFont() {
		return $this->font;
	}

	/**
	 * Change text angle
	 *
	 * @param int
	 */
	public function setAngle($angle) {
		$this->angle = (int)$angle;
	}
	
	/**
	 * Get text angle
	 *
	 * @return int
	 */
	public function getAngle() {
		return $this->angle;
	}

	/**
	 * Change text color
	 *
	 * @param Color
	 */
	public function setColor(awColor $color) {
		$this->color = $color;
	}
	
	/**
	 * Get text color
	 *
	 * @return Color
	 */
	public function getColor() {
		return $this->color;
	}
	
	/**
	 * Change text background
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
	 * Change text background color
	 *
	 * @param awColor $color
	 */
	public function setBackgroundColor(awColor $color) {
		$this->background = $color;
	}
	
	/**
	 * Change text background gradient
	 *
	 * @param awGradient $gradient
	 */
	public function setBackgroundGradient(awGradient $gradient) {
		$this->background = $gradient;
	}
	
	/**
	 * Get text background
	 *
	 * @return Color, Gradient
	 */
	public function getBackground() {
		return $this->background;
	}

	/**
	 * Change padding
	 *
	 * @param int $left Left padding
	 * @param int $right Right padding
	 * @param int $top Top padding
	 * @param int $bottom Bottom padding
	 */
	public function setPadding($left, $right, $top, $bottom) {
		$this->padding = array((int)$left, (int)$right, (int)$top, (int)$bottom);
	}
	
	/**
	 * Get current padding
	 *
	 * @return array
	 */
	public function getPadding() {
		return $this->padding;
	}

}

registerClass('Text');

