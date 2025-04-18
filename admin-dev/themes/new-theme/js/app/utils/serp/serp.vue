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
  <div id="serp">
    <div class="serp-preview">
      <div class="serp-url">
        <span class="serp-base-url">{{ displayedBaseURL }}</span>
        {{ displayedRelativePath }}
        <i class="material-icons serp-url-more">more_vert</i>
      </div>
      <div class="serp-title">
        {{ displayedTitle }}
      </div>
      <div class="serp-description">
        {{ displayedDescription }}
      </div>
    </div>
  </div>
</template>

<script>
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'Serp',
    props: {
      url: {
        type: String,
        default: 'https://www.example.com/',
      },
      description: {
        type: String,
        default: '',
      },
      title: {
        type: String,
        default: '',
      },
    },
    computed: {
      displayedBaseURL() {
        const parseUrl = new URL(this.url);
        const baseUrl = `${parseUrl.protocol}//${parseUrl.hostname}`;

        return baseUrl;
      },
      displayedRelativePath() {
        const parseUrl = new URL(this.url);
        const relativePath = decodeURI(parseUrl.pathname).replaceAll('/', ' \u203a ');

        if (relativePath.length > 50) {
          return `${relativePath.substring(0, 50)}...`;
        }

        return relativePath;
      },
      displayedTitle() {
        if (this.title.length > 70) {
          return `${this.title.substring(0, 70)}...`;
        }

        return this.title;
      },
      displayedDescription() {
        const plainTextDescription = this.stripHtml(this.description);

        if (plainTextDescription.length > 150) {
          return `${plainTextDescription.substring(0, 150)}...`;
        }

        return plainTextDescription;
      },
    },
    methods: {
      stripHtml(html) {
        const div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent || '';
      },
    },
  });
</script>

<style lang="scss" type="text/scss" scoped>
  @import "~@scss/config/bootstrap.scss";
  @import "~@scss/config/settings.scss";

  .serp-preview {
    max-width: 43.75rem;
    padding: var(--#{$cdk}size-24) var(--#{$cdk}size-30);
    margin: var(--#{$cdk}size-16) 0;
    background-color: var(--#{$cdk}white);
    border: 1px solid var(--#{$cdk}primary-400);
    box-shadow: var(--#{$cdk}box-shadow-default);

    .serp-url {
      font-family: arial, sans-serif;
      font-size: var(--#{$cdk}size-12);
      font-style: normal;
      font-weight: 400;
      line-height: var(--#{$cdk}size-18);
      color: $serp-url-light-color;
      text-align: left;
      direction: ltr;
      cursor: pointer;
      visibility: visible;
      display: flex;
      align-items: center;
    }

    .serp-base-url {
      color: $serp-url-dark-color;
    }

    .serp-url-more {
      margin-left: var(--#{$cdk}size-12);
      font-size: var(--#{$cdk}size-18);
      color: $serp-url-light-color;
      cursor: pointer;
    }

    .serp-title {
      font-family: arial, sans-serif;
      font-size: 1.25rem;
      font-weight: 400;
      color: $serp-title-color;
      text-align: left;
      text-decoration: none;
      white-space: nowrap;
      cursor: pointer;
      visibility: visible;
    }

    .serp-title:hover {
      text-decoration: underline;
    }

    .serp-description {
      font-family: arial, sans-serif;
      font-size: 0.875rem;
      font-weight: 400;
      color: $serp-description-color;
      text-align: left;
      word-wrap: break-word;
      visibility: visible;
    }
  }
</style>
