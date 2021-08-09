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
  <div id="combination-edit-modal">
    <modal
      class="combination-modal"
      v-if="selectedCombinationId !== null"
      @close="closeModal"
    >
      <template #body>
        <div
          class="combination-loading"
          v-if="loadingCombinationForm"
        >
          <div class="spinner" />
        </div>
        <iframe
          ref="iframe"
          class="combination-iframe"
          :src="editCombinationUrl"
          @loadstart="frameLoading"
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
          @click.prevent.stop="tryClose"
          :aria-label="$t('modal.close')"
          :disabled="submittingCombinationForm"
        >
          {{ $t('modal.cancel') }}
        </button>

        <button
          type="button"
          class="btn btn-outline-secondary"
          @click.prevent.stop="showPrevious"
          :aria-label="$t('modal.previous')"
          :disabled="
            previousCombinationId === null || submittingCombinationForm
          "
        >
          <i class="material-icons">keyboard_arrow_left</i>
          {{ $t('modal.previous') }}
        </button>
        <button
          type="button"
          class="btn btn-outline-secondary"
          @click.prevent.stop="showNext"
          :aria-label="$t('modal.next')"
          :disabled="nextCombinationId === null || submittingCombinationForm"
        >
          {{ $t('modal.next') }}
          <i class="material-icons">keyboard_arrow_right</i>
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click.prevent.stop="submitForm"
          :aria-label="$t('modal.save')"
          :disabled="submittingCombinationForm || !isFormUpdated"
        >
          <span v-if="!submittingCombinationForm">
            {{ $t('modal.save') }}
          </span>
          <span
            class="spinner-border spinner-border-sm"
            v-if="submittingCombinationForm"
            role="status"
            aria-hidden="true"
          />
        </button>
      </template>

      <template #outside>
        <history
          :combinations-list="combinationsHistory"
          @selectCombination="selectCombination"
          :selected-combination="selectedCombinationId"
          :empty-image-url="emptyImageUrl"
        />
      </template>
    </modal>
    <div
      class="modal-prevent-close"
      @click.prevent.stop="preventClose"
    >
      <modal
        :modal-title="$t('modal.history.confirmTitle')"
        :cancel-label="$t('modal.cancel')"
        :confirm-label="$t('modal.confirm')"
        :close-label="$t('modal.close')"
        :confirmation="true"
        v-if="showConfirm"
        @close="hideConfirmModal"
        @confirm="confirmSelection"
      >
        <template #body>
          <p
            v-html="
              $t('modal.history.confirmBody', {
                '%combinationName%': selectedCombinationName,
              })
            "
          />
        </template>
      </modal>
    </div>
  </div>
</template>

<script>
  import CombinationsService from '@pages/product/services/combinations-service';
  import ProductMap from '@pages/product/product-map';
  import ProductEventMap from '@pages/product/product-event-map';
  import Modal from '@vue/components/Modal';
  import Router from '@components/router';
  import History from './History';

  const {$} = window;
  const CombinationEvents = ProductEventMap.combinations;

  const router = new Router();

  export default {
    name: 'CombinationModal',
    components: {Modal, History},
    data() {
      return {
        combinationsService: null,
        combinationIds: [],
        selectedCombinationId: null,
        selectedCombinationName: null,
        previousCombinationId: null,
        nextCombinationId: null,
        editCombinationUrl: '',
        loadingCombinationForm: false,
        submittingCombinationForm: false,
        combinationList: null,
        hasSubmittedCombinations: false,
        combinationsHistory: [],
        showConfirm: false,
        temporarySelection: null,
        isFormUpdated: false,
        isClosing: false,
      };
    },
    props: {
      productId: {
        type: Number,
        required: true,
      },
      eventEmitter: {
        type: Object,
        required: true,
      },
      emptyImageUrl: {
        type: String,
        required: true,
      },
    },
    mounted() {
      this.combinationList = $(ProductMap.combinations.combinationsContainer);
      this.combinationsService = new CombinationsService(this.productId);
      this.initCombinationIds();
      this.watchEditButtons();
      this.eventEmitter.on(CombinationEvents.refreshCombinationList, () => this.initCombinationIds(),
      );
    },
    methods: {
      watchEditButtons() {
        this.combinationList.on(
          'click',
          ProductMap.combinations.editCombinationButtons,
          (event) => {
            event.stopImmediatePropagation();
            const $row = $(event.target).closest('tr');
            this.selectedCombinationId = Number(
              $row.find(ProductMap.combinations.combinationIdInputsSelector).val(),
            );
            this.hasSubmittedCombinations = false;
          },
        );
      },
      async initCombinationIds() {
        this.combinationIds = await this.combinationsService.getCombinationIds();
      },
      frameLoading() {
        this.applyIframeStyling();
      },
      onFrameLoaded() {
        this.loadingCombinationForm = false;
        this.submittingCombinationForm = false;
        const iframeBody = this.$refs.iframe.contentDocument.body;
        this.applyIframeStyling();
        this.selectedCombinationName = iframeBody.querySelector(
          ProductMap.combinations.combinationName,
        ).innerHTML;

        const iframeInputs = iframeBody.querySelectorAll(
          ProductMap.combinations.editionFormInputs,
        );

        iframeInputs.forEach((input) => {
          input.addEventListener('keyup', () => {
            this.isFormUpdated = true;
          });

          input.addEventListener('change', () => {
            this.isFormUpdated = true;
          });

          this.$refs.iframe.contentDocument.addEventListener('datepickerChange', () => {
            this.isFormUpdated = true;
          });
        });
      },
      applyIframeStyling() {
        this.$refs.iframe.contentDocument.body.style.overflowX = 'hidden';
      },
      tryClose() {
        if (this.isFormUpdated) {
          this.isClosing = true;

          this.showConfirmModal();
        } else {
          this.closeModal();
        }
      },
      closeModal() {
        if (this.submittingCombinationForm) {
          return;
        }

        // If modifications have been made refresh the combination list
        if (this.hasSubmittedCombinations) {
          this.eventEmitter.emit(CombinationEvents.refreshPage);
        }
        this.hasSubmittedCombinations = false;

        // This closes the modal which is conditioned to the presence of this value
        this.selectedCombinationId = null;

        // Reset history on close
        this.combinationsHistory = [];
      },
      navigateToCombination(combinationId) {
        if (combinationId !== null) {
          if (this.isFormUpdated) {
            this.temporarySelection = combinationId;
            this.showConfirmModal();
          } else {
            this.selectedCombinationId = combinationId;
          }
        }
      },
      showPrevious() {
        this.navigateToCombination(this.previousCombinationId);
      },
      showNext() {
        this.navigateToCombination(this.nextCombinationId);
      },
      selectCombination(combination) {
        this.navigateToCombination(combination.id);
      },
      confirmSelection() {
        if (this.isClosing) {
          this.closeModal();
          this.isClosing = false;
          this.hideConfirmModal();
        } else {
          this.selectedCombinationId = this.temporarySelection;
          this.hideConfirmModal();
        }
      },
      submitForm() {
        this.submittingCombinationForm = true;
        const iframeBody = this.$refs.iframe.contentDocument.body;
        const editionForm = iframeBody.querySelector(
          ProductMap.combinations.editionForm,
        );
        editionForm.submit();
        this.hasSubmittedCombinations = true;
        const selectedCombination = {
          id: this.selectedCombinationId,
          title: iframeBody.querySelector(ProductMap.combinations.combinationName)
            .innerHTML,
        };

        if (
          (this.combinationsHistory[0]
            && this.combinationsHistory[0].id !== selectedCombination.id)
          || !this.combinationsHistory.length
        ) {
          this.combinationsHistory = this.combinationsHistory.filter(
            (combination) => combination.id !== selectedCombination.id,
          );

          this.combinationsHistory.unshift(selectedCombination);
        }

        this.isFormUpdated = false;
      },
      showConfirmModal() {
        this.showConfirm = true;
      },
      hideConfirmModal() {
        this.isClosing = false;
        this.showConfirm = false;
      },
      preventClose(event) {
        event.stopPropagation();
        event.preventDefault();
      },
    },
    watch: {
      selectedCombinationId(combinationId) {
        this.isFormUpdated = false;

        if (combinationId === null) {
          this.previousCombinationId = null;
          this.nextCombinationId = null;
          this.editCombinationUrl = null;

          return;
        }

        this.loadingCombinationForm = true;
        this.editCombinationUrl = router.generate(
          'admin_products_combinations_edit_combination',
          {
            combinationId,
            liteDisplaying: 1,
          },
        );

        const selectedIndex = this.combinationIds.indexOf(combinationId);

        if (selectedIndex === -1) {
          this.previousCombinationId = null;
          this.nextCombinationId = null;
        } else {
          this.previousCombinationId = selectedIndex === 0 ? null : this.combinationIds[selectedIndex - 1];
          this.nextCombinationId = selectedIndex === this.combinationIds.length - 1
            ? null
            : this.combinationIds[selectedIndex + 1];
        }
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#combination-edit-modal .combination-modal {
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

        .combination-iframe {
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

  .history {
    max-width: 400px;
    width: 100%;
    min-height: calc(100% - 3.5rem);
    top: 50%;
    transform: translateY(-50%);
    height: 95%;
    margin-right: 1rem;
  }
}
</style>
