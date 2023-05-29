type WebserviceMethod = 'all' | 'GET' | 'PUT' | 'POST' | 'PATCH' | 'DELETE' | 'HEAD';

type WebservicePermission = {
  resource: string
  methods: WebserviceMethod[]
};

type WebserviceCreator = {
  key?: string
  keyDescription?: string
  status?: boolean
  permissions?: WebservicePermission[]
};

export {
  WebserviceCreator,
  WebservicePermission,
  WebserviceMethod,
};
