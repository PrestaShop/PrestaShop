/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const $form = $('#stats-date-range-form');

  $('.js-stats-date-preset').on('click', (event) => {
    const $button = $(event.currentTarget);

    $('#stats-date-range-from').val($button.data('from'));
    $('#stats-date-range-to').val($button.data('to'));
    $form.trigger('submit');
  });

  $form.on('submit', (event) => {
    event.preventDefault();

    $.post($form.data('url'), $form.serialize()).done((response) => {
      if (response.has_errors) {
        window.showErrorMessage(response.errors.join(' '));

        return;
      }

      window.location.reload();
    });
  });
});
