<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once dirname(__FILE__)."/Plot.class.php";
 

/**
 * LinePlot
 *
 * @package Artichow
 */
class awLinePlot extends awPlot implements awLegendable {
	
	/**
	 * Add marks to your line plot
	 *
	 * @var Mark
	 */
	public $mark;
	
	/**
	 * Labels on your line plot
	 *
	 * @var Label
	 */
	public $label;
	
	/**
	 * Filled areas
	 *
	 * @var bool
	 */
	protected $areas = array();
	
	/**
	 * Is the line hidden
	 *
	 * @var bool
	 */
	protected $lineHide = FALSE;
	
	/**
	 * Line color
	 *
	 * @var Color
	 */
	protected $lineColor;
	
	/**
	 * Line mode
	 *
	 * @var int
	 */
	protected $lineMode = awLinePlot::LINE;
	
	/**
	 * Line type
	 *
	 * @var int
	 */
	protected $lineStyle = awLine::SOLID;
	
	/**
	 * Line thickness
	 *
	 * @var int
	 */
	protected $lineThickness = 1;
	
	/**
	 * Line background
	 *
	 * @var Color, Gradient
	 */
	protected $lineBackground;
	
	/**
	 * Line mode
	 *
	 * @var int
	 */
	const LINE = 0;
	
	/**
	 * Line in the middle
	 *
	 * @var int
	 */
	const MIDDLE = 1;
	 	
	/**
	 * Construct a new awLinePlot
	 *
	 * @param array $values Some numeric values for Y axis
	 * @param int $mode
	 */
	public function __construct($values, $mode = awLinePlot::LINE) {
	
		parent::__construct();
		
		$this->mark = new awMark;
		$this->label = new awLabel;
		
		$this->lineMode = (int)$mode;
		
		$this->setValues($values);
	
	}
	
	/**
	 * Hide line
	 *
	 * @param bool $hide
	 */
	public function hideLine($hide) {
		$this->lineHide = (bool)$hide;
	}
	
	/**
	 * Add a filled area
	 *
	 * @param int $start Begining of the area
	 * @param int $end End of the area
	 * @param mixed $background Background color or gradient of the area
	 */
	public function setFilledArea($start, $stop, $background) {
	
		if($stop <= $start) {
			awImage::drawError("Class LinePlot: End position can not be greater than begin position in setFilledArea().");
		}
	
		$this->areas[] = array((int)$start, (int)$stop, $background);
	
	}
	
	/**
	 * Change line color
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->lineColor = $color;
	}
	
	/**
	 * Change line style
	 *
	 * @param int $style
	 */
	public function setStyle($style) {
		$this->lineStyle = (int)$style;
	}
	
	/**
	 * Change line tickness
	 *
	 * @param int $tickness
	 */
	public function setThickness($tickness) {
		$this->lineThickness = (int)$tickness;
	}
	
	/**
	 * Change line background color
	 *
	 * @param awColor $color
	 */
	public function setFillColor(awColor $color) {
		$this->lineBackground = $color;
	}
	
	/**
	 * Change line background gradient
	 *
	 * @param awGradient $gradient
	 */
	public function setFillGradient(awGradient $gradient) {
		$this->lineBackground = $gradient;
	}

	/**
	 * Get the line thickness
	 *
	 * @return int
	 */
	public function getLegendLineThickness() {
		return $this->lineThickness;
	}

	/**
	 * Get the line type
	 *
	 * @return int
	 */
	public function getLegendLineStyle() {
		return $this->lineStyle;
	}

	/**
	 * Get the color of line
	 *
	 * @return Color
	 */
	public function getLegendLineColor() {
		return $this->lineColor;
	}

	/**
	 * Get the background color or gradient of an element of the component
	 *
	 * @return Color, Gradient
	 */
	public function getLegendBackground() {
		return $this->lineBackground;
	}

	/**
	 * Get a mark object
	 *
	 * @return Mark
	 */
	public function getLegendMark() {
		return $this->mark;
	}
	
	public function drawComponent(awDriver $driver, $x1, $y1, $x2, $y2, $aliasing) {
		
		$max = $this->getRealYMax();
		$min = $this->getRealYMin();
		
		// Get start and stop values
		list($start, $stop) = $this->getLimit();
		
		if($this->lineMode === awLinePlot::MIDDLE) {
			$inc = $this->xAxis->getDistance(0, 1) / 2;
		} else {
			$inc = 0;
		}
		
		// Build the polygon
		$polygon = new awPolygon;
		
		for($key = $start; $key <= $stop; $key++) {
		
			$value = $this->datay[$key];
			
			if($value !== NULL) {
			
				$p = awAxis::toPosition($this->xAxis, $this->yAxis, new awPoint($key, $value));
				$p = $p->move($inc, 0);
				$polygon->set($key, $p);
				
			}
		
		}
		
		// Draw backgrounds
		if($this->lineBackground instanceof awColor or $this->lineBackground instanceof awGradient) {
		
			$backgroundPolygon = new awPolygon;
		
			$p = $this->xAxisPoint($start);
			$p = $p->move($inc, 0);
			$backgroundPolygon->append($p);
			
			// Add others points
			foreach($polygon->all() as $point) {
				$backgroundPolygon->append(clone $point);
			}
			
			$p = $this->xAxisPoint($stop);
			$p = $p->move($inc, 0);
			$backgroundPolygon->append($p);
		
			// Draw polygon background
			$driver->filledPolygon($this->lineBackground, $backgroundPolygon);
		
		}
		
		$this->drawArea($driver, $polygon);
		
		// Draw line
		$prev = NULL;
		
		// Line color
		if($this->lineHide === FALSE) {
		
			if($this->lineColor === NULL) {
				$this->lineColor = new awColor(0, 0, 0);
			}
			
			foreach($polygon->all() as $point) {
			
				if($prev !== NULL) {
					$driver->line(
						$this->lineColor,
						new awLine(
							$prev,
							$point,
							$this->lineStyle,
							$this->lineThickness
						)
					);
				}
				$prev = $point;
				
			}

		}
		
		// Draw marks and labels
		foreach($polygon->all() as $key => $point) {

			$this->mark->draw($driver, $point);
			$this->label->draw($driver, $point, $key);
			
		}
		
	}
	
	protected function drawArea(awDriver $driver, awPolygon $polygon) {
	
		$starts = array();
		foreach($this->areas as $area) {
			list($start) = $area;
			$starts[$start] = TRUE;
		}
		
		// Draw filled areas
		foreach($this->areas as $area) {
		
			list($start, $stop, $background) = $area;
			
			$polygonArea = new awPolygon;
			
			$p = $this->xAxisPoint($start);
			$polygonArea->append($p);
			
			for($i = $start; $i <= $stop; $i++) {
				$p = clone $polygon->get($i);
				if($i === $stop and array_key_exists($stop, $starts)) {
					$p = $p->move(-1, 0);
				}
				$polygonArea->append($p);
			}
			
			$p = $this->xAxisPoint($stop);
			if(array_key_exists($stop, $starts)) {
				$p = $p->move(-1, 0);
			}
			$polygonArea->append($p);
		
			// Draw area
			$driver->filledPolygon($background, $polygonArea);
		
		}
		
	}
	
	public function getXAxisNumber() {
		if($this->lineMode === awLinePlot::MIDDLE) {
			return count($this->datay) + 1;
		} else {
			return count($this->datay);
		}
	}
	
	protected function xAxisPoint($position) {
		$y = $this->xAxisZero ? 0 : $this->getRealYMin();
		return awAxis::toPosition($this->xAxis, $this->yAxis, new awPoint($position, $y));
	}
	
	public function getXCenter() {
		return ($this->lineMode === awLinePlot::MIDDLE);
	}

}

registerClass('LinePlot');


/**
 * Simple LinePlot
 * Useful to draw simple horizontal lines
 *
 * @package Artichow
 */
class awSimpleLinePlot extends awPlot implements awLegendable {
	
	/**
	 * Line color
	 *
	 * @var Color
	 */
	protected $lineColor;
	
	/**
	 * Line start
	 *
	 * @var int
	 */
	protected $lineStart;
	
	/**
	 * Line stop
	 *
	 * @var int
	 */
	protected $lineStop;
	
	/**
	 * Line value
	 *
	 * @var flaot
	 */
	protected $lineValue;
	
	/**
	 * Line mode
	 *
	 * @var int
	 */
	protected $lineMode = awLinePlot::LINE;
	
	/**
	 * Line type
	 *
	 * @var int
	 */
	protected $lineStyle = awLine::SOLID;
	
	/**
	 * Line thickness
	 *
	 * @var int
	 */
	protected $lineThickness = 1;
	
	/**
	 * Line mode
	 *
	 * @var int
	 */
	const LINE = 0;
	
	/**
	 * Line in the middle
	 *
	 * @var int
	 */
	const MIDDLE = 1;
	 	
	/**
	 * Construct a new awLinePlot
	 *
	 * @param float $value A Y value
	 * @param int $start Line start index
	 * @param int $stop Line stop index
	 * @param int $mode Line mode
	 */
	public function __construct($value, $start, $stop, $mode = awLinePlot::LINE) {
	
		parent::__construct();
		
		$this->lineMode = (int)$mode;
		
		$this->lineStart = (int)$start;
		$this->lineStop = (int)$stop;
		$this->lineValue = (float)$value;
		
		$this->lineColor = new awColor(0, 0, 0);
	
	}
	
	/**
	 * Change line color
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->lineColor = $color;
	}
	
	/**
	 * Change line style
	 *
	 * @param int $style
	 */
	public function setStyle($style) {
		$this->lineStyle = (int)$style;
	}
	
	/**
	 * Change line tickness
	 *
	 * @param int $tickness
	 */
	public function setThickness($tickness) {
		$this->lineThickness = (int)$tickness;
	}

	/**
	 * Get the line thickness
	 *
	 * @return int
	 */
	public function getLegendLineThickness() {
		return $this->lineThickness;
	}

	/**
	 * Get the line type
	 *
	 * @return int
	 */
	public function getLegendLineStyle() {
		return $this->lineStyle;
	}

	/**
	 * Get the color of line
	 *
	 * @return Color
	 */
	public function getLegendLineColor() {
		return $this->lineColor;
	}

	public function getLegendBackground() {
		return NULL;
	}

	public function getLegendMark() {
		return NULL;
	}
	
	public function drawComponent(awDriver $driver, $x1, $y1, $x2, $y2, $aliasing) {
		
		if($this->lineMode === awLinePlot::MIDDLE) {
			$inc = $this->xAxis->getDistance(0, 1) / 2;
		} else {
			$inc = 0;
		}
		
		$p1 = awAxis::toPosition($this->xAxis, $this->yAxis, new awPoint($this->lineStart, $this->lineValue));
		$p2 = awAxis::toPosition($this->xAxis, $this->yAxis, new awPoint($this->lineStop, $this->lineValue));
		
		$driver->line(
			$this->lineColor,
			new awLine(
				$p1->move($inc, 0),
				$p2->move($inc, 0),
				$this->lineStyle,
				$this->lineThickness
			)
		);
}
	
	public function getXAxisNumber() {
		if($this->lineMode === awLinePlot::MIDDLE) {
			return count($this->datay) + 1;
		} else {
			return count($this->datay);
		}
	}
	
	protected function xAxisPoint($position) {
		$y = $this->xAxisZero ? 0 : $this->getRealYMin();
		return awAxis::toPosition($this->xAxis, $this->yAxis, new awPoint($position, $y));
	}
	
	public function getXCenter() {
		return ($this->lineMode === awLinePlot::MIDDLE);
	}

}

registerClass('SimpleLinePlot');

