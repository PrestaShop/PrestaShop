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
    class="ps-sortable-column"
    :data-sort-col-name="this.order"
    :data-sort-is-current="isCurrent"
    :data-sort-direction="sortDirection"
    @click="sortToggle"
  >
    <span role="columnheader"><slot /></span>
    <span
      role="button"
      class="ps-sort"
      aria-label="Tri"
    />
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      // column name
      order: {
        type: String,
        required: true,
      },
      // indicates the currently sorted column in the table
      currentSort: {
        type: String,
        required: true,
      },
    },
    methods: {
      sortToggle(): void {
        // toggle direction
        this.sortDirection = (this.sortDirection === 'asc') ? 'desc' : 'asc';
        this.$emit('sort', this.order, this.sortDirection);
      },
    },
    data() {
      return {
        sortDirection: 'desc',
      };
    },
    computed: {
      isCurrent(): boolean {
        return this.currentSort === this.order;
      },
    },
  });
</script>
