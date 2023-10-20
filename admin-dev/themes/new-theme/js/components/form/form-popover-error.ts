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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Component responsible for displaying form popover errors with modified
 * width which is calculated based on the
 * form group width.
 */
$(() => {
  // loads form popover instance
  $('[data-toggle="form-popover-error"]').popover({
    html: true,
    content() {
      return getErrorContent(<HTMLElement> this);
    },
  });

  /**
   * Recalculates popover position so it is always aligned horizontally and width is identical
   * to the child elements of the form.
   * @param {Object} event
   */
  const repositionPopover = (event: JQueryEventObject) => {
    const $element = $(event.currentTarget);
    const $formGroup = $element.closest('.form-group');
    const $invalidFeedbackContainer = $formGroup.find(
      '.invalid-feedback-container',
    );
    const $errorPopover = $formGroup.find('.form-popover-error');

    const localeVisibleElementWidth: number = <number>(
      $invalidFeedbackContainer.width()
    );

    $errorPopover.css('width', localeVisibleElementWidth);

    const horizontalDifference = getHorizontalDifference(
      $invalidFeedbackContainer,
      $errorPopover,
    );

    $errorPopover.css('left', `${horizontalDifference}px`);
  };

  /**
   * gets horizontal difference which helps to align popover horizontally.
   * @param {jQuery} $invalidFeedbackContainer
   * @param {jQuery} $errorPopover
   * @returns {number}
   */
  const getHorizontalDifference = (
    $invalidFeedbackContainer: JQuery,
    $errorPopover: JQuery,
  ): number | null => {
    const invalidContainerOffset = $invalidFeedbackContainer.offset();
    const errorPopoverOffset = $errorPopover.offset();

    if (invalidContainerOffset && errorPopoverOffset) {
      const inputHorizontalPosition = invalidContainerOffset.left;
      const popoverHorizontalPosition = errorPopoverOffset.left;

      return inputHorizontalPosition - popoverHorizontalPosition;
    }

    return null;
  };

  /**
   * Gets popover error content pre-fetched in html.
   * It used unique selector to identify which one content to render.
   *
   * @param popoverTriggerElement
   * @returns {jQuery}
   */
  const getErrorContent = (popoverTriggerElement: HTMLElement) => {
    const popoverTriggerId = $(popoverTriggerElement).data('id');

    return $(`.js-popover-error-content[data-id="${popoverTriggerId}"]`).html();
  };

  // registers the event which displays the popover
  $(document).on(
    'shown.bs.popover',
    '[data-toggle="form-popover-error"]',
    (event: JQueryEventObject) => repositionPopover(event),
  );
});
