import ZoneData from '@data/faker/zone';

export default {
  europe: new ZoneData({
    id: 1,
    name: 'Europe',
    status: true,
  }),
  northAmerica: new ZoneData({
    id: 2,
    name: 'North America',
    status: true,
  }),
  asia: new ZoneData({
    id: 3,
    name: 'Asia',
    status: true,
  }),
  africa: new ZoneData({
    id: 4,
    name: 'Africa',
    status: true,
  }),
  oceania: new ZoneData({
    id: 5,
    name: 'Oceania',
    status: true,
  }),
  southAmerica: new ZoneData({
    id: 6,
    name: 'South America',
    status: true,
  }),
  europeNonEu: new ZoneData({
    id: 7,
    name: 'Europe (non-EU)',
    status: true,
  }),
  centralAmericaAntilla: new ZoneData({
    id: 8,
    name: 'Central America/Antilla',
    status: true,
  }),
};
