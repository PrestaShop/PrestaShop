/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import MetaPageNameOptionHandler from '@pages/meta/meta-page-name-option-handler';

const {$} = window;

$(() => {
  const meta = new window.prestashop.component.Grid('meta');
  meta.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  const helperBlock = new ShowcaseCard('seo-urls-showcase-card');
  helperBlock.addExtension(new ShowcaseCardCloseExtension());

  new window.prestashop.component.TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  new MetaPageNameOptionHandler();

  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
      'TranslatableInput',
      'TextWithRecommendedLengthCounter',
    ],
  );
});
