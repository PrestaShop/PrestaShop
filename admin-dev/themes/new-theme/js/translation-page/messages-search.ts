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
import Jets from 'jets/jets';

export default function (): typeof Jets | boolean {
  $(() => {
    const searchSelector = '.search-translation';
    $(`${searchSelector} form`).submit((event) => {
      event.preventDefault();

      $('#jetsContent form').addClass('hide');
      const $jetsSearch = <string>$('#jetsSearch').val();

      const keywords = $jetsSearch?.toLowerCase();
      const jetsSelector = `#jetsContent > [data-jets*="${keywords}"]`;

      if ($(jetsSelector).length === 0) {
        const notificationElement = $(`${searchSelector}> .alert`)[0];
        $(notificationElement).removeClass('hide');
        setTimeout(() => {
          $(notificationElement).addClass('hide');
        }, 2000);
      } else {
        $(jetsSelector).removeClass('hide');
      }

      if ($(jetsSelector).length) {
        $('.js-results')
          .show()
          .addClass('card')
          .find('h2')
          .removeClass('hide');
      }

      return false;
    });

    $(`${searchSelector} input[type=reset]`).click((event) => {
      event.preventDefault();

      $('#jetsSearch').val('');
      $('#jetsContent form').addClass('hide');

      return false;
    });
  });

  if ($('#jetsSearch').length > 0) {
    return new Jets({
      searchTag: '#jetsSearch',
      contentTag: '#jetsContent',
      callSearchManually: true,
      manualContentHandling(tag: HTMLElement) {
        // Search for translation keys and translation values
        return (
          $(tag).find('.verbatim')[0].innerText
          + $(tag).find('textarea')[0].value
        );
      },
    });
  }

  return false;
}
