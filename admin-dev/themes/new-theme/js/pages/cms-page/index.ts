/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
