/**
 * 2007-2019 PrestaShop SA and Contributors
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

import taxRulesGroupFormMap from './tax-rules-groups-form-map';

/**
 * Class responsible for javascript actions Tax rules edit form.
 */
export default class TaxRulesGroupForm {
  constructor() {
    this.taxRulesFormAction = $(taxRulesGroupFormMap.taxRuleForm).attr('action');
    this._initTaxRuleForm();
    this._initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    $(taxRulesGroupFormMap.countrySelect).on('change', () => this._handleCountryChange());
    $(taxRulesGroupFormMap.editTaxRuleLink).on('click', (event) => this._handleEditTaxRuleClick(event));
    $(taxRulesGroupFormMap.addTaxRuleBtn).on('click', (event) => this._handleAddTaxRuleClick(event));
  }

  /**
   * Checks conditions and initiates tax rule form if they are met
   *
   *  @private
   */
  _initTaxRuleForm() {
    if (this.taxRulesFormAction.trim()) {
      this._showTaxRulesForm();

      return;
    }

    const isListEmpty = $(taxRulesGroupFormMap.taxRulesGrid).find('.grid-table-empty').length;

    if (isListEmpty) {
      this._showTaxRulesForm(
        $(taxRulesGroupFormMap.addLink).data('load-path'),
        $(taxRulesGroupFormMap.addLink).data('edit-path'));
    }
  }

  /**
   * Hide state select if country doesnt have states, show it otherwise and fill with data.
   *
   * @private
   */
  _handleCountryChange() {
    const $countryDropdown = $(taxRulesGroupFormMap.countrySelect);

    const getCountryStateUrl = $countryDropdown.data('states-url');
    const countryId = $countryDropdown.val();

    if (countryId > 0) {
      $.ajax({
        url: getCountryStateUrl,
        method: 'GET',
        dataType: 'json',
        data: {
          id_country: countryId,
        },
      }).then((response) => {
        this._resolveStateSelectVisibility(response.states);
      }).catch((response) => {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);
        }
      });
    } else {
      if ($(taxRulesGroupFormMap.stateFormRow).is(':visible')) {
        this._resolveStateSelectVisibility([]);
      }
    }
  }

  /**
   * Handles edit tax rule button click to add form action and tax rule values
   *
   * @param event
   *
   * @private
   */
  _handleEditTaxRuleClick(event) {
    event.preventDefault();

    const editLink = $(event.target).closest('a').data('edit-path');

    if (editLink === this.taxRulesFormAction) {
      this._hideTaxRulesForm();

      return;
    }

    const loadLink = $(event.target).closest('a').data('load-path');

    this._showTaxRulesForm(editLink, loadLink);
  }

  /**
   * Handles add new tax rule button click to show form with predefined field values
   *
   * @param event
   *
   * @private
   */
  _handleAddTaxRuleClick(event) {
    event.preventDefault();
    const addLink = $(taxRulesGroupFormMap.addLink).attr('href');

    if (addLink === this.taxRulesFormAction) {
      this._hideTaxRulesForm();

      return;
    }

    this._showTaxRulesForm();
  }

  /**
   * Hides tax rule form block
   *
   * @private
   */
  _hideTaxRulesForm() {
    const $taxRuleForm = $(taxRulesGroupFormMap.taxRuleForm);

    if ($taxRuleForm.is(':visible')) {
      $taxRuleForm.fadeOut();
    }

    $taxRuleForm.attr('action', '');
    this.taxRulesFormAction = '';
  }

  /**
   * Shows tax rule form and fills it with ajax response or empty data
   *
   * @param submitLink
   * @param loadEditLink
   * @param loadEmptyData
   *
   * @private
   */
  _showTaxRulesForm(submitLink = $(taxRulesGroupFormMap.addLink).attr('href'), loadEditLink = null) {
    const $taxRuleForm = $(taxRulesGroupFormMap.taxRuleForm);
    const $taxRuleFormHiddenContent = $(taxRulesGroupFormMap.taxRulesHiddenContent);
    $taxRuleFormHiddenContent.hide();

    this._enableSpinner();

    if ($taxRuleForm.hasClass('d-none')) {
      $taxRuleForm.removeClass('d-none');
    }

    $taxRuleForm.fadeIn();

    this.taxRulesFormAction = submitLink;
    $taxRuleForm.attr('action', submitLink);

    if (loadEditLink) {
      $.ajax({
        url: loadEditLink,
        method: 'GET',
        dataType: 'json',
      }).then(response => {
        this._setTaxRuleInformation(response);
        this._disableSpinner();
        $taxRuleFormHiddenContent.show();
      }).catch((response) => {
        if (typeof response.responseJSON !== 'undefined') {
          showErrorMessage(response.responseJSON.message);
          this._disableSpinner();
          this._hideTaxRulesForm();
        }
      });

      return;
    }

    if (!loadEditLink) {
      this._setTaxRuleInformation(null);
    }

    this._disableSpinner();
  }

  /**
   * Resolves visibility of state select and fills it with ajax response data if visible
   *
   * @param states
   * @param fadeSpeed
   *
   * @private
   */
  _resolveStateSelectVisibility(states, fadeSpeed = 400) {
    const $stateFormSelect = $(taxRulesGroupFormMap.stateFormSelect);
    const $stateFormRow = $(taxRulesGroupFormMap.stateFormRow);

    if (states.length === 0) {
      $stateFormRow.fadeOut(fadeSpeed);
      $stateFormSelect.attr('disabled', 'disabled');

      return;
    }

    $stateFormSelect.empty();
    $stateFormSelect.append($('<option></option>').attr('value', 0).text($stateFormSelect.data('all-translation')));
    $.each(states, function (index, value) {
      $stateFormSelect.append($('<option></option>').attr('value', value).text(index));
    });

    if ($stateFormRow.hasClass('d-none')) {
      $stateFormRow.removeClass('d-none');
    }

    $stateFormSelect.removeAttr('disabled');
    $stateFormRow.fadeIn(fadeSpeed);
  }

  /**
   * Removes form errors if new form data is loaded
   *
   * @private
   */
  _removeTaxRuleFormErrors() {
    if ($(taxRulesGroupFormMap.taxRuleForm).find(taxRulesGroupFormMap.taxRuleFormErrorAlert).length) {
      $(taxRulesGroupFormMap.taxRuleForm).find(taxRulesGroupFormMap.taxRuleFormErrorAlert).remove();
    }

    $(taxRulesGroupFormMap.taxRuleInvalidFeedbackContainer).remove();
    $(taxRulesGroupFormMap.taxRuleErrorPopoverContent).remove();
    $.each(taxRulesGroupFormMap.taxRuleFormPopoverErrorContainers, function (index, value) {
      $(value).remove();
    });
  }

  /**
   * Fills tax rule form fields with response data
   *
   * @param data
   *
   * @private
   */
  _setTaxRuleInformation(data) {
    const $countrySelect = $(taxRulesGroupFormMap.countrySelect);

    let taxRule = null;
    let states = [];

    let countryId = 0;
    let stateId = 0;
    let zipCode = 0;
    let behaviorId = 0;
    let taxId = 0;
    let description = '';

    this._removeTaxRuleFormErrors();

    if (data !== null) {
      console.log(data);
      taxRule = data[0];
      states = data[1];

      behaviorId = taxRule.behavior_id.value;
      countryId = taxRule.country_id.value;
      zipCode = taxRule.zip_code;

      stateId = taxRule.state_id === null ? 0 : taxRule.state_id.value;
      taxId = taxRule.tax_id === null ? 0 : taxRule.tax_id.value;
      description = taxRule.description === null ? 0 : taxRule.description;
    }

    $countrySelect.val(countryId);
    this._resolveStateSelectVisibility(states, 0);

    $(taxRulesGroupFormMap.stateFormSelect).val(stateId);
    $(taxRulesGroupFormMap.zipCodeInput).val(zipCode);
    $(taxRulesGroupFormMap.behaviorSelect).val(behaviorId);
    $(taxRulesGroupFormMap.taxSelect).val(taxId);
    $(taxRulesGroupFormMap.descriptionInput).val(description);
  }

  /**
   * Enables loading spinner for tax rule form
   *
   * @private
   */
  _enableSpinner() {
    if (!$('#spinner-block').length) {
      const $taxRuleForm = $(taxRulesGroupFormMap.taxRuleForm);

      $(taxRulesGroupFormMap.taxRulesHiddenContent).hide();
      $taxRuleForm.find('.card').append(this._getSpinnerBlock());
    }
  }

  /**
   * Disables loading spinner for tax rule form
   *
   * @private
   */
  _disableSpinner() {
    if ($('#spinner-block').length) {
      $('#spinner-block').remove();
      $(taxRulesGroupFormMap.taxRulesHiddenContent).fadeIn();
    }
  }

  /**
   * Return html loading spinner element
   *
   * @returns {string}
   *
   * @private
   */
  _getSpinnerBlock() {
    return `<div id="spinner-block" class="card-block row justify-content-center align-self-center">
        <span class="spinner"></span>
    </div>`;
  }
}
