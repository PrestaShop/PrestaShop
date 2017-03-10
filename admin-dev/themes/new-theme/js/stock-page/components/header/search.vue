<template>
  <div class="row">
    <div class="col-md-8">
      <div class="autocomplete m-b-2">
        <label>Search products (search by name,reference,ID,supplier)</label>
        <input type="text" class="form-control search" autocomplete="off">
      </div>
      <SearchFilter />
    </div>
  </div>
</template>

<script>
  import SearchFilter from './search-filter';

  export default {
    components: {
      SearchFilter
    }
  }
</script>
