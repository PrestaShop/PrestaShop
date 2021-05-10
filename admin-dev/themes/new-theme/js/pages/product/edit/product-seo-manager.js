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
import Serp from '@app/utils/serp';
import RedirectOptionManager from '@pages/product/edit/redirect-option-manager';
import ProductMap from '@pages/product/product-map';

const {$} = window;

export default class ProductSEOManager {
  constructor() {
    this.$previewButton = $(ProductMap.footer.previewUrlButton);

    this.init();

    return {};
  }

  /**
   * @private
   */
  init() {
    // Init the product/category search field for redirection target
    const $redirectTypeInput = $(ProductMap.seo.redirectOption.typeInput);
    const $redirectTargetInput = $(ProductMap.seo.redirectOption.targetInput);
    new RedirectOptionManager($redirectTypeInput, $redirectTargetInput);

    // Init Serp component to preview Search engine display
    const {translatableInput, translatableField} = window.prestashop.instance;
    let previewUrl = this.$previewButton.data('seoUrl');

    if (!previewUrl) {
      previewUrl = '';
    }

    new Serp(
      {
        container: ProductMap.seo.container,
        defaultTitle: ProductMap.seo.defaultTitle,
        watchedTitle: ProductMap.seo.watchedTitle,
        defaultDescription: ProductMap.seo.defaultDescription,
        watchedDescription: ProductMap.seo.watchedDescription,
        watchedMetaUrl: ProductMap.seo.watchedMetaUrl,
        multiLanguageInput: `${translatableInput.localeInputSelector}:not(.d-none)`,
        multiLanguageField: `${translatableField.translationFieldSelector}.active`,
      },
      previewUrl,
    );
  }
}
