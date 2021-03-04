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

const {$} = window;

/**
 * Back office translations type
 *
 * @type {string}
 */
const back = 'back';

/**
 * Modules translations type
 * @type {string}
 */
const themes = 'themes';

/**
 * Modules translations type
 * @type {string}
 */
const modules = 'modules';

/**
 * Mails translations type
 * @type {string}
 */
const mails = 'mails';

/**
 * Other translations type
 * @type {string}
 */
const others = 'others';

/**
 * Email body translations type
 * @type {string}
 */
const emailContentBody = 'body';

export default class FormFieldToggle {
  constructor() {
    $('.js-translation-type').on('change', this.toggleFields.bind(this));
    $('.js-email-content-type').on('change', this.toggleEmailFields.bind(this));

    this.toggleFields();
  }

  /**
   * Toggle dependant translations fields, based on selected translation type
   */
  toggleFields() {
    const selectedOption = $('.js-translation-type').val();
    const $modulesFormGroup = $('.js-module-form-group');
    const $emailFormGroup = $('.js-email-form-group');
    const $themesFormGroup = $('.js-theme-form-group');
    const $themesSelect = $themesFormGroup.find('select');
    const $noThemeOption = $themesSelect.find('.js-no-theme');
    const $firstThemeOption = $themesSelect.find('option:not(.js-no-theme):first');

    switch (selectedOption) {
      case back:
      case others:
        this.hide($modulesFormGroup, $emailFormGroup, $themesFormGroup);
        break;

      case themes:
        if ($noThemeOption.is(':selected')) {
          $themesSelect.val($firstThemeOption.val());
        }

        this.hide($modulesFormGroup, $emailFormGroup, $noThemeOption);
        this.show($themesFormGroup);
        break;

      case modules:
        this.hide($emailFormGroup, $themesFormGroup);
        this.show($modulesFormGroup);
        break;

      case mails:
        this.hide($modulesFormGroup, $themesFormGroup);
        this.show($emailFormGroup);
        break;

      default:
        break;
    }

    this.toggleEmailFields();
  }

  /**
   * Toggles fields, which are related to email translations
   */
  toggleEmailFields() {
    if ($('.js-translation-type').val() !== mails) {
      return;
    }

    const selectedEmailContentType = $('.js-email-form-group').find('select').val();
    const $themesFormGroup = $('.js-theme-form-group');
    const $noThemeOption = $themesFormGroup.find('.js-no-theme');

    if (selectedEmailContentType === emailContentBody) {
      $noThemeOption.prop('selected', true);
      this.show($noThemeOption, $themesFormGroup);
    } else {
      this.hide($noThemeOption, $themesFormGroup);
    }
  }


  /**
   * Make all given selectors hidden
   *
   * @param $selectors
   * @private
   */
  hide(...$selectors) {
    Object.values($selectors).forEach((el) => {
      el.addClass('d-none');
      el.find('select').prop('disabled', 'disabled');
    });
  }

  /**
   * Make all given selectors visible
   *
   * @param $selectors
   * @private
   */
  show(...$selectors) {
    Object.values($selectors).forEach((el) => {
      el.removeClass('d-none');
      el.find('select').prop('disabled', false);
    });
  }
}
