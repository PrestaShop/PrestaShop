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
import SpecificMap from './selectors-map';

const {$} = window;

class SpecificPriceFormHandler {
  prefixCreateForm: string;

  prefixEditForm: string;

  editModalIsOpen: boolean;

  $createPriceFormDefaultValues: Record<string, any>;

  constructor() {
    this.prefixCreateForm = 'form_step2_specific_price_';
    this.prefixEditForm = 'form_modal_';
    this.editModalIsOpen = false;

    this.$createPriceFormDefaultValues = {};
    this.storePriceFormDefaultValues();

    this.loadAndDisplayExistingSpecificPricesList();

    this.configureAddPriceFormBehavior();

    this.configureEditPriceModalBehavior();

    this.configureDeletePriceButtonsBehavior();

    this.configureMultipleModalsBehavior();
  }

  /**
   * @private
   */
  private loadAndDisplayExistingSpecificPricesList(): void {
    const listContainer = $(SpecificMap.priceList);
    const url = listContainer
      .data('listingUrl')
      .replace(/list\/\d+/, `list/${this.getProductId()}`);

    $.ajax({
      type: 'GET',
      url,
    }).done((specificPrices) => {
      const tbody = listContainer.find('tbody');
      tbody.find('tr').remove();

      if (specificPrices.length > 0) {
        listContainer.removeClass('hide');
      } else {
        listContainer.addClass('hide');
      }

      const specificPricesList = this.renderSpecificPricesListingAsHtml(
        specificPrices,
      );

      tbody.append(specificPricesList);
    });
  }

  /**
   * @param array specificPrices
   *
   * @returns string
   *
   * @private
   */
  private renderSpecificPricesListingAsHtml(
    specificPrices: Record<string, any>,
  ): string {
    let specificPricesList = '';
    const $specificPricesListElement = $('#js-specific-price-list');

    const self = this;

    $.each(specificPrices, (index, specificPrice) => {
      const deleteAttr = $specificPricesListElement.attr('data-action-delete');
      let row;

      if (deleteAttr) {
        const deleteUrl = deleteAttr.replace(
          /delete\/\d+/,
          `delete/${specificPrice.id_specific_price}`,
        );
        row = self.renderSpecificPriceRow(specificPrice, deleteUrl);
      }

      specificPricesList += row;
    });

    return specificPricesList;
  }

  /**
   * @param Object specificPrice
   * @param string deleteUrl
   *
   * @returns string
   *
   * @private
   */
  private renderSpecificPriceRow(
    specificPrice: Record<string, any>,
    deleteUrl: string,
  ): string {
    const specificPriceId = specificPrice.id_specific_price;

    /* eslint-disable max-len */
    const canDelete = specificPrice.can_delete
      ? `<a href="${deleteUrl}" class="js-delete delete btn tooltip-link delete pl-0 pr-0"><i class="material-icons">delete</i></a>`
      : '';
    const canEdit = specificPrice.can_edit
      ? `<a href="#" data-specific-price-id="${specificPriceId}" class="js-edit edit btn tooltip-link delete pl-0 pr-0"><i class="material-icons">edit</i></a>`
      : '';
    const row = `<tr> \
    <td>${specificPrice.id_specific_price}</td> \
    <td>${specificPrice.rule_name}</td> \
    <td>${specificPrice.attributes_name}</td> \
    <td>${specificPrice.currency}</td> \
    <td>${specificPrice.country}</td> \
    <td>${specificPrice.group}</td> \
    <td>${specificPrice.customer}</td> \
    <td>${specificPrice.fixed_price}</td> \
    <td>${specificPrice.impact}</td> \
    <td>${specificPrice.period}</td> \
    <td>${specificPrice.from_quantity}</td> \
    <td>${canDelete}</td> \
    <td>${canEdit}</td></tr>`;
    /* eslint-enable max-len */

    return row;
  }

  /**
   * @private
   */
  private configureAddPriceFormBehavior() {
    const usePrefixForCreate = true;
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $(SpecificMap.cancel).on('click', () => {
      this.resetCreatePriceFormDefaultValues();
      $(SpecificMap.priceForm).collapse('hide');
    });

    $(SpecificMap.save).on('click', () => this.submitCreatePriceForm());

    // eslint-disable-next-line
    $(SpecificMap.openCreate).on('click', () => this.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate),
    );

    $(SpecificMap.leavBPrice(selectorPrefix)).on('click', () => this.enableSpecificPriceFieldIfEligible(usePrefixForCreate),
    );

    // eslint-disable-next-line
    $(SpecificMap.reductionType(selectorPrefix)).on('change', () => this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate),
    );
  }

  /**
   * @private
   */
  private configureEditPriceFormInsideModalBehavior() {
    const usePrefixForCreate = false;
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $(SpecificMap.modalCancel).on('click', () => this.closeEditPriceModalAndRemoveForm(),
    );
    $(SpecificMap.modalClose).on('click', () => this.closeEditPriceModalAndRemoveForm(),
    );

    $(SpecificMap.modalSave).on('click', () => this.submitEditPriceForm());

    this.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate);

    $(SpecificMap.leavBPrice(selectorPrefix)).on('click', () => this.enableSpecificPriceFieldIfEligible(usePrefixForCreate),
    );

    $(SpecificMap.reductionType).on('change', () => this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate),
    );

    this.reinitializeDatePickers();

    this.initializeLeaveBPriceField(usePrefixForCreate);
    this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate);
  }

  /**
   * @private
   */
  private reinitializeDatePickers() {
    $('.datepicker input').datetimepicker({format: 'YYYY-MM-DD'});
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  private initializeLeaveBPriceField(usePrefixForCreate: boolean): void {
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    if ($(`${selectorPrefix}sp_price`).val() !== '') {
      $(`${selectorPrefix}sp_price`).prop('disabled', false);
      $(`${selectorPrefix}leave_bprice`).prop('checked', false);
    }
  }

  /**
   * @private
   */
  private configureEditPriceModalBehavior(): void {
    $(document).on('click', '#js-specific-price-list .js-edit', (event) => {
      event.preventDefault();

      const specificPriceId = $(event.currentTarget).data('specificPriceId');

      this.openEditPriceModalAndLoadForm(specificPriceId);
    });
  }

  /**
   * @private
   */
  private configureDeletePriceButtonsBehavior(): void {
    $(document).on('click', '#js-specific-price-list .js-delete', (event) => {
      event.preventDefault();
      this.deleteSpecificPrice(event.currentTarget);
    });
  }

  private configureMultipleModalsBehavior(): void {
    $('.modal').on('hidden.bs.modal', () => {
      if (this.editModalIsOpen) {
        $('body').addClass('modal-open');
      }
    });
  }

  /**
   * @private
   */
  private submitCreatePriceForm(): void {
    const url = $('#specific_price_form').attr('data-action');
    const data = $(
      '#specific_price_form input, #specific_price_form select, #form_id_product',
    ).serialize();

    $('#specific_price_form .js-save').attr('disabled', 'disabled');

    $.ajax({
      type: 'POST',
      url,
      data,
    })
      .done(() => {
        window.showSuccessMessage(
          window.translate_javascripts['Form update success'],
        );
        this.resetCreatePriceFormDefaultValues();
        $('#specific_price_form').collapse('hide');
        this.loadAndDisplayExistingSpecificPricesList();

        $('#specific_price_form .js-save').removeAttr('disabled');
      })
      .fail((errors) => {
        window.showErrorMessage(errors.responseJSON);

        $('#specific_price_form .js-save').removeAttr('disabled');
      });
  }

  /**
   * @private
   */
  private submitEditPriceForm(): void {
    const baseUrl = <string>(
      $('#edit-specific-price-modal-form').attr('data-action')
    );
    const specificPriceId = $('#edit-specific-price-modal-form').data(
      'specificPriceId',
    );
    const url = baseUrl.replace(/update\/\d+/, `update/${specificPriceId}`);

    /* eslint-disable-next-line max-len */
    const data = $(
      '#edit-specific-price-modal-form input, #edit-specific-price-modal-form select, #form_id_product',
    ).serialize();

    $('#edit-specific-price-modal-form .js-save').attr('disabled', 'disabled');

    $.ajax({
      type: 'POST',
      url,
      data,
    })
      .done(() => {
        window.showSuccessMessage(
          window.translate_javascripts['Form update success'],
        );
        this.closeEditPriceModalAndRemoveForm();
        this.loadAndDisplayExistingSpecificPricesList();
        $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
      })
      .fail((errors) => {
        window.showErrorMessage(errors.responseJSON);

        $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
      });
  }

  /**
   * @param string clickedLink selector
   *
   * @private
   */
  private deleteSpecificPrice(clickedLink: HTMLElement): void {
    window.modalConfirmation
      .create(
        window.translate_javascripts[
          'Are you sure you want to delete this item?'
        ],
        null,
        {
          onContinue: () => {
            const url = $(clickedLink).attr('href');
            $(clickedLink).attr('disabled', 'disabled');

            $.ajax({
              type: 'GET',
              url,
            })
              .done((response) => {
                this.loadAndDisplayExistingSpecificPricesList();
                window.showSuccessMessage(response);
                $(clickedLink).removeAttr('disabled');
              })
              .fail((errors) => {
                window.showErrorMessage(errors.responseJSON);
                $(clickedLink).removeAttr('disabled');
              });
          },
        },
      )
      .show();
  }

  /**
   * Store 'add specific price' form values
   * for future usage
   *
   * @private
   */
  private storePriceFormDefaultValues(): void {
    const storage = this.$createPriceFormDefaultValues;

    $('#specific_price_form')
      .find('select,input')
      .each((index, value) => {
        storage[<string>$(value).attr('id')] = $(value).val();
      });

    $('#specific_price_form')
      .find('input:checkbox')
      .each((index, value) => {
        storage[<string>$(value).attr('id')] = $(value).prop('checked');
      });

    this.$createPriceFormDefaultValues = storage;
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  private loadAndFillOptionsForSelectCombinationInput(
    usePrefixForCreate: boolean,
  ): void {
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);
    const inputField = $(`${selectorPrefix}sp_id_product_attribute`);
    const action = <string>inputField.attr('data-action');

    const url = action.replace(
      /product-combinations\/\d+/,
      `product-combinations/${this.getProductId()}`,
    );

    $.ajax({
      type: 'GET',
      url,
    }).done((combinations) => {
      /** remove all options except first one */
      inputField.find('option:gt(0)').remove();

      $.each(combinations, (index, combination) => {
        inputField.append(
          `<option value="${combination.id}">${combination.name}</option>`,
        );
      });

      if (inputField.data('selectedAttribute') !== '0') {
        inputField.val(inputField.data('selectedAttribute')).trigger('change');
      }
    });
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  private enableSpecificPriceTaxFieldIfEligible(
    usePrefixForCreate: boolean,
  ): void {
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    if ($(`${selectorPrefix}sp_reduction_type`).val() === 'percentage') {
      $(`${selectorPrefix}sp_reduction_tax`).hide();
    } else {
      $(`${selectorPrefix}sp_reduction_tax`).show();
    }
  }

  /**
   * Reset 'add specific price' form values
   * using previously stored default values
   *
   * @private
   */
  private resetCreatePriceFormDefaultValues(): void {
    const previouslyStoredValues = this.$createPriceFormDefaultValues;

    $('#specific_price_form')
      .find('input')
      .each((index, value) => {
        $(value).val(previouslyStoredValues[<string>$(value).attr('id')]);
      });

    $('#specific_price_form')
      .find('select')
      .each((index, value) => {
        $(value)
          .val(previouslyStoredValues[<string>$(value).attr('id')])
          .change();
      });

    $('#specific_price_form')
      .find('input:checkbox')
      .each((index, value) => {
        $(value).prop('checked', true);
      });
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  private enableSpecificPriceFieldIfEligible(
    usePrefixForCreate: boolean,
  ): void {
    const selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $(`${selectorPrefix}sp_price`)
      .prop('disabled', $(`${selectorPrefix}leave_bprice`).is(':checked'))
      .val('');
  }

  /**
   * Open 'edit specific price' form into a modal
   *
   * @param integer specificPriceId
   *
   * @private
   */
  private openEditPriceModalAndLoadForm(specificPriceId: string): void {
    const url = $('#js-specific-price-list')
      .data('actionEdit')
      .replace(/form\/\d+/, `form/${specificPriceId}`);

    $('#edit-specific-price-modal').modal('show');
    this.editModalIsOpen = true;

    $.ajax({
      type: 'GET',
      url,
    })
      .done((response) => {
        this.insertEditSpecificPriceFormIntoModal(response);
        $('#edit-specific-price-modal-form').data(
          'specificPriceId',
          specificPriceId,
        );
        this.configureEditPriceFormInsideModalBehavior();
      })
      .fail((errors) => {
        window.showErrorMessage(errors.responseJSON);
      });
  }

  /**
   * @private
   */
  private closeEditPriceModalAndRemoveForm(): void {
    $('#edit-specific-price-modal').modal('hide');
    this.editModalIsOpen = false;

    const formLocationHolder = $('#edit-specific-price-modal-form');

    formLocationHolder.empty();
  }

  /**
   * @param string form: HTML 'edit specific price' form
   *
   * @private
   */
  insertEditSpecificPriceFormIntoModal(form: HTMLElement): void {
    const formLocationHolder = $('#edit-specific-price-modal-form');

    formLocationHolder.empty();
    formLocationHolder.append(form);
  }

  /**
   * Get product ID for current Catalog Product page
   *
   * @returns integer
   *
   * @private
   */
  private getProductId(): string | number | string[] | undefined {
    return $('#form_id_product').val();
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @returns string
   *
   * @private
   */
  private getPrefixSelector(usePrefixForCreate: boolean): string {
    if (usePrefixForCreate) {
      return `#${this.prefixCreateForm}`;
    }

    return `#${this.prefixEditForm}`;
  }
}

export default SpecificPriceFormHandler;
