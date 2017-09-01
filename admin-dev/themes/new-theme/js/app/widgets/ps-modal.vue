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
  <div class="modal fade" id="ps-modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="material-icons">close</i>
                </button>
                <h4 class="modal-title">{{translations.modal_title}}</h4>
            </div>
            <div class="modal-body">
              {{translations.modal_content}}
            </div>
            <div class="modal-footer">
                <PSButton @click="onSave" class="btn-lg" primary data-dismiss="modal">{{translations.button_save}}</PSButton>
                <PSButton @click="onLeave" class="btn-lg" ghost data-dismiss="modal">{{translations.button_leave}}</PSButton>
            </div>
          </div>
      </div>
  </div>
</template>

<script>
import PSButton from 'app/widgets/ps-button';
import { EventBus } from 'app/utils/event-bus';

export default {
  props: {
    translations: {
      type: Object,
      required: false,
    },
  },
  mounted() {
    EventBus.$on('showModal', () => {
      this.showModal();
    });
    EventBus.$on('hideModal', () => {
      this.hideModal();
    });
  },
  methods: {
    showModal() {
      $(this.$el).modal('show');
    },
    hideModal() {
      $(this.$el).modal('hide');
    },
    onSave() {
      this.$emit('save');
    },
    onLeave() {
      this.$emit('leave');
    },
  },
  components: {
    PSButton,
  },
};

</script>

<style lang="sass" scoped>
   @import "../../../scss/config/_settings.scss";
  .modal-header .close {
    font-size: 1.2rem;
    color: $gray-medium;
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
