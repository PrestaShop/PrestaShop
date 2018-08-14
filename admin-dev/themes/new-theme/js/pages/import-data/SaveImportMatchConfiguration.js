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

import ImportMatchTypeProvider from './ImportMatchTypesProvider.js';

const $ = window.$;

/**
 * Class is responsible for saving import match configuration
 * in Advanced parameters -> Import -> step 2 form
 */
export default class SaveImportMatchConfiguration
{
  /**
   * Initialises all the processes relates with save import match
   */
  init() {
    this.loadEvents();
  }

  /**
   * Method responsible for all events loading related with save data match configuration
   */
  loadEvents() {
    $(document).on('click', '.js-save-import-match', () => this.save());
  }

  save() {
    const $button = $('.js-save-import-match');
    const name = $('.js-import-match-input').val();
    const rowsToSkip = $('.js-rows-skip').val();
    // todo: uncomment const matchTypes = ImportMatchTypeProvider.getTypes;
    const matchTypes = [{
      id: 1,
      value: 'test value'
    }];
    const url = $button.attr('data-url');

    $.ajax({
      type: 'POST',
      url: url,
      dataType : 'json',
      data: {
        name,
        rowsToSkip,
        matchTypes
      },
    }).then(response => {
      console.log(response);
    });
  }
}
