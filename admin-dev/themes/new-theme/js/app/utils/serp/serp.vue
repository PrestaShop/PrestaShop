
<!--**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *-->
<template>
  <div id="serp">
    <div class="serp-preview">
      <div class="serp-title">{{ displayedTitle }}</div>
      <div class="serp-url">{{ url }}<span class="serp-arrow"></span></div>
      <div class="serp-description">{{ displayedDescription }}</div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'serp',
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
  };
</script>

<style lang="scss" type="text/scss" scoped>
    .serp-preview {
        margin-top: 15px;
        margin-bottom: 15px;
        border-radius: 2px;
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.12);
        background-color: #ffffff;
        border: solid 1px #e7e7e7;
        padding: 30px;
        max-width: 700px;

        .serp-arrow {
            border-bottom-color: rgb(0, 102, 33);
            border-bottom-style: solid;
            border-bottom-width: 0px;
            border-left-color: rgba(0, 0, 0, 0);
            border-left-style: solid;
            border-left-width: 4px;
            border-right-color: rgba(0, 0, 0, 0);
            border-right-style: solid;
            border-right-width: 4px;
            border-top-color: rgb(0, 102, 33);
            border-top-style: solid;
            border-top-width: 5px;
            color: rgb(128, 128, 128);
            cursor: default;
            font-family: arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            height: 0px;
            position: absolute;
            line-height: 27px;
            margin-left: 3px;
            margin-top: 6px;
            text-align: center;
            user-select: none;
            visibility: visible;
            white-space: nowrap;
            width: 0px;
        }

        .serp-title {
            color: #1A0DAB;
            cursor: pointer;
            font-family: arial, regular;
            font-size: 18px;
            font-weight: normal;
            text-align: left;
            text-decoration: none;
            visibility: visible;
            white-space: nowrap;
        }

        .serp-url {
            color: #006621;
            font-family: arial, regular;
            font-size: 14px;
            font-style: normal;
            font-weight: normal;
            line-height: 24px;
            text-align: left;
            visibility: visible;
        }

        .serp-description {
            color: #545454;
            font-family: arial, regular;
            font-size: 13px;
            font-weight: normal;
            text-align: left;
            visibility: visible;
            word-wrap: break-word;
        }
    }
</style>
