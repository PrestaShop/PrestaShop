<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div
    class="modal fade"
    id="ps-modal"
    tabindex="-1"
    role="dialog"
  >
    <div
      class="modal-dialog"
      role="document"
    >
      <div class="modal-content">
        <div class="modal-header">
          <button
            type="button"
            class="close"
            data-dismiss="modal"
          >
            <i class="material-icons">close</i>
          </button>
          <h4 class="modal-title">
            {{ translations.modal_title }}
          </h4>
        </div>
        <div class="modal-body">
          {{ translations.modal_content }}
        </div>
        <div class="modal-footer">
          <PSButton
            @click="onSave"
            class="btn-lg"
            primary
            data-dismiss="modal"
          >
            {{ translations.button_save }}
          </PSButton>
          <PSButton
            @click="onLeave"
            class="btn-lg"
            ghost
            data-dismiss="modal"
          >
            {{ translations.button_leave }}
          </PSButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
  import PSButton from '@app/widgets/ps-button.vue';
  import {EventEmitter} from '@components/event-emitter';
  import {defineComponent} from 'vue';

  export default defineComponent({
    props: {
      translations: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    mounted() {
      EventEmitter.on('showModal', () => {
        this.showModal();
      });
      EventEmitter.on('hideModal', () => {
        this.hideModal();
      });
    },
    methods: {
      showModal(): void {
        $(this.$el).modal('show');
      },
      hideModal(): void {
        $(this.$el).modal('hide');
      },
      onSave(): void {
        this.$emit('save');
      },
      onLeave(): void {
        this.$emit('leave');
      },
    },
    components: {
      PSButton,
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .modal-header .close {
    font-size: var(--#{$cdk}size-20);
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
