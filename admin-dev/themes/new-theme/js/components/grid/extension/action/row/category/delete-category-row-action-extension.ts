/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
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
