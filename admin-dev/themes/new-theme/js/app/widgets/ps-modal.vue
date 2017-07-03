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
                <PSButton @click="onSave" class="btn-lg" primary>{{translations.button_save}}</PSButton>
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
   @import "~PrestaKit/scss/custom/_variables.scss";
  .modal-header .close {
    font-size: 1.2rem;
    color: $gray-medium;
    opacity: 1;
  }
  .modal-content {
    border-radius: 0
  }
</style>
