<template>
  <div class="col-md-4">
    <div class="movements">
      <button type="button" class="btn update-qty pull-xs-right" :class="classObject" :disabled="disabled"><i class="material-icons">edit</i>APPLY NEW QUANTITY</button>
    </div>
  </div>
</template>

<script>
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
    }
  }
</script>
<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
