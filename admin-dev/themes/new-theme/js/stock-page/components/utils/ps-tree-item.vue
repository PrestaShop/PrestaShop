<template>
  <div :class="className">
    <PSCheckbox :ref="label" :id="computedId" :item="item" @checked="onCheck"/>
    <span class="tree-label">{{label}}</span>
  </div>
</template>

<script>
  import PSCheckbox from './ps-checkbox';
  import { EventBus } from './event-bus';

  export default {
    props:['id','item','label','className'],
    computed:{
      computedId() {
        let id = this.label + this.id;
        return id.replace(/\s/g,'');
      }
    },
    methods: {
      onCheck(obj) {
        this.$emit('checked', obj);
      }
    },
    components: {
      PSCheckbox
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
    font-size: 12px;
  }
</style>
