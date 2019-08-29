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

import addressFormMap from "./address-form-map";

/**
 * Class responsible for javascript actions in customer address add/edit form.
 */
export default class CustomerAddressForm {
  constructor() {
    this.countrySelect = addressFormMap.countrySelect;
    this.stateSelect = addressFormMap.stateSelect;
    this.stateFormRowSelect = addressFormMap.stateFormRowSelect;
    this.emailInput = addressFormMap.customerEmail;

    this.firstName = addressFormMap.firstName;
    this.lastName = addressFormMap.lastName;
    this.company = addressFormMap.company;

    this._initEvents();

    return {};
  }

  /**
   * Initialize page's events.
   *
   * @private
   */
  _initEvents() {
    const $countryDropdown = $(this.countrySelect);
    const $emailInput = $(this.emailInput);

    $countryDropdown.on('change', () => this._handleCountryChange());
    $emailInput.on('blur', (event) => this._handleEmailChange(event));
  }

  /**
   * Hide state select if country doesnt have states, show it otherwise and fill with data.
   *
   * @private
   */
  _handleCountryChange() {
    const countryDropdown = $(this.countrySelect);
    const getCountryStateUrl = countryDropdown.data('states-url');
    const stateDropdown = $(this.stateSelect);
    const stateFormRowSelect = $(this.stateFormRowSelect);

    $.ajax({
      url: getCountryStateUrl,
      method: 'GET',
      dataType: 'json',
      data: {
        id_country: countryDropdown.val(),
      }
    }).then((response) => {
      if (response.states.length === 0) {
        stateFormRowSelect.fadeOut();
        stateDropdown.attr('disabled', 'disabled');

        return;
      }

      stateDropdown.removeAttr('disabled');
      stateFormRowSelect.fadeIn();

      stateDropdown.empty();
      stateDropdown.append($('<option></option>').attr('value', 0).text('-'));
      $.each(response.states, function (index, value) {
        stateDropdown.append($('<option></option>').attr('value', value).text(index));
      });
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }

  /**
   * Handles email change event to get customer data for customer fields
   *
   * @param event
   *
   * @private
   */
  _handleEmailChange(event) {
    const emailInput = $(event.target);
    const getFillCustomerDataUrl = emailInput.data('customer-information-url');
    const email = emailInput.val();

    if (email.length > 5) {
      $.ajax({
        url: getFillCustomerDataUrl,
        data: {
          email: email,
        },
        dataType: 'json',
      }).then(response => {
        this._setCustomerInformation(response);
      });
    }
  }

  /**
   * Fills customer fields with response data
   *
   * @param data
   *
   * @private
   */
  _setCustomerInformation(data) {
    const firstNameSelector = $(this.firstName);
    const lastNameSelector = $(this.lastName);
    const companySelector = $(this.company);

    firstNameSelector.val(data.first_name);
    lastNameSelector.val(data.last_name);

    if (0 > data.company.length) {
      companySelector.val(data.company);
    }
  }
}
