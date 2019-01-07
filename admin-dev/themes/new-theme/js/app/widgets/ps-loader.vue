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
  <div class="ps-loader">
    <div class="timeline-item">
      <div class="animated-background">
        <slot />
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['thumb'],
  };
</script>
<style lang="sass" scoped>
  @import "../../../scss/config/_settings.scss";
  .ps-loader {
    width: 100%;
    .animated-background {
        animation-duration: 1s;
        animation-iteration-count: infinite;
        animation-name: loading;
        animation-timing-function: linear;
        background: $gray-soft;
        background: linear-gradient(to right, $gray-soft 8%, #ccc 18%, $gray-soft 33%);
        background-size: 100%;
        height: 40px;
        position: relative;
    }

    .background-masker {
      background: white;
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
