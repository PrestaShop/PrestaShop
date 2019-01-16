/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class LocaleInputErrorPopover {
  constructor() {
    this.initEvents();
  }

  /**
   * creates new popover instance and registers events related with it.
   */
  initEvents() {
    $('[data-toggle="locale-input-popover"]').popover();

    $(document).on('shown.bs.popover', '[data-toggle="locale-input-popover"]', (event) => this.repositionPopover(event));
  }

  /**
   * Recalculates popover position so it is always aligned with locale input group horizontally and width is identical
   * to the child elements of locale input group.
   * @param {Object} event
   */
  repositionPopover(event) {
    const $element = $(event.currentTarget);
    const $formGroup = $element.closest('.form-group');
    const $localeInputGroup = $formGroup.find('.js-locale-input-group');
    const $errorPopover = $formGroup.find('.js-locale-input-error-popover');

    const localeVisibleElementWidth = $localeInputGroup.find('.js-locale-input:visible').width();

    $errorPopover.css('width', localeVisibleElementWidth);

    const horizontalDifference = this.getHorizontalDifference($localeInputGroup, $errorPopover);

    $errorPopover.css('left', `${horizontalDifference}px`);
  }

  /**
   * gets horizontal difference which helps to align popover horizontally.
   * @param {jQuery} $localeInputGroup
   * @param {jQuery} $errorPopover
   * @returns {number}
   */
  getHorizontalDifference($localeInputGroup, $errorPopover)
  {
    const localeInputHorizontalPosition = $localeInputGroup.offset().left;
    const popoverHorizontalPosition = $errorPopover.offset().left;

    return localeInputHorizontalPosition - popoverHorizontalPosition;
  }
}
