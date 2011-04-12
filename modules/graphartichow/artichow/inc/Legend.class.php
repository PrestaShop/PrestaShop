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
 * Some legends
 *
 * @package Artichow
 */
class awLegend implements awPositionable {

	/**
	 * Legends added
	 *
	 * @var array
	 */
	protected $legends = array();

	/**
	 * The current component
	 *
	 * @var Component
	 */
	protected $component;
	
	/**
	 * Background color or gradient
	 *
	 * @var Color, Gradient
	 */
	protected $background;
	
	/**
	 * Text color
	 *
	 * @var Color
	 */
	protected $textColor;
	
	/**
	 * Text font
	 *
	 * @var Font
	 */
	protected $textFont;
	
	/**
	 * Text margin
	 *
	 * @var Side
	 */
	protected $textMargin;
	
	/**
	 * Number of columns
	 *
	 * @var int
	 */
	protected $columns = NULL;
	
	/**
	 * Number of rows
	 *
	 * @var int
	 */
	protected $rows = NULL;
	
	/**
	 * Legend position
	 *
	 * @var Point
	 */
	protected $position;
	
	/**
	 * Hide legend ?
	 *
	 * @var bool
	 */
	protected $hide = FALSE;
	
	/**
	 * Space between each legend
	 *
	 * @var int
	 */
	protected $space = 4;
	
	/**
	 * Horizontal alignment
	 *
	 * @var int
	 */
	protected $hAlign;
	
	/**
	 * Vertical alignment
	 *
	 * @var int
	 */
	protected $vAlign;

	/**
	 * Margin
	 *
	 * @var array Array for left, right, top and bottom margins
	 */
	private $margin;
	
	/**
	 * Legend shadow
	 *
	 * @var Shadow
	 */
	public $shadow;
	
	/**
	 * Legend border
	 *
	 * @var Border
	 */
	public $border;
	
	/**
	 * Line legend
	 *
	 * @var int
	 */
	const LINE = 1;
	
	/**
	 * Color/Gradient background legend
	 *
	 * @var int
	 */
	const BACKGROUND = 2;
	
	/**
	 * Use marks and line as legend
	 *
	 * @var int
	 */
	const MARK = 3;
	
	/**
	 * Use marks as legend
	 *
	 * @var int
	 */
	const MARKONLY = 4;
	
	/**
	 * Right side model
	 *
	 * @var int
	 */
	const MODEL_RIGHT = 1;
	
	/**
	 * Bottom side model
	 *
	 * @var int
	 */
	const MODEL_BOTTOM = 2;

	/**
	 * Build the legend
	 *
	 * @param int $model Legend model
	 */
	public function __construct($model = awLegend::MODEL_RIGHT) {
	
		$this->shadow = new awShadow(awShadow::LEFT_BOTTOM);
		$this->border = new awBorder;
		
		$this->textMargin = new awSide(4);
		$this->setModel($model);
		
	}
	
	/**
	 * Set a predefined model for the legend
	 *
	 * @param int $model
	 */
	public function setModel($model) {
		
		$this->setBackgroundColor(new awColor(255, 255, 255, 15));
		$this->setPadding(8, 8, 8, 8);
		$this->setTextFont(new awFont2);
		$this->shadow->setSize(3);
	
		switch($model) {
		
			case awLegend::MODEL_RIGHT :
			
				$this->setColumns(1);
				$this->setAlign(awLegend::RIGHT, awLegend::MIDDLE);
				$this->setPosition(0.96, 0.50);
			
				break;
		
			case awLegend::MODEL_BOTTOM :
			
				$this->setRows(1);
				$this->setAlign(awLegend::CENTER, awLegend::TOP);
				$this->setPosition(0.50, 0.92);
			
				break;
				
			default :
			
				$this->setPosition(0.5, 0.5);
				
				break;
		
		}
	
	}
	
	/**
	 * Hide legend ?
	 *
	 * @param bool $hide TRUE to hide legend, FALSE otherwise
	 */
	public function hide($hide = TRUE) {
		$this->hide = (bool)$hide;
	}
	
	/**
	 * Show legend ?
	 *
	 * @param bool $show
	 */
	public function show($show = TRUE) {
		$this->hide = (bool)!$show;
	}
	
	
	/**
	 * Add a Legendable object to the legend
	 *
	 * @param awLegendable $legendable
	 * @param string $title Legend title
	 * @param int $type Legend type (default to awLegend::LINE)
	 */
	public function add(awLegendable $legendable, $title, $type = awLegend::LINE) {
	
		$legend = array($legendable, $title, $type);
	
		$this->legends[] = $legend;
		
	}
	
	/**
	 * Change legend padding
	 *
	 * @param int $left
	 * @param int $right
	 * @param int $top
	 * @param int $bottom
	 */
	public function setPadding($left, $right, $top, $bottom) {
		$this->padding = array((int)$left, (int)$right, (int)$top, (int)$bottom);
	}
	
	/**
	 * Change space between each legend
	 *
	 * @param int $space
	 */
	public function setSpace($space) {
		$this->space = (int)$space;
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
	 * Change number of columns
	 *
	 * @param int $columns
	 */
	public function setColumns($columns) {
		$this->rows = NULL;
		$this->columns = (int)$columns;
	}
	
	/**
	 * Change number of rows
	 *
	 * @param int $rows
	 */
	public function setRows($rows) {
		$this->columns = NULL;
		$this->rows = (int)$rows;
	}
	
	/**
	 * Change legend position
	 * X and Y positions must be between 0 and 1.
	 *
	 * @param float $x
	 * @param float $y
	 */
	public function setPosition($x = NULL, $y = NULL) {
		$x = (is_null($x) and !is_null($this->position)) ? $this->position->x : $x;
		$y = (is_null($y) and !is_null($this->position)) ? $this->position->y : $y;
		
		$this->position = new awPoint($x, $y);
	}
	
	/**
	 * Get legend position
	 *
	 * @return Point
	 */
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 * Change text font
	 *
	 * @param awFont $font
	 */
	public function setTextFont(awFont $font) {
		$this->textFont = $font;
	}
	
	/**
	 * Change text margin
	 *
	 * @param int $left
	 * @param int $right
	 */
	public function setTextMargin($left, $right) {
		$this->textMargin->set($left, $right);
	}
	
	/**
	 * Change text color
	 *
	 * @param awColor $color
	 */
	public function setTextColor(awColor $color) {
		$this->textColor = $color;
	}
	
	/**
	 * Change background
	 *
	 * @param mixed $background
	 */
	public function setBackground($background) {
		$this->background = $background;
	}
	
	/**
	 * Change background color
	 *
	 * @param awColor $color
	 */
	public function setBackgroundColor(awColor $color) {
		$this->background = $color;
	}
	
	/**
	 * Change background gradient
	 *
	 * @param awGradient $gradient
	 */
	public function setBackgroundGradient(awGradient $gradient) {
		$this->background = $gradient;
	}
	
	/**
	 * Count the number of Legendable objects in the legend
	 *
	 * @return int
	 */
	public function count() {
		return count($this->legends);
	}
	
	public function draw(awDriver $driver) {
		
		if($this->hide) {
			return;
		}
	
		$count = $this->count();
		
		// No legend to print
		if($count === 0) {
			return;
		}
		
		// Get text widths and heights of each element of the legend
		$widths = array();
		$heights = array();
		$texts = array();
		for($i = 0; $i < $count; $i++) {
			list(, $title, ) = $this->legends[$i];
			$text = new awText(
				$title,
				$this->textFont,
				$this->textColor,
				0
			);
//			$font = $text->getFont();
			$widths[$i] = $driver->getTextWidth($text) + $this->textMargin->left + $this->textMargin->right;
			$heights[$i] = $driver->getTextHeight($text);
			$texts[$i] = $text;
		}
		
		// Maximum height of the font used
		$heightMax = array_max($heights);
		
		// Get number of columns
		if($this->columns !== NULL) {
			$columns = $this->columns;
		} else if($this->rows !== NULL) {
			$columns = ceil($count / $this->rows);
		} else {
			$columns = $count;
		}
		
		// Number of  rows
		$rows = (int)ceil($count / $columns);
		
		// Get maximum with of each column
		$widthMax = array();
		for($i = 0; $i < $count; $i++) {
			// Get column width
			$column = $i % $columns;
			if(array_key_exists($column, $widthMax) === FALSE) {
				$widthMax[$column] = $widths[$i];
			} else {
				$widthMax[$column] = max($widthMax[$column], $widths[$i]);
			}
		}
		
		$width = $this->padding[0] + $this->padding[1] - $this->space;
		for($i = 0; $i < $columns; $i++) {
			$width += $this->space + 5 + 10 + $widthMax[$i];
		}
		
		$height = ($heightMax + $this->space) * $rows - $this->space + $this->padding[2] + $this->padding[3];
		
		// Look for legends position
		list($x, $y) = $driver->getSize();
		
		$p = new awPoint(
			$this->position->x * $x,
			$this->position->y * $y
		);
		
		switch($this->hAlign) {
		
			case awLegend::CENTER :
				$p->x -= $width / 2;
				break;
		
			case awLegend::RIGHT :
				$p->x -= $width;
				break;
		
		}
		
		switch($this->vAlign) {
		
			case awLegend::MIDDLE :
				$p->y -= $height / 2;
				break;
		
			case awLegend::BOTTOM :
				$p->y -= $height;
				break;
		
		}
		
		// Draw legend shadow
		$this->shadow->draw(
			$driver,
			$p,
			$p->move($width, $height),
			awShadow::OUT
		);
		
		// Draw legends base
		$this->drawBase($driver, $p, $width, $height);
		
		// Draw each legend
		for($i = 0; $i < $count; $i++) {
		
			list($component, $title, $type) = $this->legends[$i];
		
			$column = $i % $columns;
			$row = (int)floor($i / $columns);
			
			// Get width of all previous columns
			$previousColumns = 0;
			for($j = 0; $j < $column; $j++) {
				$previousColumns += $this->space + 10 + 5 + $widthMax[$j];
			}
			
			// Draw legend text
			$driver->string(
				$texts[$i],
				$p->move(
					$this->padding[0] + $previousColumns + 10 + 5 + $this->textMargin->left,
					$this->padding[2] + $row * ($heightMax + $this->space) + $heightMax / 2 - $heights[$i] / 2
				)
			);
			
			// Draw legend icon
			switch($type) {
			
				case awLegend::LINE :
				case awLegend::MARK :
				case awLegend::MARKONLY :
				
					// Get vertical position
					$x = $this->padding[0] + $previousColumns;
					$y = $this->padding[2] + $row * ($heightMax + $this->space) + $heightMax / 2 - $component->getLegendLineThickness();
					
					// Draw two lines
					if($component->getLegendLineColor() !== NULL) {
					
						$color = $component->getLegendLineColor();
				
						if($color instanceof awColor and $type !== awLegend::MARKONLY) {
						
							$driver->line(
								$color,
								new awLine(
									$p->move(
										$x, // YaPB ??
										$y + $component->getLegendLineThickness() / 2
									),
									$p->move(
										$x + 10,
										$y + $component->getLegendLineThickness() / 2
									),
									$component->getLegendLineStyle(),
									$component->getLegendLineThickness()
								)
							);
						
							unset($color);
							
						}
						
					}
					
					if($type === awLegend::MARK or $type === awLegend::MARKONLY)  {
					
						$mark = $component->getLegendMark();
					
						if($mark !== NULL) {
							$mark->draw(
								$driver,
								$p->move(
									$x + 5.5,
									$y + $component->getLegendLineThickness() / 2
								)
							);
						}
						
						unset($mark);
					
					}
					
					break;
					
				case awLegend::BACKGROUND :
				
					// Get vertical position
					$x = $this->padding[0] + $previousColumns;
					$y = $this->padding[2] + $row * ($heightMax + $this->space) + $heightMax / 2 - 5;
					
					$from = $p->move(
						$x,
						$y
					);
					
					$to = $p->move(
						$x + 10,
						$y + 10
					);
					
					$background = $component->getLegendBackground();
					
					if($background !== NULL) {
				
						$driver->filledRectangle(
							$component->getLegendBackground(),
							new awLine($from, $to)
						);
			
						// Draw rectangle border
						$this->border->rectangle(
							$driver,
							$from->move(0, 0),
							$to->move(0, 0)
						);
						
					}
					
					unset($background, $from, $to);
				
					break;
			
			}
		
		}
	
	}
	
	private function drawBase(awDriver $driver, awPoint $p, $width, $height) {

		$this->border->rectangle(
			$driver,
			$p,
			$p->move($width, $height)
		);
		
		$size = $this->border->visible() ? 1 : 0;
		
		$driver->filledRectangle(
			$this->background,
			new awLine(
				$p->move($size, $size),
				$p->move($width - $size, $height - $size)
			)
		);
		
	}

}

registerClass('Legend');

/**
 * You can add a legend to components which implements this interface
 *
 * @package Artichow
 */
interface awLegendable {

	/**
	 * Get the line type
	 *
	 * @return int
	 */
	public function getLegendLineStyle();

	/**
	 * Get the line thickness
	 *
	 * @return int
	 */
	public function getLegendLineThickness();

	/**
	 * Get the color of line
	 *
	 * @return Color
	 */
	public function getLegendLineColor();

	/**
	 * Get the background color or gradient of an element of the component
	 *
	 * @return Color, Gradient
	 */
	public function getLegendBackground();

	/**
	 * Get a Mark object
	 *
	 * @return Mark
	 */
	public function getLegendMark();

}

registerInterface('Legendable');
