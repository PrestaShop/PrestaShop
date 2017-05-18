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
  <div class="alert" :class="classObject" role="alert">
    <i v-if="isInfo" class="material-icons">info_outline</i>
    <i v-else class="material-icons">error_outline</i>
    <p class="alert-text">
      <slot />
    </p>
    <button v-if="hasClose" type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span class="material-icons">close</span>
    </button>
  </div>
</template>

<script>
  export default {
    props: {
      alertType: {
        type: String,
        required: true,
      },
      hasClose: {
        type: Boolean,
        required: true,
      },
    },
    computed: {
      classObject() {
        return {
          'alert-info': this.alertType === 'info',
          'alert-warning': this.alertType === 'warning',
          'alert-danger': this.alertType === 'danger',
        };
      },
      isInfo() {
        return this.alertType === 'info';
      },
    },
  };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .close {
    position: absolute;
    right: 0.625rem;
    top: 0.9375rem;
    opacity: 1;
    .material-icons {
      font-size: 1.5rem;
      .alert-info & {
        color: $primary;
      }
      .alert-warning & {
        color: $warning
      }
      .alert-danger & {
        color: $danger;
      }
    }
  }
  .alert {
    border-radius: 0;
    border-width: 0.125rem;
    padding: 0;
    &.alert-info {
      background: $notice;
    }
    &.alert-warning {
      background: $warning-hover;
    }
    &.alert-danger {
      background: $danger-hover;
    }
    .alert-text {
      padding: 0.9375rem 0 0.9375rem 0.9375rem;
      margin-left: 3.438rem;
      background: white;
    }
  }
</style>
