<template>
  <div>
    <input type="checkbox">
    <button type="button" class="btn btn-tertiary-outline">BULK EDIT</button>
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
