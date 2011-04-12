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
 * Handle ticks
 *
 * @package Artichow
 */
class awTick {

	/**
	 * Ticks style
	 *
	 * @var int
	 */
	protected $style = awTick::IN;

	/**
	 * Ticks size
	 *
	 * @var int
	 */
	protected $size;

	/**
	 * Ticks color
	 *
	 * @var Color
	 */
	protected $color;

	/**
	 * Ticks number
	 *
	 * @var int
	 */
	protected $number;

	/**
	 * Ticks number by other tick
	 *
	 * @var array
	 */
	protected $numberByTick;

	/**
	 * Ticks interval
	 *
	 * @var int
	 */
	protected $interval = 1;

	/**
	 * Hide ticks
	 *
	 * @var bool
	 */
	protected $hide = FALSE;

	/**
	 * Hide first tick
	 *
	 * @var bool
	 */
	protected $hideFirst = FALSE;

	/**
	 * Hide last tick
	 *
	 * @var bool
	 */
	protected $hideLast = FALSE;
	
	/**
	 * In mode
	 *
	 * @param int
	 */
	const IN = 0;
	
	/**
	 * Out mode
	 *
	 * @param int
	 */
	const OUT = 1;
	
	/**
	 * In and out mode
	 *
	 * @param int
	 */
	const IN_OUT = 2;
	
	/**
	 * Build the ticks
	 *
	 * @param int $number Number of ticks
	 * @param int $size Ticks size
	 */
	public function __construct($number, $size) {
		
		$this->setSize($size);
		$this->setNumber($number);
		$this->setColor(new awBlack);
		$this->style = awTick::IN;
	
	}
	
	/**
	 * Change ticks style
	 *
	 * @param int $style
	 */
	public function setStyle($style) {
		$this->style = (int)$style;
	}
	
	/**
	 * Get ticks style
	 *
	 * @return int
	 */
	public function getStyle() {
		return $this->style;
	}
	
	/**
	 * Change ticks color
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->color = $color;
	}
	
	/**
	 * Change ticks size
	 *
	 * @param int $size
	 */
	public function setSize($size) {
		$this->size = (int)$size;
	}
	
	/**
	 * Change interval of ticks
	 *
	 * @param int $interval
	 */
	public function setInterval($interval) {
		$this->interval = (int)$interval;
	}
	
	/**
	 * Get interval between each tick
	 *
	 * @return int
	 */
	public function getInterval() {
		return $this->interval;
	}
	
	/**
	 * Change number of ticks
	 *
	 * @param int $number
	 */
	public function setNumber($number) {
		$this->number = (int)$number;
	}
	
	/**
	 * Get number of ticks
	 *
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}
	
	/**
	 * Change number of ticks relative to others ticks
	 *
	 * @param awTick $tick Ticks reference
	 * @param int $number Number of ticks
	 */
	public function setNumberByTick(awTick $tick, $number) {
		
		$this->numberByTick = array($tick, (int)$number);
		
			}
	
	/**
	 * Hide ticks
	 *
	 * @param bool $hide
	 */
	public function hide($hide) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Hide first tick
	 *
	 * @param bool $hide
	 */
	public function hideFirst($hide) {
		$this->hideFirst = (bool)$hide;
	}
	
	/**
	 * Hide last tick
	 *
	 * @param bool $hide
	 */
	public function hideLast($hide) {
		$this->hideLast = (bool)$hide;
	}
	
	/**
	 * Draw ticks on a vector
	 *
	 * @param awDriver $driver A driver
	 * @param awVector $vector A vector
	 */
	public function draw(awDriver $driver, awVector $vector) {
		
		if($this->numberByTick !== NULL) {
			list($tick, $number) = $this->numberByTick;
			$this->number = 1 + ($tick->getNumber() - 1) * ($number + 1);
			$this->interval = $tick->getInterval();
		}
		
		if($this->number < 2 or $this->hide) {
			return;
		}
		
		$angle = $vector->getAngle();
	//	echo "INIT:".$angle."<br>";
		switch($this->style) {
		
			case awTick::IN :
				$this->drawTicks($driver, $vector, NULL, $angle + M_PI / 2);
				break;
		
			case awTick::OUT :
				$this->drawTicks($driver, $vector, $angle + 3 * M_PI / 2, NULL);
				break;
		
			default :
				$this->drawTicks($driver, $vector, $angle + M_PI / 2, $angle + 3 * M_PI / 2);
				break;
		
		}
	
	}
	
	protected function drawTicks(awDriver $driver, awVector $vector, $from, $to) {
	
		// Draw last tick
		if($this->hideLast === FALSE) {
		
			//echo '<b>';
			if(($this->number - 1) % $this->interval === 0) {
				$this->drawTick($driver, $vector->p2, $from, $to);
			}
			//echo '</b>';
			
		}
		
		$number = $this->number - 1;
		$size = $vector->getSize();
		
		// Get tick increment in pixels
		$inc = $size / $number;
		
		// Check if we must hide the first tick
		$start = $this->hideFirst ? $inc : 0;
		$stop = $inc * $number;
		
		$position = 0;
		
		for($i = $start; round($i, 6) < $stop; $i += $inc) {
		
			if($position % $this->interval === 0) {
				$p = $vector->p1->move(
					round($i * cos($vector->getAngle()), 6),
					round($i * sin($vector->getAngle() * -1), 6)
				);
				$this->drawTick($driver, $p, $from, $to);
			}
			
			$position++;
			
		}
		//echo '<br><br>';
	}
	
	protected function drawTick(awDriver $driver, awPoint $p, $from, $to) {
//	echo $this->size.':'.$angle.'|<b>'.cos($angle).'</b>/';
		// The round avoid some errors in the calcul
		// For example, 12.00000008575245 becomes 12
		$p1 = $p;
		$p2 = $p;
		
		if($from !== NULL) {
			$p1 = $p1->move(
				round($this->size * cos($from), 6),
				round($this->size * sin($from) * -1, 6)
			);
		}
		
		if($to !== NULL) {
			$p2 = $p2->move(
				round($this->size * cos($to), 6),
				round($this->size * sin($to) * -1, 6)
			);
		}
		//echo $p1->x.':'.$p2->x.'('.$p1->y.':'.$p2->y.')'.'/';
		$vector = new awVector(
			$p1, $p2
		);
		
		$driver->line(
			$this->color,
			$vector
		);
		
	}

}

registerClass('Tick');
