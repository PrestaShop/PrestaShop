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
    font-size: 1.2rem;
    color: $gray-medium;
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
