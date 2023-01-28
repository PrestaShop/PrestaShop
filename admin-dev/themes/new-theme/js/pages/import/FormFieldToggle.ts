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

  toggleForm(): void {
    const selectedOption = $('#entity').find('option:selected');
    const selectedEntity = parseInt(<string>selectedOption.val(), 10);
    const entityName = selectedOption.text().toLowerCase();

    this.toggleEntityAlert(selectedEntity);
    this.toggleFields(selectedEntity, entityName);
    this.loadAvailableFields(selectedEntity);
  }

  /**
   * Toggle alert warning for selected import entity
   *
   * @param {int} selectedEntity
   */
  toggleEntityAlert(selectedEntity: number): void {
    const $alert = $('.js-entity-alert');

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
  toggleFields(selectedEntity: number, entityName: string): void {
    const $truncateFormGroup = $('.js-truncate-form-group');
    const $matchRefFormGroup = $('.js-match-ref-form-group');
    const $regenerateFormGroup = $('.js-regenerate-form-group');
    const $forceIdsFormGroup = $('.js-force-ids-form-group');
    const $entityNamePlaceholder = $('.js-entity-name');

    if (entityStoreContacts === selectedEntity) {
      $truncateFormGroup.hide();
      $truncateFormGroup.find('input[name="truncate"]').first().trigger('click');
    } else {
      $truncateFormGroup.show();
    }

    if ([entityProducts, entityCombinations].includes(selectedEntity)) {
      $matchRefFormGroup.show();
    } else {
      $matchRefFormGroup.hide();
    }

    if (
      [
        entityCategories,
        entityProducts,
        entityBrands,
        entitySuppliers,
        entityStoreContacts,
      ].includes(selectedEntity)
    ) {
      $regenerateFormGroup.show();
    } else {
      $regenerateFormGroup.hide();
    }

    if (
      [
        entityCategories,
        entityProducts,
        entityCustomers,
        entityAddresses,
        entityBrands,
        entitySuppliers,
        entityStoreContacts,
        entityAlias,
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
  loadAvailableFields(entity: number): void {
    const $availableFields = $('.js-available-fields');

    $.ajax({
      url: $availableFields.data('url'),
      data: {
        entity,
      },
      dataType: 'json',
    }).then((response) => {
      this.removeAvailableFields($availableFields);

      for (let i = 0; i < response.length; i += 1) {
        this.appendAvailableField(
          $availableFields,
          response[i].label + (response[i].required ? '*' : ''),
          response[i].description,
        );
      }

      $availableFields.find('[data-toggle="popover"]').popover();
    });
  }

  /**
   * Remove available fields content from given container.
   *
   * @param {jQuery} $container
   * @private
   */
  private removeAvailableFields($container: JQuery): void {
    $container.find('[data-toggle="popover"]').popover('hide');
    $container.empty();
  }

  /**
   * Append a help box to given field.
   *
   * @param {jQuery} $field
   * @param {String} helpBoxContent
   * @private
   */
  private appendHelpBox($field: JQuery, helpBoxContent: string): void {
    const $helpBox = $('.js-available-field-popover-template').clone();

    $helpBox.attr('data-content', helpBoxContent);
    $helpBox.removeClass('js-available-field-popover-template d-none');
    $field.append($helpBox);
  }

  /**
   * Append available field to given container.
   *
   * @param {jQuery} $appendTo field will be appended to this container.
   * @param {String} fieldText
   * @param {String} helpBoxContent
   * @private
   */
  private appendAvailableField(
    $appendTo: JQuery,
    fieldText: string,
    helpBoxContent: string,
  ): void {
    const $field = $('.js-available-field-template').clone();

    $field.text(fieldText);

    if (helpBoxContent) {
      // Append help box next to the field
      this.appendHelpBox($field, helpBoxContent);
    }

    $field.removeClass('js-available-field-template d-none');
    $field.appendTo($appendTo);
  }
}
