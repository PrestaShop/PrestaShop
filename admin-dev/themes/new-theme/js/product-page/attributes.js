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

export default function () {
  $(document).ready(function () {
    $('.js-attribute-checkbox').change((event) => {
      if ($(event.target).is(':checked')) {
        if ($(`.token[data-value="${$(event.target).data('value')}"] .close`).length === 0) {
          $('#form_step3_attributes').tokenfield(
            'createToken',
            {value: $(event.target).data('value'), label: $(event.target).data('label')}
          );
        }
      } else {
        $(`.token[data-value="${$(event.target).data('value')}"] .close`).click();
      }
    });
  });

  $('#form_step3_attributes')
    .on('tokenfield:createdtoken', function (e) {
      if (!$(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', true);
      }
    })
    .on('tokenfield:removedtoken', function (e) {
      if ($(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', false);
      }
    });

  $('input.form-control[counter], textarea.form-control:not(.autoload_rte)[counter]').each(function () {
    let counter = $(this).attr('counter');

    if (typeof counter === undefined || counter === false) {
      return;
    }

    handleCounter($(this));
    $(this).on('input', function () {
      handleCounter($(this));
    });

    function handleCounter(object) {
      let counter = $(object).attr('counter');
      let counter_type = $(object).attr('counter_type');
      let max = $(object).val().length;

      $(object).parent().find('span.currentLength').text(max);
      if ('recommended' !== counter_type && max > counter) {
        $(object).parent().find('span.maxLength').addClass('text-danger');
      } else {
        $(object).parent().find('span.maxLength').removeClass('text-danger');
      }
    }
  });
}
