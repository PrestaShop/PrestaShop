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

import Grid from '../../components/grid/grid';
import FiltersResetExtension from '../../components/grid/extension/filters-reset-extension';
import SortingExtension from '../../components/grid/extension/sorting-extension';
import ExportToSqlManagerExtension from '../../components/grid/extension/export-to-sql-manager-extension';
import ReloadListExtension from '../../components/grid/extension/reload-list-extension';
import BulkActionCheckboxExtension from '../../components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkExtension from '../../components/grid/extension/submit-bulk-action-extension';
import SubmitRowActionExtension from '../../components/grid/extension/action/row/submit-row-action-extension';
import LinkRowActionExtension from '../../components/grid/extension/link-row-action-extension';
import CategoryPositionExtension from '../../components/grid/extension/column/catalog/category-position-extension';
import AsyncToggleColumnExtension from '../../components/grid/extension/column/common/async-toggle-column-extension';
import DeleteCategoryRowActionExtension from '../../components/grid/extension/action/row/category/delete-category-row-action-extension';
import DeleteCategoriesBulkActionExtension from '../../components/grid/extension/action/bulk/category/delete-categories-bulk-action-extension';
import TranslatableInput from '../../components/translatable-input';
import ChoiceTable from '../../components/choice-table';
import textToLinkRewriteCopier from '../../components/text-to-link-rewrite-copier';
import ChoiceTree from '../../components/form/choice-tree';
import FormSubmitButton from '../../components/form-submit-button';
import TaggableField from '../../components/taggable-field';
import FiltersSubmitButtonEnablerExtension
  from '../../components/grid/extension/filters-submit-button-enabler-extension';
import ShowcaseCard from '../../components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '../../components/showcase-card/extension/showcase-card-close-extension';
import TextWithRecommendedLengthCounter from '../../components/form/text-with-recommended-length-counter';
import TranslatableField from '../../components/translatable-field';
import TinyMCEEditor from '../../components/tinymce-editor';
import Serp from '../../app/utils/serp/index';

const $ = window.$;

$(() => {
  const attachmentGrid = new Grid('attachment');

  attachmentGrid.addExtension(new FiltersResetExtension());
  attachmentGrid.addExtension(new SortingExtension());
  attachmentGrid.addExtension(new ExportToSqlManagerExtension());
  attachmentGrid.addExtension(new ReloadListExtension());
  attachmentGrid.addExtension(new BulkActionCheckboxExtension());
  attachmentGrid.addExtension(new SubmitBulkExtension());
});
