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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    font-size: 1.2rem;
    color: $gray-medium;
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
