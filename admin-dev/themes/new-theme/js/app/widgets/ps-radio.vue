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
  <div class="radio">
    <input type="radio" :id="id" name="radio-group" :checked="checked" @change="onChange">
    <label :for="id">{{label}}</label>
  </div>
</template>

<script>
  export default{
    props: {
      id: { type: String, required: true },
      label: { type: String, required: false },
      checked: { type: Boolean, required: false },
      value: { type: String, required: false },
    },
    methods: {
      onChange() {
        this.$emit('change', this.value);
      },
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../scss/config/_settings.scss";
  [type="radio"]:checked,
  [type="radio"]:not(:checked) {
    position: absolute;
    left: -9999px;
  }
  [type="radio"]:checked + label,
  [type="radio"]:not(:checked) + label
  {
    position: relative;
    padding-left: 28px;
    cursor: pointer;
    line-height: 20px;
    display: inline-block;
    color: $gray-dark;
  }
  [type="radio"]:checked + label:before,
  [type="radio"]:not(:checked) + label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 18px;
    height: 18px;
    border: 2px solid $gray-medium;
    border-radius: 100%;
    background: #ffffff;
  }
  [type="radio"]:checked + label:before {
    border: 2px solid $primary;
  }
  [type="radio"]:checked + label:after,
  [type="radio"]:not(:checked) + label:after {
    content: '';
    width: 10px;
    height: 10px;
    background: $primary;
    position: absolute;
    top: 4px;
    left: 4px;
    border-radius: 100%;
    transition: all 0.2s ease;
  }
  [type="radio"]:not(:checked) + label:after {
    opacity: 0;
    transform: scale(0);
  }
  [type="radio"]:checked + label:after {
    opacity: 1;
    transform: scale(1);
  }
</style>
