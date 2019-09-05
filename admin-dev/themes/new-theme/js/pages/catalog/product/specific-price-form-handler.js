/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

class SpecificPriceFormHandler {

  constructor() {
    this.prefixCreateForm = 'form_step2_specific_price_';
    this.prefixEditForm = 'form_modal_';
    this.editModalIsOpen = false;

    this.$createPriceFormDefaultValues = new Object();
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
  loadAndDisplayExistingSpecificPricesList() {
    var listContainer = $('#js-specific-price-list');
    var url = listContainer.data('listingUrl').replace(/list\/\d+/, 'list/' + this.getProductId());

    $.ajax({
      type: 'GET',
      url: url,
    })
        .done(specificPrices => {
          var tbody = listContainer.find('tbody');
          tbody.find('tr').remove();

          if (specificPrices.length > 0) {
            listContainer.removeClass('hide');
          } else {
            listContainer.addClass('hide');
          }

          var specificPricesList = this.renderSpecificPricesListingAsHtml(specificPrices);

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
  renderSpecificPricesListingAsHtml(specificPrices) {
    var specificPricesList = '';

    var self = this;

    $.each(specificPrices, (index, specificPrice) => {
      var deleteUrl = $('#js-specific-price-list').attr('data-action-delete').replace(/delete\/\d+/, 'delete/' + specificPrice.id_specific_price);
      var row = self.renderSpecificPriceRow(specificPrice, deleteUrl);

      specificPricesList = specificPricesList + row;
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
  renderSpecificPriceRow(specificPrice, deleteUrl) {

    var specificPriceId = specificPrice.id_specific_price;

    var row = '<tr>' +
        '<td>' + specificPrice.rule_name + '</td>' +
        '<td>' + specificPrice.attributes_name + '</td>' +
        '<td>' + specificPrice.currency + '</td>' +
        '<td>' + specificPrice.country + '</td>' +
        '<td>' + specificPrice.group + '</td>' +
        '<td>' + specificPrice.customer + '</td>' +
        '<td>' + specificPrice.fixed_price + '</td>' +
        '<td>' + specificPrice.impact + '</td>' +
        '<td>' + specificPrice.period + '</td>' +
        '<td>' + specificPrice.from_quantity + '</td>' +
        '<td>' + (specificPrice.can_delete ? '<a href="' + deleteUrl + '" class="js-delete delete btn tooltip-link delete pl-0 pr-0"><i class="material-icons">delete</i></a>' : '') + '</td>' +
        '<td>' + (specificPrice.can_edit ? '<a href="#" data-specific-price-id="' + specificPriceId + '" class="js-edit edit btn tooltip-link delete pl-0 pr-0"><i class="material-icons">edit</i></a>' : '') + '</td>' +
        '</tr>';

    return row;
  }

  /**
   * @private
   */
  configureAddPriceFormBehavior() {
    const usePrefixForCreate = true;
    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $('#specific_price_form .js-cancel').click(() => {
      this.resetCreatePriceFormDefaultValues();
      $('#specific_price_form').collapse('hide');
    });

    $('#specific_price_form .js-save').on('click', () => this.submitCreatePriceForm());

    $('#js-open-create-specific-price-form').on('click', () => this.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate));

    $(selectorPrefix + 'leave_bprice').on('click', () => this.enableSpecificPriceFieldIfEligible(usePrefixForCreate));

    $(selectorPrefix + 'sp_reduction_type').on('change', () => this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate));
  }

  /**
   * @private
   */
  configureEditPriceFormInsideModalBehavior() {
    const usePrefixForCreate = false;
    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $('#form_modal_cancel').click(() => this.closeEditPriceModalAndRemoveForm());
    $('#form_modal_close').click(() => this.closeEditPriceModalAndRemoveForm());

    $('#form_modal_save').click(() => this.submitEditPriceForm());

    this.loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate);

    $(selectorPrefix + 'leave_bprice').on('click', () => this.enableSpecificPriceFieldIfEligible(usePrefixForCreate));

    $(selectorPrefix + 'sp_reduction_type').on('change', () => this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate));

    this.reinitializeDatePickers();

    this.initializeLeaveBPriceField(usePrefixForCreate);
    this.enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate);
  }

  /**
   * @private
   */
  reinitializeDatePickers() {
    $('.datepicker input').datetimepicker({format: 'YYYY-MM-DD'});
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  initializeLeaveBPriceField(usePrefixForCreate) {
    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    if ($(selectorPrefix + 'sp_price').val() != '') {
      $(selectorPrefix + 'sp_price').prop('disabled', false);
      $(selectorPrefix + 'leave_bprice').prop('checked', false);
    }
  }

  /**
   * @private
   */
  configureEditPriceModalBehavior() {
    $(document).on('click', '#js-specific-price-list .js-edit', (event) => {
      event.preventDefault();

      var specificPriceId = $(event.currentTarget).data('specificPriceId');

      this.openEditPriceModalAndLoadForm(specificPriceId);
    });

  }

  /**
   * @private
   */
  configureDeletePriceButtonsBehavior() {
    $(document).on('click', '#js-specific-price-list .js-delete', (event) => {
      event.preventDefault();
      this.deleteSpecificPrice(event.currentTarget);
    });
  }

  /**
   * @see https://vijayasankarn.wordpress.com/2017/02/24/quick-fix-scrolling-and-focus-when-multiple-bootstrap-modals-are-open/
   */
  configureMultipleModalsBehavior() {
    $('.modal').on('hidden.bs.modal', () => {
      if (this.editModalIsOpen) {
        $('body').addClass('modal-open');
      }
    });
  }

  /**
   * @private
   */
  submitCreatePriceForm() {

    const url = $('#specific_price_form').attr('data-action');
    const data = $('#specific_price_form input, #specific_price_form select, #form_id_product').serialize();

    $('#specific_price_form .js-save').attr('disabled', 'disabled');

    $.ajax({
      type: 'POST',
      url: url,
      data: data,
    })
        .done(response => {
          showSuccessMessage(translate_javascripts['Form update success']);
          this.resetCreatePriceFormDefaultValues();
          $('#specific_price_form').collapse('hide');
          this.loadAndDisplayExistingSpecificPricesList();

          $('#specific_price_form .js-save').removeAttr('disabled');

        })
        .fail(errors => {
          showErrorMessage(errors.responseJSON);

          $('#specific_price_form .js-save').removeAttr('disabled');
        });
  }

  /**
   * @private
   */
  submitEditPriceForm() {
    const baseUrl = $('#edit-specific-price-modal-form').attr('data-action');
    const specificPriceId = $('#edit-specific-price-modal-form').data('specificPriceId');
    const url = baseUrl.replace(/update\/\d+/, 'update/' + specificPriceId);

    const data = $('#edit-specific-price-modal-form input, #edit-specific-price-modal-form select, #form_id_product').serialize();

    $('#edit-specific-price-modal-form .js-save').attr('disabled', 'disabled');

    $.ajax({
      type: 'POST',
      url: url,
      data: data,
    })
        .done(response => {
          showSuccessMessage(translate_javascripts['Form update success']);
          this.closeEditPriceModalAndRemoveForm();
          this.loadAndDisplayExistingSpecificPricesList();
          $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
        })
        .fail(errors => {
          showErrorMessage(errors.responseJSON);

          $('#edit-specific-price-modal-form .js-save').removeAttr('disabled');
        });
  }

  /**
   * @param string clickedLink selector
   *
   * @private
   */
  deleteSpecificPrice(clickedLink) {
    modalConfirmation.create(translate_javascripts['This will delete the specific price. Do you wish to proceed?'], null, {
      onContinue: () => {

        var url = $(clickedLink).attr('href');
        $(clickedLink).attr('disabled', 'disabled');

        $.ajax({
          type: 'GET',
          url: url,
        })
            .done(response => {
              this.loadAndDisplayExistingSpecificPricesList();
              showSuccessMessage(response);
              $(clickedLink).removeAttr('disabled');
            })
            .fail(errors => {
              showErrorMessage(errors.responseJSON);
              $(clickedLink).removeAttr('disabled');

            });
      }
    }).show();
  }

  /**
   * Store 'add specific price' form values
   * for future usage
   *
   * @private
   */
  storePriceFormDefaultValues() {
    var storage = this.$createPriceFormDefaultValues;

    $('#specific_price_form').find('select,input').each((index, value) => {
      storage[$(value).attr('id')] = $(value).val();
    });

    $('#specific_price_form').find('input:checkbox').each((index, value) => {
      storage[$(value).attr('id')] = $(value).prop('checked');
    });

    this.$createPriceFormDefaultValues = storage;
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  loadAndFillOptionsForSelectCombinationInput(usePrefixForCreate) {

    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    var inputField = $(selectorPrefix + 'sp_id_product_attribute');
    var url = inputField.attr('data-action').replace(/product-combinations\/\d+/, 'product-combinations/' + this.getProductId());

    $.ajax({
      type: 'GET',
      url: url,
    })
        .done(combinations => {
          /** remove all options except first one */
          inputField.find('option:gt(0)').remove();

          $.each(combinations, (index, combination) => {
            inputField.append('<option value="' + combination.id + '">' + combination.name + '</option>');
          });

          if (inputField.data('selectedAttribute') != '0') {
            inputField.val(inputField.data('selectedAttribute')).trigger('change');
          }
        });
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  enableSpecificPriceTaxFieldIfEligible(usePrefixForCreate) {

    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    if ($(selectorPrefix + 'sp_reduction_type').val() === 'percentage') {
      $(selectorPrefix + 'sp_reduction_tax').hide();
    } else {
      $(selectorPrefix + 'sp_reduction_tax').show();
    }
  }

  /**
   * Reset 'add specific price' form values
   * using previously stored default values
   *
   * @private
   */
  resetCreatePriceFormDefaultValues() {
    var previouslyStoredValues = this.$createPriceFormDefaultValues;

    $('#specific_price_form').find('input').each((index, value) => {
      $(value).val(previouslyStoredValues[$(value).attr('id')]);
    });

    $('#specific_price_form').find('select').each((index, value) => {
      $(value).val(previouslyStoredValues[$(value).attr('id')]).change();
    });

    $('#specific_price_form').find('input:checkbox').each((index, value) => {
      $(value).prop("checked", true);
    });
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @private
   */
  enableSpecificPriceFieldIfEligible(usePrefixForCreate) {
    var selectorPrefix = this.getPrefixSelector(usePrefixForCreate);

    $(selectorPrefix + 'sp_price').prop('disabled', $(selectorPrefix + 'leave_bprice').is(':checked')).val('');
  }

  /**
   * Open 'edit specific price' form into a modal
   *
   * @param integer specificPriceId
   *
   * @private
   */
  openEditPriceModalAndLoadForm(specificPriceId) {
    const url = $('#js-specific-price-list').data('actionEdit').replace(/form\/\d+/, 'form/' + specificPriceId);

    $('#edit-specific-price-modal').modal("show");
    this.editModalIsOpen = true;

    $.ajax({
      type: 'GET',
      url: url,
    })
        .done(response => {
          this.insertEditSpecificPriceFormIntoModal(response);
          $('#edit-specific-price-modal-form').data('specificPriceId', specificPriceId);
          this.configureEditPriceFormInsideModalBehavior();
        })
        .fail(errors => {
          showErrorMessage(errors.responseJSON);
        });
  }

  /**
   * @private
   */
  closeEditPriceModalAndRemoveForm() {
    $('#edit-specific-price-modal').modal("hide");
    this.editModalIsOpen = false;

    var formLocationHolder = $('#edit-specific-price-modal-form');

    formLocationHolder.empty();
  }

  /**
   * @param string form: HTML 'edit specific price' form
   *
   * @private
   */
  insertEditSpecificPriceFormIntoModal(form) {
    var formLocationHolder = $('#edit-specific-price-modal-form');

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
  getProductId() {
    return $('#form_id_product').val();
  }

  /**
   * @param boolean usePrefixForCreate
   *
   * @returns string
   *
   * @private
   */
  getPrefixSelector(usePrefixForCreate) {
    if (usePrefixForCreate == true) {
      return '#' + this.prefixCreateForm;
    } else {
      return '#' + this.prefixEditForm;
    }
  }
}

export default SpecificPriceFormHandler;
