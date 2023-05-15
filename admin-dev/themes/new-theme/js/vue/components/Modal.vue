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
    <transition name="fade">
      <div class="modal show">
        <div
          class="modal-dialog modal-dialog-centered"
          role="document"
        >
          <div
            class="modal-content"
            aria-labelledby="modalTitle"
            aria-describedby="modalDescription"
            v-click-outside="clickOutsideClose"
          >
            <header
              class="modal-header"
            >
              <slot name="header">
                <h5 class="modal-title">
                  {{ modalTitle }}
                </h5>
                <button
                  type="button"
                  class="close"
                  data-dismiss="modal"
                  aria-label="Close"
                  @click.prevent.stop="close"
                >
                  <span aria-hidden="true">×</span>
                </button>
              </slot>
            </header>
            <section
              class="modal-body"
            >
              <slot name="body" />
            </section>
            <footer class="modal-footer">
              <slot
                name="footer"
                v-if="!confirmation"
              >
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  @click.prevent.stop="close"
                  aria-label="Close modal"
                >
                  {{ $t(closeLabel) }}
                </button>
              </slot>

              <slot
                name="footer-confirmation"
                v-if="confirmation"
              >
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  @click.prevent.stop="close"
                  aria-label="Close modal"
                >
                  {{ $t(cancelLabel) }}
                </button>

                <button
                  type="button"
                  class="btn btn-primary"
                  @click.prevent.stop="confirm"
                >
                  {{ $t(confirmLabel) }}
                </button>
              </slot>
            </footer>
          </div>
        </div>
        <slot name="outside" />
      </div>
    </transition>
    <div
      class="modal-backdrop show"
      @click.prevent.stop="close"
    />
  </div>
</template>

<script lang="ts">
  import ClickOutside from '@PSVue/directives/click-outside';
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'Modal',
    directives: {
      ClickOutside,
    },
    props: {
      closeOnClickOutside: {
        type: Boolean,
        required: false,
        default: true,
      },
      confirmation: {
        type: Boolean,
        required: false,
        default: false,
      },
      cancelLabel: {
        type: String,
        required: false,
        default() {
          return 'modal.cancel';
        },
      },
      confirmLabel: {
        type: String,
        required: false,
        default() {
          return 'modal.apply';
        },
      },
      closeLabel: {
        type: String,
        required: false,
        default() {
          return 'modal.close';
        },
      },
      modalTitle: {
        type: String,
        required: false,
        default() {
          return '';
        },
      },
    },
    methods: {
      clickOutsideClose(): void {
        if (this.closeOnClickOutside) {
          this.$emit('close');
        }
      },
      close(): void {
        this.$emit('close');
      },
      confirm(): void {
        this.$emit('confirm');
      },
    },
  });
</script>

<style lang="scss" scoped>
  .modal.show {
    display: block;
  }
  .modal-fade-enter-active, .modal-fade-leave-active {
    transition: opacity .5s;
  }
  .modal-fade-enter, .modal-fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
</style>
