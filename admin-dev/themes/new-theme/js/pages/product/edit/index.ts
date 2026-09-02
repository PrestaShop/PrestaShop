/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import NavbarHandler from '@components/navbar-handler';
import ProductMap from '@pages/product/product-map';
import ProductConst from '@pages/product/constants';

import AttachmentsManager from '@pages/product/edit/manager/attachments-manager';
import CategoriesManager from '@pages/product/edit/manager/categories-manager';
import CombinationsList from '@pages/product/combination/combination-list';
import CustomizationsManager from '@pages/product/edit/manager/customizations-manager';
import FeatureValuesManager from '@pages/product/edit/manager/feature-values-manager';
import ProductFooterManager from '@pages/product/edit/manager/product-footer-manager';
import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductModulesManager from '@pages/product/edit/manager/product-modules-manager';
import ProductPartialUpdater from '@pages/product/edit/product-partial-updater';
import ProductSEOManager from '@pages/product/edit/manager/product-seo-manager';
import ProductShopsModal from '@pages/product/shop/product-shops-modal';
import ProductTypeSwitcher from '@pages/product/edit/product-type-switcher';
import VirtualProductManager from '@pages/product/edit/manager/virtual-product-manager';
import RelatedProductsManager from '@pages/product/edit/manager/related-products-manager';
import PackedProductsManager from '@pages/product/edit/manager/packed-products-manager';
import SpecificPricesManager from '@pages/product/edit/manager/specific-prices-manager';
import initDropzone from '@pages/product/image/dropzone';
import initImagesShopAssociation from '@pages/product/image/images-shop-association';
import PriceSummary from '@pages/product/edit/price-summary';
import ProductOptionsManager from '@pages/product/edit/manager/product-options-manager';
import ProductShippingManager from '@pages/product/edit/manager/product-shipping-manager';
import ProductDetailsManager from '@pages/product/edit/manager/product-details-manager';
import SummaryQuantityUpdater from '@pages/product/edit/summary-quantity-updater';
import initCarrierSelector from '@pages/product/carrier';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
    'DeltaQuantityInput',
    'ModifyAllShopsCheckbox',
    'DisablingSwitch',
  ]);

  const $productForm = $(ProductMap.productForm);
  const productId = parseInt($productForm.data('productId'), 10);
  const shopId = parseInt($productForm.data('shopId'), 10);
  const productType = $productForm.data('productType');
  const {eventEmitter} = window.prestashop.instance;

  // Init product model along with input watching and syncing
  const productFormModel: ProductFormModel = new ProductFormModel($productForm, eventEmitter);

  if (productType === ProductConst.PRODUCT_TYPE.COMBINATIONS) {
    // Combinations manager must be initialized BEFORE nav handler, or it won't trigger the pagination if the tab is
    // selected on load
    new CombinationsList(productId, productFormModel, shopId);
    //  quantity dynamically updates only in combination list
    new SummaryQuantityUpdater(eventEmitter, productId, shopId);
  }

  const navbar = new NavbarHandler($(ProductMap.navigationBar));

  // When combination page is opened on quantity tab we automatically switch to the combination one which replaces it for product with combinations
  if (productType === ProductConst.PRODUCT_TYPE.COMBINATIONS && navbar.getHashTarget() === ProductMap.stock.navigationTarget) {
    navbar.switchToTarget(ProductMap.combinations.navigationTarget);
  }

  new ProductSEOManager(eventEmitter);
  new ProductOptionsManager(productType, productFormModel);
  new ProductShippingManager();

  // Product type has strong impact on the page rendering so when it is modified it must be submitted right away
  new ProductTypeSwitcher($productForm);

  // try-catch block prevents javascript termination when error is thrown.
  // So only the related component won't work instead of breaking whole product page
  try {
    new CategoriesManager(eventEmitter);
  } catch (e: any) {
    console.error('Failed to initialize categories manager');
  }

  new ProductFooterManager();
  new ProductModulesManager();
  new RelatedProductsManager(eventEmitter);
  if (productType === ProductConst.PRODUCT_TYPE.PACK) {
    new PackedProductsManager(eventEmitter);
  }
  new PriceSummary(productFormModel);

  new ProductPartialUpdater(
    eventEmitter,
    $productForm,
  );

  // From here we init component specific to edition
  initDropzone(ProductMap.dropzoneImagesContainer);
  initImagesShopAssociation(ProductMap.manageShopImagesButtonContainer, shopId);

  if (productType !== ProductConst.PRODUCT_TYPE.VIRTUAL) {
    initCarrierSelector(ProductMap.shipping.carrierSelectorContainer, eventEmitter);
  }

  new FeatureValuesManager(eventEmitter);
  new CustomizationsManager();
  new AttachmentsManager();
  new SpecificPricesManager(productId);
  new ProductDetailsManager();

  if (productType === ProductConst.PRODUCT_TYPE.VIRTUAL) {
    new VirtualProductManager(productFormModel);
  }

  new ProductShopsModal();
});
