<!--**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *-->
<template>
  <div>
    <div
      :class="{parent, 'bg-light': parent}"
      class="d-flex flex-wrap permission-row"
    >
      <div
        class="col-4 text-nowrap"
        :class="`depth-level-${levelDepth}`"
      >
        &raquo;

        <strong v-if="parent">{{ permission.name }}</strong>
        <template v-else>
          {{ permission.name }}
        </template>
      </div>

      <div class="col-8 d-flex flex-wrap">
        <div
          class="text-center"
          :class="getClasses(types, index === 0)"
          v-for="(type, index) in types"
          :key="index"
        >
          <ps-checkbox
            :value="type"
            v-model="permissionValues"
            @change="sendUpdatePermissionRequest(type)"
            :disabled="!canEdit || !canEditCheckbox(type)"
          />
        </div>
      </div>
    </div>

    <div v-if="permission.children !== undefined">
      <row
        v-for="(p, pId) in permission.children"
        :key="p.id"
        :can-edit="canEdit"
        :permission="p"
        :permission-id="pId"
        :permission-key="permissionKey"
        :level-depth="levelDepth + 1"
        :profile-permissions.sync="profilePermissions"
        :employee-permissions="employeePermissions"
        :types="types"
        @childUpdated="onChildUpdate"
        @sendRequest="sendRequest"
      />
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import PsCheckbox from '@app/components/checkbox.vue';
  import ColSize from '@app/mixins/col-size.vue';

  export default Vue.extend({
    name: 'Row',
    mixins: [
      ColSize,
    ],
    components: {
      PsCheckbox,
    },
    props: {
      parent: {
        type: Boolean,
        required: false,
        default: false,
      },
      profilePermissions: {
        type: Object,
        required: true,
      },
      employeePermissions: {
        type: Object,
        required: false,
        default: () => ({}),
      },
      permission: {
        type: Object,
        required: true,
      },
      permissionId: {
        type: String,
        required: true,
      },
      permissionKey: {
        type: String,
        required: true,
      },
      levelDepth: {
        type: Number,
        required: true,
      },
      canEdit: {
        type: Boolean,
        required: false,
        default: false,
      },
      types: {
        type: Array,
        required: true,
      },
    },
    data(): {permissionValues: Array<string>, TYPE_ALL: string} {
      return {
        permissionValues: [],
        TYPE_ALL: 'all',
      };
    },
    watch: {
      profilePermissions: {
        handler: function mandatoryFunctionForDeepWatching(): void {
          this.refreshPermissions();
        },
        deep: true,
      },
    },
    /**
     * build v-model depends on selected permissions
     */
    mounted() {
      this.refreshPermissions();
    },
    computed: {
      displayLevelDepth(): string {
        if (this.levelDepth < 2) {
          return '';
        }

        return Array(this.levelDepth - 1).join('&nbsp;&nbsp;');
      },
    },
    methods: {
      canEditCheckbox(type: string): boolean {
        // We don't check for employee permissions
        if (Object.keys(this.employeePermissions).length === 0) {
          return true;
        }

        // Permission id not found
        if (!this.employeePermissions[this.permissionId]) {
          return false;
        }

        // Check if we can check TYPE_ALL checkbox
        if (type === this.TYPE_ALL) {
          let canBeChecked = true;

          // eslint-disable-next-line no-restricted-syntax
          for (const t of this.types) {
            if (this.employeePermissions[this.permissionId][<string>t] === '0') {
              canBeChecked = false;
              break;
            }
          }

          return canBeChecked;
        }

        // Normal behavior
        return this.employeePermissions[this.permissionId][type] === '1';
      },
      /**
       * Get the types length, depends if you have the TYPE_ALL or not
       */
      getTypesLength(): number {
        return this.types.includes(this.TYPE_ALL)
          ? this.types.length - 1
          : this.types.length;
      },
      /**
       *: void Get permission from id
       */
      getPermission(): Record<string, any> {
        return this.profilePermissions[this.permissionId];
      },
      /**
       * Check if profile has permission
       */
      hasPermission(type: string): boolean {
        const permission = this.getPermission();

        return permission !== undefined && parseInt(permission[type], 10) === 1;
      },
      /**
       * Refresh permissions and checkboxes
       */
      refreshPermissions(): void {
        Object.values(this.types).forEach((type) => {
          const stringType = type as string;

          if (this.hasPermission(stringType)) {
            this.addPermission(stringType);
          } else if (this.permissionValues.includes(stringType)) {
            this.removePermission(stringType);
          }
        });

        if (this.permissionValues.length === this.getTypesLength()) {
          this.addPermission(this.TYPE_ALL);
        }
      },
      /**
       * Check checkboxes permissions are in this row.
       * - if type this.TYPE_ALL is used just toggle values
       * - otherwise check if all must be checked or not
       */
      checkCheckboxesPermissions(type: string): void {
        // no need to check there is no type all
        if (!this.types.includes(this.TYPE_ALL)) {
          return;
        }

        // We click on the type all
        if (type === this.TYPE_ALL) {
          this.permissionValues = this.permissionValues.includes(type)
            ? [...this.types] as Array<string>
            : [];

          return;
        }

        // Nothing change
        if (this.permissionValues.length !== this.getTypesLength()) {
          return;
        }

        // We can add the TYPE_ALL because we check all checkboxes
        if (this.permissionValues.includes(this.TYPE_ALL)) {
          this.removePermission(this.TYPE_ALL);
        } else {
          this.addPermission(this.TYPE_ALL);
        }
      },
      /**
       * Execute ajax request to update permissions
       * @param String type
       * @param bool sendRequest Check if ajax request must be sent
       */
      sendUpdatePermissionRequest(type: string, sendRequest = true): void {
        this.checkCheckboxesPermissions(type);

        if (sendRequest === true) {
          const params: Record<string, any> = {
            permission: type,
            is_active: this.permissionValues.includes(type),
          };

          params[this.permissionKey] = this.permission[this.permissionKey] !== undefined
            ? this.permission[this.permissionKey]
            : this.permissionId;

          this.$emit('sendRequest', params);
        }

        // Update profile permission to prevent wrong bulk refresh
        this.types.forEach((t) => {
          this.profilePermissions[this.permissionId][<string>t] = this.permissionValues.includes(<string>t) ? '1' : '0';
        });

        if (this.permissionValues.includes(type)) {
          this.$emit('childUpdated', type);
        }
      },
      /**
       * Add permission to current values
       * @return bool
       */
      addPermission(type: string): boolean {
        if (this.permissionValues.includes(type)) {
          return false;
        }

        this.permissionValues.push(type);
        return true;
      },
      /**
       * Remove permission
       * @return bool
       */
      removePermission(type: string): boolean {
        if (!this.permissionValues.includes(type)) {
          return false;
        }

        this.permissionValues.splice(this.permissionValues.indexOf(type), 1);
        return true;
      },
      /**
       * A child has been updated
       */
      onChildUpdate(type: string): void {
        // type already includes
        this.sendUpdatePermissionRequest(
          type,
          this.addPermission(type),
        );
      },
      /**
       * Recursive emit send request
       */
      sendRequest(data: Record<string, any>): void {
        this.$emit('sendRequest', data);
      },
    },
  });
</script>

<style lang="scss">
  @for $i from 2 through 5 {
    .depth-level-#{$i} {
      padding-left: #{$i}rem;
    }
  }

  @media (max-width: 320px) {
    .permission-row {
      font-size: 0.8rem;
    }
  }
</style>
