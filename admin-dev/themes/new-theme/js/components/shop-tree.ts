/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const ShopTreeMap = {
  groups: '#shop-tree .js-shop-tree-group',
  collapseAllButton: '#shop-tree .js-shop-tree-collapse-all',
  expandAllButton: '#shop-tree .js-shop-tree-expand-all',
};

export default function initShopTree(): void {
  const groups = $(ShopTreeMap.groups);

  $(ShopTreeMap.collapseAllButton).on('click', () => groups.collapse('hide'));
  $(ShopTreeMap.expandAllButton).on('click', () => groups.collapse('show'));
}
