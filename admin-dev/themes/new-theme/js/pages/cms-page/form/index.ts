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

import ChoiceTree from '@components/form/choice-tree';
import textToLinkRewriteCopier from '@components/text-to-link-rewrite-copier';
import Serp from '@app/utils/serp/index';
import TextWithRecommendedLengthCounter from '@components/form/text-with-recommended-length-counter';

const {$} = window;

$(() => {
  new ChoiceTree('#cms_page_page_category_id');

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
      'TranslatableField',
      'TinyMCEEditor',
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

  new window.prestashop.component.PreviewOpener('.js-preview-url');

  textToLinkRewriteCopier({
    sourceElementSelector: 'input.js-copier-source-title',
    /* eslint-disable-next-line max-len */
    destinationElementSelector: `${translatorInput.localeInputSelector}:not(.d-none) input.js-copier-destination-friendly-url`,
  });

  new ChoiceTree('#cms_page_shop_association').enableAutoCheckChildren();

  new TextWithRecommendedLengthCounter();
});
