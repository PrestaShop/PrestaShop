const faker = require('faker');

/**
 * Create new review to use on FO on product page
 * @class
 */
class ProductReviewData {
  /**
   * Constructor for class ProductReview
   * @param productReviewToCreate {Object} Could be used to add a review on a product
   */
  constructor(productReviewToCreate = {}) {
    /** @type {string} Title of the review */
    this.reviewTitle = productReviewToCreate.reviewTitle || faker.lorem.sentence(faker.random.number({min: 3, max: 7}));

    /** @type {string} Content of the review */
    this.reviewContent = productReviewToCreate.reviewContent
      || faker.lorem.sentences(faker.random.number({min: 3, max: 10}));

    /** @type {string} Rating of the review */
    this.reviewRating = productReviewToCreate.reviewRating || faker.random.number({min: 1, max: 5});
  }
}

module.exports = ProductReviewData;
