<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->

<template>
  <div class="content-topbar container-fluid">
    <div class="row py-2">
      <div class="col row ml-1">
        <PSCheckbox
          ref="low-filter"
          id="low-filter"
          @checked="onCheck"
        />
        <span class="ml-2">{{ trans('filter_low_stock') }}</span>
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
  import PSCheckbox from '@app/widgets/ps-checkbox.vue';
  import {defineComponent} from 'vue';
  import translate from '@app/pages/stock/mixins/translate';

  export default defineComponent({
    props: {
      filters: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    mixins: [translate],
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
        const filtersClone = {...this.filters};
        const params = $.param(filtersClone);

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
