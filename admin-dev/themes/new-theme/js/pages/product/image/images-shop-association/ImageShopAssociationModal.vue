<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<script lang="ts" setup>
  import Modal from '@PSVue/components/Modal.vue';
  import {ref, computed} from 'vue';
  import {getProductImages, getProductShopImages, updateProductShopImages} from '@pages/product/service/image';
  import ImageShopGrid from '@pages/product/image/images-shop-association/ImageShopGrid.vue';
  import ProductEventMap from '@pages/product/product-event-map';

  const DropzoneEvents = ProductEventMap.dropzone;

  const modalOpened = ref(false);
  const loadingAssociations = ref(false);
  const submittingAssociations = ref(false);

  const productImages = ref<ProductImage[]>([]);
  const productShops = ref<ProductShop[]>([]);

  interface ImagesShopAssociationProps {
    productId: number;
    shopId: number;
  }
  const props = defineProps<ImagesShopAssociationProps>();

  const closeModal = (): void => {
    modalOpened.value = false;
  };
  const openModal = (): void => {
    if (modalOpened.value) {
      return;
    }

    modalOpened.value = true;
    loadAssociations();
  };

  const loadAssociations = async (): Promise<void> => {
    if (loadingAssociations.value) {
      return;
    }

    loadingAssociations.value = true;

    // Get data from APIs
    const shopImagesResponse = await getProductShopImages(props.productId);
    const shopImages = await shopImagesResponse.json();
    await updateImages(shopImages);

    loadingAssociations.value = false;
  };

  const saveAssociations = async (): Promise<void> => {
    if (submittingAssociations.value) {
      return;
    }

    submittingAssociations.value = true;

    // Formatted data for each image contains the list of associated shops IDs
    const formattedAssociations = productImages.value.map((productImage: ProductImage) => ({
      imageId: productImage.imageId,
      shops: productImage.associations
        .reduce((filteredShops: number[], association: ProductShopImage) => {
          if (association.isAssociated) {
            filteredShops.push(association.shopId);
          }

          return filteredShops;
        }, []),
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
  };

  const updateImages = async (shopImages: any[]): Promise<void> => {
    // Reformat data for product images
    const newProductImages: ProductImage[] = [];
    const images = await getProductImages(props.productId, props.shopId);
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
  };

  const hasDeletedImages = computed(() => {
    let hasDeletedImage = false;
    productImages.value.forEach((productImage: ProductImage) => {
      let isImageDeleted = true;

      productImage.associations.forEach((association: ProductShopImage) => {
        if (association.isAssociated) {
          isImageDeleted = false;
        }
      });

      if (isImageDeleted) {
        hasDeletedImage = true;
      }
    });

    return hasDeletedImage;
  });
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
    <Teleport to="body">
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
          <div
            class="alert alert-warning"
            role="alert"
            v-if="!loadingAssociations && productImages.length > 0 && hasDeletedImages"
          >
            <p class="alert-text">
              {{ $t('warning.deletedImages') }}
            </p>
          </div>
          <div
            :class="`image-shop-grid-container ${hasDeletedImages ? 'delete-warning' : ''}`"
            v-if="!loadingAssociations && productImages.length > 0"
          >
            <image-shop-grid
              :product-images="productImages"
              :product-shops="productShops"
            />
          </div>
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
    </Teleport>
  </div>
</template>
