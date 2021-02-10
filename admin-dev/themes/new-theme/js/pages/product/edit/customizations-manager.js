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

import ProductMap from '../product-map.js';
import EventMap from '../event-map.js';

const {$} = window;

export default class CustomizationsManager {
  constructor() {
    this.$customizationsContainer = $(ProductMap.customizations.customizationsContainer);
    this.$customizationFieldsList = $(ProductMap.customizations.customizationFieldsList);
    this.eventEmitter = window.prestashop.instance.eventEmitter;

    this.init();
  }

  init() {
    this.$customizationsContainer.on('click', ProductMap.customizations.addCustomizationBtn, () => {
      this.addCustomizationField();
    });
    this.$customizationsContainer.on('click', ProductMap.customizations.removeCustomizationBtn, (e) => {
      this.removeCustomizationField(e);
    });
  }

  addCustomizationField() {
    const prototype = this.$customizationFieldsList.data('prototype');
    const index = this.getIndex();
    const newItem = prototype.replace(new RegExp(ProductMap.customizations.indexPlaceholder, 'g'), this.getIndex());
    this.$customizationFieldsList.append(newItem);
    this.eventEmitter.emit(EventMap.customizations.customizationFieldAdded, {index});
  }

  removeCustomizationField(event) {
    $(event.currentTarget).closest(ProductMap.customizations.customizationFieldItem).remove();
    this.eventEmitter.emit(EventMap.customizations.customizationFieldRemoved);
  }

  getIndex() {
    return this.$customizationFieldsList.find(ProductMap.customizations.customizationFieldItem).length;
  }
}
