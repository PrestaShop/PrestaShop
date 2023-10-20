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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/* eslint-disable max-len */

import Router from '@components/router';

const router = new Router();
const {$} = window;

export const getProductImages = async (productId: number, shopId: number): Promise<JQuery.jqXHR<any>> => {
  const imagesUrl = router.generate('admin_products_images_for_shop', {
    productId,
    shopId,
  });

  return $.get(imagesUrl);
};

export const getProductShopImages = async (productId: number): Promise<Response> => fetch(router.generate('admin_products_product_shop_images', {productId}));
export const updateProductShopImages = async (productId: number, imageAssociations: any): Promise<Response> => {
  const formData = new FormData();
  formData.append('image_associations', JSON.stringify(imageAssociations));

  return fetch(
    router.generate('admin_products_product_shop_images', {productId}),
    {
      method: 'POST',
      body: formData,
    },
  );
};

export const saveImageInformations = async (
  selectedFile: Record<string, any>,
  token: string,
  formName: string,
  shopId: number|null,
): Promise<JQuery.jqXHR<any>> => {
  const saveUrl = router.generate('admin_products_update_image', {
    productImageId: selectedFile.image_id,
  });

  const data: Record<string, any> = {};
  data[`${formName}[is_cover]`] = selectedFile.is_cover ? 1 : 0;
  Object.keys(selectedFile.legends).forEach((langId) => {
    data[`${formName}[legend][${langId}]`] = selectedFile.legends[langId];
  });
  data[`${formName}[_token]`] = token;
  data[`${formName}[shop_id]`] = shopId || 0;
  data[`${formName}[apply_to_all_stores]`] = selectedFile.applyToAllStores;

  return $.ajax(saveUrl, {
    method: 'PATCH',
    data,
  });
};

export const replaceImage = async (selectedFile: Record<string, any>, newFile: Blob, formName: string, token: string): Promise<JQuery.jqXHR<any>> => {
  const replaceUrl = router.generate('admin_products_update_image', {
    productImageId: selectedFile.image_id,
  });

  const formData = new FormData();
  formData.append(`${formName}[file]`, newFile);
  formData.append(`${formName}[_token]`, token);
  formData.append('_method', 'PATCH');

  return $.ajax(replaceUrl, {
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
  });
};

export const saveImagePosition = async (
  productImageId: number,
  newPosition: number,
  formName: string,
  token: string,
  shopId: number|null,
): Promise<JQuery.jqXHR<any>> => {
  const sortUrl = router.generate('admin_products_update_image', {
    productImageId,
  });

  const data: Record<string, any> = {};
  data[`${formName}[position]`] = newPosition;
  data[`${formName}[_token]`] = token;
  data[`${formName}[shop_id]`] = shopId || 0;

  return $.ajax(sortUrl, {
    method: 'PATCH',
    data,
  });
};

export const removeProductImage = async (productImageId: string): Promise<JQuery.jqXHR<any>> => {
  const deleteUrl = router.generate('admin_products_delete_image', {
    productImageId,
  });

  return $.post(deleteUrl);
};

export default {
  getProductImages,
  saveImageInformations,
  replaceImage,
  saveImagePosition,
  removeProductImage,
};
