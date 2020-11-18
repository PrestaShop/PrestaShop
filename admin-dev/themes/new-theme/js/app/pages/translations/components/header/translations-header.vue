<!--**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div class="header-toolbar">
    <div class="container-fluid">
      <Breadcrumb />
      <div class="title-row">
        <h1 class="title">{{trans('head_title')}}</h1>
      </div>
    </div>
  </div>
</template>

<script>
  import Breadcrumb from './breadcrumb';

  const $ = global.$;

  function getOldHeaderToolbarButtons() {
    return $('.header-toolbar')
      .first()
      .find('.toolbar-icons');
  }

  export default {
    components: {
      Breadcrumb,
    },
    mounted() {
      // move the toolbar buttons to this header
      const toolbarButtons = getOldHeaderToolbarButtons();
      toolbarButtons.insertAfter($(this.$el).find('.title-row > .title'));

      // signal header change (so size can be updated)
      const event = $.Event('vueHeaderMounted', {
        name: 'stock-header',
      });
      $(document).trigger(event);
    },
  };
</script>
