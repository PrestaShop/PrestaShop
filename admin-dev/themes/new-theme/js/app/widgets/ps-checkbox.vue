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
  <div class="custom-checkbox">
    <div class="checkbox">
      <input type="checkbox" :id="id" v-model="checked" :class="{'indeterminate' : isIndeterminate }">
      <span @click="onClick"></span>
    </div>
    <slot name="label"></slot>
  </div>
</template>

<script>
  export default {
    props: ['id', 'model', 'isIndeterminate'],
    watch: {
      checked(val) {
        this.$emit('checked', {
          checked: val,
          item: this.model,
        });
      },
    },
    methods: {
      onClick() {
        this.checked = !this.checked;
      },
    },
    data: () => ({
      checked: false,
    }),
  };
</script>
<style lang="sass" type="text/scss" scoped>
  @import "../../../scss/config/_settings.scss";
  .custom-checkbox {
    & > .checkbox {
      width: 15px;
      height: 15px;
      position: relative;
      background: white;
      display: inline-block;
      vertical-align: baseline;
      margin-bottom: -2px; // same as border
      span {
        width: 15px;
        height: 15px;
        cursor: pointer;
        position: absolute;
        top: 0;
        left: 0;
        border: 2px $gray-light solid;
        border-radius: 2px;
        &:after {
          content: '';
          width: 12px;
          height: 5px;
          position: absolute;
          top: 1px;
          left: 0;
          border: 2px solid white;
          border-top: none;
          border-right: none;
          background: transparent;
          opacity: 0;
          transform: rotate(-45deg);
        }
        &::before {
          content: '';
          width: 12px;
          height: 12px;
          position: absolute;
          top: 0;
          left: 0;
        }
      }
      input[type=checkbox] {
        display: none;
        &:checked + span {
          border: 2px $brand-primary solid;
        }
        &:checked + span:before {
          background: $brand-primary;
        }
        &:checked + span:after {
          opacity: 1;
        }
        &.indeterminate + span:after {
          transform: rotate(0);
          height: 0;
          width: 11px;
          top: 4px;
        }
      }
    }
  }
</style>
