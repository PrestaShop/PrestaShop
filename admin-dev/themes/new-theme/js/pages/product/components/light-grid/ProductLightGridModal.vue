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
  <div id="product-light-grid-modal">
    <modal
      class="product-light-grid-modal"
      v-if="isOpen"
      @close="closeModal"
    >
      <template #body>
        <div
          class="product-light-grid-loading"
          v-if="loadingForm"
        >
          <div class="spinner" />
        </div>
        <iframe
          ref="iframe"
          class="product-light-grid-iframe"
          :src="url"
          @load="onFrameLoaded"
          vspace="0"
          hspace="0"
          scrolling="auto"
        />
      </template>
      <template #footer>
        <button
          type="button"
          class="btn btn-secondary btn-close"
          @click="closeModal"
          :aria-label="$t('modal.close')"
        >
          {{ $t('modal.cancel') }}
        </button>
      </template>
    </modal>
  </div>
</template>

<script>
  import ProductMap from '@pages/product/product-map';
  import Modal from '@vue/components/Modal';
  import Router from '@components/router';

  const router = new Router();

  export default {
    name: 'ProductLightGridModal',
    components: {Modal},
    data() {
      return {
        isOpen: false,
        loadingForm: false,
        url: null,
        container: null,
      };
    },
    mounted() {
      this.container = document.querySelector(`${ProductMap.productForm} .toolbar`);
      this.watchShowButton();
    },
    methods: {
      watchShowButton() {
        const showModalBtn = this.container.querySelector('#product_form_open_quicknav');
        showModalBtn.addEventListener('click', (e) => {
          e.stopImmediatePropagation();
          this.url = router.generate(
            'admin_products_v2_light_list',
            {
              liteDisplaying: 1,
            },
          );
          this.loadingForm = true;
          this.isOpen = true;
        });
      },
      onFrameLoaded() {
        this.applyIframeStyling();
        this.loadingForm = false;
      },
      applyIframeStyling() {
        this.$refs.iframe.contentDocument.body.style.overflowX = 'hidden';
      },
      closeModal() {
        this.isOpen = false;
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#product-light-grid-modal .product-light-grid-modal {
  .modal {
    display: flex;
    align-items: flex-start;
    justify-content: center;
  }

  .modal-dialog {
    max-width: 990px;
    width: 90%;
    height: 95%;
    margin: 0;

    .modal-header {
      display: none;
    }

    .modal-content {
      height: 100%;
      padding: 0;
      margin: 0 1rem;
      overflow: hidden;

      .modal-body {
        padding: 0.5rem;
        margin: 0;
        background: #eaebec;

        .product-light-grid-loading {
          position: absolute;
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 1;
          background: white;
        }

        .product-light-grid-iframe {
          padding: 0;
          margin: 0;
          border: 0;
          outline: none;
          vertical-align: top;
          width: 100%;
          height: 100%;
          display: block;

          .card {
            margin-bottom: 0;
          }
        }
      }

      .modal-footer {
        margin: 0;
        padding: 0.6rem 1rem;
        display: flex;
        flex-direction: row;
        justify-content: flex-end;

        .btn-close {
          margin-right: auto;
        }
      }
    }
  }
}
</style>
