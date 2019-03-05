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

/**
 * Component responsible for displaying form popover errors with modified width which is calculated based on the
 * form group width.
 */
$(() => {
  // loads form popover instance
  $('[data-toggle="form-popover-error"]').popover();

  /**
   * Recalculates popover position so it is always aligned horizontally and width is identical
   * to the child elements of the form.
   * @param {Object} event
   */
  const repositionPopover = (event) => {
    const $element = $(event.currentTarget);
    const $formGroup = $element.closest('.form-group');
    const $invalidFeedbackContainer = $formGroup.find('.invalid-feedback-container');
    const $errorPopover = $formGroup.find('.form-popover-error');

    const localeVisibleElementWidth = $invalidFeedbackContainer.width();

    $errorPopover.css('width', localeVisibleElementWidth);

    const horizontalDifference = getHorizontalDifference($invalidFeedbackContainer, $errorPopover);

    $errorPopover.css('left', `${horizontalDifference}px`);
  };

  /**
   * gets horizontal difference which helps to align popover horizontally.
   * @param {jQuery} $invalidFeedbackContainer
   * @param {jQuery} $errorPopover
   * @returns {number}
   */
  const getHorizontalDifference = ($invalidFeedbackContainer, $errorPopover) => {
    const inputHorizontalPosition = $invalidFeedbackContainer.offset().left;
    const popoverHorizontalPosition = $errorPopover.offset().left;

    return inputHorizontalPosition - popoverHorizontalPosition;
  };

  // registers the event which displays the popover
  $(document).on('shown.bs.popover', '[data-toggle="form-popover-error"]', (event) => repositionPopover(event));
});

