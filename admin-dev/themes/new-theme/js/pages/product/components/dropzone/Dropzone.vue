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
    <div id="product-images-dropzone" class="dropzone dropzone-container">
      <div class="dz-preview openfilemanager">
        <div><span><i class="material-icons">add_a_photo</i></span></div>
      </div>
      <div class="dz-message"></div>
    </div>

    <dropzone-window class="dropzone-window" v-if="selectedFiles.length > 0" :selectedFiles="selectedFiles" :dropzone="dropzone" @unselectAll="unselectAll" @removeSelection="removeSelection" @selectAll="selectAll" :files="files" />

    <div class="dz-template d-none">
      <div class="dz-preview dz-file-preview">
        <div class="dz-image">
          <img data-dz-thumbnail />
        </div>
        <div class="dz-details">
          <div class="dz-filename"><span data-dz-name></span></div>
          <div class="dz-size" data-dz-size></div>
        </div>
        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
        <div class="dz-success-mark"><span>✔</span></div>
        <div class="dz-error-mark"><span>✘</span></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="dz-hover">
          <i class="material-icons drag-indicator">drag_indicator</i>
          <div class="md-checkbox">
            <label>
              <input type="checkbox"/>
              <i class="md-checkbox-control"></i>
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import DropzoneWindow from './DropzoneWindow'
  import Router from "@components/router";

  const {$} = window;

  export default {
    name: 'Dropzone',
    data() {
      return {
        dropzone: null,
        configuration: {
          url: '#',
          clickable: '.openfilemanager',
          previewTemplate: null
        },
        files: [],
        selectedFiles: [],
        translations: []
      }
    },
    components: {
      DropzoneWindow
    },
    computed: {
    },
    mounted() {
      const router = new Router();
      // @todo: seems to be enough to configure the post URL, but upload response needs to be handled (especially in case of error)
      // we also could fet the full list of images on upload and refresh the list
      this.configuration.url = router.generate('admin_products_v2_add_image', {productId: 20});
      this.configuration.previewTemplate = document.querySelector('.dz-template').innerHTML;

      this.initProductImages();
    },
    methods: {
      async initProductImages() {
        const router = new Router();
        const imagesUrl = router.generate('admin_products_v2_get_images', {productId: 20});

        const response = await fetch(imagesUrl);
        const images = await response.json();
        const container = $('#product-images-dropzone');
        images.forEach((image) => {
          const preview = $(this.configuration.previewTemplate);
          preview.find('img').first().prop('src', image.path);
          container.append(preview);
        });

        this.initDropZone();
      },
      initDropZone() {
        this.dropzone = new window.Dropzone(".dropzone-container", this.configuration);

        this.dropzone.on('addedfile', file => {
          file.previewElement.addEventListener('click', () => {
            const input = file.previewElement.querySelector('.md-checkbox input');
            input.checked = !input.checked;

            if(input.checked) {
              if(!this.selectedFiles.includes(file)) {
                this.selectedFiles.push(file);
                file.previewElement.classList.toggle('selected');
              }
            }else {
              this.selectedFiles = this.selectedFiles.filter(e => e !== file);
              file.previewElement.classList.toggle('selected');
            }
          })

          this.files.push(file);
        })
      },
      selectAll() {
        this.selectedFiles = this.files;

        this.selectedFiles.forEach(file => {
          const input = file.previewElement.querySelector('.md-checkbox input');
          input.checked = true;
          file.previewElement.classList.add('selected');
        })
      },
      unselectAll() {
        this.selectedFiles.forEach(file => {
          const input = file.previewElement.querySelector('.md-checkbox input');
          input.checked = !input.checked;
          file.previewElement.classList.toggle('selected');
        })

        this.selectedFiles = [];
      },
      removeSelection() {
        this.selectedFiles.forEach(file => {
          this.dropzone.removeFile(file);

          this.files = this.files.filter(e => file !== e);
        })

        this.selectedFiles = [];
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '~@scss/config/_settings.scss';

  .product-page #product-images-dropzone {
    &.dropzone-container {
      .dz-default {
        display: none;
      }

      .dz-message {
        display: none;
      }

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
            background-color: rgba(0, 0, 0, .7);

            .drag-indicator, .md-checkbox {
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
        transition: .25s ease-out;
        pointer-events: none;
        z-index: 11;

        .drag-indicator {
          position: absolute;
          top: .5rem;
          left: .5rem;
          color: #ffffff;
          opacity: 0;
          transition: .25s ease-out;
        }

        .md-checkbox {
          position: absolute;
          bottom: .5rem;
          left: .5rem;
          opacity: 0;
          transition: .25s ease-out;

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
