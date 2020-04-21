<template>
  <div>
    <div
      style="width: 800px; height: 300px; border: 1px solid black"
      @dragover.prevent
      @drop.prevent="uploadImages"
    >
    </div>
  </div>
</template>

<script>
  import Router from '@components/router.js';

  const router = new Router();

  export default {
    name: 'ProductImageUpload',
    methods: {
      async uploadImages(fileList) {
        const formData = new FormData();

        for (const file in Object.values(fileList)) {
            //@todo: still not working
          formData.append(file.name + file.lastModified, file);
        }

        try {
          const response = await fetch(router.generate('admin_products_v2_images'), {
            method: 'POST',
            body: formData,
          }).then((resp) => resp.json());
          // @todo hanlde success or intended error message
          console.log(response);
        } catch (e) {
          // @todo: handle error
          console.log(e);
        }
      },
    },
  };
</script>
