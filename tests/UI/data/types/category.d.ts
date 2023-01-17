import type GroupData from '@data/faker/group';

type CategoryCreator = {
  name?: string
  displayed?: boolean
  metaTitle?: string
  metaDescription?: string
  groupAccess?: GroupData
};

export default CategoryCreator;
