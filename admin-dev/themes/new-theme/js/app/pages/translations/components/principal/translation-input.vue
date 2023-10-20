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
  <div class="form-group">
    <label>{{ label }}</label>
    <textarea
      class="form-control"
      rows="2"
      v-model="getTranslated"
      :class="{ missing : isMissing }"
    />
    <PSButton
      class="mt-3 float-sm-right"
      :primary="false"
      ghost
      @click="resetTranslation"
    >
      {{ trans('button_reset') }}
    </PSButton>
    <small class="mt-3">{{ extraInfo }}</small>
  </div>
</template>

<script lang="ts">
  import PSButton from '@app/widgets/ps-button.vue';
  import {EventEmitter} from '@components/event-emitter';
  import TranslationMixin from '@app/pages/translations/mixins/translate';
  import {defineComponent} from 'vue';

  export default defineComponent({
    name: 'TranslationInput',
    mixins: [TranslationMixin],
    props: {
      id: {
        type: Number,
        required: false,
        default: 0,
      },
      extraInfo: {
        type: String,
        required: false,
        default: '',
      },
      label: {
        type: String,
        required: true,
      },
      translated: {
        type: Object,
        required: true,
      },
    },
    computed: {
      getTranslated: {
        get(): any {
          return this.translated.user ? this.translated.user : this.translated.project;
        },
        set(modifiedValue: any): void {
          const modifiedTranslated = this.translated;
          modifiedTranslated.user = modifiedValue;
          modifiedTranslated.edited = modifiedValue;
          this.$emit('input', modifiedTranslated);
          this.$emit('editedAction', {
            translation: modifiedTranslated,
            id: this.id,
          });
        },
      },
      isMissing(): boolean {
        return this.getTranslated === null;
      },
    },
    methods: {
      resetTranslation(): void {
        this.getTranslated = '';
        EventEmitter.emit('resetTranslation', this.translated);
      },
    },
    components: {
      PSButton,
    },
  });
</script>

<style lang="scss" scoped>
  @import '~@scss/config/_settings.scss';

  .form-group {
    overflow: hidden;
  }
  .missing {
    border: 1px solid $danger;
  }
</style>
