<template>
  <button type="button" class="btn" :class="buttonType" @click="onClick" >
    <slot />
  </button>
</template>

<script>
 export default {
   props: {
     primary: {
       type: Boolean,
       required: true,
     },
   },
   computed: {
     buttonType() {
       if (this.primary) {
         return 'btn-primary';
       }
       return 'btn-secondary';
     },
   },
   methods: {
     onClick() {
       this.$emit('click');
     },
   },
 };
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";
  .btn {
    border-radius:0;
    height: 34px;
  }
  .btn-secondary {
    background-color: $gray-medium;
    border: none;
  }
</style>
