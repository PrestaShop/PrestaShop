<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Graph.class.php";

abstract class awShape {
	
	/**
	 * Is the shape hidden ?
	 *
	 * @var bool
	 */
	protected $hide = FALSE;
	
	/**
	 * Shape style
	 *
	 * @var int
	 */
	public $style;
	
	/**
	 * Shape thickness
	 *
	 * @var int
	 */
	public $thickness;
	
	/**
	 * Solid shape
	 *
	 * @var int
	 */
	const SOLID = 1;
	
	/**
	 * Dotted shape
	 *
	 * @var int
	 */
	const DOTTED = 2;
	
	/**
	 * Dashed shape
	 *
	 * @var int
	 */
	const DASHED = 3;
	
	/**
	 * Change shape style
	 *
	 * @param int $style Line style
	 */
	public function setStyle($style) {
		$this->style = (int)$style;
	}
	
	/**
	 * Return shape style
	 *
	 * @return int
	 */
	public function getStyle() {
		return $this->style;
	}
	
	/**
	 * Change shape thickness
	 *
	 * @param int $thickness Shape thickness in pixels
	 */
	public function setThickness($thickness) {
		$this->thickness = (int)$thickness;
	}
	
	/**
	 * Return shape thickness
	 *
	 * @return int
	 */
	public function getThickness() {
		return $this->thickness;
	}
	
	/**
	 * Hide the shape
	 *
	 * @param bool $hide
	 */
	public function hide($hide) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Show the shape
	 *
	 * @param bool $shape
	 */
	public function show($shape) {
		$this->hide = (bool)!$shape;
	}
	
	/**
	 * Is the line hidden ?
	 *
	 * @return bool
	 */
	public function isHidden() {
		return $this->hide;
	}
	
}

registerClass('Shape', TRUE);

/**
 * Describe a point
 *
 * @package Artichow
 */
class awPoint extends awShape {

	/**
	 * X coord
	 *
	 * @var float
	 */
	public $x;

	/**
	 * Y coord
	 *
	 * @var float
	 */
	public $y;
	
	/**
	 * Build a new awpoint
	 *
	 * @param float $x
	 * @param float $y
	 */
	public function __construct($x, $y) {
	
		$this->setLocation($x, $y);
		
	}
	
	/**
	 * Change X value
	 *
	 * @param float $x
	 */
	public function setX($x) {
		$this->x = (float)$x;
	}
	
	/**
	 * Change Y value
	 *
	 * @param float $y
	 */
	public function setY($y) {
		$this->y = (float)$y;
	}
	
	/**
	 * Change point location
	 *
	 * @param float $x
	 * @param float $y
	 */
	public function setLocation($x, $y) {
		$this->setX($x);
		$this->setY($y);
	}
	
	/**
	 * Get point location
	 *
	 * @param array Point location
	 */
	public function getLocation() {
		return array($this->x, $this->y);
	}
	
	/**
	 * Get distance to another point
	 *
	 * @param awPoint $p A point
	 * @return float
	 */
	public function getDistance(awPoint $p) {
	
		return sqrt(pow($p->x - $this->x, 2) + pow($p->y - $this->y, 2));
	
	}
	
	/**
	 * Move the point to another location
	 *
	 * @param Point A Point with the new awlocation
	 */
	public function move($x, $y) {
	
		return new awPoint(
			$this->x + $x,
			$this->y + $y
		);
		
	}

}

registerClass('Point');
 

/**
 * Describe a line
 *
 * @package Artichow
 */
class awLine extends awShape {

	/**
	 * Line first point
	 *
	 * @param Point
	 */
	public $p1;

	/**
	 * Line second point
	 *
	 * @param Point
	 */
	public $p2;
	
	/**
	 * The line slope (the m in y = mx + p)
	 * 
	 * @param float
	 */
	private $slope;
	
	/**
	 * The y-intercept value of the line (the p in y = mx + p)
	 * 
	 * @param float
	 */
	private $origin;
	
	/**
	 * Build a new awline
	 *
	 * @param awPoint $p1 First point
	 * @param awPoint $p2 Second point
	 * @param int $type Style of line (default to solid)
	 * @param int $thickness Line thickness (default to 1)
	 */
	public function __construct($p1 = NULL, $p2 = NULL, $type = awLine::SOLID, $thickness = 1) {
	
		$this->setLocation($p1, $p2);
		$this->setStyle($type);
		$this->setThickness($thickness);
		
	}
	
	/**
	 * Build a line from 4 coords
	 *
	 * @param int $x1 Left position
	 * @param int $y1 Top position
	 * @param int $x2 Right position
	 * @param int $y2 Bottom position
	 */
	public static function build($x1, $y1, $x2, $y2) {
	
		return new awLine(
			new awPoint($x1, $y1),
			new awPoint($x2, $y2)
		);
	
	}
	
	/**
	 * Change X values of the line
	 *
	 * @param int $x1 Begin value
	 * @param int $x2 End value
	 */
	public function setX($x1, $x2) {
		$this->p1->setX($x1);
		$this->p2->setX($x2);
		
		// Resets slope and origin values so they are
		// recalculated when and if needed.
		$this->slope = NULL;
		$this->origin = NULL;
	}
	
	/**
	 * Change Y values of the line
	 *
	 * @param int $y1 Begin value
	 * @param int $y2 End value
	 */
	public function setY($y1, $y2) {
		$this->p1->setY($y1);
		$this->p2->setY($y2);
		
		// Resets slope and origin values so they are
		// recalculated when and if needed.
		$this->slope = NULL;
		$this->origin = NULL;
	}
	
	/**
	 * Change line location
	 *
	 * @param awPoint $p1 First point
	 * @param awPoint $p2 Second point
	 */
	public function setLocation($p1, $p2) {
		if(is_null($p1) or $p1 instanceof awPoint) {
			$this->p1 = $p1;
		}
		if(is_null($p2) or $p2 instanceof awPoint) {
			$this->p2 = $p2;
		}
		
		// Resets slope and origin values so they are
		// recalculated when and if needed.
		$this->slope = NULL;
		$this->origin = NULL;
	}
	
	/**
	 * Get line location
	 *
	 * @param array Line location
	 */
	public function getLocation() {
		return array($this->p1, $this->p2);
	}
	
	/**
	 * Get the line size
	 *
	 * @return float
	 */
	public function getSize() {
	
		$square = pow($this->p2->x - $this->p1->x, 2) + pow($this->p2->y - $this->p1->y, 2);
		return sqrt($square);
	
	}
	
	/**
	 * Calculate the line slope
	 * 
	 */
	private function calculateSlope() {
		if($this->isHorizontal()) {
			$this->slope = 0;
		} else {
			$slope = ($this->p1->y - $this->p2->y) / ($this->p1->x - $this->p2->x);
			
			$this->slope = $slope;
		}
	}
	
	/**
	 * Calculate the y-intercept value of the line
	 * 
	 */
	private function calculateOrigin() {
		if($this->isHorizontal()) {
			$this->origin = $this->p1->y; // Or p2->y
		} else {
			$y1 = $this->p1->y;
			$y2 = $this->p2->y;
			$x1 = $this->p1->x;
			$x2 = $this->p2->x;
			
			$origin = ($y2 * $x1 - $y1 * $x2) / ($x1 - $x2);
			
			$this->origin = $origin;
		}
	}
	
	/**
	 * Calculate the slope and y-intercept value of the line
	 * 
	 */
	private function calculateEquation() {
		$this->calculateSlope();
		$this->calculateOrigin();
	}
	
	/**
	 * Get the line slope value
	 *
	 * @return float
	 */
	public function getSlope() {
		if($this->isVertical()) {
			return NULL;
		} elseif($this->slope !== NULL) {
			return $this->slope;
		} else {
			$this->calculateSlope();
			return $this->slope;
		}
	}
	
	/**
	 * Get the line y-intercept value
	 * 
	 * @return float
	 */
	public function getOrigin() {
		if($this->isVertical()) {
			return NULL;
		} elseif($this->origin !== NULL) {
			return $this->origin;
		} else {
			$this->calculateOrigin();
			return $this->origin;
		}
	}
	
	/**
	 * Get the line equation
	 * 
	 * @return array An array containing the slope and y-intercept value of the line
	 */
	public function getEquation() {
		$slope	= $this->getSlope();
		$origin = $this->getOrigin();
		
		return array($slope, $origin);
	}
	
	/**
	 * Return the x coordinate of a point on the line
	 * given its y coordinate.
	 *
	 * @param float $y The y coordinate of the Point
	 * @return float $x The corresponding x coordinate
	 */
	public function getXFrom($y) {
		$x = NULL;
		
		if($this->isVertical()) {
			list($p, ) = $this->getLocation();
			$x = $p->x;
		} else {
			list($slope, $origin) = $this->getEquation();
			
			if($slope !== 0) {
				$y = (float)$y;
				$x = ($y - $origin) / $slope;
			}
		}
		
		return $x;
	}
	
	/**
	 * Return the y coordinate of a point on the line
	 * given its x coordinate.
	 *
	 * @param float $x The x coordinate of the Point
	 * @return float $y The corresponding y coordinate
	 */
	public function getYFrom($x) {
		$y = NULL;
		
		if($this->isHorizontal()) {
			list($p, ) = $this->getLocation();
			$y = $p->y;
		} else {
			list($slope, $origin) = $this->getEquation();
			
			if($slope !== NULL) {
				$x = (float)$x;
				$y = $slope * $x + $origin;
			}
		}
		
		return $y;
	}
	
	/**
	 * Test if the line can be considered as a point
	 *
	 * @return bool
	 */
	public function isPoint() {
		return ($this->p1->x === $this->p2->x and $this->p1->y === $this->p2->y);
	}
	
	/**
	 * Test if the line is a vertical line
	 *
	 * @return bool
	 */
	public function isVertical() {
		return ($this->p1->x === $this->p2->x);
	}
	
	/**
	 * Test if the line is an horizontal line
	 *
	 * @return bool
	 */
	public function isHorizontal() {
		return ($this->p1->y === $this->p2->y);
	}
	
	/**
	 * Returns TRUE if the line is going all the way from the top
	 * to the bottom of the polygon surrounding box.
	 * 
	 * @param $polygon Polygon A Polygon object
	 * @return bool
	 */
	public function isTopToBottom(awPolygon $polygon) {
		list($xMin, $xMax) = $polygon->getBoxXRange();
		list($yMin, $yMax) = $polygon->getBoxYRange();
		
		if($this->isHorizontal()) {
			return FALSE;
		} else {			
			if($this->p1->y < $this->p2->y) {
				$top = $this->p1;
				$bottom = $this->p2;
			} else {
				$top = $this->p2;
				$bottom = $this->p1;
			}
			
			return (
				$this->isOnBoxTopSide($top, $xMin, $xMax, $yMin)
				and
				$this->isOnBoxBottomSide($bottom, $xMin, $xMax, $yMax)
			);
		}
	}
	
	/**
	 * Returns TRUE if the line is going all the way from the left side
	 * to the right side of the polygon surrounding box.
	 * 
	 * @param $polygon Polygon A Polygon object
	 * @return bool
	 */
	public function isLeftToRight(awPolygon $polygon) {
		list($xMin, $xMax) = $polygon->getBoxXRange();
		list($yMin, $yMax) = $polygon->getBoxYRange();
		
		if($this->isVertical()) {
			return FALSE;
		} else {
			if($this->p1->x < $this->p2->x) {
				$left = $this->p1;
				$right = $this->p2;
			} else {
				$left = $this->p2;
				$right = $this->p1;
			}
		}
		
		return (
			$this->isOnBoxLeftSide($left, $yMin, $yMax, $xMin)
			and
			$this->isOnBoxRightSide($right, $yMin, $yMax, $xMax)
		);
	}
	
	private function isOnBoxTopSide(awPoint $point, $xMin, $xMax, $yMin) {
		if(
			$point->y === $yMin
			and 
			$point->x >= $xMin
			and
			$point->x <= $xMax
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	private function isOnBoxBottomSide(awPoint $point, $xMin, $xMax, $yMax) {
		if(
			$point->y === $yMax
			and
			$point->x >= $xMin
			and
			$point->x <= $xMax
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function isOnBoxLeftSide(awPoint $point, $yMin, $yMax, $xMin) {
		if(
			$point->x === $xMin
			and 
			$point->y >= $yMin
			and 
			$point->y <= $yMax
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function isOnBoxRightSide(awPoint $point, $yMin, $yMax, $xMax) {
		if(
			$point->x === $xMax
			and 
			$point->y >= $yMin
			and 
			$point->y <= $yMax
		) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
}

registerClass('Line');

/**
 * A vector is a type of line
 * The sense of the vector goes from $p1 to $p2.
 *
 * @package Artichow
 */
class awVector extends awLine {
	
	/**
	 * Get vector angle in radians
	 *
	 * @return float
	 */
	public function getAngle() {
	
		if($this->isPoint()) {
			return 0.0;
		}
		
		$size = $this->getSize();
	
		$width = ($this->p2->x - $this->p1->x);
		$height = ($this->p2->y - $this->p1->y) * -1;
		
		if($width >= 0 and $height >= 0) {
			return acos($width / $size);
		} else if($width <= 0 and $height >= 0) {
			return acos($width / $size);
		} else {
			$height *= -1;
			if($width >= 0 and $height >= 0) {
				return 2 * M_PI - acos($width / $size);
			} else if($width <= 0 and $height >= 0) {
				return 2 * M_PI - acos($width / $size);
			}
		}
	
	}

}

registerClass('Vector');
 

/**
 * Describe a polygon
 *
 * @package Artichow
 */
class awPolygon extends awShape {

	/**
	 * Polygon points
	 *
	 * @var array
	 */
	protected $points = array();

	/**
	 * Set a point in the polygon
	 *
	 * @param int $pos Point position
	 * @param awPoint $point
	 */
	public function set($pos, $point) {
		if(is_null($point) or $point instanceof awPoint) {
			$this->points[$pos] = $point;
		}
	}
	
	/**
	 * Add a point at the end of the polygon
	 *
	 * @param awPoint $point
	 */
	public function append($point) {
		if(is_null($point) or $point instanceof awPoint) {
			$this->points[] = $point;
		}
	}
	
	/**
	 * Get a point at a position in the polygon
	 *
	 * @param int $pos Point position
	 * @return Point
	 */
	public function get($pos) {
		return $this->points[$pos];
	}
	
	/**
	 * Count number of points in the polygon
	 *
	 * @return int
	 */
	public function count() {
		return count($this->points);
	}
	
	/**
	 * Returns all points in the polygon
	 *
	 * @return array
	 */
	public function all() {
		return $this->points;
	}
	
	/**
	 * Returns the different lines formed by the polygon vertices
	 * 
	 * @return array
	 */
	public function getLines() {
		$lines = array();
		$count = $this->count();
	
		for($i = 0; $i < $count - 1; $i++) {
			$lines[] = new Line($this->get($i), $this->get($i + 1));
		}
		
		// "Close" the polygon
		$lines[] = new Line($this->get($count - 1), $this->get(0));

		return $lines;
	}
	
	/**
	 * Get the upper-left and lower-right points
	 * of the bounding box around the polygon
	 * 
	 * @return array An array of two Point objects
	 */
	public function getBoxPoints() {
		$count = $this->count();
		$x = $y = array();
		
		for($i = 0; $i < $count; $i++) {
			$point = $this->get($i);
	
			list($x[], $y[]) = $point->getLocation();
		}
		
		$upperLeft  = new Point(min($x), min($y));
		$lowerRight = new Point(max($x), max($y));
		
		return array($upperLeft, $lowerRight);
	}
	
	/**
	 * Return the range of the polygon on the y axis,
	 * i.e. the minimum and maximum y value of any point in the polygon
	 * 
	 * @return array
	 */
	public function getBoxYRange() {
		list($p1, $p2) = $this->getBoxPoints();
	
		list(, $yMin) = $p1->getLocation();
		list(, $yMax) = $p2->getLocation();
		
		return array($yMin, $yMax);
	}
	
	/**
	 * Return the range of the polygon on the x axis,
	 * i.e. the minimum and maximum x value of any point in the polygon
	 *
	 * @return array
	 */
	public function getBoxXRange() {
		list($p1, $p2) = $this->getBoxPoints();
	
		list($xMin, ) = $p1->getLocation();
		list($xMax, ) = $p2->getLocation();
		
		return array($xMin, $xMax);
	}

}

registerClass('Polygon');
