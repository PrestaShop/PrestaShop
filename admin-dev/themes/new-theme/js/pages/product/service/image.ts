/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    method: 'POST',
    data: {
      ...data,
      _method: 'PATCH',
    },
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
    method: 'POST',
    data: {
      ...data,
      _method: 'PATCH',
    },
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
