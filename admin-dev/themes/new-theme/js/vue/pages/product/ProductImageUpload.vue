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
          <img
            v-for="(image, index) in images"
            :key="index"
            :src="image.basePath"
            :alt="image.legend"
          >
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
    props: {
      productId: {
        Type: Number,
      },
      langId: {
        Type: Number,
      },
    },
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
        uploadImages(this.productId, event.currentTarget.files).then((resp) => {
          debugger;
        });
      },
      onDragUpload(event) {
        uploadImages(this.productId, event.dataTransfer.files).then((resp) => {
          this.loadImages();
        });
      },
      loadImages() {
        // @todo: handle error cases
        getImages(this.productId).then((resp) => {
          this.images = resp.body.data.images;
        });
      },
    },
  };

  async function getImages(productId) {
    return fetch(router.generate('admin_products_v2_images', {productId}))
      .then((r) => r.json().then((data) => ({
        status: r.status,
        body: data,
      })));
  }

  async function uploadImages(productId, fileList) {
    return fetch(router.generate('admin_products_v2_images_upload', {productId}), {
      method: 'POST',
      body: formatBody(fileList),
    }).then((r) => r.json().then((data) => ({
      status: r.status,
      body: data,
    })));
  }

  function formatBody(fileList) {
    const formData = new FormData();

    Array.from(fileList).forEach((file, index) => {
      formData.append(`${index}`, file);
    });

    return formData;
  }

  export default productImageUpload;
</script>
