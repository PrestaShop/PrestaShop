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
  <div class="ps-checkboxes-dropdown">
    <div class="dropdown">
      <button
        :class="[
          'btn',
          'dropdown-toggle',
          selectedChoices.length > 0 ? 'btn-primary' : 'btn-outline-secondary',
          'btn',
          {disabled: this.disabled}
        ]"
        type="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        id="form_invoice_prefix"
        :data-role="`filter-by-${label.toLowerCase()}`"
      >
        {{ label }} {{ nbFiles }}
      </button>
      <div
        class="dropdown-menu"
        aria-labelledby="form_invoice_prefix"
        @click="preventClose($event)"
      >
        <div
          class="md-checkbox"
          v-for="choice in choices"
          :key="choice.id"
          :data-role="`${label.toLowerCase()}-${choice.id}`"
        >
          <label class="dropdown-item">
            <div class="md-checkbox-container">
              <input
                type="checkbox"
                :checked="isSelected(choice)"
                @change="toggleSelection(choice)"
              >
              <i class="md-checkbox-control" />
              {{ choice.name }}
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import EventEmitter from '@components/event-emitter';
  import {Choice} from '@app/components/checkboxes-dropdown/types';

  export default defineComponent({
    data(): {
      selectedChoices: Array<Record<string, any>>
    } {
      return {
        selectedChoices: [],
      };
    },
    props: {
      parentId: {
        type: Number,
        default: 1,
      },
      choices: {
        type: Array as PropType<Choice[]>,
        required: true,
      },
      initialChoiceIds: {
        type: Array as PropType<number[]>,
        default: () => [],
      },
      label: {
        type: String,
        required: true,
      },
      eventEmitter: {
        type: Object as PropType<typeof EventEmitter>,
        required: true,
      },
      // provide this property if you need to clear all selected events from parent component
      clearSelectedChoicesEvent: {
        type: String,
        default: '',
      },
      disabled: {
        type: Boolean,
        default: false,
      },
    },
    mounted() {
      this.selectedChoices = this.choices.filter((item) => this.initialChoiceIds.includes(item.id));
      if (this.clearSelectedChoicesEvent) {
        this.eventEmitter.on(this.clearSelectedChoicesEvent, () => this.clear());
      }
    },
    computed: {
      nbFiles(): string | null {
        return this.selectedChoices.length > 0
          ? `(${this.selectedChoices.length})`
          : null;
      },
    },
    methods: {
      isSelected(item: Record<string, any>): boolean {
        return this.selectedChoices.some((e) => item.id === e.id);
      },
      toggleSelection(item: Record<string, any>): void {
        if (this.selectedChoices.some((e) => item.id === e.id)) {
          this.$emit('unselectChoice', item, this.parentId);
          this.selectedChoices = this.selectedChoices.filter(
            (selectedItem) => selectedItem.id !== item.id,
          );
        } else {
          this.$emit('selectChoice', item, this.parentId);
          this.selectedChoices.push(item);
        }
      },
      preventClose(event: Event): void {
        event.stopPropagation();
      },
      clear(): void {
        this.selectedChoices = [];
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";
@import "~@scss/config/_bootstrap.scss";

.ps-checkboxes-dropdown {
  margin: 0 0.35rem;

  @include media-breakpoint-down(xs) {
    margin-bottom: .5rem;
  }

  .dropdown-item {
    padding: 0.438rem 1rem 0.438rem 0.938rem;
    line-height: normal;
    color: inherit;
    border-bottom: 0;

    .md-checkbox-container {
      position: relative;
      padding-left: 28px;
    }
  }
}
</style>
