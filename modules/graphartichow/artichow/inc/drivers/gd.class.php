<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Driver.class.php";

/**
 * Draw your objects
 *
 * @package Artichow
 */

class awGDDriver extends Driver {
	
	/**
	 * A GD resource
	 *
	 * @var $resource
	 */
	public $resource;
	
	public function __construct() {
		parent::__construct();
		
		$this->driverString = 'gd';
	}

	public function init(awImage $image) {
		
		if($this->resource === NULL) {
			
			$this->setImageSize($image->width, $image->height);
			
			// Create image
			$this->resource = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
			if(!$this->resource) {
				awImage::drawError("Class Image: Unable to create a graph.");
			}
			
			imagealphablending($this->resource, TRUE);
			
			// Antialiasing is now handled by the Driver object
			$this->setAntiAliasing($image->getAntiAliasing());
			
			// Original color
			$this->filledRectangle(
				new awWhite,
				new awLine(
					new awPoint(0, 0),
					new awPoint($this->imageWidth, $this->imageHeight)
				)
			);
			
			$shadow = $image->shadow;
			if($shadow !== NULL) {
				$shadow = $shadow->getSpace();
				$p1 = new awPoint($shadow->left, $shadow->top);
				$p2 = new awPoint($this->imageWidth - $shadow->right - 1, $this->imageHeight - $shadow->bottom - 1);
				
				
				// Draw image background
				$this->filledRectangle($image->getBackground(), new awLine($p1, $p2));
				
				// Draw image border
				$image->border->rectangle($this, $p1, $p2);
			}
			
		}
	}
	
	public function initFromFile(awFileImage $fileImage, $file) {
		
		$image = @getimagesize((string)$file);
		
		if($image and in_array($image[2], array(2, 3))) {
		
			$fileImage->setSize($image[0], $image[1]);
			
			switch($image[2]) {
			
				case 2 :
					$this->resource = imagecreatefromjpeg($file);
					break;
			
				case 3 :
					$this->resource = imagecreatefrompng($file);
					break;
			
			}

			$this->setImageSize($fileImage->width, $fileImage->height);
		} else {
			awImage::drawError("Class FileImage: Artichow does not support the format of this image (must be in PNG or JPEG)");
		}
	}
	
	public function setImageSize($width, $height) {
	
		$this->imageWidth = $width;
		$this->imageHeight = $height;
	
	}
	
	public function setPosition($x, $y) {
		
		// Calculate absolute position
		$this->x = round($x * $this->imageWidth - $this->w / 2);
		$this->y = round($y * $this->imageHeight - $this->h / 2);
	
	}
	
	public function setAbsPosition($x, $y) {
		
		$this->x = $x;
		$this->y = $y;
	
	}
	
	public function movePosition($x, $y) {

		$this->x += (int)$x;
		$this->y += (int)$y;
	
	}
	
	public function setSize($w, $h) {
	
		// Calcul absolute size
		$this->w = round($w * $this->imageWidth);
		$this->h = round($h * $this->imageHeight);
		
		return $this->getSize();
	
	}
	
	public function setAbsSize($w, $h) {
	
		$this->w = $w;
		$this->h = $h;
		
		return $this->getSize();
	
	}
	
	public function getSize() {
		
		return array($this->w, $this->h);
	
	}
	
	public function setAntiAliasing($bool) {
		
		if(function_exists('imageantialias')) {
			imageantialias($this->resource, (bool)$bool);

			$this->antiAliasing = (bool)$bool;
		} elseif ($bool == true) {
			awImage::drawErrorFile('missing-anti-aliasing');
		}
	}
	
	public function getColor(awColor $color) {

		if($color->alpha === 0 or function_exists('imagecolorallocatealpha') === FALSE) {
			$gdColor = imagecolorallocate($this->resource, $color->red, $color->green, $color->blue);
		} else {
			$gdColor = imagecolorallocatealpha($this->resource, $color->red, $color->green, $color->blue, $color->alpha);
		}

		return $gdColor;
	}
	
	public function copyImage(awImage $image, awPoint $p1, awPoint $p2) {
	
		list($x1, $y1) = $p1->getLocation();
		list($x2, $y2) = $p2->getLocation();
	
		$driver = $image->getDriver();
		imagecopy($this->resource, $driver->resource, $this->x + $x1, $this->y + $y1, 0, 0, $x2 - $x1, $y2 - $y1);
	
	}
	
	public function copyResizeImage(awImage $image, awPoint $d1, awPoint $d2, awPoint $s1, awPoint $s2, $resample = TRUE) {
		
		if($resample) {
			$function = 'imagecopyresampled';
		} else {
			$function = 'imagecopyresized';
		}
		
		$driver = $image->getDriver();
	
		$function(
			$this->resource,
			$driver->resource,
			$this->x + $d1->x, $this->y + $d1->y,
			$s1->x, $s1->y,
			$d2->x - $d1->x, $d2->y - $d1->y,
			$s2->x - $s1->x, $s2->y - $s1->y
		);
	
	}
	
	public function string(awText $text, awPoint $point, $width = NULL) {
		
		$font = $text->getFont();
		
		// Can we deal with that font?
		if($this->isCompatibleWithFont($font) === FALSE) {
			awImage::drawError('Class GDDriver: Incompatible font type (\''.get_class($font).'\')');
		}
		
		// Check which FontDriver to use
		if($font instanceof awPHPFont) {
			$fontDriver = $this->phpFontDriver;
		} else {
			$fontDriver = $this->fileFontDriver;
		}
		
		if($text->getBackground() !== NULL or $text->border->visible()) {
		
			list($left, $right, $top, $bottom) = $text->getPadding();

			$textWidth = $fontDriver->getTextWidth($text, $this);
			$textHeight = $fontDriver->getTextHeight($text, $this);
			
			$x1 = floor($point->x - $left);
			$y1 = floor($point->y - $top);
			$x2 = $x1 + $textWidth + $left + $right;
			$y2 = $y1 + $textHeight + $top + $bottom;
			
			$this->filledRectangle(
				$text->getBackground(),
				awLine::build($x1, $y1, $x2, $y2)
			);
			
			$text->border->rectangle(
				$this,
				new awPoint($x1 - 1, $y1 - 1),
				new awPoint($x2 + 1, $y2 + 1)
			);
			
		}
		
		$fontDriver->string($this, $text, $point, $width);
		
	}
	
	public function point(awColor $color, awPoint $p) {
	
		if($p->isHidden() === FALSE) {
			$rgb = $this->getColor($color);
			imagesetpixel($this->resource, $this->x + round($p->x), $this->y + round($p->y), $rgb);
		}
	
	}
	
	public function line(awColor $color, awLine $line) {
	
		if($line->thickness > 0 and $line->isHidden() === FALSE) {
	
			$rgb = $this->getColor($color);
			$thickness = $line->thickness;
			
			list($p1, $p2) = $line->getLocation();
			
			$this->startThickness($thickness);
			
			switch($line->getStyle()) {
			
				case awLine::SOLID :
					imageline($this->resource, $this->x + round($p1->x), $this->y + round($p1->y), $this->x + round($p2->x), $this->y + round($p2->y), $rgb);
					break;
					
				case awLine::DOTTED :
					$size = sqrt(pow($p2->y - $p1->y, 2) + pow($p2->x - $p1->x, 2));
					$cos = ($p2->x - $p1->x) / $size;
					$sin = ($p2->y - $p1->y) / $size;
					for($i = 0; $i <= $size; $i += 2) {
						$p = new awPoint(
							round($i * $cos + $p1->x),
							round($i * $sin + $p1->y)
						);
						$this->point($color, $p);
					}
					break;
					
				case awLine::DASHED :
					$width = $p2->x - $p1->x;
					$height = $p2->y - $p1->y;
					$size = sqrt(pow($height, 2) + pow($width, 2));
					
					if($size == 0) {
						return;
					}
					
					$cos = $width / $size;
					$sin = $height / $size;
					
					$functionX = ($width  > 0) ? 'min' : 'max';
					$functionY = ($height > 0) ? 'min' : 'max';
					
					for($i = 0; $i <= $size; $i += 6) {
						
						$t1 = new awPoint(
							round($i * $cos + $p1->x),
							round($i * $sin + $p1->y)
						);
						
						$t2 = new awPoint(
							round($functionX(($i + 3) * $cos, $width) + $p1->x),
							round($functionY(($i + 3) * $sin, $height) + $p1->y)
						);
						
						$this->line($color, new awLine($t1, $t2));
						
					}
					break;
			
			}
			
			$this->stopThickness($thickness);
			
		}
		
	}
	
	public function arc(awColor $color, awPoint $center, $width, $height, $from, $to) {
	
		imagefilledarc(
			$this->resource,
			$this->x + $center->x, $this->y + $center->y,
			$width, $height,
			$from, $to,
			$this->getColor($color),
			IMG_ARC_EDGED | IMG_ARC_NOFILL
		);
	
	}
	
	public function filledArc(awColor $color, awPoint $center, $width, $height, $from, $to) {
	
		imagefilledarc(
			$this->resource,
			$this->x + $center->x, $this->y + $center->y,
			$width, $height,
			$from, $to,
			$this->getColor($color),
			IMG_ARC_PIE
		);
	
	}
	
	public function ellipse(awColor $color, awPoint $center, $width, $height) {
	
		list($x, $y) = $center->getLocation();
	
		$rgb = $this->getColor($color);
		imageellipse(
			$this->resource,
			$this->x + $x,
			$this->y + $y,
			$width,
			$height,
			$rgb
		);
		
	}
	
	public function filledEllipse($background, awPoint $center, $width, $height) {
	
		if($background instanceof awColor) {
	
			list($x, $y) = $center->getLocation();
		
			$rgb = $this->getColor($background);
			
			imagefilledellipse(
				$this->resource,
				$this->x + $x,
				$this->y + $y,
				$width,
				$height,
				$rgb
			);
			
		} else if($background instanceof awGradient) {
	
			list($x, $y) = $center->getLocation();
			
			$x1 = $x - round($width / 2);
			$y1 = $y - round($height / 2);
			$x2 = $x1 + $width;
			$y2 = $y1 + $height;
		
			$gradientDriver = new awGDGradientDriver($this);
			$gradientDriver->filledEllipse(
				$background,
				$x1, $y1,
				$x2, $y2
			);
		
		}
		
	}
	
	public function rectangle(awColor $color, awLine $line) {
	
		list($p1, $p2) = $line->getLocation();
		
		switch($line->getStyle()) {
		
			case awLine::SOLID :
				$thickness = $line->getThickness();
				$this->startThickness($thickness);
				$rgb = $this->getColor($color);
				imagerectangle($this->resource, $this->x + $p1->x, $this->y + $p1->y, $this->x + $p2->x, $this->y + $p2->y, $rgb);
				$this->stopThickness($thickness);
				break;
			
			default :
				
				$side = clone $line;
				
				
								
				// Top side
				$side->setLocation(
					new awPoint($p1->x, $p1->y),
					new awPoint($p2->x, $p1->y)
				);
				$this->line($color, $side);
				
				// Right side
				$side->setLocation(
					new awPoint($p2->x, $p1->y),
					new awPoint($p2->x, $p2->y)
				);
				$this->line($color, $side);
				
				// Bottom side
				$side->setLocation(
					new awPoint($p1->x, $p2->y),
					new awPoint($p2->x, $p2->y)
				);
				$this->line($color, $side);
				
				// Left side
				$side->setLocation(
					new awPoint($p1->x, $p1->y),
					new awPoint($p1->x, $p2->y)
				);
				$this->line($color, $side);
			
				break;
		
		}
	
	}
	
	public function filledRectangle($background, awLine $line) {
	
		$p1 = $line->p1;
		$p2 = $line->p2;
	
		if($background instanceof awColor) {
			$rgb = $this->getColor($background);
			imagefilledrectangle($this->resource, $this->x + $p1->x, $this->y + $p1->y, $this->x + $p2->x, $this->y + $p2->y, $rgb);
		} else if($background instanceof awGradient) {
			$gradientDriver = new awGDGradientDriver($this);
			$gradientDriver->filledRectangle($background, $p1, $p2);
		}
	
	}
	
	public function polygon(awColor $color, awPolygon $polygon) {
		
		switch($polygon->getStyle()) {
		
			case awPolygon::SOLID :
				$thickness = $polygon->getThickness();
				$this->startThickness($thickness);
				$points = $this->getPolygonPoints($polygon);
				$rgb = $this->getColor($color);
				imagepolygon($this->resource, $points, $polygon->count(), $rgb);
				$this->stopThickness($thickness);
				break;
				
			default :
			
				if($polygon->count() > 1) {
				
					$prev = $polygon->get(0);
					
					$line = new awLine;
					$line->setStyle($polygon->getStyle());
					$line->setThickness($polygon->getThickness());
					
					for($i = 1; $i < $polygon->count(); $i++) {
						$current = $polygon->get($i);
						$line->setLocation($prev, $current);
						$this->line($color, $line);
						$prev = $current;
					}
					
					// Close the polygon
					$line->setLocation($prev, $polygon->get(0));
					$this->line($color, $line);
					
				}
		
		}
		
	}
	
	public function filledPolygon($background, awPolygon $polygon) {
		
		if($background instanceof awColor) {
			
			$points = $this->getPolygonPoints($polygon);
			$rgb = $this->getColor($background);
			
			imagefilledpolygon($this->resource, $points, $polygon->count(), $rgb);
			
		} else if($background instanceof awGradient) {
			
			$gradientDriver = new awGDGradientDriver($this);
			$gradientDriver->filledPolygon($background, $polygon);
			
		}

	}

	public function send(awImage $image) {

		$this->drawImage($image);

	}
	
	public function get(awImage $image) {
		
		return $this->drawImage($image, TRUE, FALSE);
		
	}
	
	public function getTextWidth(awText $text) {
		$font = $text->getFont();
		
		if($font instanceof awPHPFont) {
			$fontDriver = $this->phpFontDriver;
		} else {
			$fontDriver = $this->fileFontDriver;
		}
		
		return $fontDriver->getTextWidth($text, $this);
	}
	
	public function getTextHeight(awText $text) {
		$font = $text->getFont();
		
		if($font instanceof awPHPFont) {
			$fontDriver = $this->phpFontDriver;
		} else {
			$fontDriver = $this->fileFontDriver;
		}
		
		return $fontDriver->getTextHeight($text, $this);
	}
	
	protected function isCompatibleWithFont(awFont $font) {
		if($font instanceof awFDBFont) {
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	private function drawImage(awImage $image, $return = FALSE, $header = TRUE) {
		
		$format = $image->getFormatString();
		
		// Test if format is available
		if((imagetypes() & $image->getFormat()) === FALSE) {
			awImage::drawError("Class Image: Format '".$format."' is not available on your system. Check that your PHP has been compiled with the good libraries.");
		}
	
		// Get some infos about this image
		switch($format) {
			case 'jpeg' :
				$function = 'imagejpeg';
				break;
			case 'png' :
				$function = 'imagepng';
				break;
			case 'gif' :
				$function = 'imagegif';
				break;
		}
		
		// Send headers to the browser
		if($header === TRUE) {
			$image->sendHeaders();
		}
		
		if($return) {
			ob_start();
		}
		
		$function($this->resource);
		
		if($return) {
			return ob_get_clean();
		}
	}
	
	private function getPolygonPoints(awPolygon $polygon) {
		
		$points = array();
		
		foreach($polygon->all() as $point) {
			$points[] = $point->x + $this->x;
			$points[] = $point->y + $this->y;
		}
		
		return $points;
		
	}
	
	private function startThickness($thickness) {
		
		if($thickness > 1) {
		
			// Beurk :'(
			if($this->antiAliasing and function_exists('imageantialias')) {
				imageantialias($this->resource, FALSE);
			}
			imagesetthickness($this->resource, $thickness);
			
		}
		
	}
	
	private function stopThickness($thickness) {
		
		if($thickness > 1) {
		
			if($this->antiAliasing and function_exists('imageantialias')) {
				imageantialias($this->resource, TRUE);
			}
			imagesetthickness($this->resource, 1);
			
		}
		
	}
	

}

registerClass('GDDriver');

/**
 * To your gradients
 *
 * @package Artichow
 */

class awGDGradientDriver {

	/**
	 * A driver
	 *
	 * @var awGDDriver
	 */
	protected $driver;

	/**
	 * Build your GDGradientDriver
	 *
	 * @var awGDDriver $driver 
	 */
	public function __construct(awGDDriver $driver) {
	
		$this->driver = $driver;
		
	}
	
	public function drawFilledFlatTriangle(awGradient $gradient, awPoint $a, awPoint $b, awPoint $c) {
	
		if($gradient->angle !== 0) {
			awImage::drawError("Class GDGradientDriver: Flat triangles can only be used with 0 degree gradients.");
		}
	
		// Look for right-angled triangle
		if($a->x !== $b->x and $b->x !== $c->x) {
			awImage::drawError("Class GDGradientDriver: Not right-angled flat triangles are not supported yet.");
		}
		
		if($a->x === $b->x) {
			$d = $a;
			$e = $c;
		} else {
			$d = $c;
			$e = $a;
		}
		
		$this->init($gradient, $b->y - $d->y);
	
		for($i = $c->y + 1; $i < $b->y; $i++) {
			
			$color = $this->color($i - $d->y);
			$pos = ($i - $d->y) / ($b->y - $d->y);
			
			$p1 = new awPoint($e->x, $i);
			$p2 = new awPoint(1 + floor($e->x - $pos * ($e->x - $d->x)), $i);
			
			$this->driver->filledRectangle($color, new awLine($p1, $p2));
			
			unset($color);
			
		}
	
	}
	
	protected function drawFilledTriangle(awGradient $gradient, awPolygon $polygon) {
		
		if($gradient->angle === 0) {
			$this->drawFilledTriangleVertically($gradient, $polygon);
		} elseif($gradient->angle === 90) {
			$this->drawFilledTriangleHorizontally($gradient, $polygon);
		}
		
	}
	
	private function drawFilledTriangleVertically(awGradient $gradient, awPolygon $polygon) {
		list($yMin, $yMax) = $polygon->getBoxYRange();
		
		$this->init($gradient, $yMax - $yMin);
		
		// Get the triangle line we will draw our lines from
		$fromLine = NULL;
		$lines = $polygon->getLines();
				
		$count = count($lines);
					
		// Pick the side of the triangle going from the top
		// to the bottom of the surrounding box
		for($i = 0; $i < $count; $i++) {
			if($lines[$i]->isTopToBottom($polygon)) {
				list($fromLine) = array_splice($lines, $i, 1);
				break;
			}
		}
		
		// If for some reason the three points are aligned,
		// $fromLine will still be NULL
		if($fromLine === NULL) {
			return;
		}
						
		$fillLine = NULL;
		for($y = round($yMin); $y < round($yMax); $y++) {
			
			$fromX = $fromLine->getXFrom($y);
			
			$toX = array();
			foreach($lines as $line) {
				$xValue = $line->getXFrom($y);
				
				if(!is_null($xValue)) {
					$toX[] = $xValue;
				}
			}
			
			if(count($toX) === 1) {
				$fillLine = new Line(
					new Point($fromX, $y),
					new Point($toX[0], $y)
				);
			} else {
			
				$line1 = new Line(
					new Point($fromX, $y),
					new Point($toX[0], $y)
				);
				$line2 = new  Line(
					new Point($fromX, $y),
					new Point($toX[1], $y)
				);
			
				if($line1->getSize() < $line2->getSize()) {
					$fillLine = $line1;
				} else {
					$fillLine = $line2;
				}
			}
			
			if(!$fillLine->isPoint()) {
				$color = $this->color($y - $yMin);
				$this->driver->line($color, $fillLine);
				
				unset($color);
			}
		}
	
	}
	
	private function drawFilledTriangleHorizontally(awGradient $gradient, awPolygon $polygon) {
		list($xMin, $xMax) = $polygon->getBoxXRange();
		
		$this->init($gradient, $xMax - $xMin);
		
		// Get the triangle line we will draw our lines from
		$fromLine = NULL;
		$lines = $polygon->getLines();
				
		$count = count($lines);
					
		// Pick the side of the triangle going all the way
		// from the left side to the right side of the surrounding box
		for($i = 0; $i < $count; $i++) {
			if($lines[$i]->isLeftToRight($polygon)) {
				list($fromLine) = array_splice($lines, $i, 1);
				break;
			}
		}
		
		// If for some reason the three points are aligned,
		// $fromLine will still be NULL
		if($fromLine === NULL) {
			return;
		}

		$fillLine = NULL;
		for($x = round($xMin); $x < round($xMax); $x++) {
			
			$fromY = floor($fromLine->getYFrom($x));
			
			$toY = array();
			foreach($lines as $line) {
				$yValue = $line->getYFrom($x);
				
				if(!is_null($yValue)) {
					$toY[] = floor($yValue);
				}
			}
			
			if(count($toY) === 1) {
				$fillLine = new Line(
					new Point($x, $fromY),
					new Point($x, $toY[0])
				);
			} else {
			
				$line1 = new Line(
					new Point($x, $fromY),
					new Point($x, $toY[0])
				);
				$line2 = new  Line(
					new Point($x, $fromY),
					new Point($x, $toY[1])
				);
			
				if($line1->getSize() < $line2->getSize()) {
					$fillLine = $line1;
				} else {
					$fillLine = $line2;
				}
			}
			
			$color = $this->color($x - $xMin);
			if($fillLine->isPoint()) {
				$this->driver->point($color, $fillLine->p1);
			} elseif($fillLine->getSize() >= 1) {
				$this->driver->line($color, $fillLine);
			}
			unset($color);
		}
	
	}
	
	public function filledRectangle(awGradient $gradient, awPoint $p1, awPoint $p2) {
	
		list($x1, $y1) = $p1->getLocation();
		list($x2, $y2) = $p2->getLocation();
	
		if($y1 < $y2) {
			$y1 ^= $y2 ^= $y1 ^= $y2;
		}
	
		if($x2 < $x1) {
			$x1 ^= $x2 ^= $x1 ^= $x2;
		}
		
		if($gradient instanceof awLinearGradient) {
			$this->rectangleLinearGradient($gradient, new awPoint($x1, $y1), new awPoint($x2, $y2));
		} else {
			awImage::drawError("Class GDGradientDriver: This gradient is not supported by rectangles.");
		}
	
	}
	
	public function filledPolygon(awGradient $gradient, awPolygon $polygon) {
	
		if($gradient instanceof awLinearGradient) {
			$this->polygonLinearGradient($gradient, $polygon);
		} else {
			awImage::drawError("Class GDGradientDriver: This gradient is not supported by polygons.");
		}
	
	}
	
	protected function rectangleLinearGradient(awLinearGradient $gradient, awPoint $p1, awPoint $p2) {
	
		list($x1, $y1) = $p1->getLocation();
		list($x2, $y2) = $p2->getLocation();
	
		if($y1 - $y2 > 0) {
		
			if($gradient->angle === 0) {
			
				$this->init($gradient, $y1 - $y2);
		
				for($i = $y2; $i <= $y1; $i++) {
				
					$color = $this->color($i - $y2);
					
					$p1 = new awPoint($x1, $i);
					$p2 = new awPoint($x2, $i);
			
					$this->driver->filledRectangle($color, new awLine($p1, $p2));
					
					unset($color);
					
				}
				
			} else if($gradient->angle === 90) {
			
				$this->init($gradient, $x2 - $x1);
		
				for($i = $x1; $i <= $x2; $i++) {
				
					$color = $this->color($i - $x1);
					
					$p1 = new awPoint($i, $y2);
					$p2 = new awPoint($i, $y1);
			
					$this->driver->filledRectangle($color, new awLine($p1, $p2));
					
					unset($color);
					
				}
				
			}
			
		}
	
	}
	
	public function filledEllipse(awGradient $gradient, $x1, $y1, $x2, $y2) {
	
		if($y1 < $y2) {
			$y1 ^= $y2 ^= $y1 ^= $y2;
		}
	
		if($x2 < $x1) {
			$x1 ^= $x2 ^= $x1 ^= $x2;
		}
		
		if($gradient instanceof awRadialGradient) {
			$this->ellipseRadialGradient($gradient, $x1, $y1, $x2, $y2);
		} else if($gradient instanceof awLinearGradient) {
			$this->ellipseLinearGradient($gradient, $x1, $y1, $x2, $y2);
		} else {
			awImage::drawError("Class GDGradientDriver: This gradient is not supported by ellipses.");
		}
	
	}
	
	protected function ellipseRadialGradient(awGradient $gradient, $x1, $y1, $x2, $y2) {
	
		if($y1 - $y2 > 0) {
	
			if($y1 - $y2 != $x2 - $x1) {
				awImage::drawError("Class GDGradientDriver: Radial gradients are only implemented on circle, not ellipses.");
			}
			
			$c = new awPoint($x1 + ($x2 - $x1) / 2, $y1 + ($y2 - $y1) / 2); 
			$r = ($x2 - $x1) / 2;
			$ok = array();
			
			// Init gradient
			$this->init($gradient, $r);
			
			for($i = 0; $i <= $r; $i += 0.45) {
			
				$p = ceil((2 * M_PI * $i));
				
				if($p > 0) {
					$interval = 360 / $p;
				} else {
					$interval = 360;
				}
				
				$color = $this->color($i);
				
				for($j = 0; $j < 360; $j += $interval) {
				
					$rad = ($j / 360) * (2 * M_PI);
					
					$x = round($i * cos($rad));
					$y = round($i * sin($rad));
					
					$l = sqrt($x * $x + $y * $y);
					
					if($l <= $r) {
					
						if(
							array_key_exists((int)$x, $ok) === FALSE or
							array_key_exists((int)$y, $ok[$x]) === FALSE
						) {
						
							// Print the point
							$this->driver->point($color, new awPoint($c->x + $x, $c->y + $y));
							
							$ok[(int)$x][(int)$y] = TRUE;
						
						}
						
					}
				
				}
				
				unset($color);
			
			}
		
		}
	
	}
	
	protected function ellipseLinearGradient(awGradient $gradient, $x1, $y1, $x2, $y2) {
	
		// Gauche->droite : 90°
	
		if($y1 - $y2 > 0) {
	
			if($y1 - $y2 != $x2 - $x1) {
				awImage::drawError("Class GDGradientDriver: Linear gradients are only implemented on circle, not ellipses.");
			}
			
			$r = ($x2 - $x1) / 2;
			
			// Init gradient
			$this->init($gradient, $x2 - $x1);
			
			for($i = -$r; $i <= $r; $i++) {
				
				$h = sin(acos($i / $r)) * $r;
				
				$color = $this->color($i + $r);
				
				if($gradient->angle === 90) {
				
					// Print the line
					$p1 = new awPoint(
						$x1 + $i + $r,
						round(max($y2 + $r - $h + 1, $y2))
					);
					
					$p2 = new awPoint(
						$x1 + $i + $r,
						round(min($y1 - $r + $h - 1, $y1))
					);
					
				} else {
				
					// Print the line
					$p1 = new awPoint(
						round(max($x1 + $r - $h + 1, $x1)),
						$y2 + $i + $r
					);
					
					$p2 = new awPoint(
						round(min($x2 - $r + $h - 1, $x2)),
						$y2 + $i + $r
					);
					
				}
				
				$this->driver->filledRectangle($color, new awLine($p1, $p2));
				
				unset($color);
			
			}
		
		}
	
	}
	
	protected function polygonLinearGradient(awLinearGradient $gradient, awPolygon $polygon) {
	
		$count = $polygon->count();
		
		if($count >= 4) {
		
			$left = $polygon->get(0);
			$right = $polygon->get($count - 1);
			
			if($gradient->angle === 0) {
			
				// Get polygon maximum and minimum
				$offset = $polygon->get(0);
				$max = $min = $offset->y;
				for($i = 1; $i < $count - 1; $i++) {
					$offset = $polygon->get($i);
					$max = max($max, $offset->y);
					$min = min($min, $offset->y);
				}
				
				$this->init($gradient, $max - $min);
			
				$prev = $polygon->get(1);
				
				$sum = 0;
			
				for($i = 2; $i < $count - 1; $i++) {
				
					$current = $polygon->get($i);
					
					$interval = 1;
					
					if($i !== $count - 2) {
						$current->x -= $interval;
					}
					
					if($current->x - $prev->x > 0) {
				
						// Draw rectangle
						$x1 = $prev->x;
						$x2 = $current->x;
						$y1 = max($prev->y, $current->y);
						$y2 = $left->y;
						
						$gradient = new awLinearGradient(
							$this->color($max - $min - ($y2 - $y1)),
							$this->color($max - $min),
							0
						);
						
						if($y1 > $y2) {
							$y2 = $y1;
						}
						
						$this->driver->filledRectangle(
							$gradient,
							awLine::build($x1, $y1, $x2, $y2)
						);
						
						$top = ($prev->y < $current->y) ? $current : $prev;
						$bottom = ($prev->y >= $current->y) ? $current : $prev;
						
						$gradient = new awLinearGradient(
							$this->color($bottom->y - $min),
							$this->color($max - $min - ($y2 - $y1)),
							0
						);
						
	
						$gradientDriver = new awGDGradientDriver($this->driver);
						$gradientDriver->drawFilledFlatTriangle(
							$gradient,
							new awPoint($prev->x, min($prev->y, $current->y)),
							$top,
							new awPoint($current->x, min($prev->y, $current->y))
						);
						unset($gradientDriver);
						
						$sum += $current->x - $prev->x;
						
					}
					
					$prev = $current;
					$prev->x += $interval;
				
				}
			
			} else if($gradient->angle === 90) {
				
				$width = $right->x - $left->x;
				$this->init($gradient, $width);
				
				$pos = 1;
				$next = $polygon->get($pos++);
				
				$this->next($polygon, $pos, $prev, $next);
			
				for($i = 0; $i <= $width; $i++) {
				
					$x = $left->x + $i;
					
					$y1 = round($prev->y + ($next->y - $prev->y) * (($i + $left->x - $prev->x) / ($next->x - $prev->x)));
					$y2 = $left->y;
				
					// Draw line
					$color = $this->color($i);
					// YaPB : PHP does not handle alpha on lines
					$this->driver->filledRectangle($color, awLine::build($x, $y1, $x, $y2));

					unset($color);
					
					// Jump to next point
					if($next->x == $i + $left->x) {
					
						$this->next($polygon, $pos, $prev, $next);
						
					}
				
				}
	
			}
			
		} else if($count === 3) {
			$this->drawFilledTriangle(
				$gradient,
				$polygon
			);
		}
	
	}
	
	private function next($polygon, &$pos, &$prev, &$next) {
	
		do {
			$prev = $next;
			$next = $polygon->get($pos++);
		}
		while($next->x - $prev->x == 0 and $pos < $polygon->count());
		
	}
	
	/**
	 * Start colors
	 *
	 * @var int
	 */
	private $r1, $g1, $b1, $a1;
	
	/**
	 * Stop colors
	 *
	 * @var int
	 */
	private $r2, $g2, $b2, $a2;
	
	/**
	 * Gradient size in pixels
	 *
	 * @var int
	 */
	private $size;
	
	
	private function init(awGradient $gradient, $size) {
		
		list(
			$this->r1, $this->g1, $this->b1, $this->a1
		) = $gradient->from->rgba();
		
		list(
			$this->r2, $this->g2, $this->b2, $this->a2
		) = $gradient->to->rgba();
		
		$this->size = $size;
	}
	
	private function color($pos) {
	
		return new awColor(
			$this->getRed($pos),
			$this->getGreen($pos),
			$this->getBlue($pos),
			$this->getAlpha($pos)
		);
		
	}
	
	
	private function getRed($pos) {
		if((float)$this->size !== 0.0) {
			return (int)round($this->r1 + ($pos / $this->size) * ($this->r2 - $this->r1));
		} else {
			return 0;
		}
	}
	
	private function getGreen($pos) {
		if((float)$this->size !== 0.0) {
			return (int)round($this->g1 + ($pos / $this->size) * ($this->g2 - $this->g1));
		} else {
			return 0;
		}
	}
	
	private function getBlue($pos) {
		if((float)$this->size !== 0.0) {
			return (int)round($this->b1 + ($pos / $this->size) * ($this->b2 - $this->b1));
		} else {
			return 0;
		}
	}
	
	private function getAlpha($pos) {
		if((float)$this->size !== 0.0) {
			return (int)round(($this->a1 + ($pos / $this->size) * ($this->a2 - $this->a1)) / 127 * 100);
		} else {
			return 0;
		}
	}

}

registerClass('GDGradientDriver');

/*
 * Check for GD2
 */
if(function_exists('imagecreatetruecolor') === FALSE) {
	awImage::drawErrorFile('missing-gd2');
}

