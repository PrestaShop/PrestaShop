<template>
  <div class="row mt-5">
    <div class="col-sm-4">
      <h2>{{trans('title_format')}}</h2>
      <PSTable class="mt-3">
        <thead>
          <tr>
            <th>{{trans('table_title_language')}}</th>
            <th>{{trans('table_title_edit')}}</th>
            <th class="text-sm-right">{{trans('table_title_example')}}</th>
          </tr>
        </thead>
        <tbody>
          <CurrencyLine
            v-for="(language, index) in 5"
            key=${index}
          />
        </tbody>
      </PSTable>
    </div>
  </div>
</template>

<script>
import PSTable from 'app/widgets/ps-table/ps-table';
import CurrencyLine from './currency-line';

  export default {
    components: {
      PSTable,
      CurrencyLine,
    },
  };
</script>
