<template>
  <div>
    <div>
      <div
        style="width: 800px; height: 300px; border: 1px solid black"
        @dragover.prevent
        @drop.prevent="onDragUpload"
        @click="triggerFileInput"
      >
        <input
          type="file"
          multiple
          id="onclick-img-upload"
          style="display:none"
          @change="onClickUpload"
        >
        <div>
          <div
            v-for="(image, index) in images"
            :key="index"
          >
            <img
              :src="image.url"
              :alt="image.url"
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import Router from '@components/router.js';

  const router = new Router();

  const productImageUpload = {
    name: 'ProductImageUpload',
    data() {
      return {
        images: [],
      };
    },
    mounted() {
      this.loadImages();
    },
    methods: {
      triggerFileInput() {
        const fileInput = document.getElementById('onclick-img-upload');
        fileInput.click();
      },
      onClickUpload(event) {
        uploadImages(event.currentTarget.files).then((resp) => {
          // @todo success.
          console.log(resp);
          // @todo: just to make sure component rerenders on update. Use loadImages when api ready.
          this.images.push({url: 'test-3'});
        });
      },
      onDragUpload(event) {
        uploadImages(event.dataTransfer.files).then((resp) => {
          // @todo success.
          console.log(resp);
        });
      },
      loadImages() {
        // @todo use ajax to get real existing images.
        this.images = [
          {
            url: 'test-1',
          },
          {
            url: 'test-2',
          },
        ];
      },
    },
  };

  async function uploadImages(fileList) {
    return fetch(router.generate('admin_products_v2_images', {
      productId: 1//@todo: where do i store product id?
    }), {
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
