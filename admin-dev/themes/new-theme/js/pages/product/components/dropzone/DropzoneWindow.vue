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
  <div class="dropzone-window">
    <div class="dropzone-window-header row">
      <div class="dropzone-window-header-left">
        <p class="dropzone-window-number">
          <span>{{ selectedFiles.length }} {{ $t('window.selectedFiles') }}</span>
        </p>
      </div>
      <div class="dropzone-window-header-right">
        <i
          class="material-icons"

          data-toggle="pstooltip"
          :data-original-title="$t('window.zoom')"
        >search</i>
        <i
          class="material-icons"
          data-toggle="pstooltip"
          :data-original-title="$t('window.delete')"
          @click="$emit('removeSelection')"
        >delete</i>
        <i
          class="material-icons"
          data-toggle="pstooltip"
          :data-original-title="$t('window.close')"
          @click="$emit('unselectAll')"
        >close</i>
      </div>
    </div>

    <p
      class="dropzone-window-select"
      @click="$emit('selectAll')"
      v-if="files.length > 0 && selectedFiles.length !== files.length"
    >
      {{ $t('window.selectAll') }}
    </p>
    <p
      class="dropzone-window-unselect"
      v-if="selectedFiles.length === files.length"
      @click="$emit('unselectAll')"
    >
      {{ $t('window.unselectAll') }}
    </p>

    <div
      class="md-checkbox"
      v-if="selectedFiles.length === 1"
    >
      <label>
        <input type="checkbox">
        <i class="md-checkbox-control" />
        {{ $t('window.useAsCover') }}
      </label>
    </div>

    <label
      for="caption-textarea"
      class="control-label"
    >{{ $t('Caption') }}</label>
    <textarea
      id="caption-textarea"
      name="caption-textarea"
      class="form-control"
    />

    <button class="btn btn-primary save-image-settings">
      {{ $t('window.saveImage') }}
    </button>
  </div>
</template>

<script>
  export default {
    name: 'DropzoneWindow',
    props: {
      selectedFiles: {
        type: Array,
        default: () => [],
      },
      files: {
        type: Array,
        default: () => [],
      },
    },
    mounted() {
      window.prestaShopUiKit.initToolTips();
    },
    methods: {
    },
  };
</script>

<style lang="scss" type="text/scss">
  @import '~@scss/config/_settings.scss';

  .product-page {
    .dropzone-window {
      width: 33%;
      background-color: darken(#ffffff, 2%);
      align-self: stretch;
      padding: 1rem;

      &-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        p {
          margin-bottom: 0;
        }

        .material-icons {
          cursor: pointer;
          color: $gray-500;
          transition: .25s ease-out;

          &:hover {
            color: primary;
          }
        }
      }
    }
  }
</style>
