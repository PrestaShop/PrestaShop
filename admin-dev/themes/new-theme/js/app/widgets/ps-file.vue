<template>
  <div class="ps-file d-flex">
    <span class="file-ico p-1 align-self-center">
      <i class="material-icons">insert_drive_file</i>
    </span>
    <input type="text" class="form-control" />
    <PSButton :primary="false" >
      <slot />
    </PSButton>
  </div>
</template>

<script>
import PSButton from 'app/widgets/ps-button';

export default {
  components: {
    PSButton,
  },
};
</script>

<style lang="sass">
  @import "../../../scss/config/_settings.scss";
  .ps-file {
    .file-ico {
      border: $gray-light 1px solid;
      border-right: none;
      color: $gray-medium;
    }
  }
</style>
