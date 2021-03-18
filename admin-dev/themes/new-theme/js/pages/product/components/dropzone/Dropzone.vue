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
      @removeSelection="removeSelection"
      @selectAll="selectAll"
      :files="files"
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
      </div>
    </div>
  </div>
</template>

<script>
  import Router from '@components/router';
  import {getProductImages} from '@pages/product/services/images';
  import DropzoneWindow from './DropzoneWindow';

  const {$} = window;

  const router = new Router();

  export default {
    name: 'Dropzone',
    data() {
      return {
        dropzone: null,
        configuration: {
          url: router.generate('admin_products_v2_add_image', {
            productId: this.productId,
          }),
          clickable: '.openfilemanager',
          previewTemplate: null,
        },
        files: [],
        selectedFiles: [],
        translations: [],
        loading: true,
      };
    },
    props: {
      productId: {
        type: String,
        required: true,
      },
    },
    components: {
      DropzoneWindow,
    },
    computed: {},
    mounted() {
      this.configuration.previewTemplate = document.querySelector(
        '.dz-template',
      ).innerHTML;

      this.initProductImages();
    },
    methods: {
      /**
       * This methods is used to initialize product images we already have uploaded
       */
      async initProductImages() {
        try {
          const images = await getProductImages(this.productId);

          this.loading = false;
          this.initDropZone();

          images.forEach((image) => {
            this.dropzone.displayExistingFile(image, image.path);
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
        this.dropzone = new window.Dropzone(
          '.dropzone-container',
          this.configuration,
        );

        this.dropzone.on('addedfile', (file) => {
          console.log(file)
          file.previewElement.addEventListener('click', () => {
            const input = file.previewElement.querySelector('.md-checkbox input');
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

        this.dropzone.on('error', (fileWithError, message) => {
          $.growl.error({message: message.error});
          this.dropzone.removeFile(fileWithError);
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
      removeSelection() {
        this.selectedFiles.forEach((file) => {
          this.dropzone.removeFile(file);

          this.files = this.files.filter((e) => file !== e);
        });

        this.selectedFiles = [];
        this.removeTooltips();
      },
      /**
       * Method to manage checkboxes of files mainly used on selectAll and unselectAll
       */
      editCheckboxes(checked) {
        this.selectedFiles.forEach((file) => {
          const input = file.previewElement.querySelector('.md-checkbox input');
          input.checked = typeof checked !== 'undefined' ? checked : !input.checked;

          file.previewElement.classList.toggle('selected', checked);
        });
      },
      /**
       * We sometime need to remove tooltip because Vue kick the markup of the component
       */
      removeTooltips() {
        $('.tooltip.show').each((i, element) => {
          $(element).remove();
        });
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

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
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
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
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}
</style>
