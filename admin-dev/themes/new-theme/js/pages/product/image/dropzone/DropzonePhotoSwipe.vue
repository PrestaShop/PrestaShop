<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    class="pswp"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
  >
    <div class="pswp__bg" />

    <div class="pswp__scroll-wrap">
      <div class="pswp__container">
        <div class="pswp__item" />
        <div class="pswp__item" />
        <div class="pswp__item" />
      </div>

      <div class="pswp__ui pswp__ui--hidden">
        <div class="pswp__top-bar">
          <div class="pswp__counter" />

          <button
            type="button"
            class="pswp__button pswp__button--close"
            :title="$t('window.closePhotoSwipe')"
          >
            <i class="material-icons">close</i>
          </button>

          <button
            type="button"
            class="pswp__button pswp__button--share"
            :title="$t('window.download')"
          >
            <i class="material-icons">file_download</i>
          </button>

          <button
            type="button"
            class="pswp__button pswp__button--fs"
            :title="$t('window.toggleFullscreen')"
          >
            <i class="material-icons">fullscreen</i>
          </button>

          <button
            type="button"
            class="pswp__button pswp__button--zoom"
            :title="$t('window.zoomPhotoSwipe')"
          >
            <i class="material-icons">zoom_in</i>
          </button>

          <div class="pswp__preloader">
            <div class="pswp__preloader__icn">
              <div class="pswp__preloader__cut">
                <div class="pswp__preloader__donut" />
              </div>
            </div>
          </div>
        </div>

        <div
          class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"
        >
          <div class="pswp__share-tooltip" />
        </div>

        <button
          type="button"
          class="pswp__button pswp__button--arrow--left"
          :title="$t('window.previousPhotoSwipe')"
        >
          <i class="material-icons rtl-flip">arrow_back</i>
        </button>

        <button
          type="button"
          class="pswp__button pswp__button--arrow--right"
          :title="$t('window.nextPhotoSwipe')"
        >
          <i class="material-icons rtl-flip">arrow_forward</i>
        </button>

        <div class="pswp__caption">
          <div class="pswp__caption__center" />
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import PhotoSwipe from 'photoswipe';
  import PhotoSwipeUIDefault from 'photoswipe/dist/photoswipe-ui-default';
  import ProductMap from '@pages/product/product-map';
  import ProductEventMap from '@pages/product/product-event-map';
  import {defineComponent} from 'vue';

  const PhotoSwipeMap = ProductMap.dropzone.photoswipe;
  const PhotoSwipeEventMap = ProductEventMap.dropzone.photoswipe;

  export default defineComponent({
    name: 'DropzonePhotoSwipe',
    props: {
      files: {
        type: Array,
        default: () => [],
      },
    },
    mounted() {
      const pswpElement = <HTMLElement> document.querySelector(PhotoSwipeMap.element);

      if (pswpElement) {
        const options = {
          index: 0,
          shareButtons: [
            {
              id: 'download',
              label: this.$t('window.downloadImage'),
              url: '{{raw_image_url}}',
              download: true,
            },
          ],
        };

        // This is needed to make our files compatible for photoswipe
        const items = this.files.map((file) => {
          const newFile = <Record<string, any>> file;
          newFile.src = newFile.dataURL;
          newFile.h = newFile.height;
          newFile.w = newFile.width;

          return newFile;
        });

        const gallery = new PhotoSwipe(
          pswpElement,
          PhotoSwipeUIDefault,
          items,
          <PhotoSwipe.Options> options,
        );

        gallery.init();

        // We must tell to the rich component that the gallery have been closed
        gallery.listen(PhotoSwipeEventMap.destroy, () => {
          this.$emit(PhotoSwipeEventMap.closeGallery);
        });
      }
    },
    methods: {},
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.product-page #product-images-container {
  .pswp__button {
    background: none;
    color: white;

    &::before {
      content: none;
    }

    i {
      pointer-events: none;
    }
  }
}
</style>
