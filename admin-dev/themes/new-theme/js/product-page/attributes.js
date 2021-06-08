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

export default function () {
  $(document).ready(() => {
    $('.js-attribute-checkbox').change((event) => {
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
