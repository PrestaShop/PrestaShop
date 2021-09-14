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
  <div>
    test
  </div>
</template>

<script>
  import ProductMap from '@pages/product/product-map';
  import Router from '@components/router';

  const {$} = window;
  const router = new Router();
  const SpecificPriceMap = ProductMap.specificPrice;

  export default {
    name: 'SpecificPriceList',
    data() {
      return {
        getListDataUrl: router.generate(
          // @todo: new route to load list data
          'admin_products_specific_prices_create',
          {
            productId: this.productId,
          },
        ),
        container: null,
      };
    },
    props: {
      eventEmitter: {
        type: Object,
        required: true,
      },
      productId: {
        type: Number,
        required: true,
      },
    },
    mounted() {
      this.container = $(SpecificPriceMap.container);
      this.watchActions();
    },
    methods: {
      watchActions() {
        this.container.on(
          'click',
          SpecificPriceMap.editBtn,
          (event) => {
            event.stopImmediatePropagation();
            // @todo: open edit modal
          },
        );
        this.container.on(
          'click',
          SpecificPriceMap.deleteBtn,
          (event) => {
            event.stopImmediatePropagation();
            // @todo: delete specific price (show confirm modal first?)
          },
        );
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import '~@scss/config/_settings.scss';

#specific-price-form-modal .specific-price-modal {

}
</style>
