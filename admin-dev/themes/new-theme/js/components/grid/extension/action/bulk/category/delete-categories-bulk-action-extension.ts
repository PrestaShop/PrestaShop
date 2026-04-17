/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
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
