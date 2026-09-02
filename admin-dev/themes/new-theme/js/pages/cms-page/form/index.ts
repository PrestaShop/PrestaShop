/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import textToLinkRewriteCopier from '@components/text-to-link-rewrite-copier';
import Serp from '@app/utils/serp/index';

const {$} = window;

$(() => {
  new window.prestashop.component.ChoiceTree('#cms_page_page_category_id');

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'TranslatableField',
      'TinyMCEEditor',
      'TextWithRecommendedLengthCounter',
    ],
  );

  const translatorInput = window.prestashop.instance.translatableInput;

  new Serp(
    {
      container: '#serp-app',
      defaultTitle: 'input[name^="cms_page[title]"]',
      watchedTitle: 'input[name^="cms_page[meta_title]"]',
      defaultDescription: 'input[name^="cms_page[description]"]',
      watchedDescription: 'input[name^="cms_page[meta_description]"]',
      watchedMetaUrl: 'input[name^="cms_page[friendly_url]"]',
      multiLanguageInput: `${translatorInput.localeInputSelector}:not(.d-none)`,
      multiLanguageItem: translatorInput.localeItemSelector,
    },
    $('#serp-app').data('cms-url'),
  );

  new window.prestashop.component.TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  new window.prestashop.component.PreviewOpener('.js-preview-url');

  textToLinkRewriteCopier({
    sourceElementSelector: 'input.js-copier-source-title',
    /* eslint-disable-next-line max-len */
    destinationElementSelector: `${translatorInput.localeInputSelector}:not(.d-none) input.js-copier-destination-friendly-url`,
  });

  new window.prestashop.component.ChoiceTree('#cms_page_shop_association').enableAutoCheckChildren();
});
