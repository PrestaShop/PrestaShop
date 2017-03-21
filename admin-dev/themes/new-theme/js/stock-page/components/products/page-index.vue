<template>
  <a v-on:click="changePage($event, index)" class="page-link" :class="classObject">{{ index }}</a>
</template>

<script>
  export default {
    props: ['index','current'],
    computed: {
      classObject() {
        return {
          current: (this.current === this.index)
        }
      }
    },
    methods: {
      changePage(event, pageIndex) {
        event.preventDefault();
        this.$emit('pageChanged', pageIndex);
      }
    }
  }
</script>
