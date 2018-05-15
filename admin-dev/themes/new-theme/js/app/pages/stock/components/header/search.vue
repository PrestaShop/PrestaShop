<!--**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div id="search" class="row mb-2">
    <div class="col-md-8">
      <div class="mb-2">
        <form class="search-form" @submit.prevent>
          <label>{{trans('product_search')}}</label>
          <div class="input-group">
            <PSTags ref="psTags" :tags="tags" @tagChange="onSearch" />
            <span class="input-group-btn">
              <PSButton @click="onClick" class="search-button" :primary="true">
                <i class="material-icons">search</i>
                {{trans('button_search')}}
              </PSButton>
            </span>
          </div>
        </form>
      </div>
      <Filters @applyFilter="applyFilter"/>
    </div>
    <div class="col-md-4 alert-box">
      <transition name="fade">
        <PSAlert
          v-if="showAlert"
          :alertType="alertType"
          :hasClose="true"
          @closeAlert="onCloseAlert"
        >
          <span v-if="error">{{trans('alert_bulk_edit')}}</span>
          <span v-else>{{trans('notification_stock_updated')}}</span>
        </PSAlert>
      </transition>
    </div>
  </div>
</template>

<script>
  import Filters from './filters';
  import PSTags from 'app/widgets/ps-tags';
  import PSButton from 'app/widgets/ps-button';
  import PSAlert from 'app/widgets/ps-alert';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    components: {
      Filters,
      PSTags,
      PSButton,
      PSAlert,
    },
    computed: {
      error() {
        return (this.alertType === 'ALERT_TYPE_DANGER');
      },
    },
    methods: {
      onClick() {
        const tag = this.$refs.psTags.tag;
        this.$refs.psTags.add(tag);
      },
      onSearch() {
        this.$emit('search', this.tags);
      },
      applyFilter(filters) {
        this.$emit('applyFilter', filters);
      },
      onCloseAlert() {
        this.showAlert = false;
      },
    },
    watch: {
      $route() {
        this.tags = [];
      },
    },
    mounted() {
      EventBus.$on('displayBulkAlert', (type) => {
        this.alertType = type === 'success' ? 'ALERT_TYPE_SUCCESS' : 'ALERT_TYPE_DANGER';
        this.showAlert = true;
        setTimeout(_ => {
          this.showAlert = false;
        }, 5000);
      });
    },
    data: () => ({
      tags: [],
      showAlert: false,
      alertType: 'ALERT_TYPE_DANGER',
      duration: false,
    }),
  };
</script>
