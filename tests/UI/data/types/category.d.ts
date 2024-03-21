import CategoryData from '@data/faker/category';

import {
  // Import data
  type FakerGroup,
} from '@prestashop-core/ui-testing';

type CategoryCreator = {
  id?: number
  position?: number
  name?: string
  displayed?: boolean
  description?: string
  metaTitle?: string
  metaDescription?: string
  groupAccess?: FakerGroup
  coverImage?: string
  thumbnailImage?: string
  children?: CategoryData[]
  products?: string[]
};

type CategoryFilter = {
  filterBy: string
  value: string
};

export {
  CategoryCreator,
  CategoryFilter,
};
