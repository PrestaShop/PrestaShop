<template>
  <div>
    <div id="productImageDropzone" style="width: 800px; height: 300px; border: 1px solid black">
      <drag-file-upload dropzone-selector="#productImageDropzone"></drag-file-upload>
    </div>
  </div>
</template>

<script>
  import DragFileUpload from '../../components/DragFileUpload';

  export default {
    name: 'ProductImageUpload',
    components: {DragFileUpload},
  };
</script>
