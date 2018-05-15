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
  <ul class="nav nav-tabs" id="tab" role="tablist">
    <li class="nav-item">
      <router-link data-toggle="tab" class="nav-link" :class="{active : isOverview}" to="/" role="tab">{{trans('menu_stock')}}</router-link>
    </li>
    <li class="nav-item">
       <router-link data-toggle="tab" class="nav-link" :class="{active : isMovements}" to="/movements" role="tab">{{trans('menu_movements')}}</router-link>
    </li>
  </ul>
</template>

<script>
  export default {
    computed: {
      isOverview() {
        return this.$route.name === 'overview';
      },
      isMovements() {
        return this.$route.name === 'movements';
      },
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../../../../scss/config/_settings.scss";
  .nav-item {
    cursor: pointer;
  }
  .nav-link.active, .nav-link.active:hover, .nav-link.active:focus {
    border-top: none;
    border-bottom: 3px solid $brand-primary;
  }
</style>
