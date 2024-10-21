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

import textToLinkRewriteCopier from '@components/text-to-link-rewrite-copier';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const {$} = window;

$(() => {
  const cmsCategory = new window.prestashop.component.Grid('cms_page_category');

  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(cmsCategory));
  cmsCategory.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );

  const translatorInput = window.prestashop.instance.translatableInput;

  textToLinkRewriteCopier({
    sourceElementSelector: 'input[name^="cms_page_category[name]"]',
    /* eslint-disable-next-line max-len */
    destinationElementSelector: `${translatorInput.localeInputSelector}:not(.d-none) input[name^="cms_page_category[friendly_url]"]`,
  });

  new window.prestashop.component.ChoiceTree('#cms_page_category_parent_category');

  const shopChoiceTree = new window.prestashop.component.ChoiceTree('#cms_page_category_shop_association');
  shopChoiceTree.enableAutoCheckChildren();

  const cmsGrid = new window.prestashop.component.Grid('cms_page');
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(cmsGrid));
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  cmsGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const helperBlock = new ShowcaseCard('cms-pages-showcase-card');
  helperBlock.addExtension(new ShowcaseCardCloseExtension());
});
