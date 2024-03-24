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
import $ from 'jquery';
import prestashop from 'prestashop';

const currentStepClass = prestashop.selectors.checkout.currentStep;
const currentStepSelector = `.${currentStepClass}`;

export default class Steps {
  constructor() {
    this.$steps = $(prestashop.selectors.checkout.step);
    this.$steps.off('click');

    this.$clickableSteps = $(currentStepSelector).prevAll().addBack();
    this.$clickableSteps.addClass('-clickable');
  }

  getClickableSteps() {
    return this.$clickableSteps;
  }

  makeCurrent(step) {
    this.$steps.removeClass('-current');
    this.$steps.removeClass(currentStepClass);
    step.makeCurrent();
  }

  static getClickedStep(event) {
    return new Step($(event.target).closest(prestashop.selectors.checkout.step));
  }
}

class Step {
  constructor($element) {
    this.$step = $element;
  }

  isUnreachable() {
    return this.$step.hasClass('-unreachable');
  }

  makeCurrent() {
    this.$step.addClass('-current');
    this.$step.addClass(currentStepClass);
  }

  hasContinueButton() {
    return $('button.continue', this.$step).length > 0;
  }

  disableAllAfter() {
    const $nextSteps = this.$step.nextAll();
    $nextSteps.addClass('-unreachable').removeClass('-complete');
    $(prestashop.selectors.checkout.stepTitle, $nextSteps).addClass('not-allowed');
  }

  enableAllBefore() {
    const $nextSteps = this.$step.nextAll(`${prestashop.selectors.checkout.step}.-clickable`);
    $nextSteps.removeClass('-unreachable').addClass('-complete');
    $(prestashop.selectors.checkout.stepTitle, $nextSteps).removeClass('not-allowed');
  }
}
