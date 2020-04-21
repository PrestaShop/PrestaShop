<template>
  <div>
    <div>
      <div
        type="file"
        style="width: 800px; height: 300px; border: 1px solid black"
        @dragover.prevent
        @drop.prevent="onDragUpload"
        @click="triggerFileInput"
      />
      <input
        type="file"
        multiple
        id="onclick-img-upload"
        style="display:none"
        @change="onClickUpload"
      >
    </div>
  </div>
</template>

<script>
  import Router from '@components/router.js';

  const router = new Router();

  const productImageUpload = {
    name: 'ProductImageUpload',
    methods: {
      triggerFileInput() {
        const fileInput = document.getElementById('onclick-img-upload');
        fileInput.click();
      },
      onClickUpload(event) {
        uploadImages(event.currentTarget.files).then((resp) => {
          // @todo success.
          console.log(resp);
        });
      },
      onDragUpload(event) {
        uploadImages(event.dataTransfer.files).then((resp) => {
          // @todo success.
          console.log(resp);
        });
      },
    },
  };

  async function uploadImages(fileList) {
    return fetch(router.generate('admin_products_v2_images'), {
      method: 'POST',
      body: formatBody(fileList),
    }).then((resp) => resp.json());
  }

  function formatBody(fileList) {
    const formData = new FormData();

    Array.from(fileList).forEach((file) => {
      formData.append(`${file.name}${file.lastModified}`, file);
    });

    return formData;
  }

  export default productImageUpload;
</script>
