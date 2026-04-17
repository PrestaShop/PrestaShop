/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TranslationSettingsMap from './TranslationSettingsMap';

const {$} = window;

const $coreType = $(TranslationSettingsMap.exportCoreType);
const $themesType = $(TranslationSettingsMap.exportThemesType);
const $modulesType = $(TranslationSettingsMap.exportModulesType);

const $coreValues = $(TranslationSettingsMap.exportCoreValues).closest(
  '.form-group',
);
const $themesValues = $(TranslationSettingsMap.exportThemesValues).closest(
  '.form-group',
);
const $modulesValues = $(TranslationSettingsMap.exportModulesValues).closest(
  '.form-group',
);

const $coreCheckboxes = $(TranslationSettingsMap.exportCoreValues);
const $themesSelect = $(TranslationSettingsMap.exportThemesValues);
const $modulesSelect = $(TranslationSettingsMap.exportModulesValues);

const $exportButton = $(TranslationSettingsMap.exportLanguageButton);

/**
 * Toggles show/hide for the selectors of subtypes (in case of Core type), theme or module when a Type is selected
 *
 * Example : If Core type is selected, the subtypes checkboxes are shown,
 * Theme and Module types are unselected and their value selector are hidden
 */
export default class ExportFormFieldToggle {
  constructor() {
    $coreType.on('change', this.coreTypeChanged.bind(this));
    $themesType.on('change', this.themesTypeChanged.bind(this));
    $modulesType.on('change', this.modulesTypeChanged.bind(this));

    $coreCheckboxes.on('change', this.subChoicesChanged.bind(this));
    $themesSelect.on('change', this.subChoicesChanged.bind(this));
    $modulesSelect.on('change', this.subChoicesChanged.bind(this));

    this.check($coreType);
  }

  coreTypeChanged(): void {
    if (!$coreType.is(':checked')) {
      return;
    }

    $coreType.prop('disabled', false);
    this.uncheck($themesType, $modulesType);
    this.show($coreValues);
    this.hide($themesValues, $modulesValues);
    this.subChoicesChanged();
  }

  themesTypeChanged(): void {
    if (!$themesType.is(':checked')) {
      return;
    }

    $themesType.prop('disabled', false);
    this.uncheck($coreType, $modulesType);
    this.show($themesValues);
    this.hide($coreValues, $modulesValues);
    this.subChoicesChanged();
  }

  modulesTypeChanged(): void {
    if (!$modulesType.is(':checked')) {
      return;
    }

    $modulesValues.prop('disabled', false);
    this.uncheck($themesType, $coreType);
    this.show($modulesValues);
    this.hide($themesValues, $coreValues);
    this.subChoicesChanged();
  }

  subChoicesChanged(): void {
    if (
      ($coreType.prop('checked')
        && $coreCheckboxes.find(':checked').length > 0)
      || ($themesType.prop('checked') && $themesSelect.val() !== null)
      || ($modulesType.prop('checked') && $modulesSelect.val() !== null)
    ) {
      $exportButton.prop('disabled', false);

      return;
    }

    $exportButton.prop('disabled', true);
  }

  /**
   * Make all given selectors hidden
   *
   * @param $selectors
   * @private
   */
  private hide(...$selectors: Array<JQuery>): void {
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
  show(...$selectors: Array<JQuery>): void {
    Object.values($selectors).forEach((el) => {
      el.removeClass('d-none');
      el.find('select, input').prop('disabled', false);
    });
  }

  /**
   * Make all given selectors unchecked
   *
   * @param $selectors
   * @private
   */
  uncheck(...$selectors: Array<JQuery>): void {
    Object.values($selectors).forEach((el) => {
      el.prop('checked', false);
    });
  }

  /**
   * Make all given selectors checked
   *
   * @param $selectors
   * @private
   */
  check(...$selectors: Array<JQuery>): void {
    Object.values($selectors).forEach((el) => {
      el.prop('checked', true);
    });
  }
}
