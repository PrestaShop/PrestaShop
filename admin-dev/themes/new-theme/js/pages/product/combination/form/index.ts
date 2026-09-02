/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ImageSelector from '@pages/product/combination/form/image-selector';
import CombinationMap from '@pages/product/combination/form/combination-map';
import CombinationFormModel from '@pages/product/combination/form/combination-form-model';
import FeatureValuesManager from '@pages/product/edit/manager/feature-values-manager';
import ProductMap from '@pages/product/product-map';
import ProductSuppliersCollection from '@pages/product/supplier/product-suppliers-collection';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
    'DeltaQuantityInput',
    'DisablingSwitch',
    'ModifyAllShopsCheckbox',
  ]);

  const $combinationForm: JQuery = $(CombinationMap.combinationForm);
  const {eventEmitter} = window.prestashop.instance;
  // Init combination model along with input watching and syncing
  const combinationFormModel = new CombinationFormModel($combinationForm, eventEmitter);

  new ProductSuppliersCollection(
    CombinationMap.suppliers.productSuppliers,
    combinationFormModel.getCombination().suppliers.defaultSupplierId,
    combinationFormModel.getCombination().price.wholesalePrice,
  );
  new ImageSelector();
  // Same widget as the product one, scoped to the combination form (so it doesn't interfere with
  // the product widget) and using the combination selector map + a custom-event change notifier.
  new FeatureValuesManager(eventEmitter, {
    map: ProductMap.combinationFeatureValues,
    container: $combinationForm,
    deleteModalId: 'modal-confirm-delete-combination-feature-value',
    onChange: () => document.dispatchEvent(new Event('combinationFeatureValuesChange')),
  });
});
