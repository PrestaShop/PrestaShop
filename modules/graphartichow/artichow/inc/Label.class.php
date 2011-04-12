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
 * Draw labels
 *
 * @package Artichow
 */
class awLabel implements awPositionable {

	/**
	 * Label border
	 *
	 * @var int
	 */
	public $border;

	/**
	 * Label texts
	 *
	 * @var array
	 */
	protected $texts;

	/**
	 * Text font
	 *
	 * @var int
	 */
	protected $font;

	/**
	 * Text angle
	 *
	 * @var int
	 */
	protected $angle = 0;

	/**
	 * Text color
	 *
	 * @var Color
	 */
	protected $color;

	/**
	 * Text background
	 *
	 * @var Color, Gradient
	 */
	private $background;

	/**
	 * Callback function
	 *
	 * @var string
	 */
	private $function;

	/**
	 * Padding
	 *
	 * @var int
	 */
	private $padding;

	/**
	 * Move position from this vector
	 *
	 * @var Point
	 */
	protected $move;

	/**
	 * Label interval
	 *
	 * @var int
	 */
	protected $interval = 1;

	/**
	 * Horizontal align
	 *
	 * @var int
	 */
	protected $hAlign = awLabel::CENTER;

	/**
	 * Vertical align
	 *
	 * @var int
	 */
	protected $vAlign = awLabel::MIDDLE;
	
	/**
	 * Hide all labels ?
	 *
	 * @var bool
	 */
	protected $hide = FALSE;
	
	/**
	 * Keys to hide
	 *
	 * @var array
	 */
	protected $hideKey = array();
	
	/**
	 * Values to hide
	 *
	 * @var array
	 */
	protected $hideValue = array();
	
	/**
	 * Hide first label
	 *
	 * @var bool
	 */
	protected $hideFirst = FALSE;
	
	/**
	 * Hide last label
	 *
	 * @var bool
	 */
	protected $hideLast = FALSE;
	
	/**
	 * Build the label
	 *
	 * @param string $label First label
	 */
	public function __construct($label = NULL, $font = NULL, $color = NULL, $angle = 0) {
	
		if(is_array($label)) {
			$this->set($label);
		} else if(is_string($label)) {
			$this->set(array($label));
		}
		
		if($font === NULL) {
			$font = new awFont2;
		}
		
		$this->setFont($font);
		$this->setAngle($angle);
		
		if($color instanceof awColor) {
			$this->setColor($color);
		} else {
			$this->setColor(new awColor(0, 0, 0));
		}
		
		$this->move = new awPoint(0, 0);
		
		$this->border = new awBorder;
		$this->border->hide();
		
	}
	
	/**
	 * Get an element of the label from its key
	 *
	 * @param int $key Element key
	 * @return string A value
	 */
	public function get($key) {
		return array_key_exists($key, $this->texts) ? $this->texts[$key] : NULL;
	}
	
	/**
	 * Get all labels
	 *
	 * @return array
	 */
	public function all() {
		return $this->texts;
	}
	
	/**
	 * Set one or several labels
	 *
	 * @param array $labels Array of string or a string
	 */
	public function set($labels) {
	
		if(is_array($labels)) {
			$this->texts = $labels;
		} else {
			$this->texts = array((string)$labels);
		}
		
	}
	
	/**
	 * Count number of texts in the label
	 *
	 * @return int
	 */
	public function count() {
		return is_array($this->texts) ? count($this->texts) : 0;
	}
	
	/**
	 * Set a callback function for labels
	 *
	 * @param string $function
	 */
	public function setCallbackFunction($function) {
		$this->function = is_null($function) ? $function : (string)$function;
	}
	
	/**
	 * Return the callback function for labels
	 *
	 * @return string
	 */
	public function getCallbackFunction() {
		return $this->function;
	}
	
	/**
	 * Change labels format
	 *
	 * @param string $format New format (printf style: %.2f for example)
	 */
	public function setFormat($format) {
		$function = 'label'.time().'_'.(microtime() * 1000000);
		eval('function '.$function.'($value) {
			return sprintf("'.addcslashes($format, '"').'", $value);
		}');
		$this->setCallbackFunction($function);
	}
	
	/**
	 * Change font for label
	 *
	 * @param awFont $font New font
	 * @param awColor $color Font color (can be NULL)
	 */
	public function setFont(awFont $font, $color = NULL) {
		$this->font = $font;
		if($color instanceof awColor) {
			$this->setColor($color);
		}
	}
	
	/**
	 * Change font angle
	 *
	 * @param int $angle New angle
	 */
	public function setAngle($angle) {
		$this->angle = (int)$angle;
	}
	
	/**
	 * Change font color
	 *
	 * @param awColor $color
	 */
	public function setColor(awColor $color) {
		$this->color = $color;
	}
	
	/**
	 * Change text background
	 *
	 * @param mixed $background
	 */
	public function setBackground($background) {
		$this->background = $background;
	}
	
	/**
	 * Change text background color
	 *
	 * @param Color
	 */
	public function setBackgroundColor(awColor $color) {
		$this->background = $color;
	}
	
	/**
	 * Change text background gradient
	 *
	 * @param Gradient
	 */
	public function setBackgroundGradient(awGradient $gradient) {
		$this->background = $gradient;
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
	 * Hide all labels ?
	 *
	 * @param bool $hide
	 */
	public function hide($hide = TRUE) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Show all labels ?
	 *
	 * @param bool $show
	 */
	public function show($show = TRUE) {
		$this->hide = (bool)!$show;
	}
	
	/**
	 * Hide a key
	 *
	 * @param int $key The key to hide
	 */
	public function hideKey($key) {
		$this->hideKey[$key] = TRUE;
	}
	
	/**
	 * Hide a value
	 *
	 * @param int $value The value to hide
	 */
	public function hideValue($value) {
		$this->hideValue[] = $value;
	}
	
	/**
	 * Hide first label
	 *
	 * @param bool $hide
	 */
	public function hideFirst($hide) {
		$this->hideFirst = (bool)$hide;
	}
	
	/**
	 * Hide last label
	 *
	 * @param bool $hide
	 */
	public function hideLast($hide) {
		$this->hideLast = (bool)$hide;
	}
	
	/**
	 * Set label interval
	 *
	 * @param int
	 */
	public function setInterval($interval) {
	
		$this->interval = (int)$interval;
		
	}
	
	/**
	 * Change label position
	 *
	 * @param int $x Add this interval to X coord
	 * @param int $y Add this interval to Y coord
	 */
	public function move($x, $y) {
	
		$this->move = $this->move->move($x, $y);
	
	}
	
	/**
	 * Change alignment
	 *
	 * @param int $h Horizontal alignment
	 * @param int $v Vertical alignment
	 */
	public function setAlign($h = NULL, $v = NULL) {
		if($h !== NULL) {
			$this->hAlign = (int)$h;
		}
		if($v !== NULL) {
			$this->vAlign = (int)$v;
		}
	}
	
	/**
	 * Get a text from the labele
	 *
	 * @param mixed $key Key in the array text
	 * @return Text
	 */
	public function getText($key) {
	
		if(is_array($this->texts) and array_key_exists($key, $this->texts)) {
		
			$value = $this->texts[$key];
			
			if(is_string($this->function)) {
				$value = call_user_func($this->function, $value);
			}
		
			$text = new awText($value);
			$text->setFont($this->font);
			$text->setAngle($this->angle);
			$text->setColor($this->color);
			
			if($this->background instanceof awColor) {
				$text->setBackgroundColor($this->background);
			} else if($this->background instanceof awGradient) {
				$text->setBackgroundGradient($this->background);
			}
			
			$text->border = $this->border;
			
			if($this->padding !== NULL) {
				call_user_func_array(array($text, 'setPadding'), $this->padding);
			}
			
			return $text;
			
		} else {
			return NULL;
		}
	
	}
	
	/**
	 * Get max width of all texts
	 *
	 * @param awDriver $driver A driver
	 * @return int
	 */
	public function getMaxWidth(awDriver $driver) {
	
		return $this->getMax($driver, 'getTextWidth');
	
	}
	
	/**
	 * Get max height of all texts
	 *
	 * @param awDriver $driver A driver
	 * @return int
	 */
	public function getMaxHeight(awDriver $driver) {
	
		return $this->getMax($driver, 'getTextHeight');
		
	}
	
	/**
	 * Draw the label
	 *
	 * @param awDriver $driver
	 * @param awPoint $p Label center
	 * @param int $key Text position in the array of texts (default to zero)
	 */
	public function draw(awDriver $driver, awPoint $p, $key = 0) {
	
		if(($key % $this->interval) !== 0) {
			return;
		}
	
		// Hide all labels
		if($this->hide) {
			return;
		}
		
		// Key is hidden
		if(array_key_exists($key, $this->hideKey)) {
			return;
		}
		
		// Hide first label
		if($key === 0 and $this->hideFirst) {
			return;
		}
		
		// Hide last label
		if($key === count($this->texts) - 1 and $this->hideLast) {
			return;
		}
	
		$text = $this->getText($key);
		
		if($text !== NULL) {
		
			// Value must be hidden
			if(in_array($text->getText(), $this->hideValue)) {
				return;
			}
		
			$x = $p->x;
			$y = $p->y;
			
			// Get padding
			list($left, $right, $top, $bottom) = $text->getPadding();
			
//			$font = $text->getFont();
			$width = $driver->getTextWidth($text);
			$height = $driver->getTextHeight($text);
			
			switch($this->hAlign) {
			
				case awLabel::RIGHT :
					$x -= ($width + $right);
					break;
			
				case awLabel::CENTER :
					$x -= ($width - $left + $right) / 2;
					break;
			
				case awLabel::LEFT :
					$x += $left;
					break;
			
			}
			
			switch($this->vAlign) {
			
				case awLabel::TOP :
					$y -= ($height + $bottom);
					break;
			
				case awLabel::MIDDLE :
					$y -= ($height - $top + $bottom) / 2;
					break;
			
				case awLabel::BOTTOM :
					$y += $top;
					break;
			
			}
		
			$driver->string($text, $this->move->move($x, $y));
			
		}
		
	}
	
	protected function getMax(awDriver $driver, $function) {
	
		$max = NULL;
	
		foreach($this->texts as $key => $text) {
		
			$text = $this->getText($key);
			$font = $text->getFont();
		
			if(is_null($max)) {
				$max = $font->{$function}($text);
			} else {
				$max = max($max, $font->{$function}($text));
			}
		
		}
		
		return $max;
		
	}

}

registerClass('Label');
