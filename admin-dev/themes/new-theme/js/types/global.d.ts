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
  Dropzone: Dropzone;
  data: any;
  pstooltip: any;
  permissionsMessages: Array<string>;
}

interface JQuery {
  tableDnD(params: unknown): void;
  tokenfield: any;
  clickableDropdown: () => void;
  datetimepicker: any;
  select2: any;
  sortable: any;
  fancybox: any;
  growl: any;
  pstooltip: any;
  serializeJSON: any;
}

interface JQueryStatic {
  tableDnD: TableDnD;
  tokenfield: any;
  clickableDropdown: () => void;
  datetimepicker: any;
  select2: any;
  sortable: any;
  fancybox: any;
  growl: any;
  pstooltip: any;
  serializeJSON: any;
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
  errors?: Record<string, string>;
}

interface PrestashopWindow {
  customRoutes: unknown;
  component: any;
  instance: any;
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

interface SelectorsMap extends Record<string, string> {
  [key: string]: string;
}

type VTransModal = Vue & {
  showModal: () => void;
  $once: any;
};

type VPrincipal = Vue & {
  saveTranslations: () => void;
  edited: () => boolean;
};

interface KeyStringRecord extends Dropzone.DropzoneOptions {
  [key: string]: any;
}
