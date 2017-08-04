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
  <div class="row product-actions">
    <div
      class="col-md-8 qty flex"
      :class="classObject"
    >
      <PSCheckbox id="bulk-action" class="m-t-1" />
      <div>
        <div class="m-l-1">
          <small>{{trans('title_bulk')}}</small>
        </div>
        <PSNumber
          class="m-l-1"
          @focus="focusIn"
          @blur="focusOut"
        />
      </div>
    </div>
    <MovementType />
  </div>
</template>

<script>
  import MovementType from './movement-type';
  import PSNumber from 'app/widgets/ps-number';
  import PSCheckbox from 'app/widgets/ps-checkbox';

  export default {
    computed: {
      classObject() {
        return {
          active: this.isActive,
        };
      },
    },
    methods: {
      focusIn() {
        this.isActive = true;
      },
      focusOut() {
        this.isActive = false;
      },
    },
    data: () => ({
      isActive: false,
    }),
    components: {
      MovementType,
      PSNumber,
      PSCheckbox,
    },
  };
</script>
