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
 * Class is responsible for import match configuration
 * in Advanced parameters -> Import -> step 2 form
 */
export default class ImportMatchConfiguration
{
  /**
   * Initializes all the processes related with import matches
   */
  constructor() {
    this.loadEvents();
  }

  /**
   * Loads all events data match configuration
   */
  loadEvents() {
    $(document).on('click', '.js-save-import-match', (event) => this.save(event));
    $(document).on('click', '.js-load-import-match', (event) => this.load(event));
    $(document).on('click', '.js-delete-import-match', (event) => this.delete(event));
  }

  /**
   * Save the import match configuration
   */
  save(event) {
    event.preventDefault();
    const ajaxUrl = $('.js-save-import-match').attr('data-url');
    const formData = $('.import-form').serialize();

    $.ajax({
      type: 'POST',
      url: ajaxUrl,
      data: formData,
    }).then(response => {
      if (typeof response.errors !== 'undefined' && response.errors.length) {
        this._showErrorPopUp(response.errors);
      } else if (response.matches.length > 0){
        let $dataMatchesDropdown = this.matchesDropdown;

        for (let key in response.matches) {
          let $existingMatch = $dataMatchesDropdown.find('option[value=' + response.matches[key].id_import_match + ']');

          // If match already exists with same id - do nothing
          if ($existingMatch.length > 0) {
            continue;
          }

          let $newOption = $('<option>');
          $newOption.attr('value', response.matches[key].id_import_match);
          $newOption.text(response.matches[key].name);

          // Append the new option to the matches dropdown
          $dataMatchesDropdown.append($newOption);
        }
      }
    });
  }

  /**
   * Load the import match
   */
  load(event) {
    event.preventDefault();
    const ajaxUrl = $('.js-load-import-match').attr('data-url');

    $.ajax({
      type: 'GET',
      url: ajaxUrl,
      data: {
        import_match_id: this.matchesDropdown.val()
      },
    }).then(response => {
      if (response) {
        this.rowsSkipInput.val(response.skip);

        let entityFields = response.match.split('|');

        for (let i in entityFields) {
          $('#type_value_' + i).val(entityFields[i]);
        }
      }
    });
  }

  /**
   * Delete the import match
   */
  delete(event) {
    event.preventDefault();
    const ajaxUrl = $('.js-delete-import-match').attr('data-url');
    const $dataMatchesDropdown = this.matchesDropdown;
    const selectedMatchId = $dataMatchesDropdown.val();

    $.ajax({
      type: 'DELETE',
      url: ajaxUrl,
      data: {
        import_match_id: selectedMatchId
      },
    }).then(() => {
        // Delete the match option from matches dropdown
        $dataMatchesDropdown.find('option[value=' + selectedMatchId + ']').remove();
    });
  }

  /**
   * Shows error messages in the native error pop-up
   *
   * @param {Array} errors
   *
   * @private
   */
  _showErrorPopUp(errors) {
    alert(errors);
  }

  /**
   * Get the matches dropdown.
   *
   * @returns {*|HTMLElement}
   */
  get matchesDropdown() {
    return $('#matches');
  }

  /**
   * Get the "rows to skip" input
   *
   * @returns {*|HTMLElement}
   */
  get rowsSkipInput() {
    return $('#rows_skip');
  }
}
