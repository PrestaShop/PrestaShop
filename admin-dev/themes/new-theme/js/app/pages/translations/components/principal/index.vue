<template>
  <div class="col-xs-9">
    <div class="card p-a-1">
      <TranslationInput
        v-for="(translation, key) in translationsCatalog"
        :key="key"
        :label="translation.default"
        :translated="getTranslated(translation)"
        :extraInfo="getDomain(translation.tree_domain)"
      >
      </TranslationInput>
    </div>
  </div>
</template>

<script>
  import TranslationInput from './translation-input';

  export default {
    computed: {
      translationsCatalog () {
        return this.$store.getters.catalog;
      }
    },
    methods: {
      getTranslated (el) {
        return el.database ? el.database : el.xliff;
      },
      getDomain(domains) {
        let domain = '';
        domains.forEach((d) => {
          domain += d + ' > ';
        });

        return domain.slice(0, -3);
      }
    },
    components: {
      TranslationInput,
    }
  }
</script>
