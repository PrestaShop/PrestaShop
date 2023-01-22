import SqlTableData from '@data/faker/sqlTable';

export default {
  ps_access: new SqlTableData({
    name: 'ps_access',
    columns: [
      'id_profile',
      'id_authorization_role',
    ],
  }),
  ps_alias: new SqlTableData({
    name: 'ps_alias',
    columns: [
      'id_alias',
      'alias',
      'search',
      'active',
    ],
  }),
};
