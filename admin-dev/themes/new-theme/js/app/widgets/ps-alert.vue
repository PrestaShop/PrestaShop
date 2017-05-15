<template>
  <div class="alert" :class="classObject" role="alert">
    <i v-if="isInfo" class="material-icons">info_outline</i>
    <i v-else class="material-icons">error_outline</i>
    <p class="alert-text">
      <slot />
    </p>
    <button v-if="hasClose" type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span class="material-icons">close</span>
    </button>
  </div>
</template>

<script>
  export default {
    props: {
      alertType : {
        type: String,
        required: true
      },
      hasClose: {
        type: Boolean,
        required: true
      }
    },
    computed: {
      classObject() {
        return {
          'alert-info': this.alertType === 'info',
          'alert-warning': this.alertType === 'warning',
          'alert-danger': this.alertType === 'danger'
        }
      },
      isInfo() {
        return this.alertType === 'info'
      }
    },
  }
</script>

<style lang="sass" scoped>
  @import "~PrestaKit/scss/custom/_variables.scss";

  .close {
    position: absolute;
    right: 0.625rem;
    top: 0.9375rem;
    opacity: 1;
    .material-icons {
      font-size: 1.5rem;
      .alert-info & {
        color: $primary;
      }
      .alert-warning & {
        color: $warning
      }
      .alert-danger & {
        color: $danger;
      }
    }
  }
  .alert {
    border-radius: 0;
    border-width: 0.125rem;
    padding: 0;
    &.alert-info {
      background: $notice;
    }
    &.alert-warning {
      background: $warning-hover;
    }
    &.alert-danger {
      background: $danger-hover;
    }
    .alert-text {
      padding: 0.9375rem 0 0.9375rem 0.9375rem;
      margin-left: 3.438rem;
      background: white;
    }
  }
</style>