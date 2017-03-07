/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';

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

  $('input.form-control[counter], textarea.form-control[counter]').each(function () {
    var attr = $(this).attr('counter');

    if (typeof attr === undefined || attr === false) {
      return;
    }

    $(this).parent().find('span.currentLength').text($(this).val().length);
    $(this).parent().find('span.currentTotalMax').text(attr);
    $(this).on('input', function () {
      $(this).parent().find('span.currentLength').text($(this).val().length);
      $(this).parent().find('span.currentTotalMax').text(attr);
    });
  });
}
