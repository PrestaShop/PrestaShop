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
import ProductEventMap from '@pages/product/product-event-map';
import initCombinationModal from '@pages/product/components/combination-modal';

const {$} = window;
const CombinationEvents = ProductEventMap.combinations;

export default class CombinationsManager {
  /**
   * @param {int} productId
   * @returns {{}}
   */
  constructor(productId) {
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$productForm = $(ProductMap.productForm);
    this.$combinationsContainer = $(ProductMap.combinations.combinationsContainer);
    this.combinationIdInputsSelector = ProductMap.combinations.combinationIdInputsSelector;
    this.initialized = false;
    this.combinationsService = new CombinationsService(this.getProductId());

    this.init(productId);

    return {};
  }

  /**
   * @param {int} productId
   *
   * @private
   */
  init(productId) {
    this.paginator = new DynamicPaginator(
      ProductMap.combinations.paginationContainer,
      this.combinationsService,
      new CombinationsGridRenderer(),
    );
    this.initSubmittableInputs();
    this.$combinationsContainer.on('change', ProductMap.combinations.isDefaultInputsSelector, (e) => {
      if (!e.currentTarget.checked) {
        return;
      }
      this.updateDefaultCombination(e.currentTarget);
    });

    // Paginate to first page when tab is shown
    this.$productForm.find(ProductMap.combinations.navigationTab).on('shown.bs.tab', () => this.firstInit());

    // Init combination edition modal
    initCombinationModal(ProductMap.combinations.editModal, productId);

    this.watchEvents();
  }

  /**
   * @private
   */
  watchEvents() {
    this.eventEmitter.on(CombinationEvents.refreshList, () => this.paginator.paginate(this.paginator.getCurrentPage()));
  }

  /**
   * @private
   */
  initSubmittableInputs() {
    const combinationToken = this.getCombinationToken();
    const {quantityKey} = ProductMap.combinations.combinationItemForm;
    const {impactOnPriceKey} = ProductMap.combinations.combinationItemForm;
    const {referenceKey} = ProductMap.combinations.combinationItemForm;
    const {tokenKey} = ProductMap.combinations.combinationItemForm;

    new SubmittableInput(ProductMap.combinations.quantityInputWrapper, async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {[quantityKey]: input.value, [tokenKey]: combinationToken},
      );
    });

    new SubmittableInput(ProductMap.combinations.impactOnPriceInputWrapper, async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {[impactOnPriceKey]: input.value, [tokenKey]: combinationToken},
      );
    });

    new SubmittableInput(ProductMap.combinations.referenceInputWrapper, async (input) => {
      await this.combinationsService.updateListedCombination(
        this.findCombinationId(input),
        {[referenceKey]: input.value, [tokenKey]: combinationToken},
      );
    });
  }

  /**
   * @param {HTMLElement} checkedInput
   *
   * @private
   */
  async updateDefaultCombination(checkedInput) {
    const checkedInputs = this.$combinationsContainer.find(
      `${ProductMap.combinations.isDefaultInputsSelector}:checked`,
    );
    const checkedDefaultId = this.findCombinationId(checkedInput);

    await this.combinationsService.updateListedCombination(
      checkedDefaultId,
      {
        'combination_item[is_default]': checkedInput.value,
        'combination_item[_token]': this.getCombinationToken(),
      },
    );

    $.each(checkedInputs, (index, input) => {
      if (this.findCombinationId(input) !== checkedDefaultId) {
        $(input).prop('checked', false);
      }
    });
  }

  /**
   * @private
   */
  firstInit() {
    if (this.initialized) {
      return;
    }

    this.initialized = true;
    this.paginator.paginate(1);
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
