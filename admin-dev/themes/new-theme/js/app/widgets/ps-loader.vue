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
  <div class="ps-loader">
    <div class="timeline-item">
      <div class="animated-background">
        <slot />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';

  export default defineComponent({
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .ps-loader {
    width: 100%;
    .animated-background {
      animation-duration: 1s;
      animation-iteration-count: infinite;
      animation-name: loading;
      animation-timing-function: linear;
      background: var(--#{$cdk}primary-200);
      background:
        linear-gradient(to right, var(--#{$cdk}primary-200) 10%,
        var(--#{$cdk}primary-500) 20%,
        var(--#{$cdk}primary-200) 30%);
      background-size: 100%;
      height: var(--#{$cdk}size-40);
      position: relative;
    }

    .background-masker {
      background: var(--#{$cdk}white);
      position: absolute;
    }
  }

  @keyframes loading{
    0%{
      background-position: -500px 0
    }
    100%{
      background-position: 500px 0
    }
  }
</style>
