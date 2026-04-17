/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
