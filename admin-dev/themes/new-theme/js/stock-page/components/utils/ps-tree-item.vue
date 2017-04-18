<template>
  <div :class="className">
    <Checkbox :ref="computedId" :id="computedId" :item="item" @checked="onCheck"/>
    <span class="tree-label">{{label}}</span>
  </div>
</template>

<script>
  import Checkbox from './checkbox';
  import { EventBus } from './event-bus';

  export default {
    props:['id','item','label','className'],
    computed:{
      computedId() {
        return this.label + this.id;
      }
    },
    methods: {
      onCheck(obj) {
        this.$emit('checked', obj);
      }
    },
    components: {
      Checkbox
    },
    mounted() {
      EventBus.$on('toggleCheckbox', (tag) => {
        let checkbox = this.$refs[tag];
        if(checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      });
    },
  }
</script>

<style lang="sass" scoped>
  .tree-label {
    margin-left: 5px;
  }
</style>
