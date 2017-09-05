<template>
	<div class="row py-2 container">
		<PSCheckbox id="low-filter" class="mt-1 ml-1" @checked="onCheck" />
		<p class="ml-2">{{trans('filter_low_stock')}}</p>
	</div>
</template>

<script>
import PSCheckbox from 'app/widgets/ps-checkbox';
export default {
  methods: {
    onCheck(checkbox) {
      const isChecked = checkbox.checked ? 1 : 0;
      this.$emit('lowStockChecked', isChecked);
    },
  },
  components: {
    PSCheckbox,
  },
};
</script>
