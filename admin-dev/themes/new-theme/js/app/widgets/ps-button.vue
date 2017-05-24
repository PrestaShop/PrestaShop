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
  <button type="button" class="btn" :class="classObject" @click="onClick" >
    <slot />
  </button>
</template>

<script>
 export default {
   props: {
     primary: { type: Boolean, required: true },
     ghost: { type: Boolean },
   },
   computed: {
     classObject() {
       return {
         'btn-primary': this.primary,
         'btn-secondary': !this.primary,
         ghost: this.ghost,
       };
     },
   },
   methods: {
     onClick() {
       this.$emit('click');
     },
   },
 };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .btn {
    border-radius:0;
    height: 34px;
  }
  .btn-secondary {
    background-color: $gray-medium;
    border: none;
    &.ghost {
      background-color: white;
      border: $gray-medium 2px solid;
      color: $gray-medium;
      &:hover {
        border: $gray-dark 2px solid;
      }
    }
    &:hover {
      background-color: $gray-dark;
      color: white;
    }
  }

</style>
