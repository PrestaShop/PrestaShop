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
  margin: 0 var(--#{$cdk}size-16);
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
    height: calc(100% - 162px);
    overflow: auto;
  }

  &-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: calc(100% - 4rem);

    &-tip {
      color: var(--#{$cdk}primary-600);
      font-size: var(--#{$cdk}size-16);
      text-align: center;
      max-width: 280px;
      margin-top: var(--#{$cdk}size-28);
    }
  }

  &-item {
    list-style-type: none;
    padding: var(--#{$cdk}size-12) var(--#{$cdk}size-16);
    transition: 0.25s ease-out;
    cursor: pointer;
    position: relative;

    i {
      color: var(--#{$cdk}primary-800);
      opacity: 0;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      right: var(--#{$cdk}size-16);
      font-size: var(--#{$cdk}size-20);
      transition: 0.25s ease-out;
    }

    &.selected {
      background: var(--#{$cdk}primary-200);
    }

    &:hover {
      background: var(--#{$cdk}primary-100);
      color: var(--#{$cdk}primary-800);

      i {
        opacity: 1;
      }
    }
  }

  .history-handle {
    position: absolute;
    top: 0;
    right: calc(-1 * var(--#{$cdk}size-32));
    background-color: var(--#{$cdk}primary-100);
    width: var(--#{$cdk}size-32);
    height: var(--#{$cdk}size-40);
    border: 1px solid var(--#{$cdk}primary-400);
    border-top-right-radius: var(--#{$cdk}size-5);
    border-bottom-right-radius: var(--#{$cdk}size-5);
    border-left: none;
    cursor: pointer;

    &::after {
      position: absolute;
      top: 50%;
      right: var(--#{$cdk}size-4);
      transform: translateY(-50%);
      font-family: var(--#{$cdk}font-family-material-icons);
      font-size: var(--#{$cdk}size-24);
      content: 'keyboard_arrow_left';
    }
  }

  transition: width 500ms linear;

  &.collapsed {
    margin-left: 0;
    width: 0;
    border: none;

    .history-handle {
      border: none;
      right: calc(-1 * var(--#{$cdk}size-32));
      background-color: var(--#{$cdk}white);

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
