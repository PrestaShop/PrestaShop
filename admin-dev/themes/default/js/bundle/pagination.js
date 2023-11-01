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

$(() => {
  /*
  * Link action on the select list in the navigator toolbar.
  * When change occurs, the page is refreshed (location.href redirection)
  */
  $('select[name="paginator_select_page_limit"]').on('change', function () {
    const url = $(this).attr('psurl').replace(/_limit/, $('option:selected', this).val());
    window.location.href = url;
    return false;
  });

  /*
   * Input field changes management
  */
  // eslint-disable-next-line
  function checkInputPage(eventOrigin) {
    const e = eventOrigin || event;
    // eslint-disable-next-line
    const char = e.type === 'keypress' ? String.fromCharCode(e.keyCode || e.which) : (e.clipboardData || window.clipboardData).getData('Text');

    if (/[^\d]/gi.test(char)) {
      return false;
    }
  }
  $('input[name="paginator_jump_page"]').each(function () {
    this.onkeypress = checkInputPage;
    this.onpaste = checkInputPage;

    // eslint-disable-next-line
    $(this).on('keyup', function (e) {
      const val = parseInt($(e.target).val(), 10);

      if (e.which === 13) { // ENTER
        e.preventDefault();
        if (val > 0) {
          const limit = $(e.target).attr('pslimit');
          const url = $(this).attr('psurl').replace(/999999/, (val - 1) * limit);
          window.location.href = url;
          return false;
        }
      }
      const max = parseInt($(e.target).attr('psmax'), 10);

      if (val > max) {
        $(this).val(max);
        return false;
      }
    });

    // eslint-disable-next-line
    $(this).on('blur', function (e) {
      const val = parseInt($(e.target).val(), 10);

      if (parseInt(val, 10) > 0) {
        const limit = $(e.target).attr('pslimit');
        const url = $(this).attr('psurl').replace(/999999/, (val - 1) * limit);
        window.location.href = url;
        return false;
      }
    });
  });
});
