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

const $ = window.$;

/**
 * Class DeleteCustomerRowActionExtension handles submitting of row action
 */
export default class DeleteCustomerRowActionExtension {

  constructor() {
    return {
      extend: (grid) => this.extend(grid),
    };
  }

  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getContainer().on('click', '.js-delete-customer-row-action', (event) => {
      event.preventDefault();

      const $deleteCategoriesModal = $('#' + grid.getId() + '_grid_delete_customers_modal');
      $deleteCategoriesModal.modal('show');

      $deleteCategoriesModal.on('click', '.js-submit-delete-customers', () => {
        const $button = $(event.currentTarget);
        const categoryId = $button.data('customer-id');

        const $categoriesToDeleteInputBlock = $('#delete_customers_customers_to_delete');

        const categoryInput = $categoriesToDeleteInputBlock
          .data('prototype')
          .replace(/__name__/g, $categoriesToDeleteInputBlock.children().length);

        const $item = $($.parseHTML(categoryInput)[0]);
        $item.val(categoryId);

        $categoriesToDeleteInputBlock.append($item);

        const $form = $deleteCategoriesModal.find('form');

        $form.attr('action', $button.data('customer-delete-url'));
        $form.submit();
      });
    });
  }
}
