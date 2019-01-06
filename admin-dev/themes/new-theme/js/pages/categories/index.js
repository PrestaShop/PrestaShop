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

import Grid from '../../components/grid/grid';
import FiltersResetExtension from "../../components/grid/extension/filters-reset-extension";
import SortingExtension from "../../components/grid/extension/sorting-extension";
import ExportToSqlManagerExtension from "../../components/grid/extension/export-to-sql-manager-extension";
import ReloadListExtension from "../../components/grid/extension/reload-list-extension";
import BulkActionCheckboxExtension from "../../components/grid/extension/bulk-action-checkbox-extension";
import SubmitBulkExtension from "../../components/grid/extension/submit-bulk-action-extension";
import SubmitRowActionExtension from "../../components/grid/extension/action/row/submit-row-action-extension";
import LinkRowActionExtension from "../../components/grid/extension/link-row-action-extension";
import CategoryPositionExtension from "../../components/grid/extension/column/catalog/category-position-extension";
import AsyncToggleColumnExtension from "../../components/grid/extension/column/common/async-toggle-column-extension";
import DeleteCategoryRowActionExtension from "../../components/grid/extension/action/row/category/delete-category-row-action-extension";
import DeleteCategoriesBulkActionExtension from "../../components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension";
import TranslatableInput from "../../components/translatable-input";
import ChoiceTable from "../../components/choice-table";
import TextWithLengthCounter from "../../components/form/text-with-length-counter";
import NameToLinkRewriteCopier from "./name-to-link-rewrite-copier";
import ChoiceTree from "../../components/form/choice-tree";
import FormSubmitButton from "../../components/form-submit-button";

const $ = window.$;

$(() => {
  const categoriesGrid = new Grid('categories');

  categoriesGrid.addExtension(new FiltersResetExtension());
  categoriesGrid.addExtension(new SortingExtension());
  categoriesGrid.addExtension(new CategoryPositionExtension());
  categoriesGrid.addExtension(new ExportToSqlManagerExtension());
  categoriesGrid.addExtension(new ReloadListExtension());
  categoriesGrid.addExtension(new BulkActionCheckboxExtension());
  categoriesGrid.addExtension(new SubmitBulkExtension());
  categoriesGrid.addExtension(new SubmitRowActionExtension());
  categoriesGrid.addExtension(new LinkRowActionExtension());
  categoriesGrid.addExtension(new AsyncToggleColumnExtension());
  categoriesGrid.addExtension(new DeleteCategoryRowActionExtension());
  categoriesGrid.addExtension(new DeleteCategoriesBulkActionExtension());

  new TranslatableInput();
  new ChoiceTable();
  new TextWithLengthCounter();
  new NameToLinkRewriteCopier();
  new FormSubmitButton();

  new ChoiceTree('#category_id_parent');
  new ChoiceTree('#category_shop_association').enableAutoCheckChildren();

  new ChoiceTree('#root_category_id_parent');
  new ChoiceTree('#root_category_shop_association').enableAutoCheckChildren();
});
