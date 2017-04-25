<template>
  <div :class="className">
    <PSCheckbox :ref="model.name" :id="id" :model="model" @checked="onCheck"/>
    <span class="tree-label">{{model.name}}</span>
  </div>
</template>

<script>
  import PSCheckbox from 'app/widgets/ps-checkbox';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    props:['model','className'],
    computed: {
      id() {
        return this.model.id;
      },
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
