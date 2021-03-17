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
import CombinationsGridRenderer from '@pages/product/edit/combinations-grid-renderer';
import CombinationsService from '@pages/product/services/combinations-service';
import DynamicPaginator from '@components/pagination/dynamic-paginator';
import SubmittableInput from '@components/form/submittable-input';

const {$} = window;

export default class CombinationsManager {
  constructor() {
    this.$productForm = $(ProductMap.productForm);
    this.$combinationsContainer = $(ProductMap.combinations.combinationsContainer);
    this.combinationIdInputsSelector = ProductMap.combinations.combinationIdInputsSelector;
    this.initialized = false;
    this.combinationsService = new CombinationsService(this.getProductId());

    this.init();

    return {};
  }

  init() {
    this.paginator = new DynamicPaginator(
      ProductMap.combinations.paginationContainer,
      this.combinationsService,
      new CombinationsGridRenderer(),
    );
    this.initSubmittableInputs();
    this.onDefaultCombinationSelection();
    // Paginate to first page when tab is shown
    this.$productForm.find(ProductMap.combinations.navigationTab).on('shown.bs.tab', () => this.firstInit());
  }

  initSubmittableInputs() {
    const combinationToken = this.getCombinationToken();

    new SubmittableInput('.combination-quantity', async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {'combination_item[quantity][value]': input.value, 'combination_item[_token]': combinationToken},
      );
    });

    new SubmittableInput('.combination-impact-on-price', async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {'combination_item[impact_on_price][value]': input.value, 'combination_item[_token]': combinationToken},
      );
    });

    new SubmittableInput('.combination-reference', async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {'combination_item[reference][value]': input.value, 'combination_item[_token]': combinationToken},
      );
    });
  }

  onDefaultCombinationSelection() {
    this.$combinationsContainer.on('change', ProductMap.combinations.isDefaultInputsSelector, async (e) => {
      const checkedInputs = this.$combinationsContainer.find(
        `${ProductMap.combinations.isDefaultInputsSelector}:checked`,
      );

      // stop loop when only one input is checked
      if (checkedInputs.length === 1) {
        return;
      }

      await this.combinationsService.updateListedCombination(
        this.findCombinationId(e.currentTarget),
        {
          'combination_item[is_default]': e.currentTarget.value,
          'combination_item[_token]': this.getCombinationToken(),
        },
      );

      $.each(checkedInputs, (index, input) => {
        if (this.findCombinationId(input) !== this.findCombinationId(e.currentTarget)) {
          $(input).prop('checked', false);
        }
      });
    });
  }

  firstInit() {
    if (this.initialized) {
      return;
    }

    this.initialized = true;
    this.paginator.paginateToLastViewed();
  }

  /**
   * @returns {String}
   */
  getCombinationToken() {
    return $(ProductMap.combinations.combinationsContainer).data('combinationToken');
  }

  /**
   * @returns {Number}
   *
   * @private
   */
  getProductId() {
    return Number(this.$productForm.data('productId'));
  }

  /**
   * @param {HTMLElement} input of the same table row
   *
   * @returns {Number}
   *
   * @private
   */
  findCombinationId(input) {
    return $(input).closest('tr').find('.combination-id-input').val();
  }
}
