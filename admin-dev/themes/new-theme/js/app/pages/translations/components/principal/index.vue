<template>
  <div class="col-xs-9">
    <div class="card p-a-1">
      <form :action="saveAction" method="post" @submit.prevent="saveTranslations">
        <PSButton :primary="true" type="submit">
          {{ trans('button_save') }}
        </PSButton>

        <TranslationInput
          v-for="(translation, key) in translationsCatalog"
          :key="key"
          :translated="translation"
          :label="translation.default"
          :extraInfo="getDomain(translation.tree_domain)">
        </TranslationInput>
        <PSButton :primary="true" type="submit">
          {{ trans('button_save') }}
        </PSButton>
      </form>
    </div>
  </div>
</template>

<script>
  import TranslationInput from './translation-input';
  import PSButton from 'app/widgets/ps-button';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    computed: {
      translationsCatalog () {
        this.translations = this.$store.getters.catalog.data;
        return this.translations;
      },
      saveAction () {
        return this.$store.getters.catalog.info ? this.$store.getters.catalog.info.edit_url : '';
      },
      resetAction () {
        return this.$store.getters.catalog.info ? this.$store.getters.catalog.info.reset_url : '';
      }
    },
    methods: {
      getDomain(domains) {
        let domain = '';
        domains.forEach((d) => {
          domain += d + ' > ';
        });

        return domain.slice(0, -3);
      },
      saveTranslations() {
        let modifiedTranslations = this.getModifiedTranslations();

        if (modifiedTranslations.length) {
          this.$store.dispatch('saveTranslations', {
            url: this.saveAction,
            translations: this.getModifiedTranslations()
          });
        }
      },
      getModifiedTranslations() {
        let modifiedTranslations = [];

        this.translations.forEach((translation) => {
          if (translation.edited) {
            modifiedTranslations.push({
              default: translation.default,
              edited: translation.edited,
              domain: translation.tree_domain.join(''),
              locale: 'fr-FR'
            });
          }
        });

        return modifiedTranslations;
      }
    },
    data() {
      return {
        translations: [],
        originalTranslations: []
      }
    },
    mounted () {
      EventBus.$on('resetTranslation', (el) => {
        let translations = [];

        translations.push({
          default: el.default,
          domain: el.tree_domain.join(''),
          locale: 'fr-FR'
        });

        this.$store.dispatch('resetTranslation', {
          url: this.resetAction,
          translations: translations
        });
      })
    },
    components: {
      TranslationInput,
      PSButton,
    }
  }
</script>
