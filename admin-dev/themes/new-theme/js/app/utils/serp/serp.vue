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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
        if (this.description.length > 150) {
          return `${this.description.substring(0, 150)}...`;
        }

        return this.description;
      },
    },
  });
</script>

<style lang="scss" type="text/scss" scoped>
  @import "~@scss/config/bootstrap.scss";
  @import "~@scss/config/settings.scss";

  .serp-preview {
    max-width: 43.75rem;
    padding: 1.5rem 1.875rem;
    margin: 0.938rem 0;
    background-color: $white;
    border: solid 1px $widget-border-color;
    @include border-radius(0.25rem);
    @include box-shadow(0 0 0.375rem 0 rgba($black, 0.1));

    .serp-url {
      font-family: arial, sans-serif;
      font-size: 0.875rem;
      font-style: normal;
      font-weight: 400;
      line-height: 1.5rem;
      color: $serp-url-light-color;
      text-align: left;
      direction: ltr;
      cursor: pointer;
      visibility: visible;
    }

    .serp-base-url {
      color: $serp-url-dark-color;
    }

    .serp-url-more {
      margin: -0.25rem 0 0 0.875rem;
      font-size: 1.125rem;
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
