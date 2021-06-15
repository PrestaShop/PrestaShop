const faker = require('faker');

module.exports = class ProductReview {
  constructor(productReviewToCreate = {}) {
    this.reviewTitle = productReviewToCreate.reviewTitle
      || faker.lorem.sentence(faker.random.number({min: 3, max: 7}));
    this.reviewContent = productReviewToCreate.reviewContent
      || faker.lorem.sentences(faker.random.number({min: 3, max: 10}));
    this.reviewRating = productReviewToCreate.reviewRating
      || faker.random.number({min: 1, max: 5});
  }
};
