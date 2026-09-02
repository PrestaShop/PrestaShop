<!--*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    <div class="d-flex flex-column flex-md-row justify-content-md-between">
      <div>
        <small>{{ extraInfo }}</small>
      </div>
      <div>
        <PSButton
          class="mt-2"
          :primary="false"
          ghost
          @click="resetTranslation"
        >
          {{ trans('button_reset') }}
        </PSButton>
      </div>
    </div>
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
    border: 1px solid var(--#{$cdk}red-500);
  }
</style>
