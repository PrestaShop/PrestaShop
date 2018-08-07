/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

/**
 * $@todo: need to migrate deleted legacy JS behaviors:
 * - https://github.com/PrestaShop/PrestaShop/blob/develop/admin-dev/themes/default/js/bundle/product/form.js#L186
 * - https://github.com/PrestaShop/PrestaShop/blob/develop/admin-dev/themes/default/js/bundle/product/form.js#L191
 * - https://github.com/PrestaShop/PrestaShop/blob/develop/admin-dev/themes/default/js/bundle/product/form.js#L557
 */
class SpecificPriceFormHandler {
  constructor() {
    this.$createPriceFormDefaultValues = new Object();
    this.storePriceFormDefaultValues();

    this.loadAndDisplayExistingSpecificPricesList();

    this.configureAddPriceFormBehavior();

    this.configureEditPriceModalBehavior();

    this.configureDeletePriceButtonsBehavior();
  }

  loadAndDisplayExistingSpecificPricesList() {
    var listContainer = $('#js-specific-price-list');
    var url = listContainer.attr('data').replace(/list\/\d+/, 'list/' + this.getProductId());

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
      var row = self.renderSpecificPriceRow(specificPrice, deleteUrl)

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
        '</tr>';

    return row;
  }

  configureAddPriceFormBehavior() {

    $('#specific_price_form .js-cancel').click(() => {
      this.resetCreatePriceFormDefaultValues();
      $('#specific_price_form').collapse('hide');
    });

    $('#specific_price_form .js-save').on('click', () => this.submitCreatePriceForm());

    this.loadAndFillOptionsForSelectCombinationInput();

    $('#form_step2_specific_price_sp_reduction_type').change(() => this.showSpecificPriceTaxFieldIfEligible());

    $('#form_step2_specific_price_leave_bprice').on('click', () => this.enableSpecificPriceFieldIfEligible());

    $('#form_step2_specific_price_sp_reduction_type').on('change', () => this.enableSpecificPriceTaxFieldIfEligible());
  }

  configureEditPriceModalBehavior() {
    // to be implemented
  }

  configureDeletePriceButtonsBehavior() {
    $(document).on('click', '#js-specific-price-list .js-delete', (event) => {
      event.preventDefault();
      this.deleteSpecificPrice(event.currentTarget);
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

  loadAndFillOptionsForSelectCombinationInput() {
    var inputField = $('#form_step2_specific_price_sp_id_product_attribute');
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
   * Get product ID for current Catalog Product page
   *
   * @returns integer
   */
  getProductId() {
    return $('#form_id_product').val();
  }

  showSpecificPriceTaxFieldIfEligible() {
    if ($('#form_step2_specific_price_sp_reduction_type').val() === 'percentage') {
      $('#form_step2_specific_price_sp_reduction_tax').hide();
    } else {
      $('#form_step2_specific_price_sp_reduction_tax').show();
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

  enableSpecificPriceFieldIfEligible() {
    $('#form_step2_specific_price_sp_price').prop('disabled', $('#form_step2_specific_price_leave_bprice').is(':checked')).val('');
  }

  enableSpecificPriceTaxFieldIfEligible() {
    const uglySelect2Selector = $('#select2-form_step2_specific_price_sp_reduction_tax-container').parent().parent();

    if ($('#form_step2_specific_price_sp_reduction_type').val() === 'amount') {
      uglySelect2Selector.show();
    } else {
      uglySelect2Selector.hide();
    }
  }
}

export default SpecificPriceFormHandler;
