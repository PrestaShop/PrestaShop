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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import ProductTypeSelector from '@pages/product/create/product-type-selector';
import ProductMap from '@pages/product/product-map';
import ComponentsMap from '@components/components-map';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'ShopSelector',
  ]);

  const shopSelectorInput = document.querySelector<HTMLSelectElement>(ComponentsMap.shopSelector.selectInput);
  const shopSelectorGroup = shopSelectorInput?.closest<HTMLElement>('.form-group');

  // If multi shop is enabled the shop selector will be present
  if (shopSelectorGroup) {
    // Hide all other form groups and only show the shop selector first
    const formGroups = document.querySelectorAll<HTMLElement>(`${ProductMap.create.createFieldId} > .form-group`);
    formGroups.forEach((formGroup: HTMLElement) => {
      formGroup.classList.add('d-none');
    });
    shopSelectorGroup.classList.remove('d-none');

    // As soon as a shop is selected show the rest of the form
    shopSelectorInput?.addEventListener('change', () => {
      formGroups.forEach((formGroup: HTMLElement) => {
        formGroup.classList.remove('d-none');
      });
      shopSelectorGroup.classList.add('d-none');
    });
  }

  new ProductTypeSelector(ProductMap.create.createModalSelector);
});
