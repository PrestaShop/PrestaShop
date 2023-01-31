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
    >
      <div
        class="carrier-selector-line"
        v-if="carriers.length"
      >
        <checkboxes-dropdown
          :items="carriers"
          :parent-id="1"
          :label="getLabel()"
          @addItem="addCarrier"
          @removeItem="removeCarrier"
          :event-emitter="eventEmitter"
          :disabled="allCarriersSelected"
        />
      </div>
    </div>
    <div
      v-if="selectedCarriers.length"
      id="selected-carriers"
    >
      <span
        v-if="selectedCarriers.length && !allCarriersSelected"
      >
        <ul>
          <li v-for="(selectedCarrier, key) in selectedCarriers">
            {{ selectedCarrier.label }}<span v-if="key !== selectedCarriers.length -1 ">, </span>
            <input
              type="hidden"
              :value="selectedCarrier.value"
              :disabled="allCarriersSelected"
            >
          </li>
        </ul>
      </span>
    </div>
  </div>
</template>

<script lang="ts">
  import checkboxesDropdown from '@app/components/checkboxesDropdown.vue';
  import {defineComponent, PropType} from 'vue';

  export interface Carrier {
    id: number,
    name: string,
    label: string,
  }

  export default defineComponent({
    name: 'CarrierSelector',
    data(): {
      selectedCarriers: Carrier[],
      allCarriersSelected: boolean,
    } {
      return {
        selectedCarriers: [],
        allCarriersSelected: false,
      };
    },
    props: {
      carriers: {
        type: Array as PropType<Carrier[]>,
        required: true,
      },
      eventEmitter: {
        type: Object,
        required: true,
      },
    },
    components: {
      checkboxesDropdown,
    },
    mounted() {
      this.selectedCarriers = [];
      // this.eventEmitter.on(CombinationEvents.clearCarriers, () => this.clearAll());
    },
    methods: {
      /**
       * This methods is used to initialize product carriers
       */
      addCarrier(carrier: Carrier): void {
        this.selectedCarriers.push(carrier);
        this.updateCarriers();
      },
      removeCarrier(carrier: Carrier): void {
        this.selectedCarriers = this.selectedCarriers.filter(
          (e: Carrier) => carrier.id !== e.id,
        );

        this.updateCarriers();
      },
      toggleAllSelected(event: Event): void {
        const input = <HTMLInputElement> event.currentTarget;
        this.allCarriersSelected = input.id === 'select-all-carriers-checkbox' && input.checked;
      },
      updateCarriers(): void {
        // this.eventEmitter.emit(CombinationEvents.updateAttributeGroups, this.selectedCarriers);
      },
      getLabel(): string {
        return this.selectedCarriers.length > 0
          ? this.$t('selectedCarriers.label')
          : this.$t('allCarriers.label');
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
