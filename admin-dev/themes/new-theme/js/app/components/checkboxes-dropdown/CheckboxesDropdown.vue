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
    <div
      class="dropdown"
      :data-role="`filter-by-${label.toLowerCase()}-block`"
    >
      <button
        :class="[
          'btn',
          'dropdown-toggle',
          selectedChoiceIds.length > 0 ? 'btn-primary' : 'btn-outline-secondary',
          'btn',
          {disabled: this.disabled}
        ]"
        type="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        :data-role="`filter-by-${label.toLowerCase()}-btn`"
      >
        {{ label }} {{ nbFiles }}
      </button>
      <div
        class="dropdown-menu"
        @click="preventClose"
      >
        <div
          class="md-checkbox"
          v-for="choice in choices"
          :key="choice.id"
        >
          <label class="dropdown-item">
            <div class="md-checkbox-container">
              <input
                :value="choice.id"
                :name="choice.name"
                type="checkbox"
                :checked="isSelected(choice)"
                @change="toggleSelection(choice)"
              >
              <i class="md-checkbox-control" />
              {{ choice.label }}
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent, PropType} from 'vue';
  import {Choice} from '@app/components/checkboxes-dropdown/types';

  export default defineComponent({
    props: {
      parentId: {
        type: Number,
        default: 1,
      },
      choices: {
        type: Array as PropType<Choice[]>,
        required: true,
      },
      selectedChoiceIds: {
        type: Array as PropType<number[]>,
        default: () => [],
      },
      label: {
        type: String,
        required: true,
      },
      disabled: {
        type: Boolean,
        default: false,
      },
    },
    computed: {
      nbFiles(): string | null {
        return this.selectedChoiceIds.length > 0
          ? `(${this.selectedChoiceIds.length})`
          : null;
      },
    },
    methods: {
      isSelected(choice: Choice): boolean {
        return this.selectedChoiceIds.some((id) => choice.id === id);
      },
      toggleSelection(choice: Choice): void {
        if (this.selectedChoiceIds.some((id) => choice.id === id)) {
          this.$emit('unselectChoice', choice, this.parentId);
        } else {
          this.$emit('selectChoice', choice, this.parentId);
        }
      },
      preventClose(event: Event): void {
        event.stopPropagation();
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
