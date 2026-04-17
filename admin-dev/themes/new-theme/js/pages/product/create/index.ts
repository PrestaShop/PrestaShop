/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
