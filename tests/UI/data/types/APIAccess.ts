type APIAccessCreator = {
  id?: number
  clientName?: string
  clientId?: string
  description?: string
  tokenLifetime?: number
  scopes?: string[]
};

export default APIAccessCreator;
