import {Store} from 'vuex';

interface State {
  sort: string;
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

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $store: Store<State>;
    trans: (text: string) => string;
    showModal: () => void;
  }
}
