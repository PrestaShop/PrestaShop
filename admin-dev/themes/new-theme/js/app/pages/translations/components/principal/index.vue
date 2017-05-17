<template>
  <div class="col-xs-9">
    <div class="card p-a-1">
      <PSPagination
        pageNumber="3"
        activeMultiPagination="5"
        :current="currentPagination"
        :pagesCount="pagesCount"
        @pageChanged="onPageChanged"
      />
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
      <PSPagination
        :current="currentPagination"
        :pagesCount="pagesCount"
        @pageChanged="onPageChanged"
      />
    </div>
  </div>
</template>

<script>
  import TranslationInput from './translation-input';
  import PSButton from 'app/widgets/ps-button';
  import PSPagination from 'app/widgets/ps-pagination';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    computed: {
      translationsCatalog () {
        this.translations = this.$store.getters.catalog.data.data;
        return this.translations;
      },
      saveAction () {
        return this.$store.getters.catalog.data.info ? this.$store.getters.catalog.data.info.edit_url : '';
      },
      resetAction () {
        return this.$store.getters.catalog.data.info ? this.$store.getters.catalog.data.info.reset_url : '';
      },
      pagesCount() {
        return this.$store.getters.totalPages;
      },
      currentPagination() {
        return this.$store.getters.pageIndex;
      },
    },
    methods: {
      onPageChanged(pageIndex) {
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch();
      },
      fetch() {
        this.$store.dispatch('getCatalog', {
          url: this.$store.getters.catalog.info.current_url_without_pagination,
          page_size: this.$store.state.translationsPerPage,
          page_index: this.$store.getters.pageIndex,
        });
      },
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
            translations: this.getModifiedTranslations(),
            store: this.$store
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
              locale: window.data.locale,
              theme: window.data.selected
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
          locale: window.data.locale,
          theme: window.data.selected
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
      PSPagination,
    }
  }
</script>
