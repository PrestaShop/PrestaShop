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

import ProductMap from '@pages/product/product-map';

export default class ProductModulesManager {
  $previewContainer: JQuery;

  $selectorContainer: JQuery;

  $contentContainer: JQuery;

  $moduleSelector: JQuery;

  $selectorPreviews: JQuery;

  $moduleContents: JQuery;

  constructor() {
    this.$previewContainer = $(ProductMap.modules.previewContainer);
    this.$selectorContainer = $(ProductMap.modules.selectorContainer);
    this.$contentContainer = $(ProductMap.modules.contentContainer);
    this.$moduleSelector = $(ProductMap.modules.moduleSelector);
    this.$selectorPreviews = $(ProductMap.modules.selectorPreviews);
    this.$moduleContents = $(ProductMap.modules.moduleContents);

    this.init();
  }

  /**
   * @private
   */
  private init(): void {
    this.$previewContainer.removeClass('d-none');
    this.$selectorContainer.addClass('d-none');
    this.$contentContainer.addClass('d-none');
    this.$selectorPreviews.addClass('d-none');
    this.$moduleContents.addClass('d-none');

    this.$previewContainer.on('click', ProductMap.modules.previewButton, (event) => {
      const $button = $(event.target);
      this.selectModule($button.data('target'));
    });

    this.$moduleSelector.on('change', () => this.showSelectedModule());
  }

  /**
   * @param {string} moduleId
   *
   * @private
   */
  private selectModule(moduleId: string): void {
    this.$previewContainer.addClass('d-none');
    this.$selectorContainer.removeClass('d-none');
    this.$contentContainer.removeClass('d-none');

    this.$moduleSelector.val(moduleId);
    // trigger change because this is a select2 component, and module is switched when change even triggers
    this.$moduleSelector.trigger('change');
  }

  /**
   * @private
   */
  private showSelectedModule(): void {
    this.$selectorPreviews.addClass('d-none');
    this.$moduleContents.addClass('d-none');

    const moduleId = <string> this.$moduleSelector.val();

    $(ProductMap.modules.selectorPreview(moduleId)).removeClass('d-none');
    $(ProductMap.modules.moduleContent(moduleId)).removeClass('d-none');
  }
}
