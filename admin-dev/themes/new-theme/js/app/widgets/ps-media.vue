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
  <div class="media">
    <img v-if="displayThumb" :src="thumbnail" class="thumbnail d-flex"  />
    <div v-else class="no-img"></div>
    <div class="ml-2 desc media-body">
      <slot />
    </div>
  </div>
</template>
<script>
  export default {
    props: ['thumbnail'],
    computed: {
      displayThumb() {
        return !!this.thumbnail;
      },
    },
  };
</script>

<style lang="sass" scoped>
  @import "../../../scss/config/_settings.scss";
  .product-title {
    .has-combination & {
      font-weight: 600;
    }
  }
  .thumbnail, .no-img {
      border: $gray-light 1px solid;
      max-width: 47px;
  }
  .no-img {
    background: white;
    width: 47px;
    height: 47px;
    display: inline-block;
    vertical-align: middle;
  }
  .desc {
    white-space: normal;
  }
  small {
    color: $gray-medium;
  }
</style>
