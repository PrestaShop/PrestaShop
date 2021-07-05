interface Window {
  $: JQueryStatic;
  showSuccessMessage(message: string): void;
  showErrorMessage(message: string): void;
  prestashop: PrestashopWindow;
  isShopMaintenance: boolean;
  moduleTranslations: Record<string, any>;
  moduleURLs: Record<string, any>;
  str2url: any;
  prestaShopUiKit: any;
  // eslint-disable-next-line
  update_success_msg: string;
  adminNotificationPushLink: string;
  baseAdminDir: string;
  tokenAdminOrders: string;
  tokenAdminCustomers: string;
  tokenAdminCustomerThreads: string;
  // eslint-disable-next-line
  translate_javascripts: Record<string, any>;
  modalConfirmation: any;
  // eslint-disable-next-line
  ps_round: any;
  Dropzone: any;
  data: any;
  pstooltip: any;
}

interface TypeaheadDatasetConfig {
  display: string | ((text: string) => void);
  value: string;
  limit: number;
  dataLimit: number;
  onSelect(selectedItem: unknown, event: Event, searchInput: JQuery): boolean;
  onClose(event: Event, searchInput: JQuery): void;
  templates?: Record<string, unknown>;
}

interface TypeaheadConfig {
  minLength: number;
  highlight: boolean;
  cache: boolean;
  hint: boolean;
}

/* eslint-disable */
interface JQuery {
  tableDnD(params: unknown): void;
  passy: any;
  tokenfield: any;
  clickableDropdown: () => void;
  datetimepicker: any;
  select2: any;
  sortable: any;
  fancybox: any;
  growl: any;
  pstooltip: any;
}
/* eslint-disable */

interface JQueryStatic {
  tableDnD: TableDnD;
  passy: any;
  tokenfield: any;
  clickableDropdown: () => void;
  datetimepicker: any;
  select2: any;
  sortable: any;
  fancybox: any;
  growl: any;
  pstooltip: any;
}

interface TableDnD {
  serialize(): string;
  jsonize(): string;
}

interface AjaxError {
  responseJSON: AjaxResponse;
}

interface AjaxResponse {
  message: string;
  responseJSON?: AjaxResponse;
}

interface PrestashopWindow {
  customRoutes: unknown;
  component: any;
  instance: any;
}

interface RegExpPositions extends RegExpExecArray {
  rowId: string;
  oldPosition: string;
}

type FetchResponse = Record<string, any>;

type OptionsObject = FetchResponse;


type VTags = Vue & { 
  add: (tag: any) => void;
  tag: any;
};

type VTagsInput = Vue & { 
  value: any;
};

type VCheckbox = Vue & { 
  $data: VCheckboxDatas;
};

type VCheckboxDatas = Vue & { 
  checked: boolean;
};

type VTransModal = Vue & { 
  showModal: () => void;
  $once: any;
};

type VPrincipal = Vue & { 
  saveTranslations: () => void;
  edited: () => boolean;
};
