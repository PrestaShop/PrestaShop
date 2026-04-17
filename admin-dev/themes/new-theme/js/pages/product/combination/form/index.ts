/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ImageSelector from '@pages/product/combination/form/image-selector';
import CombinationMap from '@pages/product/combination/form/combination-map';
import CombinationFormModel from '@pages/product/combination/form/combination-form-model';
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
});
