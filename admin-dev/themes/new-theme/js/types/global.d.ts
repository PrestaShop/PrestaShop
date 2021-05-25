interface Window {
  $: JQueryStatic;
  showSuccessMessage(message: string): void;
  showErrorMessage(message: string): void;
  prestashop: PrestashopWindow;
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
}
/* eslint-disable */

interface JQueryStatic {
  tableDnD: TableDnD;
  passy: any;
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
}

interface PrestashopWindow {
  customRoutes: unknown;
}

interface RegExpPositions extends RegExpExecArray {
  rowId: string;
  oldPosition: string;
}

type FetchResponse = Record<string, number | string | Record<string, unknown>>;

type OptionsObject = FetchResponse;
