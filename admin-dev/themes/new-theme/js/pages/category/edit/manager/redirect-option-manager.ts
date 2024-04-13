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
import CategoryMap from '@pages/category/category-map';

const {$} = window;

/**
 * This component is used in category page to selected where the redirection points to when the
 * category is disabled. It is composed on two inputs:
 * - a selection of the redirection type
 * - a rich component to select a category
 */
export default class RedirectOptionManager {
  eventEmitter: EventEmitter;

  $redirectTypeInput: JQuery;

  $redirectTargetInput: JQuery;

  $redirectTargetRow: JQuery;

  entitySearchInput!: EntitySearchInput;

  /**
   * @param {EventEmitter} eventEmitter
   */
  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.$redirectTypeInput = $(CategoryMap.redirectOption.typeInput);
    this.$redirectTargetInput = $(CategoryMap.redirectOption.targetInput);

    // Target only inputs present in the redirect target row
    this.$redirectTargetRow = this.$redirectTargetInput.closest(CategoryMap.redirectOption.groupSelector);

    if (this.$redirectTargetInput.length) {
      this.entitySearchInput = new EntitySearchInput(this.$redirectTargetInput, {});
      this.watchRedirectType();
    }
  }

  /**
   * Watch the selected redirection type and adapt the inputs accordingly.
   *
   * @private
   */
  private watchRedirectType(): void {
    this.$redirectTypeInput.on('change', () => {
      const redirectType = this.$redirectTypeInput.val();

      switch (redirectType) {
        case '301':
        case '302':
          this.entitySearchInput.setValues([]);
          this.showTarget();
          break;
        case '404':
        case '410':
        default:
          this.entitySearchInput.setValues([]);
          this.hideTarget();
          break;
      }
    });
  }

  private showTarget(): void {
    this.$redirectTargetRow.removeClass('d-none');
  }

  private hideTarget(): void {
    this.$redirectTargetRow.addClass('d-none');
  }
}
