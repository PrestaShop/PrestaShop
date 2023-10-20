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
 * Class CategoryDeleteRowActionExtension handles submitting of row action
 */
export default class DeleteCategoryRowActionExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid: Grid): void {
    grid
      .getContainer()
      .on('click', GridMap.rows.categoryDeleteAction, (event) => {
        event.preventDefault();

        const $deleteCategoriesModal = $(
          GridMap.bulks.deleteCategoriesModal(grid.getId()),
        );
        $deleteCategoriesModal.modal('show');

        $deleteCategoriesModal.on(
          'click',
          GridMap.bulks.submitDeleteCategories,
          () => {
            const $button = $(event.currentTarget);
            const categoryId = $button.data('category-id');

            const $categoriesToDeleteInputBlock = $(
              GridMap.bulks.categoriesToDelete,
            );

            const categoryInput = $categoriesToDeleteInputBlock
              .data('prototype')
              .replace(
                /__name__/g,
                $categoriesToDeleteInputBlock.children().length,
              );

            const $item = $($.parseHTML(categoryInput)[0]);
            $item.val(categoryId);

            $categoriesToDeleteInputBlock.append($item);

            const $form = $deleteCategoriesModal.find('form');

            $form.attr('action', $button.data('category-delete-url'));
            $form.submit();
          },
        );
      });
  }
}
