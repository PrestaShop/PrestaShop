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
import SortingExtension from '@components/grid/extension/sorting-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import ReloadListActionExtension from '@components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '@components/grid/extension/export-to-sql-manager-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import SubmitBulkExtension from '@components/grid/extension/submit-bulk-action-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import ColumnTogglingExtension from '@components/grid/extension/column-toggling-extension';
import PositionExtension from '@components/grid/extension/position-extension';
import ChoiceTree from '@components/form/choice-tree';
import TranslatableInput from '@components/translatable-input';
import textToLinkRewriteCopier from '@components/text-to-link-rewrite-copier';
import FiltersSubmitButtonEnablerExtension from '@components/grid/extension/filters-submit-button-enabler-extension';
import TaggableField from '@components/taggable-field';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const {$} = window;

$(() => {
  const cmsCategory = new Grid('cms_page_category');

  cmsCategory.addExtension(new ReloadListActionExtension());
  cmsCategory.addExtension(new ExportToSqlManagerExtension());
  cmsCategory.addExtension(new FiltersResetExtension());
  cmsCategory.addExtension(new SortingExtension());
  cmsCategory.addExtension(new LinkRowActionExtension());
  cmsCategory.addExtension(new SubmitBulkExtension());
  cmsCategory.addExtension(new BulkActionCheckboxExtension());
  cmsCategory.addExtension(new SubmitRowActionExtension());
  cmsCategory.addExtension(new ColumnTogglingExtension());
  cmsCategory.addExtension(new PositionExtension(cmsCategory));
  cmsCategory.addExtension(new FiltersSubmitButtonEnablerExtension());

  const translatorInput = new TranslatableInput();

  textToLinkRewriteCopier({
    sourceElementSelector: 'input[name^="cms_page_category[name]"]',
    /* eslint-disable-next-line max-len */
    destinationElementSelector: `${translatorInput.localeInputSelector}:not(.d-none) input[name^="cms_page_category[friendly_url]"]`,
  });

  new ChoiceTree('#cms_page_category_parent_category');

  const shopChoiceTree = new ChoiceTree('#cms_page_category_shop_association');
  shopChoiceTree.enableAutoCheckChildren();

  new TaggableField({
    tokenFieldSelector: 'input[name^="cms_page_category[meta_keywords]"]',
    options: {
      createTokensOnBlur: true,
    },
  });

  const cmsGrid = new Grid('cms_page');
  cmsGrid.addExtension(new ReloadListActionExtension());
  cmsGrid.addExtension(new ExportToSqlManagerExtension());
  cmsGrid.addExtension(new FiltersResetExtension());
  cmsGrid.addExtension(new SortingExtension());
  cmsGrid.addExtension(new ColumnTogglingExtension());
  cmsGrid.addExtension(new BulkActionCheckboxExtension());
  cmsGrid.addExtension(new SubmitBulkExtension());
  cmsGrid.addExtension(new SubmitRowActionExtension());
  cmsGrid.addExtension(new PositionExtension(cmsGrid));
  cmsGrid.addExtension(new FiltersSubmitButtonEnablerExtension());
  cmsGrid.addExtension(new LinkRowActionExtension());

  const helperBlock = new ShowcaseCard('cms-pages-showcase-card');
  helperBlock.addExtension(new ShowcaseCardCloseExtension());
});
