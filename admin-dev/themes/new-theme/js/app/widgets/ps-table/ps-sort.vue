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
  <button class="ps-sort" @click.prevent="sortFilter">
    <i class="material-icons">swap_vert</i>
  </button>
</template>

<script>
  export default {
    props: ['order'],
    methods: {
      sortFilter() {
        this.isSorted = !this.isSorted;
        this.$emit('sort', this.order, this.isSorted);
      },
    },
    data: () => ({ isSorted: true }),
  };
</script>

<style lang="sass" scoped>
  @import "../../../../scss/config/_settings.scss";
  .ps-sort {
    cursor: pointer;
    background: transparent;
    border: none;
    outline: none;
  }
</style>
