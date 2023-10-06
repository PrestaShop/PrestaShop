import FOBasePage from '@pages/FO/FObasePage';

import ProductReviewData from '@data/faker/productReview';
import {
  ProductAttribute, ProductImageUrls, ProductInformations,
} from '@data/types/product';

import type {Page} from 'playwright';

/**
 * Product page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Product extends FOBasePage {
  public readonly messageNotVisibleToCustomers: string;

  private readonly warningMessage: string;

  private readonly productFlags: string;

  private readonly productFlag: string;

  private readonly productName: string;

  private readonly productCoverImg: string;

  private readonly thumbFirstImg: string;

  private readonly thumbSecondImg: string;

  private readonly productQuantity: string;

  private readonly shortDescription: string;

  private readonly productDescription: string;

  private readonly customizedTextarea: string;

  private readonly saveCustomizationButton: string;

  private readonly addToCartButton: string;

  private readonly blockCartModal: string;

  private readonly proceedToCheckoutButton: string;

  private readonly productQuantitySpan: string;

  private readonly productDetail: string;

  private readonly productFeaturesList: string;

  private readonly continueShoppingButton: string;

  private readonly productAvailabilityIcon: string;

  private readonly productAvailability: string;

  private readonly productSizeSelect: string;

  private readonly productSizeOption: (size: string) => string;

  private readonly productColorUl: string;

  private readonly productColorInput: (color: string) => string;

  private readonly productColors: string;

  private readonly metaLink: string;

  private readonly facebookSocialSharing: string;

  private readonly twitterSocialSharing: string;

  private readonly pinterestSocialSharing: string;

  private readonly productPricesBlock: string;

  private readonly discountAmountSpan: string;

  private readonly discountPercentageSpan: string;

  private readonly regularPrice: string;

  private readonly packProductsPrice: string;

  private readonly productPrice: string;

  private readonly taxShippingDeliveryBlock: string;

  private readonly deliveryInformationSpan: string;

  private readonly discountTable: string;

  private readonly quantityDiscountValue: string;

  private readonly unitDiscountColumn: string;

  private readonly unitDiscountValue: string;

  private readonly productUnitPrice: string;

  private readonly commentCount: string;

  private readonly emptyReviewBlock: string;

  private readonly productReviewList: string;

  private readonly productReviewRows: string;

  private readonly productReviewRow: (row: number) => string;

  private readonly productReviewTitle: (row: number) => string;

  private readonly productReviewContent: (row: number) => string;

  private readonly productRatingBlock: (row: number) => string;

  private readonly productRatingStar: (row: number) => string;

  private readonly emptyReviewAddReviewButton: string;

  private readonly notEmptyReviewAddReviewButton: string;

  private readonly productReviewModal: string;

  private readonly reviewForm: string;

  private readonly reviewTitle: string;

  private readonly reviewTextContent: string;

  private readonly reviewRating: (rating: number) => string;

  private readonly reviewSubmitButton: string;

  private readonly reviewSentConfirmationModal: string;

  private readonly closeReviewSentConfirmationModalButton: string;

  private readonly productInPackList: (productInList: number) => string;

  private readonly productInPackImage: (productInList: number) => string;

  private readonly productInPackName: (productInList: number) => string;

  private readonly productInPackPrice: (productInList: number) => string;

  private readonly productInPackQuantity: (productInList: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on product page
   */
  constructor() {
    super();

    // Messages
    this.messageNotVisibleToCustomers = 'This product is not visible to your customers.';

    // Selectors for product page
    this.warningMessage = 'main div.alert-warning p.alert-text';
    this.productFlags = '#content ul.product-flags';
    this.productFlag = '#content li.product-flag';
    this.productName = '#main h1';
    this.productCoverImg = '#content .product-cover img';
    this.thumbFirstImg = '#content li:nth-child(1) img.js-thumb';
    this.thumbSecondImg = '#content li:nth-child(2) img.js-thumb';
    this.productQuantity = '#quantity_wanted';
    this.shortDescription = '#product-description-short';
    this.productDescription = '#description';
    this.customizedTextarea = '.product-customization-item .product-message';
    this.saveCustomizationButton = 'button[name=\'submitCustomizedData\']';
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.blockCartModal = '#blockcart-modal';
    this.proceedToCheckoutButton = `${this.blockCartModal} div.cart-content-btn a`;
    this.productQuantitySpan = '#product-details div.product-quantities label';
    this.productDetail = 'div.product-information a[href=\'#product-details\']';
    this.productFeaturesList = '#product-details section.product-features';
    this.continueShoppingButton = `${this.blockCartModal} div.cart-content-btn button`;
    this.productAvailabilityIcon = '#product-availability i';
    this.productAvailability = '#product-availability';
    this.productSizeSelect = '#group_1';
    this.productSizeOption = (size: string) => `${this.productSizeSelect} option[title=${size}]`;
    this.productColorUl = '#group_2';
    this.productColorInput = (color: string) => `${this.productColorUl} input[title=${color}]`;
    this.productColors = 'div.product-variants div:nth-child(2)';
    this.metaLink = '#main > meta';
    this.facebookSocialSharing = '.social-sharing .facebook a';
    this.twitterSocialSharing = '.social-sharing .twitter a';
    this.pinterestSocialSharing = '.social-sharing .pinterest a';

    // Product prices block
    this.productPricesBlock = 'div.product-prices';
    this.productUnitPrice = `${this.productPricesBlock} p.product-unit-price`;
    this.discountAmountSpan = `${this.productPricesBlock} .discount.discount-amount`;
    this.discountPercentageSpan = `${this.productPricesBlock} .discount.discount-percentage`;
    this.regularPrice = `${this.productPricesBlock} .regular-price`;
    this.packProductsPrice = `${this.productPricesBlock} .product-pack-price span`;
    this.productPrice = `${this.productPricesBlock} .current-price span`;
    this.taxShippingDeliveryBlock = `${this.productPricesBlock} div.tax-shipping-delivery-label`;
    this.deliveryInformationSpan = `${this.taxShippingDeliveryBlock} span.delivery-information`;

    // Volume discounts table
    this.discountTable = '.table-product-discounts';
    this.quantityDiscountValue = `${this.discountTable} td:nth-child(1)`;
    this.unitDiscountColumn = `${this.discountTable} th:nth-child(2)`;
    this.unitDiscountValue = `${this.discountTable} td:nth-child(2)`;
    // Consult review selectors
    this.commentCount = '.comments-nb';
    this.emptyReviewBlock = '#empty-product-comment';
    this.productReviewList = '#product-comments-list';
    this.productReviewRows = `${this.productReviewList} div.product-comment-list-item.row`;
    this.productReviewRow = (row: number) => `${this.productReviewRows}:nth-child(${row})`;
    this.productReviewTitle = (row: number) => `${this.productReviewRow(row)} h4`;
    this.productReviewContent = (row: number) => `${this.productReviewRow(row)} p`;
    this.productRatingBlock = (row: number) => `${this.productReviewRow(row)} .grade-stars`;
    this.productRatingStar = (row: number) => `${this.productReviewRow(row)} .star-on`;
    // Add review selectors
    this.emptyReviewAddReviewButton = '#empty-product-comment button';
    this.notEmptyReviewAddReviewButton = '#product-comments-list-footer button';
    this.productReviewModal = '#post-product-comment-modal';
    this.reviewForm = '#post-product-comment-form';
    this.reviewTitle = `${this.reviewForm} input[name=comment_title]`;
    this.reviewTextContent = `${this.reviewForm} textarea[name=comment_content]`;
    this.reviewRating = (rating: number) => `.star-full div:nth-child(${rating})`;
    this.reviewSubmitButton = `${this.reviewForm} button[type=submit]`;
    this.reviewSentConfirmationModal = '#product-comment-posted-modal';
    this.closeReviewSentConfirmationModalButton = `${this.reviewSentConfirmationModal} button`;

    // Products in pack selectors
    this.productInPackList = (productInList: number) => `.product-pack article:nth-child(${productInList})`;
    this.productInPackImage = (productInList: number) => `${this.productInPackList(productInList)} div.thumb-mask img`;
    this.productInPackName = (productInList: number) => `${this.productInPackList(productInList)} div.pack-product-name a`;
    this.productInPackPrice = (productInList: number) => `${this.productInPackList(productInList)} div.pack-product-price`;
    this.productInPackQuantity = (productInList: number) => `${this.productInPackList(productInList)}`
      + ' div.pack-product-quantity';
  }

  // Methods

  /**
   * Get product page URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getProductPageURL(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.metaLink, 'content');
  }

  /**
   * Get product tag
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getProductTag(page: Page): Promise<string> {
    return this.getTextContent(page, this.productFlags);
  }

  /**
   * Is product tag visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isProductTagVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productFlag);
  }

  /**
   * Get product price
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductPrice(page: Page): Promise<string> {
    return this.getTextContent(page, this.productPrice);
  }

  /**
   * Get Product information (Product name, price, short description, description)
   * @param page {Page} Browser tab
   * @returns {Promise<ProductInformations>}
   */
  async getProductInformation(page: Page): Promise<ProductInformations> {
    return {
      name: await this.getTextContent(page, this.productName),
      price: await this.getPriceFromText(page, this.productPrice),
      summary: await this.getTextContent(page, this.shortDescription, false),
      description: (await page.locator(`${this.productDescription}:visible`).count())
        ? await this.getTextContent(page, this.productDescription)
        : '',
    };
  }

  /**
   * Get product information in pack
   * @param page {Page} Browser tab
   * @param productInList {number} Product in pack list
   * @returns {Promise<{image: string, quantity: number, price: string, name: string}>}
   */
  async getProductInPackList(page: Page, productInList: number = 1): Promise<{
    image: string | null, quantity: number,
    price: string, name: string
  }> {
    // Add +1 due to span before the article
    const productIdentifier: number = productInList + 1;

    return {
      image: await this.getAttributeContent(page, this.productInPackImage(productIdentifier), 'src'),
      name: await this.getTextContent(page, this.productInPackName(productIdentifier)),
      price: await this.getTextContent(page, this.productInPackPrice(productIdentifier)),
      quantity: await this.getNumberFromText(page, this.productInPackQuantity(productIdentifier)),
    };
  }

  /**
   * get regular price
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getRegularPrice(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.regularPrice);
  }

  /**
   * Get the price of products in pack
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getPackProductsPrice(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.packProductsPrice);
  }

  /**
   * Get product attributes from a Ul selector
   * @param page {Page} Browser tab
   * @param ulSelector {string} Selector to locate the element
   * @returns {Promise<Array<string>>}
   */
  async getProductsAttributesFromUl(page: Page, ulSelector: string): Promise<Array<string | null>> {
    return page.$$eval(`${ulSelector} li .attribute-name`, (all) => all.map((el) => el.textContent));
  }

  /**
   * Get product attributes
   * @param page {Page} Browser tab
   * @returns {Promise<ProductAttribute[]>}
   */
  async getProductAttributes(page: Page): Promise<ProductAttribute[]> {
    return [
      {
        name: 'size',
        value: await this.getTextContent(page, this.productSizeSelect),
      },
      {
        name: 'color',
        value: (await this.getProductsAttributesFromUl(page, this.productColorUl)).join(' '),
      },
    ];
  }

  /**
   * Get selected product attributes
   * @param page {Page} Browser tab
   * @returns {Promise<ProductAttribute[]>}
   */
  async getSelectedProductAttributes(page: Page): Promise<ProductAttribute[]> {
    return [
      {
        name: 'size',
        value: await this.getTextContent(page, `${this.productSizeSelect} option[selected]`, false),
      },
      {
        name: 'color',
        value: await this.getAttributeContent(page, `${this.productColors} input[checked]`, 'title') ?? '',
      },
    ];
  }

  /**
   * Get product image urls
   * @param page {Page} Browser tab
   * @returns {Promise<ProductImageUrls>}
   */
  async getProductImageUrls(page: Page): Promise<ProductImageUrls> {
    return {
      coverImage: await this.getAttributeContent(page, this.productCoverImg, 'src'),
      thumbImage: await this.getAttributeContent(page, this.thumbFirstImg, 'src'),
    };
  }

  /**
   * Get product unit price
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductUnitPrice(page: Page): Promise<string> {
    return this.getTextContent(page, this.productUnitPrice);
  }

  /**
   * Get discount column title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getDiscountColumnTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.unitDiscountColumn);
  }

  /**
   * Get quantity discount value from volume discounts table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getQuantityDiscountValue(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.quantityDiscountValue);
  }

  /**
   * Get discount value from volume discounts table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getDiscountValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.unitDiscountValue);
  }

  /**
   * Get discount amount
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDiscountAmount(page: Page): Promise<string> {
    return this.getTextContent(page, this.discountAmountSpan);
  }

  /**
   * Get discount percentage
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDiscountPercentage(page: Page): Promise<string> {
    return this.getTextContent(page, this.discountPercentageSpan);
  }

  /**
   * Get product availability label
   * @param page {Page} Browser tab
   * @return {promise<string>}
   */
  async getProductAvailabilityLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.productAvailability, false);
  }

  /**
   * Get delivery information text
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getDeliveryInformationText(page: Page): Promise<string> {
    return this.getTextContent(page, this.deliveryInformationSpan);
  }

  /**
   * Is delivery time displayed
   * @param page
   */
  async isDeliveryTimeDisplayed(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.deliveryInformationSpan);
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param id {number} Id for the thumb
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, id: number): Promise<string> {
    if (id === 1) {
      await this.waitForSelectorAndClick(page, this.thumbFirstImg);
      await this.waitForVisibleSelector(page, `${this.thumbFirstImg}.selected`);
    } else {
      await this.waitForSelectorAndClick(page, this.thumbSecondImg);
      await this.waitForVisibleSelector(page, `${this.thumbSecondImg}.selected`);
    }
    return this.getAttributeContent(page, this.productCoverImg, 'src');
  }

  /**
   * Select product attributes
   * @param page {Page} Browser tab
   * @param quantity {number} Quantity of the product that customer wants
   * @param attributes {ProductAttribute[]}  Product's attributes data to select
   * @returns {Promise<void>}
   */
  async selectAttributes(page: Page, quantity: number, attributes: ProductAttribute[]): Promise<void> {
    if (attributes.length === 0) {
      return;
    }
    for (let i: number = 0; i < attributes.length; i++) {
      switch (attributes[i].name) {
        case 'color':
          await Promise.all([
            this.waitForVisibleSelector(page, `${this.productColorInput(attributes[i].value)}[checked]`),
            page.click(this.productColorInput(attributes[i].value)),
          ]);
          break;
        case 'size':
          await Promise.all([
            this.waitForAttachedSelector(page, `${this.productSizeOption(attributes[i].value)}[selected]`),
            this.selectByVisibleText(page, this.productSizeSelect, attributes[i].value),
          ]);
          break;
        default:
          throw new Error(`${attributes[i].name} is not defined`);
      }
    }
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   * @param page {Page} Browser tab
   * @param quantity {number} Quantity of the product that customer wants
   * @param combination {ProductAttribute[]}  Product's combination data to add to cart
   * @param proceedToCheckout {boolean} True to click on proceed to checkout button on modal
   * @param customizedText {string} Value of customization
   * @returns {Promise<void>}
   */
  async addProductToTheCart(
    page: Page,
    quantity: number = 1,
    combination: ProductAttribute[] = [],
    proceedToCheckout: boolean = true,
    customizedText: string = 'text',
  ): Promise<void> {
    await this.selectAttributes(page, quantity, combination);
    if (quantity !== 1) {
      await this.setValue(page, this.productQuantity, quantity.toString());
    }

    if (await this.elementVisible(page, this.customizedTextarea, 2000)) {
      await this.setValue(page, this.customizedTextarea, customizedText);
      await this.waitForSelectorAndClick(page, this.saveCustomizationButton);
    }

    await this.waitForSelectorAndClick(page, this.addToCartButton);
    await this.waitForVisibleSelector(page, `${this.blockCartModal}[style*='display: block;']`);

    if (proceedToCheckout) {
      await this.waitForVisibleSelector(page, this.proceedToCheckoutButton);
      await this.clickAndWaitForURL(page, this.proceedToCheckoutButton);
      await this.waitForPageTitleToLoad(page);
    } else {
      await this.waitForSelectorAndClick(page, this.continueShoppingButton);
      await this.waitForHiddenSelector(page, this.continueShoppingButton);
    }
  }

  /**
   * Go to social sharing link
   * @param page {Page} Browser tab
   * @param socialSharing {string} Social network's name to get link from
   * @returns {Promise<string>}
   */
  async getSocialSharingLink(page: Page, socialSharing: string): Promise<string> {
    let selector;

    switch (socialSharing) {
      case 'Facebook':
        selector = this.facebookSocialSharing;
        break;

      case 'Twitter':
        selector = this.twitterSocialSharing;
        break;

      case 'Pinterest':
        selector = this.pinterestSocialSharing;
        break;

      default:
        throw new Error(`${socialSharing} was not found`);
    }

    return this.getAttributeContent(page, selector, 'href');
  }

  /**
   * Set quantity
   * @param page {Page} Browser tab
   * @param quantity {number} Quantity to set
   * @returns {Promise<void>}
   */
  async setQuantity(page: Page, quantity: number): Promise<void> {
    await this.setValue(page, this.productQuantity, quantity.toString());
  }

  /**
   * Is quantity displayed
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isQuantityDisplayed(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.productDetail);
    return this.elementVisible(page, this.productQuantitySpan, 1000);
  }

  /**
   * Get product features list
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductFeaturesList(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.productDetail);

    return this.getTextContent(page, this.productFeaturesList);
  }

  /**
   * Is features block visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isFeaturesBlockVisible(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.productDetail);

    return this.elementVisible(page, this.productFeaturesList);
  }

  /**
   * Get product condition
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductCondition(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.productDetail);

    return this.getTextContent(page, '#product-details div.product-condition');
  }

  /**
   * Is customization block visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCustomizationBlockVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, 'div.product-container div.product-information section.product-customization', 1000);
  }

  /**
   * Is availability product displayed
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAvailabilityQuantityDisplayed(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productAvailabilityIcon, 1000);
  }

  /**
   * Is price displayed
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPriceDisplayed(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productPrice, 1000);
  }

  /**
   * Is add to cart button displayed
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddToCartButtonDisplayed(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.addToCartButton, 1000);
  }

  /**
   * Is unavailable product size displayed
   * @param page {Page} Browser tab
   * @param size {string} The product size
   * @returns {Promise<boolean>}
   */
  async isUnavailableProductSizeDisplayed(page: Page, size: string): Promise<boolean> {
    await page.waitForTimeout(2000);
    return await page.$(this.productSizeOption(size)) !== null;
  }

  /**
   * Is unavailable product color displayed
   * @param page {Page} Browser tab
   * @param color {string} Product's color to check
   * @returns {Promise<boolean>}
   */
  async isUnavailableProductColorDisplayed(page: Page, color: string): Promise<boolean> {
    return this.elementVisible(page, this.productColorInput(color), 1000);
  }

  /**
   * Is add to cart button enabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddToCartButtonEnabled(page: Page): Promise<boolean> {
    return this.elementNotVisible(page, `${this.addToCartButton}:disabled`, 1000);
  }

  /**
   * Check if delivery information text is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isDeliveryInformationVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.deliveryInformationSpan, 1000);
  }

  /**
   * Add a product review
   * @param page {Page} Browser tab
   * @param productReviewData {ProductReviewData} The content of the product review (title, content, rating)
   * @returns {Promise<boolean>}
   */
  async addProductReview(page: Page, productReviewData: ProductReviewData): Promise<boolean> {
    if (await this.getNumberOfComments(page) !== 0) {
      await page.click(this.notEmptyReviewAddReviewButton);
    } else {
      await page.click(this.emptyReviewAddReviewButton);
    }
    await this.waitForVisibleSelector(page, this.productReviewModal);
    await this.setValue(page, this.reviewTitle, productReviewData.reviewTitle);
    await this.setValue(page, this.reviewTextContent, productReviewData.reviewContent);
    await page.click(this.reviewRating(productReviewData.reviewRating));
    await page.click(this.reviewSubmitButton);
    await page.click(this.closeReviewSentConfirmationModalButton);
    return this.elementNotVisible(page, this.reviewSentConfirmationModal, 3000);
  }

  /**
   * Get the number of approved review for a product
   * @param page {Page} The browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfComments(page: Page): Promise<number> {
    return page.$$eval(this.productReviewRows, (rows) => rows.length);
  }

  /**
   * Get the title of a review
   * @param page {Page} browser tab
   * @param row {Number} the review number in the list
   * @returns {Promise<string>}
   */
  async getReviewTitle(page: Page, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.productReviewTitle(row));
  }

  /**
   * Get the content of a review
   * @param page {Page} browser tab
   * @param row {Number} the review number in the list
   * @returns {Promise<string>}
   */
  async getReviewTextContent(page: Page, row: number = 1): Promise<string> {
    return this.getTextContent(page, this.productReviewContent(row));
  }

  /**
   * Get the rating of a review
   * @param page {Page} browser tab
   * @param row {Number} the review number in the list
   * @returns {Promise<number>}
   */
  async getReviewRating(page: Page, row: number = 1): Promise<number> {
    return page.$$eval(this.productRatingStar(row), (divs) => divs.length);
  }

  /**
   * Get the warning message
   * @param page {Page} browser tab
   * @returns {Promise<string>}
   */
  async getWarningMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.warningMessage);
  }
}

export default new Product();
