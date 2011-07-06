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
 * Create your gradients
 *
 * @package Artichow
 */
abstract class awGradient {

	/**
	 * From color
	 *
	 * @var Color
	 */
	public $from;

	/**
	 * To color
	 *
	 * @var Color
	 */
	public $to;
	
	/**
	 * Build the gradient
	 *
	 * @param awColor $from From color
	 * @param awColor $to To color
	 */
	public function __construct(awColor $from, awColor $to) {
	
		$this->from = $from;
		$this->to = $to;
	
	}

}

registerClass('Gradient', TRUE);


/**
 * Create a linear gradient
 *
 * @package Artichow
 */
class awLinearGradient extends awGradient {

	/**
	 * Gradient angle
	 *
	 * @var int
	 */
	public $angle;
	
	/**
	 * Build the linear gradient
	 *
	 * @param awColor $from From color
	 * @param awColor $to To color
	 * @param int $angle Gradient angle
	 */
	public function __construct($from, $to, $angle) {
	
		parent::__construct(
			$from, $to
		);
		
		$this->angle = (int)$angle;
	
	}

}

registerClass('LinearGradient');


/**
 * Create a bilinear gradient
 *
 * @package Artichow
 */
class awBilinearGradient extends awLinearGradient {

	/**
	 * Gradient center
	 *
	 * @var float Center between 0 and 1
	 */
	public $center;
	
	/**
	 * Build the bilinear gradient
	 *
	 * @param awColor $from From color
	 * @param awColor $to To color
	 * @param int $angle Gradient angle
	 * @param int $center Gradient center
	 */
	public function __construct($from, $to, $angle, $center = 0.5) {
	
		parent::__construct(
			$from, $to, $angle
		);
		
		$this->center = (float)$center;
	
	}

}

registerClass('BilinearGradient');

/**
 * Create a radial gradient
 *
 * @package Artichow
 */
class awRadialGradient extends awGradient {

}

registerClass('RadialGradient');

