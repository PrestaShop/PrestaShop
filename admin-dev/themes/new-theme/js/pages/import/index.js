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

$(() => {
  const entityCategories = 0;
  const entityProducts = 1;
  const entityCombinations = 2;
  const entityCustomers = 3;
  const entityAddresses = 4;
  const entityBrands = 5;
  const entitySuppliers = 6;
  const entityAlias = 7;
  const entityStoreContacts = 8;

  $('.js-entity-select').on('change', toggleForm);
  toggleForm();

  function toggleForm() {
    let $selctedOption = $('#form_import_entity').find('option:selected');
    let selectedEntity = parseInt($selctedOption.val());
    let entityName = $selctedOption.text().toLowerCase();

    toggleEntityAlert(selectedEntity);
    toggleFields(selectedEntity, entityName);
    loadAvailableFields(selectedEntity);
  }

  /**
   * Toggle alert warning for selected import entity
   *
   * @param {int} selectedEntity
   */
  function toggleEntityAlert(selectedEntity) {
    let $alert = $('.js-entity-alert');

    if ([entityCategories, entityProducts].includes(selectedEntity)) {
      $alert.show()
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
  function toggleFields(selectedEntity, entityName) {
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

    if ([entityCategories,
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

    if ([entityCategories,
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
  function loadAvailableFields(entity) {
    $.ajax({
      url: '../../../ajax.php',
      data: {
        getAvailableFields:1,
        entity: entity
      },
      dataType: 'json',
    }).then(response => {
      let fields = '';
      let $availableFields = $('.js-available-fields');
      $availableFields.empty();

      for (let i = 0; i < response.length; i++) {
        fields += response[i].field;
      }

      $availableFields.html(fields);
      $availableFields.find('[data-toggle="popover"]').popover();
    }).catch(error => {

    });
  }
});
