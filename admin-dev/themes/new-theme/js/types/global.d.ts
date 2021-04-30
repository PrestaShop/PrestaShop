interface Window {
  $: JQueryStatic;
  showSuccessMessage(message: string): void;
  showErrorMessage(message: string): void;
  prestashop: PrestashopWindow;
}

interface JQuery {
  tableDnD(params: unknown): void;
}

interface JQueryStatic {
  tableDnD: TableDnD;
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
