/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';

const currentStepClass = 'js-current-step';
const currentStepSelector = `.${currentStepClass}`;

$.fn.step = function () {
  this.off('click');
  return this;
};

$.fn.getActiveSteps = function () {
  return $(currentStepSelector).prevAll().andSelf();
};

$.fn.makeClickable = function () {
  this.addClass('-clickable');
};

$.fn.isUnreachable = function () {
  return this.hasClass('-unreachable');
};

$.fn.makeCurrent = function (step) {
  this.removeClass('-current');
  this.removeClass(currentStepClass);
  step.toggleClass('-current');
  step.toggleClass(currentStepClass);
};

$.fn.hasContinueButton = function () {
  return $('button.continue', this).length > 0;
};

$.fn.disableAllAfter = function () {
  const $nextSteps = this.nextAll();
  $nextSteps.addClass('-unreachable').removeClass('-complete');
  $('.step-title', $nextSteps).addClass('not-allowed');
};

$.fn.enableAllBefore = function () {
  const $nextSteps = this.nextAll('.checkout-step.-clickable');
  $nextSteps.removeClass('-unreachable').addClass('-complete');
  $('.step-title', $nextSteps).removeClass('not-allowed');
};
