<template>
  <div class="col-md-4">
    <label>Movement type</label>
    <div class="movements">
      <select class="form-control m-r-1">
        <option selected>None</option>
        <option value="1">Supply Order</option>
        <option value="2">Inventory adjustment</option>
        <option value="3">Customer Order</option>
        <option value="4">Transfer</option>
        <option value="5">Product return</option>
      </select>
      <button type="button" class="btn update-qty" :class="classObject" :disabled="disabled"><i class="material-icons">edit</i>APPLY NEW QUANTITY</button>
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
  .movements {
    display: flex;
  }
  .update-qty {
    color: white;
    transition: background-color 0.2s ease;
  }
</style>
