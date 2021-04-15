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

import Serp from '@app/utils/serp';
import RedirectOptionManager from '@pages/product/edit/redirect-option-manager';
import ProductSuppliersManager from '@pages/product/edit/product-suppliers-manager';
import FeatureValuesManager from '@pages/product/edit/feature-values-manager';
import CustomizationsManager from '@pages/product/edit/customizations-manager';
import ProductMap from '@pages/product/product-map';
import ProductPartialUpdater from '@pages/product/edit/product-partial-updater';
import NavbarHandler from '@components/navbar-handler';
import CombinationsManager from '@pages/product/edit/combinations-manager';
import ProductTypeManager from '@pages/product/edit/product-type-manager';
import initDropzone from '@pages/product/components/dropzone';
import ProductFormModel from '@pages/product/edit/product-form-model';
import VirtualProductManager from '@pages/product/edit/virtual-product-manager';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
    'EventEmitter',
    'TextWithLengthCounter',
  ]);

  const $productForm = $(ProductMap.productForm);
  const productId = parseInt($productForm.data('productId'), 10);
  const productType = $productForm.data('productType');

  // Init product model along with input watching and syncing
  new ProductFormModel($productForm, window.prestashop.instance.eventEmitter);

  if (productId && productType === ProductMap.productType.COMBINATIONS) {
    // Combinations manager must be initialised before nav handler, or it won't trigger the pagination if the tab is
    // selected on load, but only when productId exists (edition mode)
    new CombinationsManager(productId);
  }
  new NavbarHandler(ProductMap.navigationBar);

  // Init the product/category search field for redirection target
  const $redirectTypeInput = $(ProductMap.redirectOption.typeInput);
  const $redirectTargetInput = $(ProductMap.redirectOption.targetInput);
  new RedirectOptionManager($redirectTypeInput, $redirectTargetInput);

  // Form has no productId data means that we are in creation mode
  if (!productId) {
    return;
  }

  // On creation product type can be modified as you wish, but once it is created it has strong impacts, so we must
  // force submit because it influences the available features in the form, and it also perform cleaning on non relevant
  // associations
  new ProductTypeManager($(ProductMap.productTypeSelector), $productForm);

  // Init Serp component to preview Search engine display
  const translatorInput = window.prestashop.instance.translatableInput;
  new Serp(
    {
      container: '#serp-app',
      defaultTitle: '.serp-default-title:input',
      watchedTitle: '.serp-watched-title:input',
      defaultDescription: '.serp-default-description',
      watchedDescription: '.serp-watched-description',
      watchedMetaUrl: '.serp-watched-url:input',
      multiLanguageInput: `${translatorInput.localeInputSelector}:not(.d-none)`,
      multiLanguageItem: translatorInput.localeItemSelector,
    },
    $('#product_preview').data('seo-url'),
  );

  initDropzone(ProductMap.dropzoneImagesContainer);

  // From here we init component specific to edition
  const $productFormSubmitButton = $(ProductMap.productFormSubmitButton);
  new ProductPartialUpdater(window.prestashop.instance.eventEmitter, $productForm, $productFormSubmitButton).watch();
  new FeatureValuesManager(window.prestashop.instance.eventEmitter);
  new CustomizationsManager();

  if (productType !== ProductMap.productType.COMBINATIONS) {
    new ProductSuppliersManager('#product_suppliers');
  }
  if (productType === ProductMap.productType.VIRTUAL) {
    new VirtualProductManager();
  }
});
