/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class AddRangeHandler {
  constructor() {
    $(document).on('click', '.js-add-range', () => {
      const collectionHolder = $('tbody.ranges > tr');
      const formPrototype = $('.ranges').data('prototype');

      this.addToCollection(collectionHolder, formPrototype);
    });

    return {};
  }

  /**
   * Add new form prototype to collection
   *
   * @param collectionHolder
   * @param formPrototype
   */
  addToCollection(collectionHolder, formPrototype) {
    const index = this.getCollectionCount(collectionHolder);

    // replace prototype name placeholder with current index value
    const newForm = formPrototype.replace(/__name__/g, index);

    // append the new form to collection
    collectionHolder.append(`<td>${newForm}</td>`);

    // increase collection count by one
    collectionHolder.data('index', index + 1);
  }

  /**
   * Count current items in collection
   *
   * @param collectionHolder
   *
   * @returns int
   */
  getCollectionCount(collectionHolder) {
    collectionHolder.data('index', collectionHolder.find('.js-form-block-count').length);

    return collectionHolder.data('index');
  }
}

