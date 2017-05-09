<template>
  <div>
    <label>{{label}}</label>
    <textarea >{{translated}}</textarea>
    <PSButton :primary="false">
      Reset
    </PSButton>
    {{extraInfo}}
  </div>
</template>

<script>
  import PSButton from 'app/widgets/ps-button';

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
    components: {
      PSButton
    }
  }
</script>
