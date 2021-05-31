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
 * Class is responsible for import match configuration
 * in Advanced parameters -> Import -> step 2 form.
 */
export default class ImportMatchConfiguration {
  /**
   * Initializes all the processes related with import matches.
   */
  constructor() {
    this.loadEvents();
  }

  /**
   * Loads all events for data match configuration.
   */
  loadEvents(): void {
    $(document).on(
      'click',
      '.js-save-import-match',
      (event: JQueryEventObject) => this.save(event),
    );
    $(document).on(
      'click',
      '.js-load-import-match',
      (event: JQueryEventObject) => this.load(event),
    );
    $(document).on(
      'click',
      '.js-delete-import-match',
      (event: JQueryEventObject) => this.delete(event),
    );
  }

  /**
   * Save the import match configuration.
   */
  save(event: JQueryEventObject): void {
    event.preventDefault();
    const ajaxUrl = $('.js-save-import-match').attr('data-url');
    const formData = $('.import-data-configuration-form').serialize();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
    }).then((response: Record<string, any>) => {
      if (typeof response.errors !== 'undefined' && response.errors.length) {
        this.showErrorPopUp(response.errors);
      } else if (response.matches.length > 0) {
        const $dataMatchesDropdown = this.matchesDropdown;

        Object.values(<Record<string, any>>response.matches).forEach(
          (resp: Record<string, any>) => {
            const $existingMatch = $dataMatchesDropdown.find(
              `option[value=${resp.id_import_match}]`,
            );

            // If match already exists with same id - do nothing
            if ($existingMatch.length > 0) {
              return;
            }

            // Append the new option to the matches dropdown
            this.appendOptionToDropdown(
              $dataMatchesDropdown,
              resp.name,
              resp.id_import_match,
            );
          },
        );
      }
    });
  }

  /**
   * Load the import match.
   */
  load(event: JQueryEventObject): void {
    event.preventDefault();
    const ajaxUrl = $('.js-load-import-match').attr('data-url');

    $.ajax({
      type: 'GET',
      url: ajaxUrl,
      data: {
        import_match_id: this.matchesDropdown.val(),
      },
    }).then((response) => {
      if (response) {
        this.rowsSkipInput.val(response.skip);

        const entityFields = response.match.split('|');
        Object.keys(entityFields).forEach((i) => {
          $(`#type_value_${i}`).val(entityFields[i]);
        });
      }
    });
  }

  /**
   * Delete the import match.
   */
  delete(event: JQueryEventObject): void {
    event.preventDefault();
    const ajaxUrl = $('.js-delete-import-match').attr('data-url');
    const $dataMatchesDropdown = this.matchesDropdown;
    const selectedMatchId = $dataMatchesDropdown.val();

    $.ajax({
      type: 'DELETE',
      url: ajaxUrl,
      data: {
        import_match_id: selectedMatchId,
      },
    }).then(() => {
      // Delete the match option from matches dropdown
      $dataMatchesDropdown.find(`option[value=${selectedMatchId}]`).remove();
    });
  }

  /**
   * Appends a new option to given dropdown.
   *
   * @param {jQuery} $dropdown
   * @param {String} optionText
   * @param {String} optionValue
   * @private
   */
  private appendOptionToDropdown(
    $dropdown: JQuery,
    optionText: string,
    optionValue: string,
  ) {
    const $newOption = $('<option>');

    $newOption.attr('value', optionValue);
    $newOption.text(optionText);

    $dropdown.append($newOption);
  }

  /**
   * Shows error messages in the native error pop-up.
   *
   * @param {Array} errors
   * @private
   */
  private showErrorPopUp(errors: Array<string>): void {
    alert(errors);
  }

  /**
   * Get the matches dropdown.
   *
   * @returns {*|HTMLElement}
   */
  get matchesDropdown(): JQuery {
    return $('#matches');
  }

  /**
   * Get the "rows to skip" input.
   *
   * @returns {*|HTMLElement}
   */
  get rowsSkipInput(): JQuery {
    return $('#skip');
  }
}
