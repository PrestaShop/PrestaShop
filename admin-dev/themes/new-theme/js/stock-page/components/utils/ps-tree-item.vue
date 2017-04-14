<template>
  <div>
    <Checkbox :id="computedId" :item="item" @checked="onCheck"/>
    <span class="m-l-1">{{label}}</span>
  </div>
</template>

<script>
  import Checkbox from './checkbox';

  export default {
    props:['id','item','label'],
    computed:{
      computedId() {
        var randLetter = String.fromCharCode(65 + Math.floor(Math.random() * 26));
        return randLetter + this.id;
      }
    },
    methods: {
      onCheck(obj) {
        this.$emit('checked', obj);
      }
    },
    components: {
      Checkbox
    }
  }
</script>
