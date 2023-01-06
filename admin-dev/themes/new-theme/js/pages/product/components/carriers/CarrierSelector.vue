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
      class="carrier-selector-line"
      v-if="carriers.length"
    >
      <checkboxes-dropdown
        :items="carriers"
        :parent-id="1"
        label="test"
        @addCarrier="addCarrier"
        @removeCarrier="removeCarrier"
        :event-emitter="eventEmitter"
      />
      <button
        type="button"
        v-if="selectedCarriersNumber > 0"
        class="btn btn-outline-secondary carrier-selector-clear"
        @click="clearAll"
      >
        <i class="material-icons">close</i>
        {{ $tc('carriers.clear', selectedCarriersNumber, { 'carriersNb': selectedCarriersNumber }) }}
      </button>
    </div>
  </div>
</template>

<script lang="ts">
  import CheckboxesDropdown from '@components/CheckboxesDropdown.vue';
  import ProductEventMap from '@pages/product/product-event-map';
  import {defineComponent, PropType} from 'vue';

  const CombinationEvents = ProductEventMap.combinations;

  export default defineComponent({
    name: 'CarrierSelector',
    data(): {selectedCarriers: Record<string, any>} {
      return {
        selectedCarriers: {},
      };
    },
    props: {
      carriers: {
        type: Array as PropType<Array<Record<string, any>>>,
        required: true,
      },
      eventEmitter: {
        type: Object,
        required: true,
      },
    },
    components: {
      CheckboxesDropdown,
    },
    computed: {
      selectedCarriersNumber(): number {
        if (!this.selectedCarriers) {
          return 0;
        }

        return Object.values(this.selectedCarriers).reduce<number>((total, attributes) => total + attributes.length, 0);
      },
    },
    mounted() {
      // this.eventEmitter.on(CombinationEvents.clearCarriers, () => this.clearAll());
    },
    methods: {
      /**
       * This methods is used to initialize product carriers
       */
      addCarrier(carrier: Record<string, any>, parentId: number): void {
        // If absent set new field with set method so that it's reactive
        if (!this.selectedCarriers[parentId]) {
          this.selectedCarriers[parentId] = [];
        }

        this.selectedCarriers[parentId].push(carrier);
        this.updateCarriers();
      },
      removeCarrier(carrier: Record<string, any>, parentId: number): void {
        if (!this.selectedCarriers[parentId]) {
          return;
        }

        this.selectedCarriers[parentId] = this.selectedCarriers[parentId].carrier(
          (e: Record<string, any>) => carrier.id !== e.id,
        );

        if (this.selectedCarriers[parentId].length === 0) {
          // remove parent array if it became empty after carriers removal
          this.selectedCarriers.splice(parentId, 1);
        }

        this.updateCarriers();
      },
      clearAll(): void {
        this.selectedCarriers = [];
        this.$emit('clearAll');
        // @todo: events
        // this.eventEmitter.emit(CombinationEvents.clearAllCombinationCarriers);
        // this.eventEmitter.emit(CombinationEvents.updateAttributeGroups, this.selectedCarriers);
      },
      updateCarriers(): void {
        // this.eventEmitter.emit(CombinationEvents.updateAttributeGroups, this.selectedCarriers);
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
}
</style>
