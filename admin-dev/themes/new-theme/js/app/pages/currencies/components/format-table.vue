<template>
  <div class="row mt-5">
    <div class="col-sm-6">
      <h2>{{trans('title_format')}}</h2>
      <PSTable class="mt-3">
        <tr>
          <th>{{trans('table_title_language')}}</th>
          <th>{{trans('table_title_example')}}</th>
          <th>{{trans('table_title_edit')}}</th>
        </tr>
      </PSTable>
    </div>
  </div>
</template>

<script>
import PSTable from 'app/widgets/ps-table/ps-table';
  export default {
    components: {
      PSTable,
    },
  };
</script>
