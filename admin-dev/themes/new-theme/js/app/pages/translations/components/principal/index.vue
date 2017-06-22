<!--**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <transition name="fade">
    <div class="col-xs-9 card" v-if="principalReady">
      <div class="p-a-1 row translations-wrapper">
        <PSAlert v-if="noResult" alertType="ALERT_TYPE_WARNING" :hasClose="false">
          {{noResultInfo}}
        </PSAlert>
        <div class="translations-catalog" v-else>
          <PSAlert v-if="searchActive" alertType="ALERT_TYPE_INFO" :hasClose="false">
            {{searchInfo}}
          </PSAlert>
          <div class="col-xs-8 p-t-1" >
            <h1 class="domain-info">
              <span>{{ currentDomain }}</span>
              <span>{{ currentDomainTotalTranslations }}</span>
              <span v-show="currentDomainTotalMissingTranslations"> - <span class="missing">{{ currentDomainTotalMissingTranslationsString }}</span></span>
            </h1>
          </div>
          <div class="col-xs-4">
            <PSPagination
              :currentIndex="currentPagination"
              :pagesCount="pagesCount"
              class="pull-xs-right"
              @pageChanged="onPageChanged"
            />
          </div>
          <form class="col-xs-12" :action="saveAction" method="post" @submit.prevent="saveTranslations">
            <div class="row">
              <div class="col-xs-12 m-b-2">
                <PSButton :primary="true" type="submit" class="pull-xs-right">
                  {{ trans('button_save') }}
                </PSButton>
              </div>
            </div>

            <TranslationInput
              v-for="(translation, key) in translationsCatalog"
              :key="key"
              :translated="translation"
              :label="translation.default"
              :extraInfo="getDomain(translation.tree_domain)">
            </TranslationInput>
            <PSButton :primary="true" type="submit" class="pull-xs-right m-t-3">
              {{ trans('button_save') }}
            </PSButton>
          </form>
          <div class="col-xs-12">
            <PSPagination
              :currentIndex="currentPagination"
              :pagesCount="pagesCount"
              @pageChanged="onPageChanged"
            />
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
  import TranslationInput from './translation-input';
  import PSButton from 'app/widgets/ps-button';
  import PSPagination from 'app/widgets/ps-pagination';
  import PSAlert from 'app/widgets/ps-alert';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    computed: {
      principalReady() {
        return !this.$store.state.principalLoading;
      },
      translationsCatalog() {
        this.translations = this.$store.getters.catalog.data.data;
        return this.translations;
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
        return (this.$store.state.currentDomainTotalTranslations <= 1) ? `- ${this.trans('label_total_domain_singular')}` : `- ${this.trans('label_total_domain').replace('%nb_translations%', this.$store.state.currentDomainTotalTranslations)}`;
      },
      currentDomainTotalMissingTranslations() {
        return this.$store.state.currentDomainTotalMissingTranslations;
      },
      currentDomainTotalMissingTranslationsString() {
        let totalMissingTranslationsString = '';

        if (
          this.currentDomainTotalMissingTranslations
          && this.currentDomainTotalMissingTranslations === 1
        ) {
          totalMissingTranslationsString = this.trans('label_missing_singular');
        } else if (this.currentDomainTotalMissingTranslations) {
          totalMissingTranslationsString = this.trans('label_missing').replace('%d', this.currentDomainTotalMissingTranslations);
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
        return this.trans('search_info').replace('%s', this.$store.getters.searchTags.join(' - ')).replace('%d', this.$store.state.totalTranslations);
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
        const modifiedTranslations = [];

        this.translations.forEach((translation) => {
          if (translation.edited) {
            modifiedTranslations.push({
              default: translation.default,
              edited: translation.edited,
              domain: translation.tree_domain.join(''),
              locale: window.data.locale,
              theme: window.data.selected,
            });
          }
        });

        return modifiedTranslations;
      },
    },
    data() {
      return {
        translations: [],
        originalTranslations: [],
      };
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

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .domain-info {
    font-size: 1rem;
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0
  }
</style>
