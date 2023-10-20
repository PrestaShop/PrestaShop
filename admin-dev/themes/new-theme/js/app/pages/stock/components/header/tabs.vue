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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div
    class="page-head-tabs"
    id="head_tabs"
  >
    <ul class="nav nav-pills">
      <li class="nav-item">
        <router-link
          data-toggle="tab"
          class="nav-link"
          :class="{active : isOverview}"
          to="/"
          role="tab"
        >
          {{ trans('menu_stock') }}
        </router-link>
      </li>
      <li class="nav-item">
        <router-link
          data-toggle="tab"
          class="nav-link"
          :class="{active : isMovements}"
          to="/movements"
          role="tab"
        >
          {{ trans('menu_movements') }}
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script lang="ts">
  import {defineComponent} from 'vue';
  import translate from '@app/pages/stock/mixins/translate';

  export default defineComponent({
    mixins: [translate],
    computed: {
      isOverview(): boolean {
        return this.$route.name === 'overview';
      },
      isMovements(): boolean {
        return this.$route.name === 'movements';
      },
    },
  });
</script>
