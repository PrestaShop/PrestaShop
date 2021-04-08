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
    @click="preventClose"
  >
    <div class="card-header">
      Edited combination ({{ combinationsList.length }})
    </div>

    <div class="card-block">
      <ul
        class="history-list"
        v-if="paginatedDatas.length > 0"
      >
        <li
          :class="[
            'history-item',
            selectedCombination == combination.id ||
              combinationsList.length == 1
              ? 'selected'
              : null,
          ]"
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
        <img
          :src="emptyImage"
        >
        <p class="history-empty-tip">
          You will find here the list of the combinations you edited (the list
          resets when you leave edit mode).
        </p>
      </div>
    </div>
    <div
      class="card-footer"
      v-if="combinationsList.length > 0"
    >
      <pagination
        :pagination-length="14"
        :datas="combinationsList"
        @paginated="constructDatas"
      />
    </div>
  </div>
</template>

<script>
  import ProductEventMap from '@pages/product/product-event-map';
  import Pagination from '@vue/components/Pagination';

  const CombinationsEventMap = ProductEventMap.combinations;

  export default {
    name: 'CombinationHistory',
    data() {
      return {
        paginatedDatas: [],
        currentPage: 1,
      };
    },
    components: {
      Pagination,
    },
    props: {
      combinationsList: {
        type: Array,
        default: () => [],
      },
      selectedCombination: {
        type: Number,
        required: true,
      },
      emptyImage: {
        type: String,
        required: true,
      },
    },
    mounted() {
      this.$parent.$on(CombinationsEventMap.selectCombination, (id) => {
        this.selectedCombination = {id};
      });
    },
    methods: {
      selectCombination(combination) {
        this.$emit(CombinationsEventMap.selectCombination, combination);
      },
      preventClose(event) {
        event.stopPropagation();
        event.preventDefault();
      },
      constructDatas(datas) {
        this.paginatedDatas = datas.paginatedDatas;
        this.currentPage = datas.currentPage;
      },
    },
  };
</script>

<style lang="scss" type="text/scss">
@import "~@scss/config/_settings.scss";

.history {
  &-list {
    padding: 0;
    margin: 0;
  }

  .card-block {
    padding: 0;
    height: calc(100% - 7rem);
  }

  &-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: calc(100% - 4rem);

    &-tip {
      color: #8A8A8A;
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
}
</style>
