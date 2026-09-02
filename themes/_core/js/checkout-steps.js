/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
