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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import {Grid} from '@js/types/grid';
import GridMap from '@components/grid/grid-map';

const {$} = window;

/**
 * Class DeleteCategoriesBulkActionExtension handles submitting of row action
 */
export default class DeleteCategoriesBulkActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid.getContainer().on('click', GridMap.bulks.deleteCategories, (event) => {
      event.preventDefault();

      const submitUrl = $(event.currentTarget).data('categories-delete-url');

      const $deleteCategoriesModal = $(
        GridMap.bulks.deleteCategoriesModal(grid.getId()),
      );
      $deleteCategoriesModal.modal('show');

      $deleteCategoriesModal.on(
        'click',
        GridMap.bulks.submitDeleteCategories,
        () => {
          const $checkboxes = grid
            .getContainer()
            .find(GridMap.bulks.checkedCheckbox);
          const $categoriesToDeleteInputBlock = $(
            GridMap.bulks.categoriesToDelete,
          );

          $checkboxes.each((i, element) => {
            const $checkbox = $(element);

            const categoryInput = $categoriesToDeleteInputBlock
              .data('prototype')
              .replace(/__name__/g, $checkbox.val());

            const $input = $($.parseHTML(categoryInput)[0]);
            $input.val(<string>$checkbox.val());

            $categoriesToDeleteInputBlock.append($input);
          });

          const $form = $deleteCategoriesModal.find('form');

          $form.attr('action', submitUrl);
          $form.submit();
        },
      );
    });
  }
}
