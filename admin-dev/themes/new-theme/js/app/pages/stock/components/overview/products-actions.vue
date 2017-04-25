<template>
  <div class="row">
    <div class="col-md-8">
      <button v-show="false" type="button" class="btn btn-tertiary-outline"><i class="material-icons">edit</i>BULK EDIT</button>
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
