<template>
  <div>
    <div>
      <div
        class="col"
        style="border: 1px solid black;"
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
            style="width:128px; height:128px;"
            v-for="(image, index) in images"
            :key="index"
            :src="image.basePath"
            alt=""
            @click="showImageSettings(image)"
          >
        </div>
      </div>

      <div
        style="border: 1px solid black"
        v-if="selectedImage.imageId"
        class="col"
      >
        <label>
          Cover image
          <input
            type="checkbox"
            name="cover"
            v-model="selectedImage.cover"
          >
        </label>
        <label>
          Caption
          <input
            type="text"
            v-model="selectedImage.localizedLegends"
          >
        </label>

        <button
          type="submit"
          @click.prevent="saveImageSettings(selectedImage)"
        >
          Save
        </button>
        <button type="button">
          Delete
        </button>
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
        selectedImage: {
          imageId: null,
          cover: null,
          localizedLegends: null,
        },
      };
    },
    mounted() {
      this.loadImages();
    },
    methods: {
      showImageSettings(image) {
        window.event.stopPropagation();

        this.selectedImage = {
          imageId: image.imageId,
          cover: image.cover,
          localizedLegends: image.localizedLegends,
        };
      },
      saveImageSettings(selectedImage) {
        editImage(this.productId, selectedImage);
      },
      triggerFileInput() {
        const fileInput = document.getElementById('onclick-img-upload');
        fileInput.click();
      },
      onClickUpload(event) {
        uploadImages(this.productId, event.currentTarget.files).then((resp) => {
          this.loadImages();
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
    const response = await fetch(router.generate('admin_products_v2_images', {productId}));
    const json = await response.json();

    return {status: response.status, body: json};
  }

  async function editImage(productId, selectedImage) {
    const formData = new FormData();
    formData.append('product_image', JSON.stringify({
      cover: selectedImage.cover,
      legend: selectedImage.legend,
    }));

    const response = await fetch(router.generate('admin_products_v2_images_edit', {
      productId,
      imageId: selectedImage.imageId,
    }), {
      method: 'POST',
      body: formData,
    });
    const json = await response.json();

    return {status: response.status, body: json};
  }

  async function uploadImages(productId, fileList) {
    const response = await fetch(router.generate('admin_products_v2_images_upload', {productId}), {
      method: 'POST',
      body: formatBody(fileList),
    });
    const json = await response.json();

    return {status: response.status, body: json};
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
