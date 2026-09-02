<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div data-role="currency-format-edit-modal">
    <modal
      confirmation
      :modal-title="modalTitle"
      @close="$emit('close')"
      @confirm="$emit('applyCustomization', customData)"
      v-if="language !== null"
      class=""
    >
      <template #body>
        <currency-format-form
          :language="language"
          @formatChange="customData = $event"
        />
      </template>
    </modal>
  </div>
</template>

<script>
  import Modal from '@PSVue/components/Modal';
  import {defineComponent} from 'vue';
  import CurrencyFormatForm from './CurrencyFormatForm';

  export default defineComponent({
    name: 'CurrencyModal',
    data: () => ({
      customData: null,
    }),
    components: {
      CurrencyFormatForm,
      Modal,
    },
    props: {
      language: {
        type: Object,
        required: false,
        default: null,
      },
    },
    computed: {
      modalTitle() {
        return this.$t('modal.title') + (this.language !== null ? ` + ${this.language.name}` : '');
      },
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
