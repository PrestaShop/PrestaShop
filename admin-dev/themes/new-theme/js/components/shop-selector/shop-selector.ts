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

import ClickEvent = JQuery.ClickEvent;

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
    $(ComponentsMap.shopSelector.container).each((index, container) => {
      const $container = $(container);
      const isMultiple = $container.data('multiple');

      if (isMultiple) {
        const $shopSelector = $(ComponentsMap.shopSelector.selectInput, $container).first();
        const initialShops: string[] = <string[]> $shopSelector.val() || [];
        $shopSelector.data('initialShops', initialShops.join(','));
      }
    });

    $(document).on('click', ComponentsMap.shopSelector.shopItem, (event: ClickEvent) => {
      const $clickedShop: JQuery = $(event.currentTarget);
      const $container = $clickedShop.parents(ComponentsMap.shopSelector.container).first();
      const $shopSelector = $(ComponentsMap.shopSelector.selectInput, $container).first();
      const isMultiple = $container.data('multiple');

      if (isMultiple) {
        this.selectMultipleShops($container, $shopSelector);
      } else {
        this.selectSingleShop($clickedShop, $shopSelector);
      }
    });
  }

  private selectSingleShop($selectedShop: JQuery, $shopSelector: JQuery): void {
    $(ComponentsMap.shopSelector.shopItem).removeClass(ComponentsMap.shopSelector.selectedClass);
    $selectedShop.addClass(ComponentsMap.shopSelector.selectedClass);

    $shopSelector.val($selectedShop.data('shopId')).trigger('change');
  }

  private selectMultipleShops($container: JQuery, $shopSelector: JQuery): void {
    const $shops: JQuery = $(ComponentsMap.shopSelector.shopItem, $container);
    const selectedShops: string[] = [];
    const initialShops: string[] = $shopSelector.data('initialShops').split(',').map((shopId: string) => parseInt(shopId, 10));

    $shops.each((index, shopItem) => {
      const $shopItem: JQuery = $(shopItem);

      if ($shopItem.hasClass(ComponentsMap.shopSelector.currentClass)) {
        selectedShops.push($shopItem.data('shopId'));
        return;
      }

      const $shopStatus: JQuery = $(ComponentsMap.shopSelector.shopStatus, $shopItem);
      const $checkbox:JQuery = $('input', $shopItem);
      const initiallySelected: boolean = initialShops.includes($shopItem.data('shopId'));

      if ($checkbox.is(':checked')) {
        selectedShops.push($shopItem.data('shopId'));
        $shopItem.toggleClass(ComponentsMap.shopSelector.selectedClass, !initiallySelected);
        $shopStatus.html(initiallySelected ? '' : $shopStatus.data('addedLabel'));
      } else {
        $shopStatus.html(initiallySelected ? $shopStatus.data('removedLabel') : '');
        $shopItem.toggleClass(ComponentsMap.shopSelector.selectedClass, initiallySelected);
      }
    });

    $shopSelector.val(selectedShops).trigger('change');
  }
}
