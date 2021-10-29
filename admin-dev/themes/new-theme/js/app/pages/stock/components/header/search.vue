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
    id="search"
    class="row mb-2"
  >
    <div class="col-md-8">
      <div class="mb-2">
        <form
          class="search-form"
          @submit.prevent
        >
          <label>{{ trans('product_search') }}</label>
          <div class="input-group">
            <PSTags
              ref="psTags"
              :tags="tags"
              @tagChange="onSearch"
            />
            <div class="input-group-append">
              <PSButton
                @click="onClick"
                class="search-button"
                :primary="true"
              >
                <i class="material-icons">search</i>
                {{ trans('button_search') }}
              </PSButton>
            </div>
          </div>
        </form>
      </div>
      <Filters @applyFilter="applyFilter" />
    </div>
    <div class="col-md-4 alert-box">
      <transition name="fade">
        <PSAlert
          v-if="showAlert"
          :alert-type="alertType"
          :has-close="true"
          @closeAlert="onCloseAlert"
        >
          <span v-if="error">{{ trans('alert_bulk_edit') }}</span>
          <span v-else>{{ trans('notification_stock_updated') }}</span>
        </PSAlert>
      </transition>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import PSTags from '@app/widgets/ps-tags.vue';
  import PSButton from '@app/widgets/ps-button.vue';
  import PSAlert from '@app/widgets/ps-alert.vue';
  import {EventBus} from '@app/utils/event-bus';
  import Filters from './filters.vue';

  export default Vue.extend({
    components: {
      Filters,
      PSTags,
      PSButton,
      PSAlert,
    },
    computed: {
      error(): boolean {
        return (this.alertType === 'ALERT_TYPE_DANGER');
      },
    },
    methods: {
      onClick(): void {
        const refPsTags = this.$refs.psTags as VTags;
        const {tag} = refPsTags;
        refPsTags.add(tag);
      },
      onSearch(): void {
        this.$emit('search', this.tags);
      },
      applyFilter(filters: Array<any>): void {
        this.$emit('applyFilter', filters);
      },
      onCloseAlert(): void {
        this.showAlert = false;
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    mounted() {
      EventBus.$on('displayBulkAlert', (type: string) => {
        this.alertType = type === 'success' ? 'ALERT_TYPE_SUCCESS' : 'ALERT_TYPE_DANGER';
        this.showAlert = true;
        setTimeout(() => {
          this.showAlert = false;
        }, 5000);
      });
    },
    data() {
      return {
        tags: [],
        showAlert: false,
        alertType: 'ALERT_TYPE_DANGER',
        duration: false,
      };
    },
  });
</script>
