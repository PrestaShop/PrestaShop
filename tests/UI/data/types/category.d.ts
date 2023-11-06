import type GroupData from '@data/faker/group';
import CategoryData from '@data/faker/category';

type CategoryCreator = {
  id?: number
  position?: number
  name?: string
  displayed?: boolean
  description?: string
  metaTitle?: string
  metaDescription?: string
  groupAccess?: GroupData
  coverImage?: string
  thumbnailImage?: string
  children?: CategoryData[]
};

type CategoryFilter = {
  filterBy: string
  value: string
};

export {
  CategoryCreator,
  CategoryFilter,
};
