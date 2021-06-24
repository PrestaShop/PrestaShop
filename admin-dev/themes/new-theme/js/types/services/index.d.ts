interface ServiceType {
  fetch: (offset: number, limit: number) => Promise<FetchResponse>;
}

export default ServiceType;
