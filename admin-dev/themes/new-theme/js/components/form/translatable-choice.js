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

const {$} = window;

/**
 * Component responsible for filtering select values by language selected.
 */
export default class TranslatableChoice {
  constructor() {
    // registers the event which displays the popover
    $(document).on('change', 'select.translatable_choice_language', (event) => {
      this.filterSelect(event);
    });

    $('select.translatable_choice_language').trigger('change');
  }

  filterSelect(event) {
    const $element = $(event.currentTarget);
    const $formGroup = $element.closest('.form-group');
    const language = $element.find('option:selected').val();

    // show all the languages selects
    $formGroup.find(`select.translatable_choice[data-language="${language}"]`).parent().show();

    const $selects = $formGroup.find('select.translatable_choice');

    // Hide all the selects not corresponding to the language selected
    $selects.not(`select.translatable_choice[data-language="${language}"]`).each((index, item) => {
      $(item).parent().hide();
    });

    // Bind choice selection to fill the hidden input
    this.bindValueSelection($selects);
  }

  bindValueSelection($selects) {
    $selects.each((index, element) => {
      $(element).on('change', (event) => {
        const $select = $(event.currentTarget);
        const selectId = $select.attr('id');
        $(`#${selectId}_value`).val($select.find('option:selected').val());
      });
    });
  }
}

