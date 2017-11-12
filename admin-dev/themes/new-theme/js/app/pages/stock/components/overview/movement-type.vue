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
  <div class="col-md-4">
    <div class="movements">
      <PSButton
        type="button"
        class="update-qty float-sm-right"
        :class="classObject"
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
  import PSButton from 'app/widgets/ps-button';
  export default {
    computed: {
      disabled() {
        return !this.$store.state.hasQty;
      },
      classObject() {
        return {
          'btn-primary': !this.disabled,
        };
      },
    },
    methods: {
      sendQty() {
        this.$store.dispatch('updateQtyByProductsId');
      },
    },
    components: {
      PSButton,
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../../../../scss/config/_settings.scss";
  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
