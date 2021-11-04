interface ServiceType {
  fetch: (offset: number, limit: number) => Promise<FetchResponse> | JQuery.jqXHR<any>;
}

export default ServiceType;
