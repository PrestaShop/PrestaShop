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
  <div class="translations-header">
    <Breadcrumb />
    <h1>{{trans('head_title')}}</h1>
  </div>
</template>

<script>
  import Breadcrumb from './breadcrumb';

  export default {
    components: {
      Breadcrumb,
    },
  };
</script>

<style lang="sass?outputStyle=expanded" scoped>
  @import "../../../../../../scss/config/_settings.scss";
  .translations-header {
    position: fixed;
    top: 40px;
    background: white;
    width: 100%;
    z-index: 3;
    border-bottom: 1px solid $gray-light;
    height: 128px;
  }
</style>
