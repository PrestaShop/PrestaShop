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
  <div class="combinations-filters-dropdown">
    <div class="dropdown">
      <button
        :class="[
          'btn',
          'dropdown-toggle',
          selectedFilters.length > 0 ? 'btn-primary' : 'btn-outline-secondary',
          'btn',
        ]"
        type="button"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
        id="form_invoice_prefix"
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
          v-for="filter in children"
          :key="filter.id"
        >
          <label class="dropdown-item">
            <div class="md-checkbox-container">
              <input
                type="checkbox"
                :checked="isChecked(filter)"
                @change="toggleFilter(filter)"
              >
              <i class="md-checkbox-control" />
              {{ filter.name }}
            </div>
          </label>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';

  export default Vue.extend({
    name: 'FilterDropdown',
    data(): {selectedFilters: Array<Record<string, any>>} {
      return {
        selectedFilters: [],
      };
    },
    props: {
      parentId: {
        type: Number,
        required: true,
      },
      children: {
        type: Array,
        required: true,
      },
      label: {
        type: String,
        required: true,
      },
    },
    mounted() {
      this.$parent.$on('clearAll', this.clear);
    },
    computed: {
      nbFiles(): string | null {
        return this.selectedFilters.length > 0
          ? `(${this.selectedFilters.length})`
          : null;
      },
    },
    methods: {
      isChecked(filter: Record<string, any>): boolean {
        return this.selectedFilters.includes(filter);
      },
      toggleFilter(filter: Record<string, any>): void {
        if (this.selectedFilters.includes(filter)) {
          this.$emit('removeFilter', filter, this.parentId);
          this.selectedFilters = this.selectedFilters.filter(
            (item) => item.id !== filter.id,
          );
        } else {
          this.$emit('addFilter', filter, this.parentId);
          this.selectedFilters.push(filter);
        }
      },
      preventClose(event: Event): void {
        event.stopPropagation();
      },
      clear(): void {
        this.selectedFilters = [];
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";
@import "~@scss/config/_bootstrap.scss";

.combinations-filters-dropdown {
  margin: 0 0.35rem;

  @include media-breakpoint-down(xs) {
    margin-bottom: .5rem;
  }

  .dropdown-item {
    padding: 0.438rem 0.938rem;
    padding-right: 1rem;
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
