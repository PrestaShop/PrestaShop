import SearchEngineData from '@data/faker/searchEngine';

export default {
  google: new SearchEngineData({
    id: 1,
    server: 'google',
    queryKey: 'q',
  }),
  lycos: new SearchEngineData({
    id: 8,
    server: 'lycos',
    queryKey: 'query',
  }),
  voila: new SearchEngineData({
    id: 11,
    server: 'voila',
    queryKey: 'rdata',
  }),
};
