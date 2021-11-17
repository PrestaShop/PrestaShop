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
<template>
  <div id="product-images-container">
    <div
      id="product-images-dropzone"
      :class="['dropzone', 'dropzone-container', { full: files.length <= 0 }]"
    >
      <div
        :class="[
          'dz-preview',
          'openfilemanager',
          { 'd-none': loading || files.length <= 0 },
        ]"
      >
        <div>
          <span><i class="material-icons">add_a_photo</i></span>
        </div>
      </div>
      <div
        :class="[
          'dz-default',
          'dz-message',
          'openfilemanager',
          'dz-clickable',
          { 'd-none': loading || files.length > 0 },
        ]"
      >
        <i class="material-icons">add_a_photo</i><br>
        {{ $t('window.dropImages') }}<br>
        <a>{{ $t('window.selectFiles') }}</a><br>
        <small>
          {{ $t('window.recommendedSize') }}<br>
          {{ $t('window.recommendedFormats') }}
        </small>
      </div>

      <div
        class="dropzone-loading"
        v-if="loading"
      >
        <div class="spinner" />
      </div>
    </div>

    <dropzone-window
      class="dropzone-window"
      v-if="selectedFiles.length > 0"
      :selected-files="selectedFiles"
      :dropzone="dropzone"
      @unselectAll="unselectAll"
      @removeSelection="showModal"
      @selectAll="selectAll"
      @saveSelectedFile="saveSelectedFile"
      @replacedFile="manageReplacedFile"
      @openGallery="toggleGallery"
      :files="files"
      :locales="locales"
      :selected-locale="selectedLocale"
      :loading="buttonLoading"
    />

    <modal
      v-if="isModalShown"
      :confirmation="true"
      :modal-title="
        $tc('modal.title', this.selectedFiles.length, {
          '%filesNb%': this.selectedFiles.length,
        })
      "
      :confirm-label="$t('modal.accept')"
      :cancel-label="$t('modal.close')"
      @confirm="removeSelection"
      @close="hideModal"
    />

    <div class="dz-template d-none">
      <div class="dz-preview dz-file-preview">
        <div class="dz-image">
          <img data-dz-thumbnail>
        </div>
        <div class="dz-progress">
          <span
            class="dz-upload"
            data-dz-uploadprogress
          />
        </div>
        <div class="dz-success-mark">
          <span>✔</span>
        </div>
        <div class="dz-error-mark">
          <span>✘</span>
        </div>
        <div class="dz-error-message">
          <span data-dz-errormessage />
        </div>
        <div class="dz-hover">
          <i class="material-icons drag-indicator">drag_indicator</i>
          <div class="md-checkbox">
            <label>
              <input type="checkbox">
              <i class="md-checkbox-control" />
            </label>
          </div>
        </div>
        <div class="iscover">
          {{ $t('window.cover') }}
        </div>
      </div>
    </div>

    <dropzone-photo-swipe
      :files="selectedFiles"
      @closeGallery="toggleGallery"
      v-if="selectedFiles.length > 0 && galleryOpened"
    />
  </div>
</template>

<script>
  import Router from '@components/router';
  import {
    getProductImages,
    saveImageInformations,
    saveImagePosition,
    replaceImage,
    removeProductImage,
  } from '@pages/product/services/images';
  import ProductMap from '@pages/product/product-map';
  import ProductEventMap from '@pages/product/product-event-map';
  import Modal from '@vue/components/Modal';
  import DropzoneWindow from './DropzoneWindow';
  import DropzonePhotoSwipe from './DropzonePhotoSwipe';

  const {$} = window;

  const router = new Router();
  const DropzoneMap = ProductMap.dropzone;
  const DropzoneEvents = ProductEventMap.dropzone;

  export default {
    name: 'Dropzone',
    data() {
      return {
        dropzone: null,
        configuration: {
          url: router.generate('admin_products_v2_add_image'),
          clickable: DropzoneMap.configuration.fileManager,
          previewTemplate: null,
          thumbnailWidth: 130,
          thumbnailHeight: 130,
          thumbnailMethod: 'crop',
        },
        files: [],
        selectedFiles: [],
        translations: [],
        loading: true,
        selectedLocale: null,
        buttonLoading: false,
        isModalShown: false,
        galleryOpened: false,
      };
    },
    props: {
      productId: {
        type: Number,
        required: true,
      },
      locales: {
        type: Array,
        required: true,
      },
      formName: {
        type: String,
        required: true,
      },
      token: {
        type: String,
        required: true,
      },
    },
    components: {
      DropzoneWindow,
      Modal,
      DropzonePhotoSwipe,
    },
    computed: {},
    mounted() {
      this.watchLocaleChanges();
      this.initProductImages();
    },
    methods: {
      /**
       * Watch locale changes to update the selected one
       */
      watchLocaleChanges() {
        this.selectedLocale = this.locales[0];

        window.prestashop.instance.eventEmitter.on(
          DropzoneEvents.languageSelected,
          (event) => {
            const {selectedLocale} = event;

            this.locales.forEach((locale) => {
              if (locale.iso_code === selectedLocale) {
                this.selectedLocale = locale;
              }
            });
          },
        );
      },
      /**
       * This methods is used to initialize product images we already have uploaded
       */
      async initProductImages() {
        try {
          const images = await getProductImages(this.productId);

          this.loading = false;
          this.initDropZone();

          images.forEach((image) => {
            this.dropzone.displayExistingFile(image, image.image_url);
          });
        } catch (error) {
          window.$.growl.error({message: error});
        }
      },
      /**
       * Method to initialize the dropzone, using the configuration's state and adding files
       * we already have in database.
       */
      initDropZone() {
        this.configuration.previewTemplate = document.querySelector(
          DropzoneMap.dzTemplate,
        ).innerHTML;
        this.configuration.paramName = `${this.formName}[file]`;
        this.configuration.method = 'POST';
        this.configuration.params = {};
        this.configuration.params[
          `${this.formName}[product_id]`
        ] = this.productId;
        this.configuration.params[`${this.formName}[_token]`] = this.token;

        this.sortableContainer = $('#product-images-dropzone');

        this.dropzone = new window.Dropzone(
          DropzoneMap.dropzoneContainer,
          this.configuration,
        );

        this.sortableContainer.sortable({
          items: DropzoneMap.sortableItems,
          opacity: 0.9,
          containment: 'parent',
          distance: 32,
          tolerance: 'pointer',
          cursorAt: {
            left: 64,
            top: 64,
          },
          cancel: '.disabled',
          stop: (event, ui) => {
            // Get new position (-1 because the open file manager is always first)
            const movedPosition = ui.item.index() - 1;
            this.updateImagePosition(ui.item.data('id'), movedPosition);
          },
          start: (event, ui) => {
            this.sortableContainer.find(DropzoneMap.dzPreview).css('zIndex', 1);
            ui.item.css('zIndex', 10);
          },
        });

        this.dropzone.on(DropzoneEvents.addedFile, (file) => {
          file.previewElement.dataset.id = file.image_id;

          if (file.is_cover) {
            file.previewElement.classList.add('is-cover');
          }

          file.previewElement.addEventListener('click', () => {
            const input = file.previewElement.querySelector(DropzoneMap.checkbox);
            input.checked = !input.checked;

            if (input.checked) {
              if (!this.selectedFiles.includes(file)) {
                this.selectedFiles.push(file);
                file.previewElement.classList.toggle('selected');
              }
            } else {
              this.selectedFiles = this.selectedFiles.filter((e) => e !== file);
              file.previewElement.classList.toggle('selected');
            }
          });
          this.files.push(file);
        });

        this.dropzone.on(DropzoneEvents.error, (fileWithError, message) => {
          $.growl.error({message: message.error});
          this.dropzone.removeFile(fileWithError);
        });

        this.dropzone.on(DropzoneEvents.success, (file, response) => {
          // Append the data required for a product image
          file.image_id = response.image_id;
          file.is_cover = response.is_cover;
          file.legends = response.legends;
          // Update dataset so that it can be selected later
          file.previewElement.dataset.id = file.image_id;

          if (file.is_cover) {
            file.previewElement.classList.add('is-cover');
          }
        });
      },
      /**
       * Method to select every files by checking checkboxes and add files to the files state
       */
      selectAll() {
        this.selectedFiles = this.files;

        this.editCheckboxes(true);
      },
      /**
       * Method to unselect every files by unchecking checkboxes and empty files state
       */
      unselectAll() {
        this.editCheckboxes(false);

        this.selectedFiles = [];

        this.removeTooltips();
      },
      /**
       * Method to remove every selected files from the dropzone
       */
      async removeSelection() {
        let errorMessage = false;
        let isCoverImageRemoved = false;
        const nbFiles = this.selectedFiles.length;

        await Promise.all(
          this.selectedFiles.map(async (file) => {
            try {
              await removeProductImage(file.image_id);
              this.dropzone.removeFile(file);

              this.files = this.files.filter((e) => file !== e);
              this.selectedFiles = this.selectedFiles.filter((e) => file !== e);

              if (file.is_cover) {
                isCoverImageRemoved = true;
              }
            } catch (error) {
              errorMessage = error.responseJSON
                ? error.responseJSON.error
                : error;
            }
          }),
        );

        this.removeTooltips();

        if (errorMessage) {
          $.growl.error({message: errorMessage});
        } else {
          $.growl({
            message: this.$t('delete.success', {
              '%filesNb%': nbFiles,
            }),
          });
        }

        if (isCoverImageRemoved) {
          this.resetDropzone();
        }

        this.hideModal();
      },
      /**
       * Method to manage checkboxes of files mainly used on selectAll and unselectAll
       */
      editCheckboxes(checked) {
        this.selectedFiles.forEach((file) => {
          const input = file.previewElement.querySelector(DropzoneMap.checkbox);
          input.checked = typeof checked !== 'undefined' ? checked : !input.checked;

          file.previewElement.classList.toggle('selected', checked);
        });
      },
      /**
       * We sometime need to remove tooltip because Vue kick the markup of the component
       */
      removeTooltips() {
        $(DropzoneMap.shownTooltips).each((i, element) => {
          $(element).remove();
        });
      },
      /**
       * Save selected file
       */
      async saveSelectedFile(captionValue, isCover) {
        if (!this.selectedFiles.length) {
          return;
        }

        this.buttonLoading = true;

        const selectedFile = this.selectedFiles[0];

        selectedFile.is_cover = isCover;

        selectedFile.legends = captionValue;

        try {
          const savedImage = await saveImageInformations(
            selectedFile,
            this.token,
            this.formName,
          );

          const savedImageElement = document.querySelector(
            DropzoneMap.savedImageContainer(savedImage.image_id),
          );

          /**
           * If the image was saved as cover, we need to replace the DOM in order to display
           * the correct one.
           */
          if (savedImage.is_cover) {
            if (!savedImageElement.classList.contains('is-cover')) {
              const coverElement = document.querySelector(
                DropzoneMap.coveredPreview,
              );

              if (coverElement) {
                coverElement.classList.remove('is-cover');
              }

              savedImageElement.classList.add('is-cover');

              this.files = this.files.map((file) => {
                if (file.image_id !== savedImage.image_id && file.is_cover) {
                  file.is_cover = false;
                }

                return file;
              });
            }
          }
          $.growl({message: this.$t('window.settingsUpdated')});
          this.buttonLoading = false;
        } catch (error) {
          $.growl.error({message: error.error});
          this.buttonLoading = false;
        }
      },
      /**
       * Used to save and manage some datas from a replaced file
       */
      async manageReplacedFile(event) {
        const selectedFile = this.selectedFiles[0];
        this.buttonLoading = true;

        try {
          const newImage = await replaceImage(
            selectedFile,
            event.target.files[0],
            this.formName,
            this.token,
          );
          const imageElement = document.querySelector(
            DropzoneMap.savedImage(newImage.image_id),
          );
          imageElement.src = newImage.image_url;

          $.growl({message: this.$t('window.imageReplaced')});
          this.buttonLoading = false;
        } catch (error) {
          $.growl.error({message: error.responseJSON.error});
          this.buttonLoading = false;
        }
      },
      async updateImagePosition(productImageId, newPosition) {
        try {
          await saveImagePosition(
            productImageId,
            newPosition,
            this.formName,
            this.token,
          );
        } catch (error) {
          this.sortableContainer.sortable('cancel');
          $.growl.error({message: error.responseJSON.error});
        }
      },
      /**
       * Mainly used when we wants to reset the whole list
       * to reset cover image for example on remove
       */
      resetDropzone() {
        this.loading = true;
        this.files.forEach((file) => {
          this.dropzone.removeFile(file);
        });
        this.dropzone.destroy();
        this.dropzone = null;
        this.initProductImages();
      },
      showModal() {
        this.isModalShown = true;
      },
      hideModal() {
        this.isModalShown = false;
      },
      /**
       * Method used to open the photoswipe gallery
       */
      toggleGallery() {
        this.galleryOpened = !this.galleryOpened;
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';
@import '~@scss/config/_bootstrap.scss';

.product-page #product-images-dropzone {
  &.full {
    cursor: pointer;
    width: 100%;
  }

  .dropzone-loading {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 10rem;
  }

  &.dropzone-container {
    .dz-preview {
      position: relative;
      cursor: pointer;

      .iscover {
        display: none;
        left: -2px;
        bottom: -3px;
        width: calc(100% + 4px);
        padding: 9px;
      }
      &.is-cover {
        .iscover {
          display: block;
        }
      }

      &:not(.openfilemanager) {
        border: 3px solid transparent;

        &:hover {
          border: 3px solid $primary;
        }

        .dz-image {
          border: 1px solid $gray-300;
          width: 130px;
          height: 130px;
          margin: -3px;
        }
      }

      &.openfilemanager {
        border-style: dashed;
        min-width: 130px;

        &:hover {
          border-style: solid;
        }

        > div {
          border: none;

          i {
            font-size: 2.5rem;
          }
        }
      }

      img {
        margin: 0;
      }

      &:hover {
        .dz-hover {
          background-color: rgba(0, 0, 0, 0.7);

          .drag-indicator,
          .md-checkbox {
            opacity: 1;
          }
        }
      }

      &.selected {
        .md-checkbox {
          opacity: 1;
        }
      }
    }

    .dz-hover {
      position: absolute;
      top: -3px;
      left: -3px;
      width: calc(100% + 6px);
      height: calc(100% + 6px);
      background-color: rgba(0, 0, 0, 0);
      transition: 0.25s ease-out;
      pointer-events: none;
      z-index: 11;

      .drag-indicator {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        color: #ffffff;
        opacity: 0;
        transition: 0.25s ease-out;
      }

      .md-checkbox {
        position: absolute;
        bottom: 0.5rem;
        left: 0.5rem;
        opacity: 0;
        transition: 0.25s ease-out;

        .md-checkbox-control::before {
          background: transparent;
        }

        input:checked + .md-checkbox-control::before {
          background: $primary;
        }
      }
    }
  }
}

.product-page #product-images-container {
  @include media-breakpoint-down(xs) {
    flex-wrap: wrap;
  }

  #product-images-dropzone.dropzone {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-radius: 4px;
    flex-wrap: wrap;

    @include media-breakpoint-down(xs) {
      flex-wrap: wrap;
      justify-content: space-around;
      width: 100%;

      .dz-preview {
        width: 100px;
        height: 100px;
        min-height: 100px;
        margin: 0.5rem;

        &.openfilemanager {
          min-width: 100px;
        }

        img {
          max-width: 100%;
          max-height: 100%;
        }

        .dz-image {
          width: 100px;
          height: 100px;
        }
      }
    }
  }
}
</style>
