<template>
  <tr>
    <td colspan="7">
      <div class="alert alert-info" role="alert">
        <i class="material-icons">info_outline</i>
        <p class="alert-text">
          <slot />
        </p>
      </div>
    </td>
  </tr>
</template>

<style lang="sass?outputStyle=expanded" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .alert {
    border-radius: 0;
    border-width: 2px;
    padding: 0;
    background: $notice;
    .alert-text {
      padding: 15px 0 15px 15px;
      margin-left: 55px;
      background: white;
    }
  }
</style>