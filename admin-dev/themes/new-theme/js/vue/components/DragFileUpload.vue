<template>
  <div />
</template>

<script>
  export default {
    name: 'DragFileUpload',
    props: {
      dropzoneSelector: {
        type: String,
        default: '',
      },
    },
    mounted() {
      document.querySelector(this.dropzoneSelector).addEventListener('dragover', (e) => {
        e.preventDefault();
      });
      document.querySelector(this.dropzoneSelector).addEventListener('drop', (e) => {
        e.preventDefault();
        //@todo: fire event to parent with the file?
        this.$emit('fileDropped', e);
      });
    },
  };
</script>
