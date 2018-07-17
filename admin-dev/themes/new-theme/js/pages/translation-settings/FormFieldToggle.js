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

const back = 'back';
const themes = 'themes';
const modules = 'modules';
const mails = 'mails';
const others = 'others';
const emailContentBody = 'body';

export default class FormFieldToggle {
    init() {
        $('.js-translation-type').on('change', this.toggleFields.bind(this));
        $('.js-email-content-type').on('change', this.toggleEmailFields.bind(this));

        this.toggleFields();
    }

    /**
     * Toggle dependant translations fields, based on selected translation type
     */
    toggleFields() {
        let selectedOption = $('.js-translation-type').val();
        let $modulesFormGroup = $('.js-module-form-group');
        let $emailFormGroup = $('.js-email-form-group');
        let $themesFormGroup = $('.js-theme-form-group');
        let $themesSelect = $themesFormGroup.find('select');
        let $noThemeOption = $themesSelect.find('.js-no-theme');
        let $firstThemeOption = $themesSelect.find('option:not(.js-no-theme):first');

        switch (selectedOption) {
            case back:
            case others:
                this._hide($modulesFormGroup, $emailFormGroup, $themesFormGroup);

                break;
            case themes:
                if ($noThemeOption.is(':selected')) {
                    $themesSelect.val($firstThemeOption.val());
                }

                this._hide($modulesFormGroup, $emailFormGroup, $noThemeOption);
                this._show($themesFormGroup);

                break;
            case modules:
                this._hide($emailFormGroup, $themesFormGroup);
                this._show($modulesFormGroup);

                break;
            case mails:
                this._hide($modulesFormGroup, $themesFormGroup);
                this._show($emailFormGroup);

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

        let selectedEmailContentType = $('.js-email-form-group').find('select').val();
        let $themesFormGroup = $('.js-theme-form-group');
        let $noThemeOption = $themesFormGroup.find('.js-no-theme');

        if (selectedEmailContentType === emailContentBody) {
            $noThemeOption.prop('selected', true);
            this._show($noThemeOption, $themesFormGroup);
        } else {
            this._hide($noThemeOption, $themesFormGroup);
        }
    }


    /**
     * Make all given selectors hidden
     *
     * @param $selectors
     * @private
     */
    _hide(...$selectors) {
        for (let key in $selectors) {
            $selectors[key].addClass('d-none');
        }
    }

    /**
     * Make all given selectors visible
     *
     * @param $selectors
     * @private
     */
    _show(...$selectors) {
        for (let key in $selectors) {
            $selectors[key].removeClass('d-none');
        }
    }
}
