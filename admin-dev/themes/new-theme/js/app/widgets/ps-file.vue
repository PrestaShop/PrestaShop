<template>
  <div class="input-group input-file" name="Fichier1">
    <input type="text" class="form-control" />			
    <span class="input-group-btn">
      <PSButton :primary="false" >
        <slot />
      </PSButton>
    </span>
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
