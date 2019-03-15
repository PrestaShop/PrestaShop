<template>
  <div>
    <div :class="{parent, 'bg-light': levelDepth === 1}" class="row permission-row">
      <div class="col-lg-4">
        <template v-for="i in levelDepth" v-if="i > 2">
          &nbsp;&nbsp;
        </template>

        &raquo;

        <strong v-if="levelDepth === 1">{{ permission.name }}</strong>
        <template v-else>
          {{ permission.name }}
        </template>
      </div>

      <div class="col-lg-8 row">
        <div class="col text-center" v-for="type in types">
          <div class="md-checkbox md-checkbox-inline">
            <label>
              <input
                type="checkbox"
                class="js-tab-permissions-checkbox"
                :value="type"
                v-model="permissionValues"
                @change="sendUpdatePermissionRequest(type)"
                :disabled="!canEdit || !canEditCheckbox(type)"
              >
              <i class="md-checkbox-control"></i>
            </label>
          </div>
        </div>
      </div>
    </div>

    <div v-if="permission.children !== null">
      <row
        v-for="p, pId in permission.children"
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
      >
      </row>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'row',
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
    data() {
      return {
        permissionValues: [],
        TYPE_ALL: 'all',
      };
    },
    watch: {
      profilePermissions: {
        handler: function mandatoryFunctionForDeepWatching() {
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
    methods: {
      canEditCheckbox(type) {
        if (Object.keys(this.employeePermissions).length === 0) {
          return true;
        }

        if (!this.employeePermissions[this.permissionId]) {
          return false;
        }

        return this.employeePermissions[this.permissionId][type] === '1';
      },
      /**
       * Get the types length, depends if you have the TYPE_ALL or not
       */
      getTypesLength() {
        return this.types.includes(this.TYPE_ALL) ?
               this.types.length - 1 :
               this.types.length;
      },
      /**
       * Get permission from id
       */
      getPermission() {
        return this.profilePermissions[this.permissionId];
      },
      /**
       * Check if profile has permission
       */
      hasPermission(type) {
        const permission = this.getPermission();
        return permission !== undefined && parseInt(permission[type], 10) === 1;
      },
      /**
       * Refresh permissions and checkboxes
       */
      refreshPermissions() {
        Object.values(this.types).forEach((type) => {
          if (this.hasPermission(type)) {
            this.addPermission(type);
          } else if (this.permissionValues.includes(type)) {
            this.removePermission(type);
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
      checkCheckboxesPermissions(type) {
        // no need to check there is no type all
        if (!this.types.includes(this.TYPE_ALL)) {
          return;
        }

        // We click on the type all
        if (type === this.TYPE_ALL) {
          this.permissionValues = this.permissionValues.includes(type) ?
                                  [...this.types] :
                                  [];

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
      sendUpdatePermissionRequest(type, sendRequest = true) {
        this.checkCheckboxesPermissions(type);

        if (sendRequest === true) {
          const params = {
            permission: type,
            expected_status: this.permissionValues.includes(type),
          };

          params[this.permissionKey] = this.permission[this.permissionKey] !== undefined
                                     ? this.permission[this.permissionKey]
                                     : this.permissionId;

          this.$emit('sendRequest', params);
        }

        if (this.permissionValues.includes(type)) {
          this.$emit('childUpdated', type);
        }
      },
      /**
       * Add permission to current values
       * @return bool
       */
      addPermission(type) {
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
      removePermission(type) {
        if (!this.permissionValues.includes(type)) {
          return false;
        }

        this.permissionValues.splice(this.permissionValues.indexOf(type), 1);
        return true;
      },
      /**
       * A child has been updated
       */
      onChildUpdate(type) {
        // type already includes
        this.sendUpdatePermissionRequest(
          type,
          this.addPermission(type),
        );
      },
      /**
       * Recursive emit send request
       */
      sendRequest(data) {
        this.$emit('sendRequest', data);
      },
    },
  };
</script>
