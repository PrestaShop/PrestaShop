/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

(function(jQuery) {
	jQuery.fn.typeWatch = function(o){
		// Options
		var options = jQuery.extend({
			wait : 750,
			callback : function() { },
			highlight : true,
			captureLength : 2
		}, o);
			
		function checkElement(timer, override) {
			var elTxt = jQuery(timer.el).val();
		
			// Fire if text > options.captureLength AND text != saved txt OR if override AND text > options.captureLength
			if ((elTxt.length > options.captureLength && elTxt.toUpperCase() != timer.text) 
			|| (override && elTxt.length > options.captureLength)) {
				timer.text = elTxt.toUpperCase();
				timer.cb(elTxt);
			}
		};
		
		function watchElement(elem) {			
			// Must be text or textarea
			if (elem.type.toUpperCase() == "TEXT" || elem.nodeName.toUpperCase() == "TEXTAREA") {

				// Allocate timer element
				var timer = {
					timer : null, 
					text : jQuery(elem).val().toUpperCase(),
					cb : options.callback, 
					el : elem, 
					wait : options.wait
				};

				// Set focus action (highlight)
				if (options.highlight) {
					jQuery(elem).focus(
						function() {
							this.select();
						});
				}

				// Key watcher / clear and reset the timer
				var startWatch = function(evt) {
					var timerWait = timer.wait;
					var overrideBool = false;
					
					if (evt.keyCode == 13 && this.type.toUpperCase() == "TEXT") {
						timerWait = 1;
						overrideBool = true;
					}
					
					var timerCallbackFx = function()
					{
						checkElement(timer, overrideBool);
					}
					
					// Clear timer					
					clearTimeout(timer.timer);
					timer.timer = setTimeout(timerCallbackFx, timerWait);				
										
				};
				
				jQuery(elem).keydown(startWatch);
			}
		};
		
		// Watch Each Element
		return this.each(function(index){
			watchElement(this);
		});
		
	};

})(jQuery);
