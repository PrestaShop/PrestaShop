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
  <div class="input-group date">
    <input type='text' class="form-control" />
    <div class="input-group-append">
      <span class="input-group-text">
        <i class="material-icons">event</i>
      </span>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      locale: {
        type: String,
        default: 'en',
      },
    },
    mounted() {
      $(this.$el).datetimepicker({
        format: 'MM/DD/YYYY',
        showClear: true,
      }).on('dp.change', (infos) => {
        if (infos.date) {
          this.$emit('dpChange', infos);
        } else {
          this.$emit('reset', infos);
        }
      });
    },
  };
</script>

<style lang="sass">
  @import "../../../scss/config/_settings.scss";

  .date {
    a[data-action='clear']::before {
      font-family: 'Material Icons';
      content: "\E14C";
      font-size: 20px;
      position: absolute;
      bottom: 15px;
      left: 50%;
      margin-left: -10px;
      color: $gray-dark;
      cursor:pointer;
    }
    .bootstrap-datetimepicker-widget tr td span:hover {
      background-color: white;
    }
  }

</style>
