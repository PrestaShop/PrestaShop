<template>
  <div>
    <div class="autocomplete">
      <input type="text" class="form-control search" placeholder="Search and add a related product" autocomplete="off">
      <ul class="product-list">
        <li class="media">
          <div class="media-left">
            <img class="media-object image" />
          </div>
          <div class="media-body media-middle">
            <span class="label">Faded Short Sleeves T-shirt (ref: demo_1)</span>
            <i class="material-icons delete">clear</i>
          </div>
        </li>
      </ul>
    </div>
    <SearchFilter />
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
