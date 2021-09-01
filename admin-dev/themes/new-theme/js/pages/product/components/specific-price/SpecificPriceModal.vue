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
  <div id="specific-price-form-modal">
    <modal
      class="specific-price-modal"
      v-if="openForCreate"
      @close="closeModal"
    >
      <template #body>
        <div
          class="specific-price-loading"
          v-if="loadingForm"
        >
          <div class="spinner" />
        </div>
        <iframe
          ref="iframe"
          class="specific-price-iframe"
          :src="createSpecificPriceUrl"
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
          :disabled="submittingForm"
        >
          {{ $t('modal.cancel') }}
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="submitForm"
          :aria-label="$t('modal.save')"
          :disabled="submittingForm"
        >
          <span v-if="!submittingForm">
            {{ $t('modal.save') }}
          </span>
          <span
            class="spinner-border spinner-border-sm"
            v-if="submittingForm"
            role="status"
            aria-hidden="true"
          />
        </button>
      </template>
    </modal>
  </div>
</template>

<script>
  import CombinationsService from '@pages/product/services/combinations-service';
  import ProductMap from '@pages/product/product-map';
  import Modal from '@vue/components/Modal';
  import Router from '@components/router';

  const {$} = window;

  const router = new Router();

  export default {
    name: 'SpecificPriceModal',
    components: {Modal},
    data() {
      return {
        openForCreate: false,
        loadingForm: false,
        createSpecificPriceUrl: router.generate(
          'admin_products_specific_prices_create',
          {
            //@todo hardcoded
            productId: 18,
            liteDisplaying: 1,
          },
        ),
        submittingForm: false,
        container: null,
      };
    },
    props: {
      eventEmitter: {
        type: Object,
        required: true,
      },
    },
    mounted() {
      this.container = $(ProductMap.specificPrice.container);
      this.combinationsService = new CombinationsService(this.productId);
      this.watchEditButtons();
    },
    methods: {
      submitForm() {
        this.submittingForm = true;
        const iframeBody = this.$refs.iframe.contentDocument.body;
        const form = iframeBody.querySelector(
          ProductMap.specificPrice.form,
        );
        form.submit();
        //@todo: whats next?
      },
      watchEditButtons() {
        this.container.on(
          'click',
          ProductMap.specificPrice.addSpecificPriceBtn,
          (event) => {
            event.stopImmediatePropagation();
            this.loadingForm = true;
            this.openForCreate = true;
          },
        );
      },
      onFrameLoaded() {
        this.loadingForm = false;
        this.applyIframeStyling();
      },
      applyIframeStyling() {
        this.$refs.iframe.contentDocument.body.style.overflowX = 'hidden';
      },
      closeModal() {
        this.openForCreate = false;
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#specific-price-form-modal .specific-price-modal {
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

        .combination-loading {
          position: absolute;
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 1;
          background: rgba(255, 255, 255, 0.8);
        }

        .specific-price-iframe {
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
