<template>
  <div id="currencies" class="tab-pane fade show active" role="tabpanel">
    <h2>{{trans('title_currency')}}</h2>
    <CurrenciesForm />
    <FormatTable />
    <div class="float-sm-right">
      <PSButton ghost lg>{{trans('button_cancel')}}</PSButton>
      <PSButton primary lg>{{trans('button_save')}}</PSButton>
    </div>
  </div>
</template>
<script>
  import CurrenciesForm from './currencies-form';
  import FormatTable from './format-table';
  import PSButton from 'app/widgets/ps-button';

  export default {
    components: {
      CurrenciesForm,
      FormatTable,
      PSButton,
    },
  };
</script>
