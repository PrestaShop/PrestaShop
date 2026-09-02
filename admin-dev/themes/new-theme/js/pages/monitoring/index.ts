/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import DeleteCategoryRowActionExtension
  from '@components/grid/extension/action/row/category/delete-category-row-action-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const {$} = window;

$(() => {
  const emptyCategoriesGrid = new window.prestashop.component.Grid('empty_category');

  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
  emptyCategoriesGrid.addExtension(new DeleteCategoryRowActionExtension());
  emptyCategoriesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  [
    'no_qty_product_with_combination',
    'no_qty_product_without_combination',
    'disabled_product',
    'product_without_image',
    'product_without_description',
    'product_without_price',
  ].forEach((gridName) => {
    const grid = new window.prestashop.component.Grid(gridName);

    grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  });

  const showcaseCard = new ShowcaseCard('monitoringShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
