<!--**
     * 2007-2019 PrestaShop and Contributors
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * https://opensource.org/licenses/OSL-3.0
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to license@prestashop.com so we can send you a copy immediately.
     *
     * DISCLAIMER
     *
     * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
     * versions in the future. If you wish to customize PrestaShop for your
     * needs please refer to https://www.prestashop.com for more information.
     *
     * @author    PrestaShop SA <contact@prestashop.com>
     * @copyright 2007-2019 PrestaShop SA and Contributors
     * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
     * International Registered Trademark & Property of PrestaShop SA
     *-->
<template>
  <div class="row bulk-row">
    <div class="col-lg-4"></div>

    <div class="col-lg-8 row">
      <div class="col text-center" v-for="bulk, bulkType in types">
        <div class="md-checkbox md-checkbox-inline">
          <label>
            <input
              type="checkbox"
              class="js-tab-permissions-checkbox"
              v-model="status"
              @change="updateBulk(bulkType)"
              :value="bulkType"
              :disabled="bulk.value !== true"
            />
            <i class="md-checkbox-control"></i>
          </label>
        </div>
        <br>
        <strong>{{ bulk.label }}</strong>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      types: {
        type: Object,
        required: true,
      },
      profilePermissions: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        status: [],
        TYPE_ALL: 'all',
      };
    },
    watch: {
      profilePermissions: {
        handler: function mandatoryFunctionForDeepWatching(val) {
          this.refreshPermissionsCheckboxes(val);
        },
        deep: true,
      },
    },
    mounted() {
      this.refreshPermissionsCheckboxes(this.profilePermissions);
    },
    methods: {
      /**
       * Check if checkboxes must be checked
       */
      refreshPermissionsCheckboxes(permissions) {
        Object.keys(this.types).forEach((type) => {
          if (type === this.TYPE_ALL) {
            return;
          }

          let isChecked = true;
          // eslint-disable-next-line no-restricted-syntax
          for (const perm of Object.values(permissions)) {
            if (perm[type] === '0') {
              isChecked = false;
              break;
            }
          }

          if (isChecked && !this.status.includes(type)) {
            this.status.push(type);
          }
        });
        this.checkForTypeAllCheckbox();
      },
      /**
       * Check is type all must be checked
       */
      checkForTypeAllCheckbox() {
        // no need to check there is no type all
        if (!(this.TYPE_ALL in this.types)) {
          return;
        }

        // Nothing change
        if (this.status.length !== (Object.keys(this.types).length - 1)) {
          return;
        }

        // We can add the TYPE_ALL because we check all checkboxes
        if (this.status.includes(this.TYPE_ALL)) {
          this.status.splice(this.status.indexOf(this.TYPE_ALL), 1);
        } else {
          this.status.push(this.TYPE_ALL);
        }
      },
      /**
       * Update bulk type
       */
      updateBulk(bulkType) {
        this.$emit(
          'updateBulk',
          {
            type: bulkType,
            status: this.status.includes(bulkType),
          },
        );
        this.checkForTypeAllCheckbox();
      },
    },
  };
</script>
