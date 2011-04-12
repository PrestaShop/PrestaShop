<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once dirname(__FILE__)."/Component.class.php";
 
 
/**
 * Pie
 *
 * @package Artichow
 */
class awPie extends awComponent {

	/**
	 * A dark theme for pies
	 *
	 *
	 * @var int
	 */
	const DARK = 1;

	/**
	 * A colored theme for pies
	 *
	 * @var int
	 */
	const COLORED = 2;

	/**
	 * A water theme for pies
	 *
	 * @var int
	 */
	const AQUA = 3;

	/**
	 * A earth theme for pies
	 *
	 * @var int
	 */
	const EARTH = 4;
	
	/**
	 * Pie values
	 *
	 * @var array
	 */
	protected $values;
	
	/**
	 * Pie colors
	 *
	 * @var array
	 */
	protected $colors;
	
	/**
	 * Pie legend
	 * 
	 * @var array
	 */
	protected $legendValues = array();
	
	/**
	 * Intensity of the 3D effect
	 *
	 * @var int
	 */
	protected $size;
	
	/**
	 * Border color
	 *
	 * @var Color
	 */
	protected $border;
	
	/**
	 * Pie explode
	 *
	 * @var array
	 */
	protected $explode = array();
	
	/**
	 * Initial angle
	 *
	 * @var int
	 */
	protected $angle = 0;
	
	/**
	 * Labels precision
	 *
	 * @var int
	 */
	protected $precision;
	
	/**
	 * Labels number
	 *
	 * @var int
	 */
	protected $number;
	
	/**
	 * Labels minimum
	 *
	 * @var int
	 */
	protected $minimum;
	
	/**
	 * Labels position
	 *
	 * @var int
	 */
	protected $position = 15;
	
	/**
	 * Labels of your pie
	 *
	 * @var Label
	 */
	public $label;
	
	/**
	 * Build the plot
	 *
	 * @param array $values Pie values
	 */
	public function __construct($values, $colors = awPie::COLORED) {
		
		$this->setValues($values);
		
		if(is_array($colors)) {
			$this->colors = $colors;
		} else {
		
			switch($colors) {
			
				case awPie::AQUA :
					$this->colors = array(
						new awColor(131, 220, 215),
						new awColor(131, 190, 215),
						new awColor(131, 160, 215),
						new awColor(160, 140, 215),
						new awColor(190, 131, 215),
						new awColor(220, 131, 215)
					);
					break;
			
				case awPie::EARTH :
					$this->colors = array(
						new awColor(97, 179, 110),
						new awColor(130, 179, 97),
						new awColor(168, 179, 97),
						new awColor(179, 147, 97),
						new awColor(179, 108, 97),
						new awColor(99, 107, 189),
						new awColor(99, 165, 189)
					);
					break;
			
				case awPie::DARK :
					$this->colors = array(
						new awColor(140, 100, 170),
						new awColor(130, 170, 100),
						new awColor(160, 160, 120),
						new awColor(150, 110, 140),
						new awColor(130, 150, 160),
						new awColor(90, 170, 140)
					);
					break;
					
				default :
					$this->colors = array(
						new awColor(187, 213, 151),
						new awColor(223, 177, 151),
						new awColor(111, 186, 132),
						new awColor(197, 160, 230),
						new awColor(165, 169, 63),
						new awColor(218, 177, 89),
						new awColor(116, 205, 121),
						new awColor(200, 201, 78),
						new awColor(127, 205, 177),
						new awColor(205, 160, 160),
						new awColor(190, 190, 190)
					);
					break;
			
			}
		
		}
	
		parent::__construct();
		
		$this->label = new awLabel;
		$this->label->setCallbackFunction('callbackPerCent');
		
	}
	
	/**
	 * Change legend values
	 *
	 * @param array $legend An array of values for each part of the pie
	 */
	public function setLegend($legend) {
	
		$this->legendValues = (array)$legend;
	
	}
	
	/**
	 * Set a border all around the pie
	 *
	 * @param awColor $color A color for the border
	 */
	public function setBorderColor(awColor $color) {
		$this->border = $color;
	}
	
	/**
	 * Set a border all around the pie
	 *
	 * @param awColor $color A color for the border
	 */
	public function setBorder(awColor $color) {
		if(ARTICHOW_DEPRECATED === TRUE) {
			awImage::drawError('Class Pie: Method setBorder() has been deprecated since Artichow 1.0.9. Please use setBorderColor() instead.');
		} else {
			$this->setBorderColor($color);
		}
	}
	
	/**
	 * Change 3D effect intensity
	 *
	 * @param int $size Effect size
	 */
	public function set3D($size) {
		$this->size = (int)$size;
	}
	
	/**
	 * Change initial angle
	 *
	 * @param int $angle New angle in degrees
	 */
	public function setStartAngle($angle) {
		$this->angle = (int)$angle;
	}
	
	/**
	 * Change label precision
	 *
	 * @param int $precision New precision
	 */
	public function setLabelPrecision($precision) {
		$this->precision = (int)$precision;
	}
	
	/**
	 * Change label position
	 *
	 * @param int $position New position in pixels
	 */
	public function setLabelPosition($position) {
		$this->position = (int)$position;
	}
	
	/**
	 * Change label number
	 *
	 * @param int $number New number
	 */
	public function setLabelNumber($number) {
		$this->number = is_null($number) ? $number : (int)$number;
	}
	
	/**
	 * Change label minimum
	 *
	 * @param int $minimum New minimum
	 */
	public function setLabelMinimum($minimum) {
		$this->minimum = is_null($minimum) ? $minimum : (int)$minimum;
	}
	
	/**
	 * Change Pie explode
	 *
	 * @param array $explode
	 */
	public function explode($explode) {
		$this->explode = (array)$explode;
	}
	
	public function drawEnvelope(awDriver $driver) {
	
	}
	
	public function drawComponent(awDriver $driver, $x1, $y1, $x2, $y2, $aliasing) {
		
		$count = count($this->values);
		$sum = array_sum($this->values);
		
		$width = $x2 - $x1;
		$height = $y2 - $y1;
		
		if($aliasing) {
			$x = $width / 2;
			$y = $height / 2;
		} else {
			$x = $width / 2 + $x1;
			$y = $height / 2 + $y1;
		}
		
		$position = $this->angle;
		$values = array();
		$parts = array();
		$angles = 0;
		
		if($aliasing) {
			$side = new awSide(0, 0, 0, 0);
		}
		
		foreach($this->values as $key => $value) {
		
			$angle = ($value / $sum * 360);
			
			if($key === $count - 1) {
				$angle = 360 - $angles;
			}
			
			$angles += $angle;
			
			if(array_key_exists($key, $this->explode)) {
				$middle = 360 - ($position + $angle / 2);
				$posX = $this->explode[$key] * cos($middle * M_PI / 180);
				$posY = $this->explode[$key] * sin($middle * M_PI / 180) * -1;
				
				if($aliasing) {
					$explode = new awPoint(
						$posX * 2,
						$posY * 2
					);
					$side->set(
						max($side->left, $posX * -2),
						max($side->right, $posX * 2),
						max($side->top, $posY * -2),
						max($side->bottom, $posY * 2)
					);
				} else {
					$explode = new awPoint(
						$posX,
						$posY
					);
				}
				
			} else {
				$explode = new awPoint(0, 0);
			}
			
			$values[$key] = array(
				$position, ($position + $angle), $explode
			);
			
			$color = $this->colors[$key % count($this->colors)];
			$parts[$key] = new awPiePart($color);
			
			// Add part to the legend
			$legend = array_key_exists($key, $this->legendValues) ? $this->legendValues[$key] : $key;
			$this->legend->add($parts[$key], $legend, awLegend::BACKGROUND);
			
			$position += $angle;
		
		}
		
		if($aliasing) {
		
			$mainDriver = $driver;
			
			$x *= 2;
			$y *= 2;
			$width *= 2;
			$height *= 2;
			$this->size *= 2;
			
			$image = new awImage;
			$image->border->hide();
			
			// Adds support for antialiased pies on non-white background
			$background = $this->getBackground();
			
			if($background instanceof awColor) {
				$image->setBackgroundColor($background);
			}
//			elseif($background instanceof awGradient) {
//				$image->setBackgroundColor(new White(100));
//			}
			
			$image->setSize(
				$width + $side->left + $side->right,
				$height + $side->top + $side->bottom + $this->size + 1 /* bugs.php.net ! */
			);
			
			$driver = $image->getDriver(
				$width / $image->width,
				$height / $image->height,
				($width / 2 + $side->left) / $image->width,
				($height / 2 + $side->top) / $image->height
			);
			
		}
		
		// Draw 3D effect
		for($i = $this->size; $i > 0; $i--) {
		
			foreach($values as $key => $value) {
			
				$color = clone $this->colors[$key % count($this->colors)];
				$color->brightness(-50);
				
				list($from, $to, $explode) = $value;
				
				$driver->filledArc($color, $explode->move($x, $y + $i), $width, $height, $from, $to);
				
				unset($color);
				
				if($this->border instanceof awColor) {
				
					$point = $explode->move($x, $y);
					
					if($i === $this->size) {
				
						$driver->arc($this->border, $point->move(0, $this->size), $width, $height, $from, $to);
						
					}
				
				}
			
			}
			
		}
		
		foreach($values as $key => $value) {
			
			$color = $this->colors[$key % count($this->colors)];
			
			list($from, $to, $explode) = $value;
			
			$driver->filledArc($color, $explode->move($x, $y), $width, $height, $from, $to);
			
			if($this->border instanceof awColor) {
			
				$point = $explode->move($x, $y);
				$driver->arc($this->border, $point, $width, $height, $from, $to);
			}
		
		}
		
		if($aliasing) {
		
			$x = $x / 2 + $x1;
			$y = $y / 2 + $y1;
			$width /= 2;
			$height /= 2;
			$this->size /= 2;
			
			foreach($values as $key => $value) {
				$old = $values[$key][2];
				$values[$key][2] = new awPoint(
					$old->x / 2, $old->y / 2
				);
			}
			
			$mainDriver->copyResizeImage(
				$image,
				new awPoint($x1 - $side->left / 2, $y1 - $side->top / 2),
				new awPoint($x1 - $side->left / 2 + $image->width / 2, $y1 - $side->top / 2 + $image->height/ 2),
				new awPoint(0, 0),
				new awPoint($image->width, $image->height),
				TRUE
			);
			
			$driver = $mainDriver;
		
		}
		
		// Get labels values
		$pc = array();
		foreach($this->values as $key => $value) {
			$pc[$key] = round($value / $sum * 100, $this->precision);
		}
		if($this->label->count() === 0) { // Check that there is no user defined values
			$this->label->set($pc);
		}
		
		$position = 0;
		
		foreach($pc as $key => $value) {
		
			// Limit number of labels to display
			if($position === $this->number) {
				break;
			}
			
			if(is_null($this->minimum) === FALSE and $value < $this->minimum) {
				continue;
			}
			
			$position++;
			
			list($from, $to, $explode) = $values[$key];
			
			$angle = $from + ($to - $from) / 2;
			$angleRad = (360 - $angle) * M_PI / 180;
			
			$point = new awPoint(
				$x + $explode->x + cos($angleRad) * ($width / 2 + $this->position),
				$y + $explode->y - sin($angleRad) * ($height / 2 + $this->position)
			);
			
			$angle %= 360;
			
			// We don't display labels on the 3D effect
			if($angle > 0 and $angle < 180) {
				$point = $point->move(0, -1 * sin($angleRad) * $this->size);
			}
			
			if($angle >= 45 and $angle < 135) {
				$this->label->setAlign(awLabel::CENTER, awLabel::BOTTOM);
			} else if($angle >= 135 and $angle < 225) {
				$this->label->setAlign(awLabel::RIGHT, awLabel::MIDDLE);
			} else if($angle >= 225 and $angle < 315) {
				$this->label->setAlign(awLabel::CENTER, awLabel::TOP);
			} else {
				$this->label->setAlign(awLabel::LEFT, awLabel::MIDDLE);
			}
			
			$this->label->draw(
				$driver,
				$point,
				$key
			);
			
		}
		
	}
	
	/**
	 * Return margins around the component
	 *
	 * @return array Left, right, top and bottom margins
	 */
	public function getMargin() {
		
		// Get axis informations
		
		$leftAxis = $this->padding->left;
		$rightAxis = $this->padding->right;
		$topAxis = $this->padding->top;
		$bottomAxis = $this->padding->bottom;
		
		return array($leftAxis, $rightAxis, $topAxis, $bottomAxis);
		
	}
	
	
	/**
	 * Change values of Y axis
	 * This method ignores not numeric values
	 *
	 * @param array $values
	 */
	public function setValues($values) {
	
		$this->checkArray($values);
		$this->values = $values;
		
	}
	
	
	/**
	 * Return values of Y axis
	 *
	 * @return array
	 */
	public function getValues() {
		return $this->values;
	}
	
	private function checkArray(&$array) {
	
		if(is_array($array) === FALSE) {
			awImage::drawError("Class Pie: You tried to set values that are not an array.");
		}
		
		foreach($array as $key => $value) {
			if(is_numeric($value) === FALSE) {
				unset($array[$key]);
			}
		}
		
		if(count($array) < 1) {
			awImage::drawError("Class Pie: Your graph must have at least 1 value.");
		}
	
	}

}

registerClass('Pie');

/**
 * Pie
 *
 * @package Artichow
 */
class awPiePart implements awLegendable {

	/**
	 * Pie part color
	 *
	 * @var Color
	 */
	protected $color;

	/**
	 * Build a new awPiePart
	 *
	 * @param awColor $color Pie part color
	 */
	public function __construct(awColor $color) {
	
		$this->color = $color;
	
	}

	/**
	 * Get the background color or gradient of an element of the component
	 *
	 * @return Color, Gradient
	 */
	public function getLegendBackground() {
		return $this->color;
	}

	/**
	 * Get the line thickness
	 *
	 * @return NULL
	 */
	public function getLegendLineThickness() {
	}

	/**
	 * Get the line type
	 *
	 * @return NULL
	 */
	public function getLegendLineStyle() {
	}

	/**
	 * Get the color of line
	 *
	 * @return NULL
	 */
	public function getLegendLineColor() {
	}

	/**
	 * Get a mark object
	 *
	 * @return NULL
	 */
	public function getLegendMark() {
	}

}

registerClass('PiePart');

function callbackPerCent($value) {
	return $value.'%';
}
