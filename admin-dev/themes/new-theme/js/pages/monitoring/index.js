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

import Grid from '@components/grid/grid';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import SortingExtension from '@components/grid/extension/sorting-extension';
import SubmitRowActionExtension
  from '@components/grid/extension/action/row/submit-row-action-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import DeleteCategoryRowActionExtension
  from '@components/grid/extension/action/row/category/delete-category-row-action-extension';
import AsyncToggleColumnExtension
  from '@components/grid/extension/column/common/async-toggle-column-extension';
import FiltersSubmitButtonEnablerExtension
  from '@components/grid/extension/filters-submit-button-enabler-extension';
import ReloadListActionExtension from '@components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension
  from '@components/grid/extension/export-to-sql-manager-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const $ = window.$;

$(() => {
  const emptyCategoriesGrid = new Grid('empty_category');

  emptyCategoriesGrid.addExtension(new FiltersResetExtension());
  emptyCategoriesGrid.addExtension(new SortingExtension());
  emptyCategoriesGrid.addExtension(new ReloadListActionExtension());
  emptyCategoriesGrid.addExtension(new SubmitRowActionExtension());
  emptyCategoriesGrid.addExtension(new LinkRowActionExtension());
  emptyCategoriesGrid.addExtension(new AsyncToggleColumnExtension());
  emptyCategoriesGrid.addExtension(new DeleteCategoryRowActionExtension());
  emptyCategoriesGrid.addExtension(new FiltersSubmitButtonEnablerExtension());

  [
    'no_qty_product_with_combination',
    'no_qty_product_without_combination',
    'disabled_product',
    'product_without_image',
    'product_without_description',
    'product_without_price',
  ].forEach((gridName) => {
    const grid = new Grid(gridName);

    grid.addExtension(new SortingExtension());
    grid.addExtension(new ExportToSqlManagerExtension());
    grid.addExtension(new ReloadListActionExtension());
    grid.addExtension(new FiltersResetExtension());
    grid.addExtension(new AsyncToggleColumnExtension());
    grid.addExtension(new LinkRowActionExtension());
    grid.addExtension(new FiltersSubmitButtonEnablerExtension());
  });

  const showcaseCard = new ShowcaseCard('monitoringShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
