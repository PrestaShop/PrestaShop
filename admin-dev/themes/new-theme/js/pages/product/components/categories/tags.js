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

import ProductMap from '@pages/product/product-map';

const ProductCategoryMap = ProductMap.categories;

export default class Tags {
  constructor(
    containerSelector,
    initialCategories,
  ) {
    this.container = document.querySelector(containerSelector);
    this.refresh(initialCategories);
  }

  refresh(categories) {
    this.toggle();
    this.container.innerHTML = '';

    const tagTemplate = this.container.dataset.prototype;

    categories.forEach((category) => {
      const template = tagTemplate.replace(RegExp(this.container.dataset.prototypeName, 'g'), category.id);
      const frag = document.createRange().createContextualFragment(template.trim());
      frag.firstChild.querySelector(ProductCategoryMap.tagItem).innerHTML = category.name;
      this.container.append(frag);
    }, this);
    this.listenDelete();
  }

  toggle() {
    this.container.querySelector(ProductCategoryMap.tagsContainer);
    this.container.classList.toggle(
      'd-block',
      this.container.querySelector(ProductCategoryMap.tagItem),
    );
  }

  listenDelete() {
    this.container.querySelectorAll('.pstaggerClosingCross').forEach((element) => {
      element.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();

        // const categoryId = Number(event.currentTarget.dataset.id);

        //@todo it shouldnt be possible to delete defautl category
        event.currentTarget.closest('.pstaggerTag').remove();
      });
    }, this);
  }
}
