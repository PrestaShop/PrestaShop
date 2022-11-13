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

import NavbarHandler from '@components/navbar-handler';
import ProductMap from '@pages/product/product-map';
import ProductConst from '@pages/product/constants';

import AttachmentsManager from '@pages/product/edit/attachments-manager';
import CategoriesManager from '@pages/product/components/categories/categories-manager';
import CombinationsList from '@pages/product/components/combination-list';
import CustomizationsManager from '@pages/product/edit/customizations-manager';
import FeatureValuesManager from '@pages/product/edit/feature-values-manager';
import ProductFooterManager from '@pages/product/edit/product-footer-manager';
import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductModulesManager from '@pages/product/edit/product-modules-manager';
import ProductPartialUpdater from '@pages/product/edit/product-partial-updater';
import ProductSEOManager from '@pages/product/edit/product-seo-manager';
import ProductTypeSwitcher from '@pages/product/edit/product-type-switcher';
import VirtualProductManager from '@pages/product/edit/virtual-product-manager';
import RelatedProductsManager from '@pages/product/edit/related-products-manager';
import PackedProductsManager from '@pages/product/edit/packed-products-manager';
import CreateProductModal from '@pages/product/components/create-product-modal';
import SpecificPricesManager from '@pages/product/edit/specific-prices-manager';
import initDropzone from '@pages/product/components/dropzone';
import initTabs from '@pages/product/components/nav-tabs';
import PriceSummary from '@pages/product/edit/price-summary';
import ProductOptionsManager from '@pages/product/edit/product-options-manager';
import ProductShippingManager from '@pages/product/edit/product-shipping-manager';
import ProductSpecificationsManager from '@pages/product/edit/product-specifications-manager';

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
  const productType = $productForm.data('productType');
  const {eventEmitter} = window.prestashop.instance;

  // Init product model along with input watching and syncing
  const productFormModel: ProductFormModel = new ProductFormModel($productForm, eventEmitter);

  if (productType === ProductConst.PRODUCT_TYPE.COMBINATIONS) {
    // Combinations manager must be initialized BEFORE nav handler, or it won't trigger the pagination if the tab is
    // selected on load
    new CombinationsList(productId, productFormModel);
  }

  // Responsive navigation tabs
  initTabs();
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
  new CategoriesManager(eventEmitter);
  new ProductFooterManager();
  new ProductModulesManager();
  new RelatedProductsManager(eventEmitter);
  if (productType === ProductConst.PRODUCT_TYPE.PACK) {
    new PackedProductsManager(eventEmitter);
  }
  new CreateProductModal();
  new PriceSummary(productFormModel);

  const $productFormSubmitButton = $(ProductMap.productFormSubmitButton);
  const $productFormPreviewButton = $(ProductMap.footer.previewUrlButton);
  const $productFormDuplicateButton = $(ProductMap.footer.duplicateProductButton);
  const $productFormNewProductButton = $(ProductMap.footer.newProductButton);
  const $productFormGoToCatalogButton = $(ProductMap.footer.goToCatalogButton);
  const $productFormCancelButton = $(ProductMap.footer.cancelButton);

  new ProductPartialUpdater(
    eventEmitter,
    $productForm,
    $productFormSubmitButton,
    $productFormPreviewButton,
    $productFormDuplicateButton,
    $productFormNewProductButton,
    $productFormGoToCatalogButton,
    $productFormCancelButton,
  );

  // From here we init component specific to edition
  initDropzone(ProductMap.dropzoneImagesContainer);

  new FeatureValuesManager(eventEmitter);
  new CustomizationsManager();
  new AttachmentsManager();
  new SpecificPricesManager(productId);
  new ProductSpecificationsManager();

  if (productType === ProductConst.PRODUCT_TYPE.VIRTUAL) {
    new VirtualProductManager(productFormModel);
  }
});
