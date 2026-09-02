<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
      <thead>
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
      </thead>
      <tbody>
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
      </tbody>
    </table>
  </div>
</template>
