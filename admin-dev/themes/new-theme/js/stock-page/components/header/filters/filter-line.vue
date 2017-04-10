<template>
  <div class="flex">
    <Checkbox :ref="label" :id="id" :item="item" @checked="onCheck"/>
    <span class="m-l-1">{{label}}</span>
  </div>
</template>

<script>
  import Checkbox from '../../utils/checkbox';
  import { EventBus } from '../../utils/event-bus';

  export default {
    props:['id','item', 'label'],
    methods: {
      onCheck(obj) {
        this.$emit('checked', obj);
      }
    },
    mounted() {
      EventBus.$on('tagChanged', (tag) => {
        let checkbox = this.$refs[tag];
        if(checkbox) {
          checkbox.$data.checked = !checkbox.$data.checked;
        }
      });
    },
    components: {
      Checkbox
    }
  }
</script>