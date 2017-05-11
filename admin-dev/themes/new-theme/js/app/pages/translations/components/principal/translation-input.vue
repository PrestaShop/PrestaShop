<template>
  <div>
    <label>{{label}}</label>
    <textarea v-model="getTranslated"></textarea>
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
