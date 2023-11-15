type APIAccessCreator = {
  id?: number
  clientName?: string
  clientId?: string
  description?: string
  tokenLifetime?: number
  enabled?: boolean
  scopes?: string[]
};

export default APIAccessCreator;
