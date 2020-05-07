<template>
  <div>
    <div class="input-group locale-input-group js-locale-input-group d-flex">
      <div
        v-for="(legend, langId) in selectedImage.localizedLegends"
        :key="langId"
        class="js-locale-input"
        :class="{'d-none': !shouldBeShown(langId)}"
        style="flex-grow: 1;"
      >
        <input
          type="text"
          :id="generateTranslatableLegendId(langId)"
          :name="generateTranslatableLegendName(langId)"
          :v-model="legend"
          :value="legend"
          class="form-control"
        >
      </div>
      <div v-if="shouldBeShown(selectedLocale.id_lang)">
        <div class="dropdown">
          <button
            class="btn btn-outline-secondary dropdown-toggle js-locale-btn"
            type="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
            id="product_image_legend"
          >
            {{ selectedLocale.iso_code }}
          </button>
          <div
            class="dropdown-menu"
            aria-labelledby="product_image_legend"
            x-placement="bottom-start"
            style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;"
          >
            <span
              v-for="(locale, index) in parsedLocales"
              :key="index"
              class="dropdown-item js-locale-item"
              @click="selectLocale(locale.id_lang)"
            >{{ locale.name }}</span>
          </div>
        </div>
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
</template>

<script>
  import Router from '@components/router.js';

  const router = new Router();

  const productImageSettings = {
    name: 'ProductImageSettings',
    props: {
      selectedImage: {
        Type: Object,
      },
      contextLangId: {
        Type: Number,
      },
      locales: {
        Type: String,
        default: '{}',
      },
    },
    beforeMount() {
      this.selectLocale(this.contextLangId);
    },
    data() {
      return {
        parsedLocales: JSON.parse(this.locales),
        selectedLocale: null,
      };
    },
    methods: {
      generateTranslatableLegendId(langId) {
        return `product_image_legend_${langId}`;
      },
      generateTranslatableLegendName(langId) {
        return `product_image[legend][${langId}]"`;
      },
      shouldBeShown(langId) {
        return langId === this.selectedLocale.id_lang;
      },
      selectLocale(langId) {
        for (let i = 0, len = this.parsedLocales.length; i < len; i += 1) {
          const locale = this.parsedLocales[i];

          if (parseInt(locale.id_lang, 10) === parseInt(langId, 10)) {
            this.selectedLocale = locale;
          }
        }
      },
      saveImageSettings(selectedImage) {
        editImage(selectedImage);
      },
    },
  };

  async function editImage(selectedImage) {
    const formData = new FormData();
    formData.append('product_image', JSON.stringify({
      cover: selectedImage.cover,
      legend: selectedImage.localizedLegends,
    }));

    const response = await fetch(router.generate('admin_products_v2_images_edit', {
      imageId: selectedImage.imageId,
    }), {
      method: 'POST',
      body: formData,
    });
    const json = await response.json();

    return {status: response.status, body: json};
  }

  export default productImageSettings;
</script>
