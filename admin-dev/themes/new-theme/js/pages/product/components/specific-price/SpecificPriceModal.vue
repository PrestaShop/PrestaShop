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
      v-if="isOpen"
      @close="closeModal"
      :close-label="$t('modal.close')"
      :confirm-label="$t('modal.apply')"
      :cancel-label="$t('modal.cancel')"
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
  import ProductMap from '@pages/product/product-map';
  import ProductEventMap from '@pages/product/product-event-map';
  import Modal from '@PSVue/components/Modal';
  import Router from '@components/router';
  import {defineComponent} from 'vue';

  const router = new Router();
  const SpecificPriceMap = ProductMap.specificPrice;

  export default defineComponent({
    name: 'SpecificPriceModal',
    components: {Modal},
    data() {
      return {
        isOpen: false,
        loadingForm: false,
        url: null,
        submittingForm: false,
        container: null,
      };
    },
    props: {
      eventEmitter: {
        type: Object,
        required: true,
      },
      productId: {
        type: Number,
        required: true,
      },
    },
    mounted() {
      this.container = document.querySelector(SpecificPriceMap.container);
      this.watchAddButton();
      this.watchEditButtons();
    },
    methods: {
      submitForm() {
        this.submittingForm = true;
        this.loadingForm = true;
        const iframeBody = this.$refs.iframe.contentDocument.body;
        const form = iframeBody.querySelector(SpecificPriceMap.form);
        form.submit();
      },
      watchAddButton() {
        const addButton = this.container.querySelector(SpecificPriceMap.addSpecificPriceBtn);
        addButton.addEventListener('click', (e) => {
          e.stopImmediatePropagation();
          this.url = router.generate(
            'admin_products_specific_prices_create',
            {
              productId: this.productId,
              liteDisplaying: 1,
            },
          );
          this.loadingForm = true;
          this.isOpen = true;
        });
      },
      watchEditButtons() {
        // cannot listen edit buttons directly as they are rendered dynamically
        $(SpecificPriceMap.listContainer).on('click', SpecificPriceMap.listFields.editBtn, (e) => {
          e.stopImmediatePropagation();
          this.url = router.generate(
            'admin_products_specific_prices_edit',
            {
              specificPriceId: e.currentTarget.dataset.specificPriceId,
              liteDisplaying: 1,
            },
          );
          this.loadingForm = true;
          this.isOpen = true;
        });
      },
      onFrameLoaded() {
        this.applyIframeStyling();
        this.closeAfterSubmit();
        this.submittingForm = false;
        this.loadingForm = false;
      },
      applyIframeStyling() {
        this.$refs.iframe.contentDocument.body.style.overflowX = 'hidden';
      },
      closeAfterSubmit() {
        const form = this.$refs.iframe.contentDocument.querySelector(SpecificPriceMap.form);
        const successAlertsCount = Number(form.dataset.alertsSuccess);

        if (successAlertsCount !== 0) {
          if (this.isOpen) {
            this.eventEmitter.emit(ProductEventMap.specificPrice.specificPriceUpdated);
          }
          this.closeModal();
        }
      },
      closeModal() {
        this.isOpen = false;
        this.submittingForm = false;
      },
    },
  });
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

        .specific-price-loading {
          position: absolute;
          width: 100%;
          height: 100%;
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 1;
          background: white;
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
