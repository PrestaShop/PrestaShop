import type {ProductReview} from '@data/types/product';

import {faker} from '@faker-js/faker';

/**
 * Create new review to use on FO on product page
 * @class
 */
export default class ProductReviewData {
  public reviewTitle: string;

  public reviewContent: string;

  public reviewRating: number;

  /**
   * Constructor for class ProductReview
   * @param productReviewToCreate {ProductReview} Could be used to add a review on a product
   */
  constructor(productReviewToCreate: ProductReview = {}) {
    /** @type {string} Title of the review */
    this.reviewTitle = productReviewToCreate.reviewTitle
      || faker.lorem.sentence(faker.datatype.number({min: 3, max: 7}));

    /** @type {string} Content of the review */
    this.reviewContent = productReviewToCreate.reviewContent
      || faker.lorem.sentences(faker.datatype.number({min: 3, max: 10}));

    /** @type {number} Rating of the review */
    this.reviewRating = productReviewToCreate.reviewRating || faker.datatype.number({min: 1, max: 5});
  }
}
