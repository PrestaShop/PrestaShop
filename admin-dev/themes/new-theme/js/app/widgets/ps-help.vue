<template>
  <span 
    class="help-box" 
    data-container="body" 
    data-toggle="popover"
    :data-content="this.content" 
    :data-placement="this.placement"
  >
  </span>
</template>

<script>
  export default {
    props: {
      content: {
        type: String,
      },
      placement: {
        type: String,
      },
    },
    mounted() {
      $('.help-box').popover();
    },
  };
</script>
