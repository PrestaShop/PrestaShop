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
import ComponentsMap from '@components/components-map';

/**
 * Component that handle shop selector, basically a select input customized for better UI.
 * The layout is found in the shop_selector_widget from src/PrestaShopBundle/Resources/views/Admin/TwigTemplateForm/multishop.html.twig
 *
 * The component is configurable, it can be multiple or not:
 * - in single mode the only selected shop is highlighted
 * - in multiple mode you can select several shops, their initial state is also known which allows to update a label to indicate their state Add/Removed
 *
 * In both cases on interaction the related input triggers a change event so that other components can watch it.
 */
export default class ShopSelector {
  constructor() {
    document.querySelectorAll<HTMLElement>(ComponentsMap.shopSelector.container).forEach((container: HTMLElement) => {
      const isMultiple = container.dataset.multiple;

      if (isMultiple) {
        const shopSelectorInput = container.querySelector<HTMLSelectElement>(ComponentsMap.shopSelector.selectInput);

        if (shopSelectorInput) {
          const initialShops: string[] = [];
          Array.from(shopSelectorInput.selectedOptions).forEach((option: HTMLOptionElement) => {
            initialShops.push(option.value);
          });
          shopSelectorInput.dataset.initialShops = initialShops.join(',');
        }
      }
    });

    document.querySelectorAll<HTMLElement>(ComponentsMap.shopSelector.shopItem).forEach((shopItem: HTMLElement) => {
      shopItem.addEventListener('click', (event: MouseEvent) => {
        const clickedShop: HTMLElement = event.currentTarget as HTMLElement;
        const container = clickedShop.closest<HTMLElement>(ComponentsMap.shopSelector.container);

        if (container) {
          const isMultiple = container.dataset.multiple;
          const shopSelectorInput = container.querySelector<HTMLSelectElement>(ComponentsMap.shopSelector.selectInput);

          if (!shopSelectorInput) {
            console.error(`Could not find selector ${ComponentsMap.shopSelector.selectInput}`);
            return;
          }

          if (isMultiple) {
            this.selectMultipleShops(container, shopSelectorInput);
          } else {
            this.selectSingleShop(clickedShop, shopSelectorInput);
          }
        }
      });
    });
  }

  private selectSingleShop(selectedShop: HTMLElement, shopSelectorInput: HTMLSelectElement): void {
    document.querySelectorAll<HTMLElement>(ComponentsMap.shopSelector.shopItem).forEach((shopItem: HTMLElement) => {
      shopItem.classList.remove(ComponentsMap.shopSelector.selectedClass);
    });

    selectedShop.classList.add(ComponentsMap.shopSelector.selectedClass);
    // eslint-disable-next-line no-param-reassign
    shopSelectorInput.value = selectedShop.dataset.shopId ?? '';
    shopSelectorInput.dispatchEvent(new Event('change'));
  }

  private selectMultipleShops(container: HTMLElement, shopSelectorInput: HTMLSelectElement): void {
    const selectedShops: number[] = [];
    const shopData: string = shopSelectorInput.dataset.initialShops ?? '';
    const initialShops: number[] = shopData.split(',').map((shopId: string) => parseInt(shopId, 10));

    container.querySelectorAll<HTMLElement>(ComponentsMap.shopSelector.shopItem).forEach((shopItem: HTMLElement) => {
      const shopId: number = parseInt(shopItem.dataset.shopId ?? '', 10);

      if (Number.isNaN(shopId)) {
        return;
      }

      if (shopItem.classList.contains(ComponentsMap.shopSelector.currentClass)) {
        selectedShops.push(shopId);
        return;
      }

      const shopStatus = shopItem.querySelector<HTMLElement>(ComponentsMap.shopSelector.shopStatus);
      const checkbox = shopItem.querySelector('input');
      const initiallySelected: boolean = initialShops.includes(shopId);

      if (checkbox?.checked) {
        selectedShops.push(shopId);
        shopItem.classList.toggle(ComponentsMap.shopSelector.selectedClass, !initiallySelected);
        if (shopStatus) {
          shopStatus.innerHTML = initiallySelected ? '' : shopStatus?.dataset.addedLabel ?? '';
        }
      } else {
        shopItem.classList.toggle(ComponentsMap.shopSelector.selectedClass, initiallySelected);
        if (shopStatus) {
          shopStatus.innerHTML = initiallySelected ? shopStatus?.dataset.removedLabel ?? '' : '';
        }
      }
    });

    // Finally apply/update the selected choices
    Array.from(shopSelectorInput.options).forEach((option: HTMLOptionElement) => {
      // eslint-disable-next-line no-param-reassign
      option.selected = selectedShops.includes(parseInt(option.value, 10));
    });
    shopSelectorInput.dispatchEvent(new Event('change'));
  }
}
