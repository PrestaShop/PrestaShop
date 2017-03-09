<template>
  <div class="col-md-4">
    <label>Movement type</label>
    <div class="movements">
      <select data-toggle="select2" class="form-control m-r-1">
        <option selected>Open this select menu</option>
        <option value="1">One</option>
        <option value="2">Two</option>
        <option value="3">Three</option>
      </select>
      <button type="button" class="btn update-qty"><i class="material-icons">edit</i>APPLY NEW QUANTITY</button>
    </div>
  </div>
</template>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .movements {
    display: flex;
  }
  .update-qty {
    background: $gray-light;
    color: $white;
  }
</style>
