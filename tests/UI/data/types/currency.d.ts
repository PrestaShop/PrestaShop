type CurrencyCreator = {
  name?: string
  frName?: string
  symbol?: string
  isoCode?: string
  exchangeRate?: number
  decimals?: number
  enabled?: boolean
};

type CurrencyFormat = 'leftWithSpace' | 'leftWithoutSpace' | 'rightWithSpace' | 'rightWithoutSpace';

export {
  CurrencyCreator,
  CurrencyFormat,
};
