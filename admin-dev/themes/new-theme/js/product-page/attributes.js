/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default function () {
  $(() => {
    $('.js-attribute-checkbox').on('change', (event) => {
      if ($(event.target).is(':checked')) {
        if ($(`.token[data-value="${$(event.target).data('value')}"] .close`).length === 0) {
          $('#form_step3_attributes').tokenfield(
            'createToken',
            {value: $(event.target).data('value'), label: $(event.target).data('label')},
          );
        }
      } else {
        $(`.token[data-value="${$(event.target).data('value')}"] .close`).click();
      }
    });
  });

  $('#form_step3_attributes')
    .on('tokenfield:createdtoken', (e) => {
      if (!$(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', true);
      }
    })
    .on('tokenfield:removedtoken', (e) => {
      if ($(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', false);
      }
    });

  $('input.form-control[counter], textarea.form-control:not(.autoload_rte)[counter]').each(function () {
    const counter = $(this).attr('counter');

    if (typeof counter === 'undefined' || counter === false) {
      return;
    }

    handleCounter($(this));
    $(this).on('input', function () {
      handleCounter($(this));
    });

    function handleCounter(object) {
      const counterObject = $(object).attr('counter');
      const counterType = $(object).attr('counterType');
      const max = $(object).val().length;

      $(object).parent().find('span.currentLength').text(max);
      if (counterType !== 'recommended' && max > counterObject) {
        $(object).parent().find('span.maxLength').addClass('text-danger');
      } else {
        $(object).parent().find('span.maxLength').removeClass('text-danger');
      }
    }
  });
}
