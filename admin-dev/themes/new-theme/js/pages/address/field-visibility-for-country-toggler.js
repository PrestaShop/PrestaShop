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

import Router from '@components/router';

const $ = window.$;

/**
 * Shows/hides dynamic fields in customer addresses page depending from country address format settings
 */
export default class FieldVisibilityForCountryToggler {
  constructor(countryInputSelector, fieldSelectorsByName) {
    this.$countryInput = $(countryInputSelector);
    this.fieldSelectorsByName = fieldSelectorsByName;
    this.router = new Router();

    this.$countryInput.on('change', () => this.toggle());

    return {};
  }

  /**
   * Change State selection
   *
   * @private
   */
  toggle() {
    const countryId = this.$countryInput.val();

    if (countryId === '') {
      return;
    }
    $.get(this.router.generate('admin_addresses_fields_for_country', {countryId})).then((response) => {
      Object.keys(this.fieldSelectorsByName).forEach((fieldName) => {
        const $fieldSelector = $(this.fieldSelectorsByName[fieldName]);

        if (response.fields.includes(fieldName)) {
          $fieldSelector.removeClass('d-none');
        } else {
          $fieldSelector.addClass('d-none');
        }
      });
    }).catch((response) => {
      if (typeof response.responseJSON !== 'undefined') {
        showErrorMessage(response.responseJSON.message);
      }
    });
  }
}
