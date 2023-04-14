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
  <div
    class="card history"
    :class="{ collapsed: isCollapsed, expanded: !isCollapsed }"
    @click="preventClose"
  >
    <div class="card-header">
      {{
        $t("modal.history.editedCombination", {
          "editedNb": combinationsList.length,
        })
      }}
    </div>

    <div class="card-block">
      <ul
        class="history-list"
        v-if="areCombinationsNotEmpty"
      >
        <li
          :class="['history-item', isSelected(combination.id)]"
          v-for="(combination, key) of paginatedDatas[currentPage - 1]"
          :key="key"
          @click="selectCombination(combination)"
        >
          {{ combination.title }}
          <i class="material-icons">edit</i>
        </li>
      </ul>
      <div
        class="history-empty"
        v-else
      >
        <img :src="emptyImageUrl">
        <p class="history-empty-tip">
          {{ $t("modal.history.empty") }}
        </p>
      </div>
    </div>
    <div
      class="card-footer"
      v-if="areCombinationsNotEmpty"
    >
      <pagination
        :pagination-length="14"
        :datas="combinationsList"
        @paginated="constructDatas"
      />
    </div>

    <div
      class="history-handle"
      :title="handleTitle"
      @click="togglePanel"
    />
  </div>
</template>

<script lang="ts">
  import ProductEventMap from '@pages/product/product-event-map';
  import {Combination} from '@pages/product/combination/combination-modal/CombinationModal.vue';
  import Pagination from '@PSVue/components/Pagination.vue';
  import {defineComponent, PropType} from 'vue';

  interface HistoryStates {
    paginatedDatas: Array<Record<string, any>>;
    currentPage: number;
    forcedCollapsed: boolean | null;
  }

  const CombinationsEventMap = ProductEventMap.combinations;

  export default defineComponent({
    name: 'CombinationHistory',
    data(): HistoryStates {
      return {
        paginatedDatas: [],
        currentPage: 1,
        forcedCollapsed: null,
      };
    },
    components: {
      Pagination,
    },
    props: {
      combinationsList: {
        type: Array as PropType<Array<Record<string, any>>>,
        default: () => [],
      },
      selectedCombinationId: {
        type: Number,
        required: true,
      },
      emptyImageUrl: {
        type: String,
        required: true,
      },
    },
    computed: {
      areCombinationsNotEmpty(): boolean {
        return this.combinationsList.length > 0;
      },
      isCollapsed(): boolean {
        // Null indicates initial state, collapsed by default, unless history has more than one combination
        const isCollapsed = this.forcedCollapsed === null ? this.combinationsList.length <= 1 : this.forcedCollapsed;
        this.$emit('collapsed', isCollapsed);

        return isCollapsed;
      },
      handleTitle(): string {
        return this.isCollapsed ? this.$t('modal.history.open') : this.$t('modal.history.close');
      },
    },
    methods: {
      /**
       * Used to select combination in CombinationModal parent component
       *
       * @param {object} combination
       */
      selectCombination(combination: Combination): void {
        this.$emit(CombinationsEventMap.selectCombination, combination);
      },
      /**
       * This events comes from the pagination component
       */
      preventClose(event: Event): void {
        event.stopPropagation();
        event.preventDefault();
      },
      /**
       * This events comes from the pagination component as
       * he's the one managing cutting datas into chunks
       *
       * @param {array} datas
       */
      constructDatas(datas: Record<string, any>): void {
        this.paginatedDatas = datas.paginatedDatas;
        this.currentPage = datas.currentPage;
      },
      /**
       * Used to avoid having too much logic in the markup
       */
      isSelected(idCombination: number): null | string {
        return this.selectedCombinationId === idCombination
          || this.combinationsList.length === 1
          ? 'selected'
          : null;
      },
      togglePanel(): void {
        // Null is initial state, collapsed by default, we toggle the panel
        if (this.forcedCollapsed === null) {
          // The expected toggle depends on the current state, which is this case depends on the history length
          this.forcedCollapsed = this.combinationsList.length > 1;
        } else {
          this.forcedCollapsed = !this.forcedCollapsed;
        }
        this.$emit('collapsed', this.forcedCollapsed);
      },
    },
  });
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.history {
  position: relative;
  max-width: 400px;
  width: 100%;
  min-height: calc(100% - 3.5rem);
  top: 50%;
  transform: translateY(-50%);
  height: 95%;
  margin: 0 1rem;
  border-top-right-radius: 0;

  &-list {
    padding: 0;
    margin: 0;
  }

  .card-header {
    border-top-right-radius: 0;
  }

  .card-block {
    padding: 0;
    height: calc(100% - 7rem);
    overflow: auto;
  }

  &-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: calc(100% - 4rem);

    &-tip {
      color: #8a8a8a;
      font-size: 1rem;
      text-align: center;
      max-width: 280px;
      margin-top: 1.75rem;
    }
  }

  &-item {
    list-style-type: none;
    padding: 0.75rem 1rem;
    transition: 0.25s ease-out;
    cursor: pointer;
    position: relative;

    i {
      color: $primary;
      opacity: 0;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      right: 1rem;
      font-size: 1.25rem;
      transition: 0.25s ease-out;
    }

    &.selected {
      background: #f7f7f7;
    }

    &:hover {
      background: #f0fcfd;
      color: $primary;

      i {
        opacity: 1;
      }
    }
  }

  .history-handle {
    position: absolute;
    top: -1px;
    right: -2rem;
    background-color: #fafbfc;
    width: 2rem;
    height: 46px;
    border: 1px solid #dbe6e9;
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    border-left: none;
    cursor: pointer;

    &::after {
      position: absolute;
      top: 50%;
      right: 0.25rem;
      transform: translateY(-50%);
      font-family: "Material Icons",Arial,Verdana,Tahoma,sans-serif;
      font-size: 1.5rem;
      content: 'keyboard_arrow_left';
    }
  }

  transition: width 500ms linear;

  &.collapsed {
    margin-left: 0;
    width: 0;
    border: none;

    .history-handle {
      top: -0.5px;
      right: -2rem;
      background-color: #fff;

      &::after {
        content: 'history';
      }
    }

    .card-header,
    .card-block,
    .card-footer {
      display: none;
    }
  }
}

@media screen and (max-width: 1299.98px) {
  .history {
    display: none;
  }
}
</style>
