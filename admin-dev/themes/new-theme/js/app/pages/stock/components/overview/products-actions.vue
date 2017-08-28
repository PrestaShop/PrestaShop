<!--**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="row product-actions">
    <div
      class="col-md-8 qty flex"
      :class="{'active' : isActive}"
    >
      <PSCheckbox
        id="bulk-action"
        class="m-t-1"
        :checked="false"
        :isIndeterminate="isIndeterminate"
        @checked="bulkChecked"
      />
      <div>
        <div class="m-l-1">
          <small>{{trans('title_bulk')}}</small>
        </div>
        <PSNumber
          class="m-l-1"
          @focus="focusIn"
          @blur="focusOut"
          @change="onChange"
        />
      </div>
    </div>
    <div class="col-md-4">
      <PSButton
        type="button"
        class="update-qty pull-xs-right"
        :class="{'btn-primary': disabled }"
        :disabled="disabled"
        :primary="true"
        @click="sendQty"
      >
        <i class="material-icons">edit</i>
        {{trans('button_movement_type')}}
      </PSButton>
    </div>
  </div>
</template>

<script>
  import PSNumber from 'app/widgets/ps-number';
  import PSCheckbox from 'app/widgets/ps-checkbox';
  import PSButton from 'app/widgets/ps-button';

  export default {
    computed: {
      disabled() {
        return !this.$store.state.hasQty;
      },
    },
    methods: {
      focusIn() {
        this.isActive = true;
      },
      focusOut() {
        this.isActive = false;
      },
      bulkChecked(state) {
        //this.isIndeterminate = state.checked;
      },
      sendQty() {
        this.$store.dispatch('updateQtyByProductsId');
      },
      onChange(value) {
        this.$store.dispatch('updateBulkEditQty', value);
      },
    },
    data: () => ({
      isActive: false,
      isIndeterminate: false,
    }),
    components: {
      PSNumber,
      PSCheckbox,
      PSButton,
    },
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
  .product-actions .qty {
    padding-left: 20px;
  }
</style>
