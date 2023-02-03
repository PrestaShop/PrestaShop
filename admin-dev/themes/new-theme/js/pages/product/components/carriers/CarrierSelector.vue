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
  <div class="carrier-selector">
    <div
      class="form-check form-check-radio form-radio"
      @click="showModifyAllShopsCheckbox"
    >
      <div
        class="carrier-selector-line"
        v-if="carriers.length"
      >
        <checkboxes-dropdown
          :items="carriers"
          :label="getLabel"
          @addItem="addCarrier"
          @removeItem="removeCarrier"
          :event-emitter="eventEmitter"
          :initial-item-ids="this.initialCarrierIds"
          :clear-selected-items-event="this.clearSelectedCarriersEvent"
        />
        <button
          type="button"
          v-if="this.selectedCarrierIds.length > 0"
          class="btn btn-outline-secondary carrier-choices-clear"
          @click="clearAllSelected"
        >
          <i class="material-icons">close</i>
          {{ $t('allCarriers.label') }}
        </button>
      </div>
    </div>
    <div
      v-if="selectedCarrierIds.length"
      id="selected-carriers"
    >
      <span>
        <ul>
          <li
            v-for="(selectedCarrier, key) in getSelectedCarriers"
            :key="key"
          >
            {{ selectedCarrier.label }}<span v-if="key !== getSelectedCarriers.length -1 ">, </span>
            <input
              :name="choiceInputName"
              type="hidden"
              :value="selectedCarrier.id"
            >
          </li>
        </ul>
      </span>
    </div>
    <div
      v-if="modifyAllShopsVisible"
      class="form-check form-check-radio form-checkbox"
    >
      <div
        class="md-checkbox md-checkbox-inline"
      >
        <label class="required">
          <input
            :name="modifyAllShopsName"
            type="checkbox"
            class="form-check-input"
          ><i class="md-checkbox-control"/>{{ $t('modifyAllShops.label') }}</label>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import CheckboxesDropdown from '@app/components/CheckboxesDropdown.vue';
  import {defineComponent, PropType} from 'vue';
  import EventEmitter from '@components/event-emitter';
  import ProductEventMap from '@pages/product/product-event-map';
  import {Carrier} from '@pages/product/components/carriers/types';

  export default defineComponent({
    name: 'CarrierSelector',
    data(): {
      selectedCarrierIds: number[],
      modifyAllShopsVisible: boolean,
      clearSelectedCarriersEvent: string,
    } {
      return {
        selectedCarrierIds: [],
        modifyAllShopsVisible: false,
        clearSelectedCarriersEvent: ProductEventMap.shipping.clearSelectedCarriers,
      };
    },
    props: {
      initialCarrierIds: {
        type: Array as PropType<number[]>,
        required: true,
      },
      carriers: {
        type: Array as PropType<Carrier[]>,
        required: true,
      },
      eventEmitter: {
        type: Object as PropType<typeof EventEmitter>,
        required: true,
      },
      modifyAllShopsName: {
        type: String,
        required: true,
      },
      choiceInputName: {
        type: String,
        required: true,
      },
    },
    components: {
      CheckboxesDropdown,
    },
    mounted() {
      this.selectedCarrierIds = this.initialCarrierIds;
    },
    computed: {
      getSelectedCarriers(): Carrier[] {
        return this.carriers.filter((carrier: Carrier) => this.selectedCarrierIds.includes(carrier.id));
      },
      getLabel(): string {
        return this.selectedCarrierIds.length > 0
          ? this.$t('selectedCarriers.label')
          : this.$t('allCarriers.label');
      },
    },
    methods: {
      showModifyAllShopsCheckbox(): void {
        this.modifyAllShopsVisible = true;
      },
      addCarrier(carrier: Carrier): void {
        this.selectedCarrierIds.push(carrier.id);
      },
      removeCarrier(carrier: Carrier): void {
        this.selectedCarrierIds = this.selectedCarrierIds.filter(
          (id: number) => carrier.id !== id,
        );
      },
      clearAllSelected(): void {
        this.eventEmitter.emit(this.clearSelectedCarriersEvent);
        this.selectedCarrierIds = [];
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.carrier-selector {
  .control-label {
    font-weight: 600;
    color: #000;
    margin-bottom: 1rem;
  }

  &-line {
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
    margin: 0 -0.35rem;
  }
  #selected-carriers ul li {
    list-style-type: none;
    display: inline;
  }
}
</style>
