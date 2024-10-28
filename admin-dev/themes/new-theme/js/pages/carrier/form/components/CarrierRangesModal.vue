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
  <div data-role="carrier-ranges-edit-modal">
    <modal
      v-if="isModalShown"
      :modal-title="$t('modal.title')"
      :confirm-label="$t('modal.apply')"
      :cancel-label="$t('modal.cancel')"
      :confirmation="true"
      :close-on-click-outside="false"
      @close="cancelChanges"
      @confirm="applyChanges"
      @mouseleave="mouseLeave"
    >
      <template #header>
        <h5 class="modal-title">
          {{ $t('modal.title') }}
        </h5>
      </template>
      <template #body>
        <div
          class="alert alert-danger"
          v-if="overlappingAlert"
          role="alert"
        >
          {{ $t('modal.overlappingAlert') }}
        </div>
        <div
          class="alert alert-danger"
          v-if="negativeRangeAlert"
          role="alert"
        >
          {{ $t('modal.negativeRangeAlert') }}
        </div>
        <div class="table-container">
          <table class="table table-carrier-ranges-modal">
            <thead>
              <tr>
                <th />
                <th>{{ $t('modal.col.from') }}</th>
                <th>{{ $t('modal.col.to') }} </th>
                <th>{{ $t('modal.col.action') }}</th>
              </tr>
            </thead>
            <tbody :key="refreshKey">
              <template
                :key="i"
                v-for="r, i in ranges"
              >
                <tr :data-row="i">
                  <td>
                    <button
                      type="button"
                      class="btn-add"
                      @click.prevent="addRange(i)"
                    >
                      <i class="material-icons">add</i>
                    </button>
                  </td>
                  <td>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ this.symbol }}</span>
                      </div>
                      <input
                        type="number"
                        class="form-control form-from"
                        inputmode="decimal"
                        v-model.number="r.from"
                      >
                    </div>
                  </td>
                  <td>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">{{ this.symbol }}</span>
                      </div>
                      <input
                        type="number"
                        class="form-control form-to"
                        inputmode="decimal"
                        v-model.number="r.to"
                      >
                    </div>
                  </td>
                  <td align="center">
                    <button
                      type="button"
                      @click.prevent="deleteRange(i)"
                      class="btn-delete"
                    >
                      <i class="material-icons">delete</i>
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>

          <button
            @click.prevent="addRange()"
            type="button"
            class="btn btn-sm btn-outline-secondary mt-2"
          >
            <i class="material-icons">add_box</i>
            {{ $t('modal.addRange') }}
          </button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script lang="ts">
  import Modal from '@PSVue/components/Modal.vue';
  import {defineComponent} from 'vue';
  import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';
  import {Range} from '@pages/carrier/form/types';

  interface CarrierRangesModalStates {
    isModalShown: boolean, // define if the modal is shown
    ranges: Range[], // define the ranges currently displayed
    savedRanges: Range[], // define the ranges saved before the changes
    refreshKey: number, // force the refresh of the table by incrementing this key
    errors: boolean, // define if there are errors in the ranges
    overlappingAlert: boolean, // define if there are overlapping ranges (and display an alert)
    symbol: string, // define the current symbol used in function of the shipping method
  }

  export default defineComponent({
    name: 'CarrierRangesModal',
    components: {Modal},
    data(): CarrierRangesModalStates {
      return {
        isModalShown: false,
        ranges: [],
        savedRanges: [],
        refreshKey: 0,
        errors: false,
        overlappingAlert: false,
        negativeRangeAlert: false,
        symbol: '',
      };
    },
    props: {
      eventEmitter: {
        type: Object,
        required: true,
      },
    },
    mounted() {
      // If we need to open this modal
      this.eventEmitter.on(CarrierFormEventMap.openRangeSelectionModal, (ranges: Range[]) => {
        this.ranges = ranges ?? [];
        this.openModal();
      });
      // If we need to change the shipping method symbol
      this.eventEmitter.on(CarrierFormEventMap.shippingMethodChange, (symbol: string) => { this.symbol = symbol; });
    },
    methods: {
      openModal() {
        // We add a class to the body to prevent scrolling
        document.querySelector('body')?.classList.add('overflow-hidden');
        this.isModalShown = true;

        // We save the ranges to be able to cancel the changes
        this.savedRanges.splice(0, this.savedRanges.length);
        this.ranges.forEach((range) => this.savedRanges.push({from: range.from, to: range.to}));

        // We add an empty range if there is none
        if (this.ranges.length === 0) {
          this.ranges.push({from: null, to: null});
        }

        // We reset the errors
        this.errors = false;
        this.overlappingAlert = false;
        this.negativeRangeAlert = false;
      },
      closeModal() {
        // We remove the class to allow scrolling
        document.querySelector('body')?.classList.remove('overflow-hidden');
        this.isModalShown = false;
        this.refreshKey = 0;
      },
      cancelChanges() {
        // We cancel the changes and close the modal
        this.ranges.splice(0, this.ranges.length);
        this.savedRanges.forEach((range) => this.ranges.push({from: range.from, to: range.to}));
        // We remove empty ranges
        this.ranges = this.ranges.filter((range) => range.from !== null || range.to !== null);
        // Then, we close the modal
        this.closeModal();
      },
      applyChanges() {
        // We remove empty ranges
        this.ranges = this.ranges.filter((range) => range.from !== null || range.to !== null);
        // We validate the changes
        this.validateChanges();

        if (!this.errors) {
          // We emit the new ranges
          this.eventEmitter.emit(CarrierFormEventMap.rangesUpdated, this.ranges);
          // We close the modal
          this.closeModal();
        }
      },
      validateChanges() {
        const table = <HTMLElement>document.querySelector('.table-carrier-ranges-modal');
        // Reset errors
        this.errors = false;
        this.overlappingAlert = false;
        this.negativeRangeAlert = false;
        // We remove the error class from all inputs already in error
        table.querySelectorAll('input.is-invalid').forEach((input) => {
          input.classList.remove('is-invalid');
        });

        // We sort the ranges by min values
        this.ranges.sort((a, b) => (a.from || 0) - (b.from || 0));

        // We check ranges
        let saveMax: null | number = null;
        this.ranges.forEach((range, index) => {
          // Check if all fields are filled and are not negative
          if (range.from === null || typeof range.from === 'string' || range.from < 0) {
            table.querySelectorAll(`tr[data-row="${index}"] input.form-from`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
            this.negativeRangeAlert = true;
          }
          if (range.to === null || typeof range.to === 'string' || range.to < 0) {
            table.querySelectorAll(`tr[data-row="${index}"] input.form-to`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
            this.negativeRangeAlert = true;
          }
          // Check overlapping
          if (saveMax !== null && range.from !== null && range.from < saveMax) {
            table.querySelectorAll(`tr[data-row="${index - 1}"] input.form-to`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            table.querySelectorAll(`tr[data-row="${index}"] input.form-from`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
            this.overlappingAlert = true;
          }

          // Check from < to for each range
          if (range.to !== null && range.from !== null && range.to <= range.from) {
            table.querySelectorAll(`tr[data-row="${index}"] input.form-to`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
          }

          saveMax = range.to;
        });
      },
      addRange(index: undefined | number) {
        // Add new range at the index specified, at the bottom if not specified
        // (with "from" already set to the previous "to")
        if (index === undefined) {
          this.ranges.push({from: this.ranges[this.ranges.length - 1]?.to, to: null});
        } else {
          this.ranges.splice(index + 1, 0, {from: this.ranges[index]?.to, to: null});
        }
      },
      deleteRange(rangeIndex: number) {
        // We remove the selected range
        this.ranges.splice(rangeIndex, 1);
        // We add an empty range if there is none
        if (this.ranges.length === 0) {
          this.ranges.push({from: null, to: null});
        }
      },
    },
  });
</script>

<style lang="scss" type="text/scss" scoped>
@import '~@scss/config/_settings.scss';

.modal {
  .modal-footer {
    justify-content: space-between;
  }

  .table {
    margin-bottom: 0;
    border-bottom: 0;

    tr td {
      border: 0;
    }
  }

  .btn-delete,
  .btn-add {
    border: none;
    background: none;

    i {
      font-size: 1.2em;
    }
  }

  .table-container {
    max-height: 60vh;
    overflow-y: auto;
  }
}
</style>
