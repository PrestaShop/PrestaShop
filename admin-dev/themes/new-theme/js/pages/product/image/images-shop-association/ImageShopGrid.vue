<!--**
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
 *-->
<script lang="ts" setup>
  interface ImageShopGridProps {
    productImages: ProductImage[];
    productShops: ProductShop[];
  }

  defineProps<ImageShopGridProps>();

  const isImageDelete = (productImage: ProductImage): boolean => {
    let isImageDeleted = true;

    productImage.associations.forEach((association: ProductShopImage) => {
      if (association.isAssociated) {
        isImageDeleted = false;
      }
    });

    return isImageDeleted;
  };
</script>
<template>
  <div>
    <table class="image-shop-grid">
      <tr class="header-row">
        <th>
          {{ $t('grid.imageHeader') }}
        </th>
        <th
          :key="`shop-header${shop.shopId}`"
          v-for="shop in productShops"
        >
          {{ shop.shopName }}
        </th>
      </tr>
      <tr
        :key="`image-row-${productImage.imageId}`"
        v-for="productImage in productImages"
        :class="`${isImageDelete(productImage) ? 'deleted-image' : ''}`"
      >
        <td class="shop-image-cell">
          <img
            class="img-fluid"
            :src="productImage.thumbnailUrl"
          >
        </td>
        <td
          :key="`image-shop-association-${productImage.imageId}_${shopAssociation.shopId}`"
          v-for="shopAssociation in productImage.associations"
        >
          <div :class="`md-checkbox md-checkbox-inline ${shopAssociation.isCover ? 'cover-checkbox' : ''}`">
            <label>
              <input
                :name="`shop_association_${productImage.imageId}_${shopAssociation.shopId}`"
                type="checkbox"
                class="form-check-input"
                v-model="shopAssociation.isAssociated"
                :disabled="shopAssociation.isCover"
              >
              <i class="md-checkbox-control" />
            </label>
          </div>
          <span
            class="cover-label"
            v-if="shopAssociation.isCover"
          >
            {{ $t('cover.label') }}
          </span>
        </td>
      </tr>
    </table>
  </div>
</template>
