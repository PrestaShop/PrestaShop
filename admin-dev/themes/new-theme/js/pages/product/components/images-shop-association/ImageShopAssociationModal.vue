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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<script lang="ts" setup>
  import Modal from '@PSVue/components/Modal.vue';
  import {ref} from 'vue';
  import {getProductImages, getProductShopImages, updateProductShopImages} from '@pages/product/services/images';
  import ImageShopGrid from '@pages/product/components/images-shop-association/ImageShopGrid.vue';
  import ProductEventMap from '@pages/product/product-event-map';

  const DropzoneEvents = ProductEventMap.dropzone;

  const modalOpened = ref(false);
  const loadingAssociations = ref(false);
  const submittingAssociations = ref(false);

  const productImages = ref<ProductImage[]>([]);
  const productShops = ref<ProductShop[]>([]);

  interface ImagesShopAssociationProps {
    productId: number;
  }
  const props = defineProps<ImagesShopAssociationProps>();

  function closeModal(): void {
    modalOpened.value = false;
  }
  function openModal(): void {
    if (modalOpened.value) {
      return;
    }

    modalOpened.value = true;
    loadAssociations();
  }

  async function loadAssociations(): Promise<void> {
    if (loadingAssociations.value) {
      return;
    }

    loadingAssociations.value = true;

    // Get data from APIs
    const shopImagesResponse = await getProductShopImages(props.productId);
    const shopImages = await shopImagesResponse.json();
    await updateImages(shopImages);

    loadingAssociations.value = false;
  }

  async function saveAssociations(): Promise<void> {
    if (submittingAssociations.value) {
      return;
    }

    submittingAssociations.value = true;

    // Formatted data for each image contains the list of associated shops IDs
    const formattedAssociations = productImages.value.map((productImage: ProductImage) => ({
      imageId: productImage.imageId,
      shops: productImage.associations
        .filter((association: ProductShopImage) => association.isAssociated)
        .map((association: ProductShopImage) => association.shopId),
    }));

    const newImagesResponse = await updateProductShopImages(props.productId, formattedAssociations);
    const newImages = await newImagesResponse.json();

    if (newImages.status === false) {
      $.growl.error({message: newImages.message});
    } else {
      await updateImages(newImages);
      window.prestashop.instance.eventEmitter.emit(DropzoneEvents.resetDropzone);
    }

    submittingAssociations.value = false;
  }

  async function updateImages(shopImages: any[]): Promise<void> {
    // Reformat data for product images
    const newProductImages: ProductImage[] = [];
    const images = await getProductImages(props.productId);
    images.forEach((productImage: any) => {
      const shopAssociations = shopImages.map((productShopImage: any) => {
        let isAssociated = false;
        let isCover = false;
        productShopImage.images.forEach((shopAssociation: any) => {
          if (shopAssociation.imageId === productImage.image_id) {
            isAssociated = true;
            // eslint-disable-next-line prefer-destructuring
            isCover = shopAssociation.isCover;
          }
        });

        return {shopId: productShopImage.shopId, isAssociated, isCover};
      });

      newProductImages.push({
        imageId: productImage.image_id,
        thumbnailUrl: productImage.thumbnail_url,
        associations: shopAssociations,
      });
    });

    // Reformat shops
    const newShops: ProductShop[] = [];
    shopImages.forEach((shop: any) => {
      newShops.push({
        shopId: shop.shopId,
        shopName: shop.shopName,
      });
    });

    // Update references
    productShops.value = newShops;
    productImages.value = newProductImages;
  }
</script>

<template>
  <div>
    <button
      type="button"
      class="btn-outline-secondary manage-shop-images-button btn btn"
      @click.stop="openModal"
    >
      {{ $t('button.label') }}
    </button>
    <modal
      id="images-shop-association-modal"
      v-if="modalOpened"
      :modal-title="$t('button.label')"
      @close="closeModal"
    >
      <template #body>
        <div
          class="images-shop-association-loading"
          v-if="loadingAssociations || productImages.length <= 0"
        >
          <div
            v-if="loadingAssociations"
            class="spinner"
          />
          <div v-else>
            {{ $t('modal.noImages') }}
          </div>
        </div>
        <image-shop-grid
          v-if="!loadingAssociations && productImages.length > 0"
          :product-images="productImages"
          :product-shops="productShops"
        />
      </template>

      <template #footer>
        <button
          type="button"
          class="btn btn-secondary btn-close"
          @click.prevent.stop="closeModal"
          :aria-label="$t('modal.close')"
        >
          {{ $t('modal.cancel') }}
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="saveAssociations"
          :aria-label="$t('modal.save')"
          :disabled="loadingAssociations || submittingAssociations || productImages.length <= 0"
        >
          <span v-if="!submittingAssociations">
            {{ $t('modal.save') }}
          </span>
          <span
            class="spinner-border spinner-border-sm"
            v-if="submittingAssociations"
            role="status"
            aria-hidden="true"
          />
        </button>
      </template>
    </modal>
  </div>
</template>
