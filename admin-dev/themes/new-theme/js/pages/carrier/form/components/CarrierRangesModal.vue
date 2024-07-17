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
        <button
          @click.prevent="addRange()"
          type="button"
          class="btn btn-sm btn-outline-primary"
        >
          <i class="material-icons">add_box</i>
          {{ $t('modal.addRange') }}
        </button>
      </template>
      <template #body>
        <div
          class="alert alert-danger"
          v-if="overlappingAlert"
          role="alert"
        >
          {{ $t('modal.overlappingAlert') }}
        </div>
        <div class="table-container">
          <table
            class="table table-carrier-ranges-modal"
            @drop="dragDrop"
          >
            <thead>
              <tr>
                <th/>
                <th>{{ $t('modal.col.min') }}</th>
                <th>{{ $t('modal.col.max') }} </th>
                <th>{{ $t('modal.col.action') }}</th>
              </tr>
            </thead>
            <tbody :key="refreshKey">
              <template
                :key="i"
                v-for="r,i in ranges"
              >
                <tr
                  :data-row="i"
                  draggable="true"
                  @dragstart="dragStart(i)"
                  @dragover.stop.prevent="(e) => dragOver(e, i)"
                  @dragend="dragEnd"
                >
                  <td>
                    <button
                      type="button"
                      class="btn-drag"
                    >
                      <i class="material-icons">drag_indicator</i>
                    </button>
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
                        <span class="input-group-text">€</span>
                      </div>
                      <input
                        type="number"
                        class="form-control form-min"
                        inputmode="decimal"
                        placeholder="0"
                        v-model="r.min"
                      >
                    </div>
                  </td>
                  <td>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">€</span>
                      </div>
                      <input
                        type="number"
                        class="form-control form-max"
                        inputmode="decimal"
                        placeholder="∞"
                        v-model="r.max"
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
        </div>
      </template>
    </modal>
  </div>
</template>

<script lang="ts">
  import Modal from '@PSVue/components/Modal.vue';
  import {defineComponent} from 'vue';
  import CarrierFormEventMap from '@pages/carrier/form/carrier-form-event-map';

  interface Range {
    min: number|null,
    max: number|null,
  }

  interface CarrierRangesModalStates {
    isModalShown: boolean, // define if the modal is shown
    ranges: Range[], // define the ranges currently displayed
    savedRanges: Range[], // define the ranges saved before the changes
    dragIndex: null|number, // store the index of the range being dragged
    refreshKey: number, // force the refresh of the table by incrementing this key
    errors: boolean, // define if there are errors in the ranges
    overlappingAlert: boolean, // define if there are overlapping ranges (and display an alert)
  }

  export default defineComponent({
    name: 'CarrierRangesModal',
    components: {Modal},
    data(): CarrierRangesModalStates {
      return {
        isModalShown: false,
        ranges: [],
        savedRanges: [],
        dragIndex: null,
        refreshKey: 0,
        errors: false,
        overlappingAlert: false,
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
      this.eventEmitter.on(CarrierFormEventMap.openRangeSelectionModal, () => this.openModal());
    },
    methods: {
      openModal() {
        // We add a class to the body to prevent scrolling
        document.querySelector('body')?.classList.add('overflow-hidden');
        this.isModalShown = true;

        // We save the ranges to be able to cancel the changes
        this.savedRanges.splice(0, this.savedRanges.length);
        this.ranges.forEach((range) => this.savedRanges.push({min: range.min, max: range.max}));

        // We add an empty range if there is none
        if (this.ranges.length === 0) {
          this.ranges.push({min: null, max: null});
        }

        // We reset the errors
        this.errors = false;
        this.overlappingAlert = false;
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
        this.savedRanges.forEach((range) => this.ranges.push({min: range.min, max: range.max}));
        // We remove empty ranges
        this.ranges = this.ranges.filter((range) => range.min !== null || range.max !== null);
        // Then, we close the modal
        this.closeModal();
      },
      applyChanges() {
        // We remove empty ranges
        this.ranges = this.ranges.filter((range) => range.min !== null || range.max !== null);
        // We validate the changes
        this.validateChanges();

        if (!this.errors) {
          // We emit the new ranges
          this.eventEmitter.emit('updateRanges', this.ranges);
          // We close the modal
          this.closeModal();
        }
      },
      validateChanges() {
        const table = <HTMLElement> document.querySelector('.table-carrier-ranges-modal');
        // Reset errors
        this.errors = false;
        this.overlappingAlert = false;
        // We remove the error class from all inputs already in error
        table.querySelectorAll('input.is-invalid').forEach((input) => {
          input.classList.remove('is-invalid');
        });

        // We sort the ranges by min values
        this.ranges.sort((a, b) => (a.min || 0) - (b.min || 0));

        // We check ranges
        let saveMax: null|number = null;
        this.ranges.forEach((range, index) => {
          // Check overlapping
          if (saveMax !== null && range.min !== null && range.min < saveMax) {
            table.querySelectorAll(`tr[data-row="${index - 1}"] input.form-max`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            table.querySelectorAll(`tr[data-row="${index}"] input.form-min`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
            this.overlappingAlert = true;
          }

          // Check min < max for each range
          if (range.max !== null && range.min !== null && range.max <= range.min) {
            table.querySelectorAll(`tr[data-row="${index}"] input.form-max`)
              .forEach((input) => {
                input.classList.add('is-invalid');
              });
            this.errors = true;
          }

          saveMax = range.max;
        });
      },
      addRange(index: undefined|number) {
        // Add new range at the index specified, at the bottom if not specified
        // (with "min" already set to the previous "max")
        if (index === undefined) {
          this.ranges.push({min: this.ranges[this.ranges.length - 1]?.max, max: null});
        } else {
          this.ranges.splice(index + 1, 0, {min: this.ranges[index]?.max, max: null});
        }
      },
      deleteRange(rangeIndex: number) {
        // We remove the selected range
        this.ranges.splice(rangeIndex, 1);
        // We add an empty range if there is none
        if (this.ranges.length === 0) {
          this.ranges.push({min: null, max: null});
        }
      },
      dragStart(rangeIndex: number) {
        // We save the current range index to know which one is being dragged
        this.dragIndex = rangeIndex;
      },
      dragOver(event: DragEvent, rangeIndex: number) {
        // We retrieve the row being dragged and the row being hovered
        const table = <HTMLElement> document.querySelector('.table-carrier-ranges-modal');
        const row = <HTMLElement> table.querySelector(`tr[data-row="${this.dragIndex}"]`);
        const rowHover = <HTMLElement> table.querySelector(`tr[data-row="${rangeIndex}"]`);

        // We move the dragged row before or after the hovered row depending on the position of the mouse on it
        if (row !== null && rowHover !== null) {
          if (event.offsetY > rowHover.offsetHeight / 2) {
            rowHover.parentNode?.insertBefore(row, rowHover.nextSibling);
          } else {
            rowHover.parentNode?.insertBefore(row, rowHover);
          }
        }
      },
      dragEnd() {
        // We reorder the ranges according to the new order when the drag ending
        const rangesNewOrder:Range[] = [];
        const rows = document.querySelectorAll('.table-carrier-ranges-modal tbody tr[data-row]');
        rows.forEach((row) => {
          const rowId = row.getAttribute('data-row');

          if (rowId !== null) {
            rangesNewOrder.push(this.ranges[parseInt(rowId, 10)]);
          }
        });

        // We update the ranges and +1 to the refreshKey to force the refresh
        this.ranges.splice(0, this.ranges.length, ...rangesNewOrder);
        this.dragIndex = null;
        this.refreshKey += 1;
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

    .btn-delete, .btn-drag, .btn-add {
      border: none;
      background: none;

      i {
        font-size: 1.2em;
      }
    }

    .btn-drag {
      cursor: move!important;
      outline: none;
      user-select: none;
    }

    .table-container {
      max-height: 60vh;
      overflow-y: auto;
    }
  }

</style>
