import {Store} from 'vuex';
import {Http} from 'vue-resource/types/vue_resource';

declare module 'vue/types/vue' {
  interface VueConstructor {
    resource: any;
    http: Http;
  }
  interface Vue {
    resource: any;
    trans: (text: string) => string;
    showModal: () => void;
  }
}

declare module '@vue/runtime-core' {
  interface State {
    pageIndex: number;
    totalPages: number;
    translationsPerPage: number;
    currentDomain: string;
    translations: Record<string, any>;
    catalog: Record<string, any>;
    domainsTree: Array<any>;
    totalMissingTranslations: number
    totalTranslations: number;
    currentDomainTotalTranslations: number;
    currentDomainTotalMissingTranslations: number;
    isReady: boolean;
    sidebarLoading: boolean;
    principalLoading: boolean;
    searchTags: Array<any>;
    modifiedTranslations: Array<any>;

    order: string;
    productsPerPage: number;
    products: Array<any>;
    hasQty: boolean;
    keywords: Array<any>;
    suppliers: Record<string, any>
    categories: Array<any>;
    categoryList: Array<any>;
    movements: Array<any>;
    employees: Array<any>;
    movementsTypes: Array<any>;
    isLoading: boolean;
    editBulkUrl: string;
    bulkEditQty: any;
    productsToUpdate: Array<any>;
    selectedProducts: Array<any>;
  }
  // provide typings for `this.$store`
  interface ComponentCustomProperties {
    $store: Store<State>;
  }

  interface ComputedOptions {
    $store: Store<State>;
  }
}
