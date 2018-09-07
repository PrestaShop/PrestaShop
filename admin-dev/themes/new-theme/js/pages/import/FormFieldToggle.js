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

const entityCategories = 0;
const entityProducts = 1;
const entityCombinations = 2;
const entityCustomers = 3;
const entityAddresses = 4;
const entityBrands = 5;
const entitySuppliers = 6;
const entityAlias = 7;
const entityStoreContacts = 8;

export default class FormFieldToggle {
  constructor() {
    $('.js-entity-select').on('change', () => this.toggleForm());

    this.toggleForm();
  }

  toggleForm() {
    let selectedOption = $('#entity').find('option:selected');
    let selectedEntity = parseInt(selectedOption.val());
    let entityName = selectedOption.text().toLowerCase();

    this.toggleEntityAlert(selectedEntity);
    this.toggleFields(selectedEntity, entityName);
    this.loadAvailableFields(selectedEntity);
  }

  /**
   * Toggle alert warning for selected import entity
   *
   * @param {int} selectedEntity
   */
  toggleEntityAlert(selectedEntity) {
    let $alert = $('.js-entity-alert');

    if ([entityCategories, entityProducts].includes(selectedEntity)) {
      $alert.show();
    } else {
      $alert.hide();
    }
  }

  /**
   * Toggle available options for selected entity
   *
   * @param {int} selectedEntity
   * @param {string} entityName
   */
  toggleFields(selectedEntity, entityName) {
    const $truncateFormGroup = $('.js-truncate-form-group');
    const $matchRefFormGroup = $('.js-match-ref-form-group');
    const $regenerateFormGroup = $('.js-regenerate-form-group');
    const $forceIdsFormGroup = $('.js-force-ids-form-group');
    const $entityNamePlaceholder = $('.js-entity-name');

    if (entityStoreContacts === selectedEntity) {
      $truncateFormGroup.hide();
    } else {
      $truncateFormGroup.show();
    }

    if ([entityProducts, entityCombinations].includes(selectedEntity)) {
      $matchRefFormGroup.show();
    } else {
      $matchRefFormGroup.hide();
    }

    if ([
      entityCategories,
      entityProducts,
      entityBrands,
      entitySuppliers,
      entityStoreContacts
    ].includes(selectedEntity)
    ) {
      $regenerateFormGroup.show();
    } else {
      $regenerateFormGroup.hide();
    }

    if ([
      entityCategories,
      entityProducts,
      entityCustomers,
      entityAddresses,
      entityBrands,
      entitySuppliers,
      entityStoreContacts,
      entityAlias
    ].includes(selectedEntity)
    ) {
      $forceIdsFormGroup.show();
    } else {
      $forceIdsFormGroup.hide();
    }

    $entityNamePlaceholder.html(entityName);
  }

  /**
   * Load available fields for given entity
   *
   * @param {int} entity
   */
  loadAvailableFields(entity) {
    const $availableFields = $('.js-available-fields');

    $.ajax({
      url: $availableFields.data('url'),
      data: {
        entity: entity
      },
      dataType: 'json',
    }).then(response => {
      // Hide open popovers
      $availableFields.find('[data-toggle="popover"]').popover('hide');
      $availableFields.empty();

      for (let i = 0; i < response.length; i++) {
        let $field = $('.js-available-field-template').clone();
        let fieldText = response[i].label + (response[i].required ? '*' : '');

        $field.text(fieldText);

        if (response[i].description) {
          // Help box next to the field
          let $fieldHelp = $('.js-available-field-popover-template').clone();

          $fieldHelp.attr('data-content', response[i].description);
          $fieldHelp.removeClass('js-available-field-popover-template d-none');
          $field.append($fieldHelp);
        }

        $field.removeClass('js-available-field-template d-none');
        $field.appendTo($availableFields);
      }

      $availableFields.find('[data-toggle="popover"]').popover();
    });
  }
}
