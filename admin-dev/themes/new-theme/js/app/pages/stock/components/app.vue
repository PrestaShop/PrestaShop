<template>
  <div id="app" class="stock-app">
    <StockHeader />
    <Search />
    <div class="card p-a-2">
      <router-view class="view"></router-view>
    </div>
  </div>
</template>

<script>
  import StockHeader from './header/stock-header';
  import Search from './header/search';

  export default {
    name: 'app',
    components: {
      StockHeader,
      Search
    },
  }
</script>

<style lang="sass?outputStyle=expanded">
  @import "~PrestaKit/scss/custom/_variables.scss";
  .header-toolbar {
    z-index: 0;
    height: 128px;
  }
  .stock-app {
    padding-top: 3em;
  }
  .table tr td {
    padding: 5px 0;
    vertical-align: top;
    &:not(.qty-spinner) {
      padding-top:14px;
    }
  }
  .ui-spinner {
    .ui-spinner-button {
      right: 30px;
      cursor: pointer;
      display: none;
      z-index: 3;
      transition: all 0.2s ease;
      height: 20px;
    }
    .ui-spinner-up::before {
      font-family: 'Material Icons';
      content: "\E5C7";
      font-size: 20px;
      color: $gray-dark;
      position: relative;
      top: -3px;
    }
    .ui-spinner-down::before {
      font-family: 'Material Icons';
      content: "\E5C5";
      font-size: 20px;
      color: $gray-dark;
      bottom: 5px;
      position: relative;
    }
    span {
      display: none;
    }
  }
  .qty.active .ui-spinner-button{
    display: block;
  }
  #growls.default {
    top: 20px;
  }
  .growl.growl-notice {
    background: white;
    border: 2px solid $success;
    border-radius: 0;
    padding: 0;
    min-height: 50px;
    .growl-message {
      color: $gray-dark;
      line-height: 46px;
      &::before {
        color: white;
        text-align: center;
        background: $success;
        height: 48px;
        width: 48px;
      }
    }
    .growl-close {
      color: $success;
      font-size: 20px
    }
  }
  .d-inline {
    display: inline;
  }
  .flex {
    display: flex;
    align-items: center;
    &.column {
      flex-direction: column;
    }
  }
  .btn:disabled {
    background-color: $gray-light;
    border-color: $gray-light;
  }
</style>
