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
 * Handle axis
 *
 * @package Artichow
 */
class awAxis {

	/**
	 * Axis line
	 *
	 * @var Line
	 */
	public $line;

	/**
	 * Axis labels
	 *
	 * @var Label
	 */
	public $label;

	/**
	 * Axis title
	 *
	 * @var Label
	 */
	public $title;

	/**
	 * Title position
	 *
	 * @var float
	 */
	protected $titlePosition = 0.5;

	/**
	 * Labels number
	 *
	 * @var int
	 */
	protected $labelNumber;

	/**
	 * Axis ticks
	 *
	 * @var array
	 */
	protected $ticks = array();

	/**
	 * Axis and ticks color
	 *
	 * @var Color
	 */
	protected $color;

	/**
	 * Axis left and right padding
	 *
	 * @var Side
	 */
	protected $padding;

	/**
	 * Axis range
	 *
	 * @var array
	 */
	protected $range;

	/**
	 * Hide axis
	 *
	 * @var bool
	 */
	protected $hide = FALSE;

	/**
	 * Auto-scaling mode
	 *
	 * @var bool
	 */
	protected $auto = TRUE;

	/**
	 * Axis range callback function
	 *
	 * @var array
	 */
	protected $rangeCallback = array(
		'toValue' => 'toProportionalValue',
		'toPosition' => 'toProportionalPosition'
	);

	/**
	 * Build the axis
	 *
	 * @param float $min Begin of the range of the axis
	 * @param float $max End of the range of the axis
	 */
	public function __construct($min = NULL, $max = NULL) {

		$this->line = new awVector(
			new awPoint(0, 0),
			new awPoint(0, 0)
		);

		$this->label = new awLabel;
		$this->padding = new awSide;

		$this->title = new awLabel(
			NULL,
			NULL,
			NULL,
			0
		);

		$this->setColor(new awBlack);

		if($min !== NULL and $max !== NULL) {
			$this->setRange($min, $max);
		}

	}

	/**
	 * Enable/disable auto-scaling mode
	 *
	 * @param bool $auto
	 */
	public function auto($auto) {
		$this->auto = (bool)$auto;
	}

	/**
	 * Get auto-scaling mode status
	 *
	 * @return bool
	 */
	public function isAuto() {
		return $this->auto;
	}

	/**
	 * Hide axis
	 *
	 * @param bool $hide
	 */
	public function hide($hide = TRUE) {
		$this->hide = (bool)$hide;
	}

	/**
	 * Show axis
	 *
	 * @param bool $show
	 */
	public function show($show = TRUE) {
		$this->hide = !(bool)$show;
	}

	/**
	 * Return a tick object from its name
	 *
	 * @param string $name Tick object name
	 * @return Tick
	 */
	public function tick($name) {
		
		return array_key_exists($name, $this->ticks) ? $this->ticks[$name] : NULL;
		
			}

	/**
	 * Add a tick object
	 *
	 * @param string $name Tick object name
	 * @param awTick $tick Tick object
	 */
	public function addTick($name, awTick $tick) {
		
		$this->ticks[$name] = $tick;
		
			}

	/**
	 * Delete a tick object
	 *
	 * @param string $name Tick object name
	 */
	public function deleteTick($name) {
		if(array_key_exists($name, $this->ticks)) {
			unset($this->ticks[$name]);
		}
	}

	/**
	 * Hide all ticks
	 *
	 * @param bool $hide Hide or not ?
	 */
	public function hideTicks($hide = TRUE) {
		
		foreach($this->ticks as $tick) {
			$tick->hide($hide);
		}
		
			}

	/**
	 * Change ticks style
	 *
	 * @param int $style Ticks style
	 */
	public function setTickStyle($style) {
		
		foreach($this->ticks as $tick) {
			$tick->setStyle($style);
		}
		
			}

	/**
	 * Change ticks interval
	 *
	 * @param int $interval Ticks interval
	 */
	public function setTickInterval($interval) {
		
		foreach($this->ticks as $tick) {
			$tick->setInterval($interval);
		}
		
			}

	/**
	 * Change number of ticks relative to others ticks
	 *
	 * @param awTick $to Change number of theses ticks
	 * @param awTick $from Ticks reference
	 * @param float $number Number of ticks by the reference
	 */
	public function setNumberByTick($to, $from, $number) {
		$this->ticks[$to]->setNumberByTick($this->ticks[$from], $number);
	}

	/**
	 * Reverse ticks style
	 */
	public function reverseTickStyle() {
		
		foreach($this->ticks as $tick) {
			if($tick->getStyle() === awTick::IN) {
				$tick->setStyle(awTick::OUT);
			} else if($tick->getStyle() === awTick::OUT) {
				$tick->setStyle(awTick::IN);
			}
		}
		
			}

	/**
	 * Change interval of labels
	 *
	 * @param int $interval Interval
	 */
	public function setLabelInterval($interval) {
		$this->auto(FALSE);
		$this->setTickInterval($interval);
		$this->label->setInterval($interval);
	}

	/**
	 * Change number of labels
	 *
	 * @param int $number Number of labels to display (can be NULL)
	 */
	public function setLabelNumber($number) {
		$this->auto(FALSE);
		$this->labelNumber = is_null($number) ? NULL : (int)$number;
	}

	/**
	 * Get number of labels
	 *
	 * @return int
	 */
	public function getLabelNumber() {
		return $this->labelNumber;
	}

	/**
	 * Change precision of labels
	 *
	 * @param int $precision Precision
	 */
	public function setLabelPrecision($precision) {
		$this->auto(FALSE);
		$function = 'axis'.time().'_'.(microtime() * 1000000);
		eval('function '.$function.'($value) {
			return sprintf("%.'.(int)$precision.'f", $value);
		}');
		$this->label->setCallbackFunction($function);
	}

	/**
	 * Change text of labels
	 *
	 * @param array $texts Some texts
	 */
	public function setLabelText($texts) {
		if(is_array($texts)) {
			$this->auto(FALSE);
			$function = 'axis'.time().'_'.(microtime() * 1000000);
			eval('function '.$function.'($value) {
				$texts = '.var_export($texts, TRUE).';
				return isset($texts[$value]) ? $texts[$value] : \'?\';
			}');
			$this->label->setCallbackFunction($function);
		}
	}

	/**
	 * Get the position of a point
	 *
	 * @param awAxis $xAxis X axis
	 * @param awAxis $yAxis Y axis
	 * @param awPoint $p Position of the point
	 * @return Point Position on the axis
	 */
	public static function toPosition(awAxis $xAxis, awAxis $yAxis, awPoint $p) {

		$p1 = $xAxis->getPointFromValue($p->x);
		$p2 = $yAxis->getPointFromValue($p->y);

		return new awPoint(
			round($p1->x),
			round($p2->y)
		);

	}

	/**
	 * Change title alignment
	 *
	 * @param int $alignment New Alignment
	 */
	public function setTitleAlignment($alignment) {

		switch($alignment) {

			case awLabel::TOP :
				$this->setTitlePosition(1);
				$this->title->setAlign(NULL, awLabel::BOTTOM);
				break;

			case awLabel::BOTTOM :
				$this->setTitlePosition(0);
				$this->title->setAlign(NULL, awLabel::TOP);
				break;

			case awLabel::LEFT :
				$this->setTitlePosition(0);
				$this->title->setAlign(awLabel::LEFT);
				break;

			case awLabel::RIGHT :
				$this->setTitlePosition(1);
				$this->title->setAlign(awLabel::RIGHT);
				break;

		}

	}

	/**
	 * Change title position on the axis
	 *
	 * @param float $position A new awposition between 0 and 1
	 */
	public function setTitlePosition($position) {
		$this->titlePosition = (float)$position;
	}

	/**
	 * Change axis and axis title color
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->color = $color;
		$this->title->setColor($color);
	}

	/**
	 * Change axis padding
	 *
	 * @param int $left Left padding in pixels
	 * @param int $right Right padding in pixels
	 */
	public function setPadding($left, $right) {
		$this->padding->set($left, $right);
	}

	/**
	 * Get axis padding
	 *
	 * @return Side
	 */
	public function getPadding() {
		return $this->padding;
	}

	/**
	 * Change axis range
	 *
	 * @param float $min
	 * @param float $max
	 */
	public function setRange($min, $max) {
		if($min !== NULL) {
			$this->range[0] = (float)$min;
		}
		if($max !== NULL) {
			$this->range[1] = (float)$max;
		}
	}

	/**
	 * Get axis range
	 *
	 * @return array
	 */
	public function getRange() {
		return $this->range;
	}

	/**
	 * Change axis range callback function
	 *
	 * @param string $toValue Transform a position between 0 and 1 to a value
	 * @param string $toPosition Transform a value to a position between 0 and 1 on the axis
	 */
	public function setRangeCallback($toValue, $toPosition) {
		$this->rangeCallback = array(
			'toValue' => (string)$toValue,
			'toPosition' => (string)$toPosition
		);
	}

	/**
	 * Center X values of the axis
	 *
	 * @param awAxis $axis An axis
	 * @param float $value The reference value on the axis
	 */
	public function setXCenter(awAxis $axis, $value) {

		// Check vector angle
		if($this->line->isVertical() === FALSE) {
			awImage::drawError("Class Axis: setXCenter() can only be used on vertical axes.");
		}

		$p = $axis->getPointFromValue($value);

		$this->line->setX(
			$p->x,
			$p->x
		);

	}

	/**
	 * Center Y values of the axis
	 *
	 * @param awAxis $axis An axis
	 * @param float $value The reference value on the axis
	 */
	public function setYCenter(awAxis $axis, $value) {

		// Check vector angle
		if($this->line->isHorizontal() === FALSE) {
			awImage::drawError("Class Axis: setYCenter() can only be used on horizontal axes.");
		}

		$p = $axis->getPointFromValue($value);

		$this->line->setY(
			$p->y,
			$p->y
		);

	}

	/**
	 * Get the distance between to values on the axis
	 *
	 * @param float $from The first value
	 * @param float $to The last value
	 * @return Point
	 */
	public function getDistance($from, $to) {

		$p1 = $this->getPointFromValue($from);
		$p2 = $this->getPointFromValue($to);

		return $p1->getDistance($p2);

	}

	/**
	 * Get a point on the axis from a value
	 *
	 * @param float $value
	 * @return Point
	 */
	protected function getPointFromValue($value) {

		$callback = $this->rangeCallback['toPosition'];

		list($min, $max) = $this->range;
		$position = $callback($value, $min, $max);

		return $this->getPointFromPosition($position);

	}

	/**
	 * Get a point on the axis from a position
	 *
	 * @param float $position A position between 0 and 1
	 * @return Point
	 */
	protected function getPointFromPosition($position) {

		$vector = $this->getVector();

		$angle = $vector->getAngle();
		$size = $vector->getSize();

		return $vector->p1->move(
			cos($angle) * $size * $position,
			-1 * sin($angle) * $size * $position
		);

	}

	/**
	 * Draw axis
	 *
	 * @param awDriver $driver A driver
	 */
	public function draw(awDriver $driver) {

		if($this->hide) {
			return;
		}

		$vector = $this->getVector();

		// Draw axis ticks
		$this->drawTicks($driver, $vector);

		// Draw axis line
		$this->line($driver);

		// Draw labels
		$this->drawLabels($driver);

		// Draw axis title
		$p = $this->getPointFromPosition($this->titlePosition);
		$this->title->draw($driver, $p);

	}

	public function autoScale() {

		if($this->isAuto() === FALSE) {
			return;
		}

		list($min, $max) = $this->getRange();
		$interval = $max - $min;

		if($interval > 0) {
			$partMax = $max / $interval;
			$partMin = $min / $interval;
		} else {
			$partMax = 0;
			$partMin = 0;
		}

		$difference = log($interval) / log(10);
		$difference = floor($difference);

		$pow = pow(10, $difference);

		if($pow > 0) {
			$intervalNormalize = $interval / $pow;
		} else {
			$intervalNormalize = 0;
		}

		if($difference <= 0) {

			$precision = $difference * -1 + 1;

			if($intervalNormalize > 2) {
				$precision--;
			}

		} else {
			$precision = 0;
		}

		if($min != 0 and $max != 0) {
			$precision++;
		}

		if($this->label->getCallbackFunction() === NULL) {
			$this->setLabelPrecision($precision);
		}

		if($intervalNormalize <= 1.5) {
			$intervalReal = 1.5;
			$labelNumber = 4;
		} else if($intervalNormalize <= 2) {
			$intervalReal = 2;
			$labelNumber = 5;
		} else if($intervalNormalize <= 3) {
			$intervalReal = 3;
			$labelNumber = 4;
		} else if($intervalNormalize <= 4) {
			$intervalReal = 4;
			$labelNumber = 5;
		} else if($intervalNormalize <= 5) {
			$intervalReal = 5;
			$labelNumber = 6;
		} else if($intervalNormalize <= 8) {
			$intervalReal = 8;
			$labelNumber = 5;
		} else if($intervalNormalize <= 10) {
			$intervalReal = 10;
			$labelNumber = 6;
		}

		if($min == 0) {

			$this->setRange(
				$min,
				$intervalReal * $pow
			);

		} else if($max == 0) {

			$this->setRange(
				$intervalReal * $pow * -1,
				0
			);

		}

		$this->setLabelNumber($labelNumber);

	}

	protected function line(awDriver $driver) {

		$driver->line(
			$this->color,
			$this->line
		);

	}

	protected function drawTicks(awDriver $driver, awVector $vector) {

		foreach($this->ticks as $tick) {
			$tick->setColor($this->color);
			$tick->draw($driver, $vector);
		}

	}

	protected function drawLabels($driver) {

		if($this->labelNumber !== NULL) {
			list($min, $max) = $this->range;
			$number = $this->labelNumber - 1;
			if($number < 1) {
				return;
			}
			$function = $this->rangeCallback['toValue'];
			$labels = array();
			for($i = 0; $i <= $number; $i++) {
				$labels[] = $function($i / $number, $min, $max);
			}
			$this->label->set($labels);
		}

		$labels = $this->label->count();

		for($i = 0; $i < $labels; $i++) {

			$p = $this->getPointFromValue($this->label->get($i));
			$this->label->draw($driver, $p, $i);

		}

	}

	protected function getVector() {

		$angle = $this->line->getAngle();

		// Compute paddings
		$vector = new awVector(
			$this->line->p1->move(
				cos($angle) * $this->padding->left,
				-1 * sin($angle) * $this->padding->left
			),
			$this->line->p2->move(
				-1 * cos($angle) * $this->padding->right,
				-1 * -1 * sin($angle) * $this->padding->right
			)
		);

		return $vector;

	}

	public function __clone() {

		$this->label = clone $this->label;
		$this->line = clone $this->line;
		$this->title = clone $this->title;

		foreach($this->ticks as $name => $tick) {
			$this->ticks[$name] = clone $tick;
		}

	}

}

registerClass('Axis');

function toProportionalValue($position, $min, $max) {
	return $min + ($max - $min) * $position;
}

function toProportionalPosition($value, $min, $max) {
	if($max - $min == 0) {
		return 0;
	}
	return ($value - $min) / ($max - $min);
}
