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

import EntitySearchInput from '@components/entity-search-input';
import {EventEmitter} from 'events';
import ComponentsMap from '@components/components-map';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';

const {$} = window;

/**
 * This component is used in product page to selected where the redirection points to when the
 * product is out of stock. It is composed on two inputs:
 * - a selection of the redirection type
 * - a rich component to select a product or a category
 *
 * When the type is changed the component automatically updates the labels, remote search urls
 * and values of the target.
 */
export default class RedirectOptionManager {
  eventEmitter: EventEmitter;

  $redirectTypeInput: JQuery;

  $redirectTargetInput: JQuery;

  $searchInput: JQuery;

  $redirectTargetRow: JQuery;

  $redirectTargetLabel: JQuery;

  $redirectTargetHint: JQuery;

  lastSelectedType: string | number | string[] | undefined;

  entitySearchInput!: EntitySearchInput;

  /**
   * @param {EventEmitter} eventEmitter
   */
  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.$redirectTypeInput = $(ProductMap.seo.redirectOption.typeInput);
    this.$redirectTargetInput = $(ProductMap.seo.redirectOption.targetInput);

    // Target only inputs present in the redirect target row
    this.$redirectTargetRow = this.$redirectTargetInput.closest(ProductMap.seo.redirectOption.groupSelector);
    this.$searchInput = $(ComponentsMap.entitySearchInput.searchInputSelector, this.$redirectTargetRow);
    this.$redirectTargetLabel = $(ProductMap.seo.redirectOption.labelSelector, this.$redirectTargetRow).first();
    this.$redirectTargetHint = $(ProductMap.seo.redirectOption.helpSelector, this.$redirectTargetRow);

    this.buildAutoCompleteSearchInput();
    this.watchRedirectType();
  }

  /**
   * Watch the selected redirection type and adapt the inputs accordingly.
   *
   * @private
   */
  private watchRedirectType(): void {
    this.lastSelectedType = this.$redirectTypeInput.val();

    this.$redirectTypeInput.change(() => {
      const redirectType = this.$redirectTypeInput.val();

      switch (redirectType) {
        case '301-category':
        case '302-category':
          this.entitySearchInput.setOption('remoteUrl', this.$redirectTargetInput.data('categorySearchUrl'));
          this.$searchInput.prop('placeholder', this.$redirectTargetInput.data('categoryPlaceholder'));
          this.$redirectTargetLabel.html(this.$redirectTargetInput.data('categoryLabel'));
          // If previous type was not a category we reset the selected value
          if (this.lastSelectedType !== '301-category' && this.lastSelectedType !== '302-category') {
            this.entitySearchInput.setValues([]);
          }
          this.$redirectTargetHint.html(this.$redirectTargetInput.data('categoryHelp'));
          this.entitySearchInput.setOption('allowDelete', true);
          this.entitySearchInput.setOption('filteredIdentities', this.$redirectTargetInput.data('categoryFiltered'));
          this.showTarget();
          break;
        case '301-product':
        case '302-product':
          this.entitySearchInput.setOption('remoteUrl', this.$redirectTargetInput.data('productSearchUrl'));
          this.$searchInput.prop('placeholder', this.$redirectTargetInput.data('productPlaceholder'));
          this.$redirectTargetLabel.html(this.$redirectTargetInput.data('productLabel'));
          // If previous type was not a category we reset the selected value
          if (this.lastSelectedType !== '301-product' && this.lastSelectedType !== '302-product') {
            this.entitySearchInput.setValues([]);
          }
          this.$redirectTargetHint.html(this.$redirectTargetInput.data('productHelp'));
          this.entitySearchInput.setOption('allowDelete', false);
          this.entitySearchInput.setOption('filteredIdentities', this.$redirectTargetInput.data('productFiltered'));
          this.showTarget();
          break;
        case '404':
        case '410':
        default:
          this.entitySearchInput.setValues([]);
          this.hideTarget();
          break;
      }
      this.lastSelectedType = this.$redirectTypeInput.val();
    });
  }

  private buildAutoCompleteSearchInput(): void {
    const redirectType = this.$redirectTypeInput.val();
    // On first load only allow delete for category target
    let initialAllowDelete;

    switch (redirectType) {
      case '301-category':
      case '302-category':
        initialAllowDelete = true;
        break;
      default:
        initialAllowDelete = false;
        break;
    }

    this.entitySearchInput = new EntitySearchInput(this.$redirectTargetInput, {
      allowDelete: initialAllowDelete,
      onRemovedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
      onSelectedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
    });
  }

  private showTarget(): void {
    this.$redirectTargetRow.removeClass('d-none');
  }

  private hideTarget(): void {
    this.$redirectTargetRow.addClass('d-none');
  }
}
