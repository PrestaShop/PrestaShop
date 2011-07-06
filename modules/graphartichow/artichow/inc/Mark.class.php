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
 * Draw marks
 *
 * @package Artichow
 */
class awMark {

	/**
	 * Circle mark
	 *
	 * @var int
	 */
	const CIRCLE = 1;

	/**
	 * Square mark
	 *
	 * @var int
	 */
	const SQUARE = 2;

	/**
	 * Triangle mark
	 * 
	 * @var int
	 */
	const TRIANGLE = 3;
	
	/**
	 * Inverted triangle mark
	 * 
	 * @var int
	 */
	const INVERTED_TRIANGLE = 4;

	/**
	 * Rhombus mark
	 * 
	 * @var int
	 */
	const RHOMBUS = 5;

	/**
	 * Cross (X) mark
	 * 
	 * @var int
	 */
	const CROSS = 6;

	/**
	 * Plus mark
	 * 
	 * @var int
	 */
	const PLUS = 7;

	/**
	 * Image mark
	 *
	 * @var int
	 */
	const IMAGE = 8;

	/**
	 * Star mark
	 *
	 * @var int
	 */
	const STAR = 9;

	/**
	 * Paperclip mark
	 *
	 * @var int
	 */
	const PAPERCLIP = 10;

	/**
	 * Book mark
	 *
	 * @var int
	 */
	const BOOK = 11;

	/**
	 * Must marks be hidden ?
	 *
	 * @var bool
	 */
	protected $hide;

	/**
	 * Mark type
	 *
	 * @var int
	 */
	protected $type;

	/**
	 * Mark size
	 *
	 * @var int
	 */
	protected $size = 8;

	/**
	 * Fill mark
	 *
	 * @var Color, Gradient
	 */
	protected $fill;

	/**
	 * Mark image
	 *
	 * @var Image
	 */
	protected $image;

	/**
	 * To draw marks
	 *
	 * @var Driver
	 */
	protected $driver;

	/**
	 * Move position from this vector
	 *
	 * @var Point
	 */
	protected $move;
	
	/**
	 * Marks border
	 *
	 * @var Border
	 */
	public $border;

	/**
	 * Build the mark
	 */
	public function __construct() {
		
		$this->fill = new awColor(255, 0, 0, 0);
		$this->border = new awBorder;
		$this->border->hide();
		
		$this->move = new awPoint(0, 0);
	
	}
	
	/**
	 * Change mark position
	 *
	 * @param int $x Add this interval to X coord
	 * @param int $y Add this interval to Y coord
	 */
	public function move($x, $y) {
	
		$this->move = $this->move->move($x, $y);
	
	}
	
	/**
	 * Hide marks ?
	 *
	 * @param bool $hide TRUE to hide marks, FALSE otherwise
	 */
	public function hide($hide = TRUE) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Show marks ?
	 *
	 * @param bool $show
	 */
	public function show($show = TRUE) {
		$this->hide = (bool)!$show;
	}
	
	/**
	 * Change mark type
	 *
	 * @param int $size Size in pixels
	 */
	public function setSize($size) {
		$this->size = (int)$size;
	}
	
	/**
	 * Change mark type
	 *
	 * @param int $type New mark type
	 * @param int $size Mark size (can be NULL)
	 */
	public function setType($type, $size = NULL) {
		$this->type = (int)$type;
		if($size !== NULL) {
			$this->setSize($size);
		}
	}
	
	/**
	 * Fill the mark with a color or a gradient
	 *
	 * @param mixed $fill A color or a gradient
	 */
	public function setFill($fill) {
		if($fill instanceof awColor or $fill instanceof awGradient) {
			$this->fill = $fill;
		}
	}
	
	/**
	 * Set an image
	 * Only for awMark::IMAGE type.
	 *
	 * @param Image An image
	 */
	public function setImage(awImage $image) {
		$this->image = $image;
	}
	
	/**
	 * Draw the mark
	 *
	 * @param awDriver $driver
	 * @param awPoint $point Mark center
	 */
	public function draw(awDriver $driver, awPoint $point) {
	
		// Hide marks ?
		if($this->hide) {
			return;
		}
	
		// Check if we can print marks
		if($this->type !== NULL) {
		
			$this->driver = $driver;
			$realPoint = $this->move->move($point->x, $point->y);
		
			switch($this->type) {
			
				case awMark::CIRCLE :
					$this->drawCircle($realPoint);
					break;
			
				case awMark::SQUARE :
					$this->drawSquare($realPoint);
					break;
				
				case awMark::TRIANGLE :
					$this->drawTriangle($realPoint);
					break;

				case awMark::INVERTED_TRIANGLE :
					$this->drawTriangle($realPoint, TRUE);
					break;
				
				case awMark::RHOMBUS :
					$this->drawRhombus($realPoint);
					break;

				case awMark::CROSS :
					$this->drawCross($realPoint);
					break;
					
				case awMark::PLUS :
					$this->drawCross($realPoint, TRUE);
					break;
			
				case awMark::IMAGE :
					$this->drawImage($realPoint);
					break;
					
				case awMark::STAR :
					$this->changeType('star');
					$this->draw($driver, $point);
					break;
					
				case awMark::PAPERCLIP :
					$this->changeType('paperclip');
					$this->draw($driver, $point);
					break;
					
				case awMark::BOOK :
					$this->changeType('book');
					$this->draw($driver, $point);
					break;
					
			}
		
		}
	
	}
	
	protected function changeType($image) {
		$this->setType(awMARK::IMAGE);
		$this->setImage(new awFileImage(ARTICHOW_IMAGE.DIRECTORY_SEPARATOR.$image.'.png'));
	}
	
	protected function drawCircle(awPoint $point) {
		
		$this->driver->filledEllipse(
			$this->fill,
			$point,
			$this->size, $this->size
		);
	
		$this->border->ellipse(
			$this->driver,
			$point,
			$this->size, $this->size
		);
	
	}
	
	protected function drawSquare(awPoint $point) {
	
		list($x, $y) = $point->getLocation();
	
		$x1 = (int)($x - $this->size / 2);
		$x2 = $x1 + $this->size;
		$y1 = (int)($y - $this->size / 2);
		$y2 = $y1 + $this->size;
		
		$this->border->rectangle($this->driver, new awPoint($x1, $y1), new awPoint($x2, $y2));
		
		$size = $this->border->visible() ? 1 : 0;
		
		$this->driver->filledRectangle(
			$this->fill,
			new awLine(
				new awPoint($x1 + $size, $y1 + $size),
				new awPoint($x2 - $size, $y2 - $size)
			)
		);
	
	}
	
	protected function drawTriangle(awPoint $point, $inverted = FALSE) {
		
		list($x, $y) = $point->getLocation();
		
		$size = $this->size;
		
		$triangle = new awPolygon;
		// Set default style and thickness
		$triangle->setStyle(awPolygon::SOLID);
		$triangle->setThickness(1);
		
		if($inverted === TRUE) {
			// Bottom of the triangle
			$triangle->append(new awPoint($x, $y + $size / sqrt(3)));
		
			// Upper left corner
			$triangle->append(new awPoint($x - $size / 2, $y - $size / (2 * sqrt(3))));

			// Upper right corner
			$triangle->append(new awPoint($x + $size / 2, $y - $size / (2 * sqrt(3))));
		} else {
			// Top of the triangle
			$triangle->append(new awPoint($x, $y - $size / sqrt(3)));
			
			// Lower left corner
			$triangle->append(new awPoint($x - $size / 2, $y + $size / (2 * sqrt(3))));
	
			// Lower right corner
			$triangle->append(new awPoint($x + $size / 2, $y + $size / (2 * sqrt(3))));
		}

		$this->driver->filledPolygon($this->fill, $triangle);
		
		if($this->border->visible()) {			
			$this->border->polygon($this->driver, $triangle);
		}
	}
	
	protected function drawRhombus(awPoint $point) {
	
		list($x, $y) = $point->getLocation();

		$rhombus = new awPolygon;
		// Set default style and thickness
		$rhombus->setStyle(awPolygon::SOLID);
		$rhombus->setThickness(1);
		
		// Top of the rhombus
		$rhombus->append(new awPoint($x, $y - $this->size / 2));
		
		// Right of the rhombus
		$rhombus->append(new awPoint($x + $this->size / 2, $y));
		
		// Bottom of the rhombus
		$rhombus->append(new awPoint($x, $y + $this->size / 2));
		
		// Left of the rhombus
		$rhombus->append(new awPoint($x - $this->size / 2, $y));
		
		$this->driver->filledPolygon($this->fill, $rhombus);
		
		if($this->border->visible()) {			
			$this->border->polygon($this->driver, $rhombus);
		}
	}
	
	protected function drawCross(awPoint $point, $upright = FALSE) {
	
		list($x, $y) = $point->getLocation();

		if($upright === TRUE) {
			$x11 = (int)($x);
			$y11 = (int)($y - $this->size / 2);
			$x12 = (int)($x);
			$y12 = (int)($y + $this->size / 2);
	
			$y21 = (int)($y);
			$y22 = (int)($y);
		} else {
			$x11 = (int)($x - $this->size / 2);
			$y11 = (int)($y + $this->size / 2);
			$x12 = (int)($x + $this->size / 2);
			$y12 = (int)($y - $this->size / 2);

			$y21 = (int)($y - $this->size / 2);
			$y22 = (int)($y + $this->size / 2);
		}
			
		$x21 = (int)($x - $this->size / 2);
		$x22 = (int)($x + $this->size / 2);
		
		$this->driver->line(
			$this->fill,
			new awLine(
				new awPoint($x11, $y11),
				new awPoint($x12, $y12)
			)
		);
		
		$this->driver->line(
			$this->fill,
			new awLine(
				new awPoint($x21, $y21),
				new awPoint($x22, $y22)
			)
		);
	}

	protected function drawImage(awPoint $point) {
		
		if($this->image instanceof awImage) {
		
			$width = $this->image->width;
			$height = $this->image->height;
	
			list($x, $y) = $point->getLocation();
		
			$x1 = (int)($x - $width / 2);
			$x2 = $x1 + $width;
			$y1 = (int)($y - $width / 2);
			$y2 = $y1 + $height;
		
			$this->border->rectangle($this->driver, new awPoint($x1 - 1, $y1 - 1), new awPoint($x2 + 1, $y2 + 1));
			
			$this->driver->copyImage($this->image, new awPoint($x1, $y1), new awPoint($x2, $y2));
			
		}
	
	}

}

registerClass('Mark');
