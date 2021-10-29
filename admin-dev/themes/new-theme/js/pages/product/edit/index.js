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

import AttachmentsManager from '@pages/product/edit/attachments-manager';
import CategoriesManager from '@pages/product/components/categories/categories-manager';
import CombinationsManager from '@pages/product/edit/combinations-manager';
import CustomizationsManager from '@pages/product/edit/customizations-manager';
import FeatureValuesManager from '@pages/product/edit/feature-values-manager';
import ProductFooterManager from '@pages/product/edit/product-footer-manager';
import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductModulesManager from '@pages/product/edit/product-modules-manager';
import ProductPartialUpdater from '@pages/product/edit/product-partial-updater';
import ProductSEOManager from '@pages/product/edit/product-seo-manager';
import ProductSuppliersManager from '@pages/product/edit/product-suppliers-manager';
import ProductTypeManager from '@pages/product/edit/product-type-manager';
import VirtualProductManager from '@pages/product/edit/virtual-product-manager';

import initDropzone from '@pages/product/components/dropzone';
import initTabs from '@pages/product/components/nav-tabs';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
    'DeltaQuantityInput',
  ]);

  const $productForm = $(ProductMap.productForm);
  const productId = parseInt($productForm.data('productId'), 10);
  const productType = $productForm.data('productType');

  // Responsive navigation tabs
  initTabs();

  const {eventEmitter} = window.prestashop.instance;

  // Init product model along with input watching and syncing
  const productFormModel = new ProductFormModel($productForm, eventEmitter);

  if (productId && productType === ProductMap.productType.COMBINATIONS) {
    // Combinations manager must be initialized BEFORE nav handler, or it won't trigger the pagination if the tab is
    // selected on load, it is only initialized when productId exists though (edition mode)
    new CombinationsManager(productId);
  }

  new NavbarHandler(ProductMap.navigationBar);
  new ProductSEOManager(eventEmitter);

  // Product type has strong impact on the page rendering so when it is modified it must be submitted right away
  new ProductTypeManager($(ProductMap.productTypeSelector), $productForm);
  new CategoriesManager(eventEmitter);
  new ProductFooterManager();
  new ProductModulesManager();

  const $productFormSubmitButton = $(ProductMap.productFormSubmitButton);
  new ProductPartialUpdater(
    eventEmitter,
    $productForm,
    $productFormSubmitButton,
  ).watch();

  // Form has no productId data means that we are in creation mode
  if (!productId) {
    return;
  }

  // From here we init component specific to edition
  initDropzone(ProductMap.dropzoneImagesContainer);

  new FeatureValuesManager(eventEmitter);
  new CustomizationsManager();
  new AttachmentsManager();

  if (productType !== ProductMap.productType.COMBINATIONS) {
    new ProductSuppliersManager(ProductMap.suppliers.productSuppliers, true);
  }
  if (productType === ProductMap.productType.VIRTUAL) {
    new VirtualProductManager(productFormModel);
  }
});
