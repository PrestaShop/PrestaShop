<template>
  <div class="input-group date">
    <input type='text' class="form-control" />
    <span class="input-group-addon">
      <i class="material-icons">event</i>
    </span>
  </div>
</template>

<script>
  export default {
    mounted() {
       $(this.$el).datetimepicker().on('dp.change', (infos) =>{
         this.$emit('dpChange', infos);
       });
    }
  }
</script>
