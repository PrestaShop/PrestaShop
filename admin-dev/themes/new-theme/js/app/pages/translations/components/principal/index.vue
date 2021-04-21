<!--**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <transition name="fade">
    <div
      class="col-sm-9 card"
      v-if="principalReady"
    >
      <div class="p-3 translations-wrapper">
        <PSAlert
          v-if="noResult"
          alert-type="ALERT_TYPE_WARNING"
          :has-close="false"
        >
          {{ noResultInfo }}
        </PSAlert>
        <div
          class="translations-catalog row p-0"
          v-else
        >
          <PSAlert
            v-if="searchActive"
            class="col-sm-12"
            alert-type="ALERT_TYPE_INFO"
            :has-close="false"
          >
            {{ searchInfo }}
          </PSAlert>
          <div class="col-sm-8 pt-3">
            <h3 class="domain-info">
              <span>{{ currentDomain }}</span>
              <span>{{ currentDomainTotalTranslations }}</span>
              <span
                v-show="currentDomainTotalMissingTranslations"
              > - <span class="missing">{{ currentDomainTotalMissingTranslationsString }}</span></span>
            </h3>
          </div>
          <div class="col-sm-4">
            <PSPagination
              :current-index="currentPagination"
              :pages-count="pagesCount"
              class="float-sm-right"
              @pageChanged="onPageChanged"
            />
          </div>
          <form
            class="col-sm-12"
            method="post"
            :action="saveAction"
            :isEdited="isEdited"
            @submit.prevent="saveTranslations"
          >
            <div class="row">
              <div class="col-sm-12 mb-2">
                <PSButton
                  :primary="true"
                  type="submit"
                  class="float-sm-right"
                >
                  {{ trans('button_save') }}
                </PSButton>
              </div>
            </div>

            <TranslationInput
              v-for="(translation, key) in translationsCatalog"
              :key="key"
              :id="key"
              :translated="translation"
              :label="translation.default"
              :extra-info="getDomain(translation.tree_domain)"
              @editedAction="isEdited"
            />

            <div class="row">
              <div class="col-sm-12">
                <PSButton
                  :primary="true"
                  type="submit"
                  class="float-sm-right mt-3"
                >
                  {{ trans('button_save') }}
                </PSButton>
              </div>
            </div>
          </form>
          <div class="col-sm-12">
            <PSPagination
              :current-index="currentPagination"
              :pages-count="pagesCount"
              @pageChanged="onPageChanged"
            />
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  import PSButton from '@app/widgets/ps-button';
  import PSPagination from '@app/widgets/ps-pagination';
  import PSAlert from '@app/widgets/ps-alert';
  import {EventBus} from '@app/utils/event-bus';
  import TranslationInput from './translation-input';

  export default {
    props: {
      modal: {
        type: Object,
        required: false,
        default: () => ({}),
      },
    },
    data: () => ({
      originalTranslations: [],
      modifiedTranslations: [],
    }),
    computed: {
      principalReady() {
        return !this.$store.state.principalLoading;
      },
      translationsCatalog() {
        return this.$store.getters.catalog.data.data;
      },
      saveAction() {
        return this.$store.getters.catalog.data.info ? this.$store.getters.catalog.data.info.edit_url : '';
      },
      resetAction() {
        return this.$store.getters.catalog.data.info ? this.$store.getters.catalog.data.info.reset_url : '';
      },
      pagesCount() {
        return this.$store.getters.totalPages;
      },
      currentPagination() {
        return this.$store.getters.pageIndex;
      },
      currentDomain() {
        return this.$store.state.currentDomain;
      },
      currentDomainTotalTranslations() {
        /* eslint-disable max-len */
        return (this.$store.state.currentDomainTotalTranslations <= 1)
          ? `- ${this.trans('label_total_domain_singular').replace('%nb_translation%', this.$store.state.currentDomainTotalTranslations)}`
          : `- ${this.trans('label_total_domain').replace('%nb_translations%', this.$store.state.currentDomainTotalTranslations)}`;
        /* eslint-enable max-len */
      },
      currentDomainTotalMissingTranslations() {
        return this.$store.state.currentDomainTotalMissingTranslations;
      },
      currentDomainTotalMissingTranslationsString() {
        let totalMissingTranslationsString = '';

        if (this.currentDomainTotalMissingTranslations) {
          if (this.currentDomainTotalMissingTranslations === 1) {
            totalMissingTranslationsString = this.trans('label_missing_singular');
          } else {
            totalMissingTranslationsString = this.trans('label_missing')
              .replace('%d', this.currentDomainTotalMissingTranslations);
          }
        }

        return totalMissingTranslationsString;
      },
      noResult() {
        return (this.$store.getters.currentDomain === '' || typeof this.$store.getters.currentDomain === 'undefined');
      },
      noResultInfo() {
        return this.trans('no_result').replace('%s', this.$store.getters.searchTags.join(' - '));
      },
      searchActive() {
        return this.$store.getters.searchTags.length;
      },
      searchInfo() {
        const transKey = (this.$store.state.totalTranslations <= 1) ? 'search_info_singular' : 'search_info';

        return this.trans(transKey)
          .replace('%s', this.$store.getters.searchTags.join(' - '))
          .replace('%d', this.$store.state.totalTranslations);
      },
    },
    methods: {
      /**
       * Dispatch the event to change the page index,
       * get the translations and reset the modified translations into the state
       * @param {Number} pageIndex
       */
      changePage: function changePage(pageIndex) {
        this.$store.dispatch('updatePageIndex', pageIndex);
        this.fetch();
        this.$store.state.modifiedTranslations = [];
      },
      isEdited(input) {
        if (input.translation.edited) {
          this.$store.state.modifiedTranslations[input.id] = input.translation;
        } else {
          this.$store.state.modifiedTranslations.splice(
            this.$store.state.modifiedTranslations.indexOf(input.id),
            1,
          );
        }
      },
      onPageChanged(pageIndex) {
        if (this.edited()) {
          this.modal.showModal();
          this.modal.$once('save', () => {
            this.saveTranslations();
            this.changePage(pageIndex);
          });
          this.modal.$once('leave', () => {
            this.changePage(pageIndex);
          });
        } else {
          this.changePage(pageIndex);
        }
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
          domain += `${d} > `;
        });
        return domain.slice(0, -3);
      },
      saveTranslations() {
        const modifiedTranslations = this.getModifiedTranslations();

        if (modifiedTranslations.length) {
          this.$store.dispatch('saveTranslations', {
            url: this.saveAction,
            translations: this.getModifiedTranslations(),
            store: this.$store,
          });
        }
      },
      getModifiedTranslations() {
        this.modifiedTranslations = [];
        const targetTheme = (window.data.type === 'modules') ? '' : window.data.selected;

        Object.values(this.$store.state.modifiedTranslations).forEach((translation) => {
          this.modifiedTranslations.push({
            default: translation.default,
            edited: translation.edited,
            domain: translation.tree_domain.join(''),
            locale: window.data.locale,
            theme: targetTheme,
          });
        });

        return this.modifiedTranslations;
      },
      edited() {
        return Object.keys(this.$store.state.modifiedTranslations).length > 0;
      },
    },
    mounted() {
      EventBus.$on('resetTranslation', (el) => {
        const translations = [];

        translations.push({
          default: el.default,
          domain: el.tree_domain.join(''),
          locale: window.data.locale,
          theme: window.data.selected,
        });

        this.$store.dispatch('resetTranslation', {
          url: this.resetAction,
          translations,
        });
      });
    },
    components: {
      TranslationInput,
      PSButton,
      PSPagination,
      PSAlert,
    },
  };
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0
  }
</style>
