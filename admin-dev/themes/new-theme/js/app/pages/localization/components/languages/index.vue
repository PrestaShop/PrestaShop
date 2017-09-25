<template>
  <div id="languages">
    <h2>{{trans('title_languages')}}</h2>
    <LanguagesForm />
    <div class="float-sm-right">
      <PSButton ghost lg>{{trans('button_cancel')}}</PSButton>
      <PSButton primary lg>{{trans('button_save')}}</PSButton>
    </div>
  </div>
</template>

<script>
  import LanguagesForm from './languages-form';
  import PSButton from 'app/widgets/ps-button';

  export default {
    components: {
      LanguagesForm,
      PSButton,
    },
  };
</script>
