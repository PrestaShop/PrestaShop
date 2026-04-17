/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TranslationSettingsMap from './TranslationSettingsMap';

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
    $(TranslationSettingsMap.translationType).on(
      'change',
      this.toggleFields.bind(this),
    );
    $(TranslationSettingsMap.emailContentType).on(
      'change',
      this.toggleEmailFields.bind(this),
    );

    $(window).on('load', this.toggleFields.bind(this));
  }

  /**
   * Toggle dependant translations fields, based on selected translation type
   */
  toggleFields(): void {
    const selectedOption = $(TranslationSettingsMap.translationType).val();
    const $modulesFormGroup = $(TranslationSettingsMap.modulesFormGroup);
    const $emailFormGroup = $(TranslationSettingsMap.emailFormGroup);
    const $themesFormGroup = $(TranslationSettingsMap.themesFormGroup);
    const $defaultThemeOption = $themesFormGroup.find(
      TranslationSettingsMap.defaultThemeOption,
    );

    switch (selectedOption) {
      case back:
      case others:
        this.hide($modulesFormGroup, $emailFormGroup, $themesFormGroup);
        break;

      case themes:
        this.show($themesFormGroup);
        this.hide($modulesFormGroup, $emailFormGroup, $defaultThemeOption);
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
  toggleEmailFields(): void {
    if ($(TranslationSettingsMap.translationType).val() !== mails) {
      return;
    }

    const selectedEmailContentType = $(TranslationSettingsMap.emailFormGroup)
      .find('select')
      .val();
    const $themesFormGroup = $(TranslationSettingsMap.themesFormGroup);
    const $noThemeOption = $themesFormGroup.find(
      TranslationSettingsMap.noThemeOption,
    );
    const $defaultThemeOption = $themesFormGroup.find(
      TranslationSettingsMap.defaultThemeOption,
    );

    if (selectedEmailContentType === emailContentBody) {
      $noThemeOption.prop('selected', true);
      this.show($noThemeOption, $themesFormGroup, $defaultThemeOption);
    } else {
      this.hide($noThemeOption, $themesFormGroup, $defaultThemeOption);
    }
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
      el.find('select').prop('disabled', 'disabled');
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
      el.find('select').prop('disabled', false);
    });
  }
}
