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
  <div class="content-topbar container-fluid">
    <div class="row py-2">
      <div class="col row ml-1">
        <PSCheckbox
          ref="low-filter"
          id="low-filter"
          class="mt-1"
          @checked="onCheck"
        >
          <span
            slot="label"
            class="ml-2"
          >{{ trans('filter_low_stock') }}</span>
        </PSCheckbox>
      </div>
      <div class="content-topbar-right col mr-3 d-flex align-items-center justify-content-end">
        <a :href="stockExportUrl">
          <span
            data-toggle="pstooltip"
            :title="stockExportTitle"
            data-html="true"
            data-placement="top"
          >
            <i class="material-icons">cloud_upload</i>
          </span>
        </a>
        <a
          class="ml-2"
          :href="stockImportUrl"
          target="_blank"
        >
          <span
            data-toggle="pstooltip"
            :title="stockImportTitle"
            data-html="true"
            data-placement="top"
          >
            <i class="material-icons">cloud_download</i>
          </span>
        </a>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import PSCheckbox from '@app/widgets/ps-checkbox.vue';

  export default Vue.extend({
    props: {
      filters: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    computed: {
      stockImportTitle(): string {
        return this.trans('title_import');
      },
      stockExportTitle(): string {
        return this.trans('title_export');
      },
      stockImportUrl(): string {
        return window.data.stockImportUrl;
      },
      stockExportUrl(): string {
        const params = $.param(this.filters);

        return `${window.data.stockExportUrl}&${params}`;
      },
    },
    methods: {
      onCheck(checkbox: HTMLInputElement): void {
        const isChecked = checkbox.checked ? 1 : 0;
        this.$emit('lowStockChecked', isChecked);
      },
    },
    mounted() {
      $('[data-toggle="pstooltip"]').pstooltip();
    },
    components: {
      PSCheckbox,
    },
  });
</script>
