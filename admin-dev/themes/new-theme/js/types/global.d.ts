interface Window {
  $: JQueryStatic;
  showSuccessMessage(message: string): void;
  showErrorMessage(message: string): void;
  prestashop: PrestashopWindow;
}

interface JQuery {
  tableDnD(params: unknown): void;
  passy(params: unknown, length?: number): void;
  serializeJSON: any;
}

interface JQueryStatic {
  tableDnD: TableDnD;
  passy: any;
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
  errors?: Record<string, string>;
}

interface PrestashopWindow {
  customRoutes: unknown;
}

interface RegExpPositions extends RegExpExecArray {
  rowId: string;
  oldPosition: string;
}

interface SelectorsMap extends Record<string, string> {
  [key: string]: string;
}
