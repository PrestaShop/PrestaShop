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
  <div class="header-toolbar">
    <div class="container-fluid">
      <Breadcrumb />
      <div class="title-row">
        <h1 class="title">
          {{ trans('head_title') }}
        </h1>
      </div>
    </div>
    <Tabs />
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import ComponentsMap from '@components/components-map';
  import Breadcrumb from './breadcrumb.vue';
  import Tabs from './tabs.vue';

  const {$} = window;

  function getOldHeaderToolbarButtons() {
    return $('.header-toolbar')
      .first()
      .find('.toolbar-icons');
  }

  function getNotificationsElements() {
    return $(`${ComponentsMap.ajaxConfirmation}, #${ComponentsMap.contextualNotification.messageBoxId}`);
  }

  export default Vue.extend({
    components: {
      Breadcrumb,
      Tabs,
    },
    mounted() {
      const $vueElement = $(this.$el);
      // move the toolbar buttons to this header
      const toolbarButtons = getOldHeaderToolbarButtons();
      toolbarButtons.insertAfter($vueElement.find('.title-row > .title'));

      const notifications = getNotificationsElements();
      notifications.insertAfter($vueElement);

      // signal header change (so size can be updated)
      const event = $.Event('vueHeaderMounted', {
        name: 'stock-header',
      });
      $(document).trigger(event);
    },
  });
</script>
