<template>
  <div class="col-md-4">
    <div class="movements">
      <PSButton
        type="button"
        class="update-qty pull-xs-right"
        :class="classObject"
        :disabled="disabled"
        :primary="true"
        @click="sendQty"
      >
        <i class="material-icons">edit</i>
        {{trans('button_movement_type')}}
      </PSButton>
    </div>
  </div>
</template>

<script>
  import PSButton from 'app/widgets/ps-button';
  export default {
    computed: {
      disabled() {
        return !this.$store.getters.hasQty;
      },
      classObject() {
        return {
          'btn-primary': !this.disabled
        }
      }
    },
    methods: {
      sendQty() {
        let postUrl = `${data.apiRootUrl.replace(/\?.*/,'')}/products`;
        this.$store.dispatch('updateQtyByProductsId', {
          url: postUrl
        });
      }
    },
    components: {
      PSButton
    }
  }
</script>
<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
