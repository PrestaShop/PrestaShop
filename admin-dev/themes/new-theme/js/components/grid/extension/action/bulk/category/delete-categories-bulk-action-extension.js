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

const $ = window.$;

/**
 * Class DeleteCategoriesBulkActionExtension handles submitting of row action
 */
export default class DeleteCategoriesBulkActionExtension {

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
    grid.getContainer().on('click', '.js-delete-categories-bulk-action', (event) => {
      event.preventDefault();

      const submitUrl = $(event.currentTarget).data('categories-delete-url');

      const $deleteCategoriesModal = $(`#${grid.getId()}_grid_delete_categories_modal`);
      $deleteCategoriesModal.modal('show');

      $deleteCategoriesModal.on('click', '.js-submit-delete-categories', () => {
        const $checkboxes = grid.getContainer().find('.js-bulk-action-checkbox:checked');
        const $categoriesToDeleteInputBlock = $('#delete_categories_categories_to_delete');

        $checkboxes.each((i, element) => {
          const $checkbox = $(element);

          const categoryInput = $categoriesToDeleteInputBlock
            .data('prototype')
            .replace(/__name__/g, $checkbox.val());

          const $input = $($.parseHTML(categoryInput)[0]);
          $input.val($checkbox.val());

          $categoriesToDeleteInputBlock.append($input);
        });

        const $form = $deleteCategoriesModal.find('form');

        $form.attr('action', submitUrl);
        $form.submit();
      });
    });
  }
}
