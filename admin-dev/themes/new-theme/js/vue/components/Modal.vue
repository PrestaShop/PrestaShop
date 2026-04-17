<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
