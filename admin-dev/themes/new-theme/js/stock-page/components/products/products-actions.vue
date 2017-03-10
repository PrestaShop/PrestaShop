<template>
  <div class="row m-b-3">
    <div class="col-md-8 m-t-2">
      <input type="checkbox" class="m-r-1">
      <button type="button" class="btn btn-tertiary-outline"><i class="material-icons">edit</i>BULK EDIT</button>
    </div>
    <MovementType />
  </div>
</template>

<script>
  import MovementType from './movement-type';

  export default {
    components: {
      MovementType
    }
  }
</script>
