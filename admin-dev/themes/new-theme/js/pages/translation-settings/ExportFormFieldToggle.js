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

import TranslationSettingsMap from './TranslationSettingsMap';

const {$} = window;

const $coreTypeSelector = $(TranslationSettingsMap.exportCoreTypeSelector);
const $themesTypeSelector = $(TranslationSettingsMap.exportThemesTypeSelector);
const $modulesTypeSelector = $(TranslationSettingsMap.exportModulesTypeSelector);

const $coreValuesSelector = $(TranslationSettingsMap.exportCoreValuesSelector).closest('.form-group');
const $themesValuesSelector = $(TranslationSettingsMap.exportThemesValuesSelector).closest('.form-group');
const $modulesValuesSelector = $(TranslationSettingsMap.exportModulesValuesSelector).closest('.form-group');

export default class ExportFormFieldToggle {
  constructor() {
    $coreTypeSelector.on('change', this.coreTypeChanged.bind(this));
    $themesTypeSelector.on('change', this.themesTypeChanged.bind(this));
    $modulesTypeSelector.on('change', this.modulesTypeChanged.bind(this));

    this.check($coreTypeSelector);
  }

  coreTypeChanged() {
    if (!$coreTypeSelector.is(':checked')) {
      return;
    }

    $coreTypeSelector.prop('disabled', false);
    this.uncheck($themesTypeSelector, $modulesTypeSelector);
    this.show($coreValuesSelector);
    this.hide($themesValuesSelector, $modulesValuesSelector);
  }

  themesTypeChanged() {
    if (!$themesTypeSelector.is(':checked')) {
      return;
    }

    $themesTypeSelector.prop('disabled', false);
    this.uncheck($coreTypeSelector, $modulesTypeSelector);
    this.show($themesValuesSelector);
    this.hide($coreValuesSelector, $modulesValuesSelector);
  }

  modulesTypeChanged() {
    if (!$modulesTypeSelector.is(':checked')) {
      return;
    }

    $modulesValuesSelector.prop('disabled', false);
    this.uncheck($themesTypeSelector, $coreTypeSelector);
    this.show($modulesValuesSelector);
    this.hide($themesValuesSelector, $coreValuesSelector);
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
      el.find('select, input').prop('disabled', 'disabled');
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
      el.find('select, input').prop('disabled', false);
    });
  }

  /**
   * Make all given selectors visible
   *
   * @param $selectors
   * @private
   */
  uncheck(...$selectors) {
    Object.values($selectors).forEach((el) => {
      el.prop('checked', false);
    })
  }

  /**
   * Make all given selectors visible
   *
   * @param $selectors
   * @private
   */
  check(...$selectors) {
    Object.values($selectors).forEach((el) => {
      el.prop('checked', true);
    })
  }
}
