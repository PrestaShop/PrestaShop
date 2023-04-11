import type GroupData from '@data/faker/group';

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
  metaImage?: string
};

export default CategoryCreator;
