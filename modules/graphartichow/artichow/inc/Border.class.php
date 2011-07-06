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
 * Draw border
 *
 * @package Artichow
 */
class awBorder {

	/**
	 * Border color
	 *
	 * @var Color
	 */
	protected $color;

	/**
	 * Hide border ?
	 *
	 * @var bool
	 */
	protected $hide = FALSE;

	/**
	 * Border line style
	 *
	 * @var int
	 */
	protected $style;
	
	/**
	 * Build the border
	 *
	 * @param awColor $color Border color
	 * @param int $style Border style
	 */
	public function __construct($color = NULL, $style = awLine::SOLID) {
	
		$this->setStyle($style);
		
		if($color instanceof awColor) {
			$this->setColor($color);
		} else {
			$this->setColor(new awBlack);
		}
		
	}
	
	/**
	 * Change border color
	 * This method automatically shows the border if it is hidden
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->color = $color;
		$this->show();
	}
	
	/**
	 * Change border style
	 *
	 * @param int $style
	 */
	public function setStyle($style) {
		$this->style = (int)$style;
	}
	
	/**
	 * Hide border ?
	 *
	 * @param bool $hide
	 */
	public function hide($hide = TRUE) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Show border ?
	 *
	 * @param bool $show
	 */
	public function show($show = TRUE) {
		$this->hide = (bool)!$show;
	}
	
	/**
	 * Is the border visible ?
	 *
	 * @return bool
	 */
	public function visible() {
		return !$this->hide;
	}
	
	/**
	 * Draw border as a rectangle
	 *
	 * @param awDriver $driver
	 * @param awPoint $p1 Top-left corner
	 * @param awPoint $p2 Bottom-right corner
	 */
	public function rectangle(awDriver $driver, awPoint $p1, awPoint $p2) {
	
		// Border is hidden
		if($this->hide) {
			return;
		}
	
		$line = new awLine;
		$line->setStyle($this->style);
		$line->setLocation($p1, $p2);
		
		$driver->rectangle($this->color, $line);
		
	}
	
	/**
	 * Draw border as an ellipse
	 *
	 * @param awDriver $driver
	 * @param awPoint $center Ellipse center
	 * @param int $width Ellipse width
	 * @param int $height Ellipse height
	 */
	public function ellipse(awDriver $driver, awPoint $center, $width, $height) {
	
		// Border is hidden
		if($this->hide) {
			return;
		}
		
		switch($this->style) {
		
			case awLine::SOLID :
				$driver->ellipse($this->color, $center, $width, $height);
				break;
			
			default :
				awImage::drawError("Class Border: Dashed and dotted borders and not yet implemented on ellipses.");
				break;
		
		}
		
		
	}
	
	/**
	 * Draw border as a polygon
	 * 
	 * @param awDriver $driver A Driver object
	 * @param awPolygon $polygon A Polygon object
	 */
	public function polygon(awDriver $driver, awPolygon $polygon) {
		
		// Border is hidden
		if($this->hide) {
			return;
		}
		
		$polygon->setStyle($this->style);
		$driver->polygon($this->color, $polygon);
		
		// In case of Line::SOLID, Driver::polygon() uses imagepolygon()
		// which automatically closes the shape. In any other case,
		// we have to do it manually here.
		if($this->style !== Line::SOLID) {
			$this->closePolygon($driver, $polygon);
		}
	}
	
	/**
	 * Draws the last line of a Polygon, between the first and last point
	 * 
	 * @param awDriver $driver A Driver object
	 * @param awPolygon $polygon The polygon object to close
	 */
	private function closePolygon(awDriver $driver, awPolygon $polygon) {
		$first = $polygon->get(0);
		$last  = $polygon->get($polygon->count() - 1);
		
		$line = new awLine($first, $last, $this->style, $polygon->getThickness());
		$driver->line($this->color, $line);
	}
	
}

registerClass('Border');
