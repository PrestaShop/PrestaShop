interface PaginationServiceType {
  fetch: (offset: number, limit: number) => Promise<FetchResponse> | JQuery.jqXHR<any>;
}

export default PaginationServiceType;
