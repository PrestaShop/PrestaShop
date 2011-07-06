<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once ARTICHOW."/BarPlot.class.php";

class BarDepthPattern extends Pattern {

	protected function getPlot($y, $depth) {
		
		$plot = new BarPlot($y, 1, 1, $depth);
		
		$plot->barShadow->setSize(2);
		$plot->barShadow->smooth(TRUE);
		$plot->barShadow->setColor(new Color(160, 160, 160, 10));
		
		return $plot;
		
	}

	public function create() {

		$group = new PlotGroup;
		$group->setSpace(2, 2, 2, 0);
		$group->setPadding(30, 10, NULL, NULL);
		
		$group->grid->hideVertical(TRUE);
		$group->grid->setType(Line::DASHED);
		
		$yForeground = $this->getArg('yForeground');
		$yBackground = $this->getArg('yBackground');
		
		$legendForeground = $this->getArg('legendForeground');
		$legendBackground = $this->getArg('legendBackground');
		
		$colorForeground = $this->getArg('colorForeground', new LightBlue(10));
		$colorBackground = $this->getArg('colorBackground', new Orange(25));
		
		if($yForeground === NULL) {
			awImage::drawError("Class BarDepthPattern: Argument 'yForeground' must not be NULL.");
		}
		
		// Background
		if($yBackground !== NULL) {
			
			$plot = $this->getPlot($yBackground, 6);
			$plot->setBarColor($colorBackground);
			
			$group->add($plot);
			if($legendBackground !== NULL) {
				$group->legend->add($plot, $legendBackground, Legend::BACKGROUND);
			}
			
		}
		
		// Foreground
		$plot = $this->getPlot($yForeground, 0);
		$plot->setBarColor($colorForeground);
		
		$group->add($plot);
		if($legendForeground !== NULL) {
			$group->legend->add($plot, $legendForeground, Legend::BACKGROUND);
		}
		
		$group->axis->bottom->hideTicks(TRUE);
		
		$group->legend->shadow->setSize(0);
		$group->legend->setAlign(Legend::CENTER);
		$group->legend->setSpace(6);
		$group->legend->setTextFont(new Tuffy(8));
		$group->legend->setPosition(0.50, 0.10);
		$group->legend->setBackgroundColor(new Color(255, 255, 255, 10));
		$group->legend->setColumns(2);
		
		return $group;

	}

}
