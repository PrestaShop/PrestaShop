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
  <div>
    <label>{{label}}</label>
    <textarea v-model="getTranslated" :class="{ missing : isMissing }"></textarea>
    <PSButton :primary="false" @click="resetTranslation">
      Reset
    </PSButton>
    {{extraInfo}}
  </div>
</template>

<script>
  import PSButton from 'app/widgets/ps-button';
  import { EventBus } from 'app/utils/event-bus';

  export default {
    name: 'TranslationInput',
    props: {
      extraInfo: {
        type: String,
        required: false
      },
      label: {
        type: String,
        required: true
      },
      translated: {
        required: true
      }
    },
    computed: {
      getTranslated: {
        get: function() {
          return this.translated.database ? this.translated.database : this.translated.xliff;
        },
        set: function(modifiedValue) {
          let modifiedTranslated = this.translated;
          modifiedTranslated.database = modifiedTranslated.edited = modifiedValue;
          this.$emit('input', modifiedTranslated);
        }
      },
      isMissing() {
        return this.getTranslated === null;
      }
    },
    methods: {
      resetTranslation: function () {
        this.getTranslated = '';
        EventBus.$emit('resetTranslation', this.translated);
      },
    },
    components: {
      PSButton
    }
  }
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .missing {
    border: 1px solid $danger;
  }
</style>
