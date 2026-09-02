<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *-->
<template>
  <div class="col-md-4">
    <div class="movements">
      <PSButton
        type="button"
        class="update-qty float-sm-right"
        :class="classObject"
        :disabled="disabled"
        :primary="true"
        @click="sendQty"
      >
        <i class="material-icons">edit</i>
        {{ trans('button_movement_type') }}
      </PSButton>
    </div>
  </div>
</template>

<script lang="ts">
  import PSButton from '@app/widgets/ps-button.vue';
  import {defineComponent} from 'vue';
  import TranslationMixin from '@app/pages/stock/mixins/translate';

  export default defineComponent({
    computed: {
      disabled(): boolean {
        return !this.$store.state.hasQty;
      },
      classObject(): {'btn-primary': boolean} {
        return {
          'btn-primary': !this.disabled,
        };
      },
    },
    mixins: [TranslationMixin],
    methods: {
      sendQty(): void {
        this.$store.state.hasQty = false;
        this.$store.dispatch('updateQtyByProductsId');
      },
    },
    components: {
      PSButton,
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
