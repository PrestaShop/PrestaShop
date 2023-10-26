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
  <div
    id="combination-edit-modal"
    :class="{ 'history-collapsed': historyCollapsed }"
  >
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
          :aria-label="cancelLabel"
          :disabled="submittingCombinationForm"
        >
          {{ cancelLabel }}
        </button>

        <button
          type="button"
          class="btn btn-outline-secondary btn-previous-combination"
          @click.prevent.stop="showPrevious"
          :aria-label="$t('modal.previous')"
          :disabled="
            previousCombinationId === null || submittingCombinationForm
          "
        >
          <i class="material-icons rtl-flip">keyboard_arrow_left</i>
          <span class="btn-label">{{ $t('modal.previous') }}</span>
        </button>
        <button
          type="button"
          class="btn btn-outline-secondary btn-next-combination"
          @click.prevent.stop="showNext"
          :aria-label="$t('modal.next')"
          :disabled="nextCombinationId === null || submittingCombinationForm"
        >
          <span class="btn-label">{{ $t('modal.next') }}</span>
          <i class="material-icons rtl-flip">keyboard_arrow_right</i>
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
          :selected-combination-id="selectedCombinationId"
          :empty-image-url="emptyImageUrl"
          @collapsed="(collapsed) => { this.historyCollapsed = collapsed; }"
        />
      </template>
    </modal>
    <div
      class="modal-prevent-close"
      @click.prevent.stop="preventClose"
    >
      <modal
        :modal-title="$t('modal.history.confirmTitle')"
        :confirm-label="$t('modal.confirm')"
        :confirmation="true"
        v-if="showConfirm"
        @close="hideConfirmModal"
        @confirm="confirmSelection"
      >
        <template #body>
          <p
            v-html="
              $t('modal.history.confirmBody', {
                'combinationName': selectedCombinationName,
              })
            "
          />
        </template>
      </modal>
    </div>
  </div>
</template>

<script lang="ts">
  import ProductMap from '@pages/product/product-map';
  import ProductEventMap from '@pages/product/product-event-map';
  import Modal from '@PSVue/components/Modal.vue';
  import Router from '@components/router';
  import {defineComponent} from 'vue';
  import PaginatedCombinationsService from '@pages/product/service/paginated-combinations-service';
  import PsModal from '@components/modal/modal';
  import History from './History.vue';

  export interface Combination {
    id: number;
  }

  interface CombinationModalStates {
    combinationIds: Array<number>,
    selectedCombinationId: number | null,
    selectedCombinationName: string | null,
    previousCombinationId: number | null,
    nextCombinationId: number | null,
    editCombinationUrl?: string,
    loadingCombinationForm: boolean,
    submittingCombinationForm: boolean,
    combinationList: JQuery,
    hasSubmittedCombinations: boolean,
    combinationsHistory: Array<Record<string, any>>,
    showConfirm: boolean,
    temporarySelection: number | null,
    isFormUpdated: boolean,
    isClosing: boolean,
    historyCollapsed: boolean,
  }

  const {$} = window;
  const CombinationEvents = ProductEventMap.combinations;

  const router = new Router();

  export default defineComponent({
    name: 'CombinationModal',
    components: {Modal, History},
    data(): CombinationModalStates {
      return {
        combinationIds: [],
        selectedCombinationId: null,
        selectedCombinationName: null,
        previousCombinationId: null,
        nextCombinationId: null,
        editCombinationUrl: '',
        loadingCombinationForm: false,
        submittingCombinationForm: false,
        combinationList: <JQuery>$(ProductMap.combinations.combinationsFormContainer),
        hasSubmittedCombinations: false,
        combinationsHistory: [],
        showConfirm: false,
        temporarySelection: null,
        isFormUpdated: false,
        isClosing: false,
        historyCollapsed: true,
      };
    },
    props: {
      paginatedCombinationsService: {
        type: PaginatedCombinationsService,
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
      this.combinationList = $(ProductMap.combinations.combinationsFormContainer);
      this.initCombinationIds();
      this.watchEditButtons();
      this.eventEmitter.on(CombinationEvents.refreshCombinationList, () => this.initCombinationIds());
      this.eventEmitter.on(CombinationEvents.listRendered, () => this.initCombinationIds());
    },
    computed: {
      cancelLabel(): string {
        return this.isFormUpdated ? this.$t('modal.cancel') : this.$t('modal.close');
      },
    },
    methods: {
      watchEditButtons(): void {
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
        this.combinationIds = await this.paginatedCombinationsService.getCombinationIds();
      },
      frameLoading(): void {
        this.applyIframeStyling();
      },
      getIframeDocument(): typeof document {
        const iframeDocument = <typeof document>(<HTMLIFrameElement> this.$refs.iframe).contentDocument;

        return iframeDocument;
      },
      onFrameLoaded(): void {
        this.loadingCombinationForm = false;
        this.submittingCombinationForm = false;
        const iframeBody = <HTMLElement> this.getIframeDocument().body;

        this.applyIframeStyling();

        // if form is not found it means that combination is missing
        const editionForm = iframeBody.querySelector<HTMLFormElement>(ProductMap.combinations.editionForm);

        if (!editionForm) {
          // submitted combinations is set to true to update combination list if combination is not found
          this.hasSubmittedCombinations = true;
          this.closeModal();
          const modal = new PsModal({id: 'combination-not-found-modal'});
          modal.render(iframeBody.innerHTML);
          modal.show();
          return;
        }

        this.selectedCombinationName = iframeBody.querySelector(
          ProductMap.combinations.combinationName,
        )!.innerHTML;

        const iframeInputs = <NodeListOf<Element>>iframeBody.querySelectorAll(
          ProductMap.combinations.editionFormInputs,
        );

        iframeInputs.forEach((input: Element) => {
          // @ts-ignore
          if (input.type === 'hidden') {
            return;
          }

          input.addEventListener('keyup', () => {
            this.isFormUpdated = true;
          });

          input.addEventListener('change', () => {
            this.isFormUpdated = true;
          });
        });

        this.getIframeDocument().addEventListener('datepickerChange', () => {
          this.isFormUpdated = true;
        });
      },
      applyIframeStyling(): void {
        this.getIframeDocument().body.style.overflowX = 'hidden';
      },
      tryClose(): void {
        if (this.isFormUpdated) {
          this.isClosing = true;

          this.showConfirmModal();
        } else {
          this.closeModal();
        }
      },
      closeModal(): void {
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
      navigateToCombination(combinationId: number): void {
        if (combinationId !== null) {
          if (this.isFormUpdated) {
            this.temporarySelection = combinationId;
            this.showConfirmModal();
          } else {
            this.selectedCombinationId = combinationId;
          }
        }
      },
      showPrevious(): void {
        if (this.previousCombinationId) {
          this.navigateToCombination(this.previousCombinationId);
        }
      },
      showNext(): void {
        if (this.nextCombinationId) {
          this.navigateToCombination(this.nextCombinationId);
        }
      },
      selectCombination(combination: Combination): void {
        this.navigateToCombination(combination.id);
      },
      confirmSelection(): void {
        if (this.isClosing) {
          this.closeModal();
          this.isClosing = false;
          this.hideConfirmModal();
        } else {
          this.selectedCombinationId = this.temporarySelection;
          this.hideConfirmModal();
        }
      },
      submitForm(): void {
        this.submittingCombinationForm = true;
        const iframeBody = this.getIframeDocument().body;
        const editionForm = <HTMLFormElement> iframeBody.querySelector(
          ProductMap.combinations.editionForm,
        );
        editionForm.submit();
        this.hasSubmittedCombinations = true;
        const selectedCombination = {
          id: this.selectedCombinationId,
          title: iframeBody!.querySelector(ProductMap.combinations.combinationName)!
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
      showConfirmModal(): void {
        this.showConfirm = true;
      },
      hideConfirmModal(): void {
        this.isClosing = false;
        this.showConfirm = false;
      },
      preventClose(event: Event): void {
        event.stopPropagation();
        event.preventDefault();
      },
    },
    watch: {
      selectedCombinationId(combinationId: number): void {
        this.isFormUpdated = false;

        if (combinationId === null) {
          this.previousCombinationId = null;
          this.nextCombinationId = null;
          this.editCombinationUrl = undefined;

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
  });
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#combination-edit-modal {
  .combination-modal {
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
        margin: 0;
        overflow: hidden;

        .modal-body {
          padding: 0;
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
  }

  &.history-collapsed {
    .modal-content {
      border-top-right-radius: 0;
    }
  }
}

@media screen and (max-width: 1299.98px) {
  #combination-edit-modal {
    .combination-modal {
      .modal-dialog {
        width: 100%;
        height: 100%;

        .btn-previous-combination,
        .btn-next-combination {
          padding: 0.5rem;

          .btn-label {
            display: none;
          }

          .material-icons {
            display: block;
            font-size: 1.7rem;
            color: #6c868e;
          }
        }

        .btn-previous-combination.disabled,
        .btn-next-combination.disabled {
          .material-icons {
            color: #b3c7cd;
          }
        }
      }
    }
  }
}
</style>
