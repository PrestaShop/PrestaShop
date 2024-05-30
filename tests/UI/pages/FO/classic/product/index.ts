import FOBasePage from '@pages/FO/FObasePage';
import {
  ProductAttribute, ProductImageUrls, ProductInformations,
} from '@data/types/product';

import type {Page} from 'playwright';
import {
  type FakerProductReview,
} from '@prestashop-core/ui-testing';

/**
 * Product page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Product extends FOBasePage {
  public readonly messageNotVisibleToCustomers: string;

  public readonly messageAlertNotificationSaved: string;

  public readonly messageAlertNotificationEmailInvalid: string;

  public readonly messageAlertNotificationAlreadyRegistered: string;

  protected warningMessage: string;

  protected productFlags: string;

  private readonly productFlag: (flag: string) => string;

  protected productName: string;

  protected productCoverImg: string;

  protected thumbImg: (row: number) => string;

  private readonly thumbImgProductModal: (row: number) => string;

  protected scrollBoxImages: (direction: string) => string;

  protected zoomIcon: string;

  private readonly productModal: string;

  protected productCoverImgProductModal: string;

  protected productQuantity: string;

  protected productRowQuantityUpDownButton: (direction: string) => string;

  protected shortDescription: string;

  private readonly productDescription: string;

  protected customizationBlock: string;

  protected customizedTextarea: (row: number) => string;

  private readonly customizedFile: (row: number) => string;

  protected customizationsMessage: (row: number) => string;

  private readonly customizationImg: (row: number) => string;

  private readonly saveCustomizationButton: string;

  private readonly addToCartButton: string;

  private readonly blockCartModal: string;

  protected proceedToCheckoutButton: string;

  private readonly productQuantitySpan: string;

  private readonly productDetail: string;

  private readonly productFeaturesList: string;

  private readonly continueShoppingButton: string;

  private readonly productAvailability: string;

  private readonly productAvailabilityIcon: string;

  private readonly productMinimalQuantity: string;

  protected productAttributeVariantSpan: (itemNumber: number) => string;

  protected productAttributeSelect: (itemNumber: number) => string;

  protected productAttributeButton: (itemNumber: number) => string;

  private readonly productSizeSelect: string;

  private readonly productSizeOption: (size: string) => string;

  private readonly productColorUl: string;

  private readonly productColorInput: (color: string) => string;

  private readonly productColors: string;

  private readonly metaLink: string;

  private readonly facebookSocialSharing: string;

  private readonly twitterSocialSharing: string;

  private readonly pinterestSocialSharing: string;

  protected productPricesBlock: string;

  protected discountAmountSpan: string;

  protected discountPercentageSpan: string;

  protected regularPrice: string;

  private readonly packProductsPrice: string;

  protected productPrice: string;

  private readonly taxShippingDeliveryBlock: string;

  protected deliveryInformationSpan: string;

  private readonly productInformationBlock: string;

  private readonly productMailAlertsBlock: string;

  private readonly productMailAlertsEmailInput: string;

  private readonly productMailAlertsGDPRLabel: string;

  private readonly productMailAlertsNotifyButton: string;

  private readonly productMailAlertsNotification: string;

  protected discountTable: string;

  protected quantityDiscountValue: string;

  protected unitDiscountColumn: string;

  protected unitDiscountValue: string;

  protected savedValue: string;

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

  private readonly productReviewModalGDPRLabel: string;

  private readonly reviewForm: string;

  private readonly reviewTitle: string;

  private readonly reviewTextContent: string;

  private readonly reviewRating: (rating: number) => string;

  private readonly reviewSubmitButton: string;

  private readonly reviewCancelButton: string;

  private readonly reviewSentConfirmationModal: string;

  private readonly closeReviewSentConfirmationModalButton: string;

  protected readonly productInPackList: (productInList: number) => string;

  protected productInPackImage: (productInList: number) => string;

  protected productInPackName: (productInList: number) => string;

  protected productInPackPrice: (productInList: number) => string;

  protected productInPackQuantity: (productInList: number) => string;

  private readonly productsBlock: (blockName: string) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on product page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    // Messages
    this.messageNotVisibleToCustomers = 'This product is not visible to your customers.';
    this.messageAlertNotificationSaved = 'Request notification registered';
    this.messageAlertNotificationEmailInvalid = 'Your email address is invalid.';
    this.messageAlertNotificationAlreadyRegistered = 'You will be notified when this product is available.';

    // Selectors for product page
    this.warningMessage = 'main div.alert-warning p.alert-text';
    this.productFlags = '#content ul.product-flags';
    this.productFlag = (flag: string) => `#content li.product-flag${flag.length === 0 ? '' : `.${flag}`}`;
    this.productName = '#main h1';
    this.productCoverImg = '#content .product-cover img';
    this.thumbImg = (row: number) => `#content li:nth-child(${row}) img.js-thumb`;
    this.scrollBoxImages = (direction: string) => `#content div.scroll-box-arrows.scroll i.material-icons.${direction}`;
    this.zoomIcon = 'div.images-container div.product-cover i.zoom-in';
    this.productModal = '#product-modal';
    this.productCoverImgProductModal = '#product-modal picture img.js-modal-product-cover';
    this.thumbImgProductModal = (row: number) => `#thumbnails li:nth-child(${row}) picture img.js-modal-thumb`;
    this.productQuantity = '#quantity_wanted';
    this.productRowQuantityUpDownButton = (direction: string) => 'span.input-group-btn-vertical'
      + ` button.bootstrap-touchspin-${direction}`;
    this.shortDescription = '#product-description-short';
    this.productDescription = '#description';
    this.customizationBlock = 'div.product-container div.product-information section.product-customization';
    this.customizedTextarea = (row: number) => `.product-customization-item:nth-child(${row}) .product-message`;
    this.customizedFile = (row: number) => `li:nth-child(${row}) .js-file-input`;
    this.saveCustomizationButton = 'button[name=\'submitCustomizedData\']';
    this.customizationsMessage = (row: number) => `div.product-information li:nth-child(${row}) h6`;
    this.customizationImg = (row: number) => `div.product-information li:nth-child(${row}) a.remove-image`;
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.blockCartModal = '#blockcart-modal';
    this.proceedToCheckoutButton = `${this.blockCartModal} div.cart-content-btn a`;
    this.productQuantitySpan = '#product-details div.product-quantities label';
    this.productDetail = 'div.product-information a[href=\'#product-details\']';
    this.productFeaturesList = '#product-details section.product-features';
    this.continueShoppingButton = `${this.blockCartModal} div.cart-content-btn button`;
    this.productAvailability = '#product-availability';
    this.productAvailabilityIcon = `${this.productAvailability} i`;
    this.productMinimalQuantity = 'div.product__add-to-cart .product__minimal-quantity';
    this.productAttributeVariantSpan = (itemNumber: number) => `div.product-variants-item:nth-child(${itemNumber}) span`;
    this.productAttributeSelect = (itemNumber: number) => `div.product-variants-item:nth-child(${itemNumber}) select`;
    this.productAttributeButton = (itemNumber: number) => `div.product-variants-item:nth-child(${itemNumber}) ul input`;
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

    // Product information block
    this.productInformationBlock = 'div.product-information';
    this.productMailAlertsBlock = `${this.productInformationBlock} div.js-mailalert`;
    this.productMailAlertsEmailInput = `${this.productMailAlertsBlock} input[type="email"]`;
    this.productMailAlertsGDPRLabel = `${this.productMailAlertsBlock} div.gdpr_consent label.psgdpr_consent_message `
      + 'span:nth-of-type(2)';
    this.productMailAlertsNotifyButton = `${this.productMailAlertsBlock} button`;
    this.productMailAlertsNotification = `${this.productMailAlertsBlock} article`;

    // Volume discounts table
    this.discountTable = '.table-product-discounts';
    this.quantityDiscountValue = `${this.discountTable} td:nth-child(1)`;
    this.unitDiscountColumn = `${this.discountTable} th:nth-child(2)`;
    this.unitDiscountValue = `${this.discountTable} td:nth-child(2)`;
    this.savedValue = `${this.discountTable} td:nth-child(3)`;
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
    this.productReviewModalGDPRLabel = `${this.productReviewModal} div.gdpr_consent label span:nth-of-type(2)`;
    this.reviewForm = '#post-product-comment-form';
    this.reviewTitle = `${this.reviewForm} input[name=comment_title]`;
    this.reviewTextContent = `${this.reviewForm} textarea[name=comment_content]`;
    this.reviewRating = (rating: number) => `.star-full div:nth-child(${rating})`;
    this.reviewSubmitButton = `${this.reviewForm} button[type=submit]`;
    this.reviewCancelButton = `${this.reviewForm} button[data-dismiss="modal"]`;
    this.reviewSentConfirmationModal = '#product-comment-posted-modal';
    this.closeReviewSentConfirmationModalButton = `${this.reviewSentConfirmationModal} button`;

    // Products in pack selectors
    this.productInPackList = (productInList: number) => `.product-pack article:nth-child(${productInList})`;
    this.productInPackImage = (productInList: number) => `${this.productInPackList(productInList)} div.thumb-mask img`;
    this.productInPackName = (productInList: number) => `${this.productInPackList(productInList)} div.pack-product-name a`;
    this.productInPackPrice = (productInList: number) => `${this.productInPackList(productInList)} div.pack-product-price`;
    this.productInPackQuantity = (productInList: number) => `${this.productInPackList(productInList)}`
      + ' div.pack-product-quantity';

    this.productsBlock = (blockName: string) => `#content-wrapper section[data-type="${blockName}"]`;
  }

  // Methods

  /**
   * Get product page URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductPageURL(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.metaLink, 'content');
  }

  /**
   * Get product tag
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getProductTag(page: Page): Promise<string> {
    return this.getTextContent(page, this.productFlags);
  }

  /**
   * Is product tag visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isProductTagVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productFlag(''));
  }

  /**
   * Is a specific product flag visible
   * @param page {Page} Browser tab
   * @param name {string}
   * @return {Promise<boolean>}
   */
  async hasProductFlag(page: Page, name: string): Promise<boolean> {
    return this.elementVisible(page, this.productFlag(name), 2000);
  }

  /**
   * Is the Block Mail Alert visible ?
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasBlockMailAlert(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.productMailAlertsBlock, 2000);
  }

  /**
   * Return if the GDPR field is present
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasBlockMailAlertGDPRLabel(page: Page): Promise<boolean> {
    return await page.locator(this.productMailAlertsGDPRLabel).count() !== 0;
  }

  /**
   * Return the label for the GDPR field
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getBlockMailAlertGDPRLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.productMailAlertsGDPRLabel);
  }

  /**
   * Returns notifications block in block Mail Alert
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getBlockMailAlertNotification(page: Page): Promise<string> {
    return this.getTextContent(page, this.productMailAlertsNotification);
  }

  /**
   *
   * @param page {Page} Browser tab
   * @param email {string|null} Email if needed
   * @return {Promise<string>}
   */
  async notifyEmailAlert(page: Page, email: string | null = null): Promise<string> {
    if (email) {
      await this.setValue(page, this.productMailAlertsEmailInput, email);
    }
    await page.locator(this.productMailAlertsNotifyButton).click();

    return this.getBlockMailAlertNotification(page);
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
   * Is iframe visible in product d
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isIframeVisibleInProductDescription(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.productDescription} iframe`, 1000);
  }

  /**
   * Get URL in product description
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getURLInProductDescription(page: Page): Promise<string> {
    return this.getAttributeContent(page, `${this.productDescription} iframe`, 'src');
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
   * Click on product in pack
   * @param page {Page} Browser tab
   * @param productInList {number} Product in pack list
   * @returns {Promise<void>}
   */
  async clickProductInPackList(page: Page, productInList: number = 1): Promise<void> {
    // Add +1 due to span before the article
    const productIdentifier: number = productInList + 1;

    return this.clickAndWaitForURL(page, this.productInPackName(productIdentifier));
  }

  /**
   * get regular price
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getRegularPrice(page: Page): Promise<string> {
    return this.getTextContent(page, this.regularPrice);
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
    return page.locator(`${ulSelector} li .attribute-name`).allTextContents();
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
   * Get product image urls
   * @param page {Page} Browser tab
   * @returns {Promise<ProductImageUrls>}
   */
  async getProductImageUrls(page: Page): Promise<ProductImageUrls> {
    return {
      coverImage: await this.getAttributeContent(page, this.productCoverImg, 'src'),
      thumbImage: await this.getAttributeContent(page, this.thumbImg(1), 'src'),
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
  async getDiscountColumnTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.unitDiscountColumn);
  }

  /**
   * Get quantity discount value from volume discounts table
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getQuantityDiscountValue(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.quantityDiscountValue);
  }

  /**
   * Get discount value from volume discounts table
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDiscountValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.unitDiscountValue);
  }

  /**
   * Get volume discount saved value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSavedValue(page: Page): Promise<string> {
    return this.getTextContent(page, this.savedValue);
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
   * Get minimal product quantity label
   * @param page {Page} Browser tab
   * @return {promise<string>}
   */
  async getMinimalProductQuantityLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.productMinimalQuantity);
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
   * get the URL of the cover image
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getCoverImage(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.productCoverImg, 'src');
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param imageRow {number} Row of the image
   * @returns {Promise<string>}
   */
  async selectThumbImage(page: Page, imageRow: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.thumbImg(imageRow));
    await this.waitForVisibleSelector(page, `${this.thumbImg(imageRow)}.selected`);

    return this.getAttributeContent(page, this.productCoverImg, 'src');
  }

  /**
   * Scroll box arrows images
   * @param page {Page} Browser tab
   * @param direction {string} Direction to scroll
   * @returns {Promise<void>}
   */
  async scrollBoxArrowsImages(page: Page, direction: string): Promise<void> {
    await page.locator(this.scrollBoxImages(direction)).click();
    await page.waitForTimeout(1000);
  }

  /**
   * Zoom cover image
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async zoomCoverImage(page: Page): Promise<boolean> {
    await page.locator(this.zoomIcon).click({force: true});

    return this.elementVisible(page, this.productModal, 1000);
  }

  /**
   * Select thumb image
   * @param page {Page} Browser tab
   * @param imageRow {number} Row of the image
   * @returns {Promise<string>}
   */
  async selectThumbImageFromProductModal(page: Page, imageRow: number): Promise<string> {
    await this.waitForSelectorAndClick(page, this.thumbImgProductModal(imageRow));
    await this.waitForVisibleSelector(page, `${this.thumbImgProductModal(imageRow)}.selected`);

    return this.getAttributeContent(page, this.productCoverImgProductModal, 'src');
  }

  /**
   * get the URL of the cover image
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getCoverImageFromProductModal(page: Page): Promise<string | null> {
    return this.getAttributeContent(page, this.productCoverImg, 'src');
  }

  /**
   * Close product modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeProductModal(page: Page): Promise<boolean> {
    await page.mouse.click(5, 5);

    return this.elementNotVisible(page, this.productModal, 2000);
  }

  /**
   * Select default product attributes
   * @param page {Page} Browser tab
   * @param attributes {ProductAttribute[]}  Product's attributes data to select
   * @returns {Promise<void>}
   */
  async selectDefaultAttributes(page: Page, attributes: ProductAttribute[]): Promise<void> {
    if (attributes.length === 0) {
      return;
    }
    for (let i: number = 0; i < attributes.length; i++) {
      switch (attributes[i].name) {
        case 'color':
          await Promise.all([
            this.waitForVisibleSelector(page, `${this.productColorInput(attributes[i].value)}[checked]`),
            page.locator(this.productColorInput(attributes[i].value)).click(),
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
   * Select product attributes
   * @param page {Page} Browser tab
   * @param type {string} Type of block (Select or radio)
   * @param attributes {ProductAttribute[]}  Product's attributes data to select
   * @param itemNumber {number} The row of attribute block
   * @returns {Promise<void>}
   */
  async selectAttributes(page: Page, type: string, attributes: ProductAttribute[], itemNumber: number = 1): Promise<void> {
    if (attributes.length === 0) {
      return;
    }
    for (let i: number = 0; i < attributes.length; i++) {
      if (type === 'select') {
        await Promise.all([
          this.waitForAttachedSelector(page, `${this.productAttributeSelect(itemNumber)} option[selected]`),
          this.selectByVisibleText(page, this.productAttributeSelect(itemNumber), attributes[i].value),
        ]);
      } else {
        await Promise.all([
          this.waitForVisibleSelector(page, `${this.productAttributeButton(itemNumber)}[title=${attributes[i].value}][checked]`),
          page.locator(`${this.productAttributeButton(itemNumber)}[title=${attributes[i].value}]`).click(),
        ]);
      }
    }
  }

  /**
   * Get selected attribute
   * @param page {Page} Browser tab
   * @param variantItem {string} Variant row
   * @param type {string} Type of attribute
   * @returns {Promise<string>}
   */
  async getSelectedAttribute(page: Page, variantItem: number, type: string = 'select'): Promise<string> {
    if (type === 'select') {
      return this.getTextContent(page, `${this.productAttributeSelect(variantItem)} option[selected]`, false);
    }
    return this.getTextContent(page, `${this.productAttributeButton(variantItem)}[checked] +span`, false);
  }

  /**
   * Get selected attribute text
   * @param page {Page} Browser tab
   * @param variantItem {number} Variant row
   * @returns {Promise<string>}
   */
  async getSelectedAttributeText(page: Page, variantItem: number): Promise<string> {
    return this.getTextContent(page, this.productAttributeVariantSpan(variantItem));
  }

  /**
   * Set product customizations
   * @param page {Page} Browser tab
   * @param customizedTexts {string[]} Texts to set in customizations input
   * @param save {boolean} True if we need to save
   * @returns {Promise<void>}
   */
  async setProductCustomizations(page: Page, customizedTexts: string[], save: boolean = true): Promise<void> {
    for (let i = 1; i <= customizedTexts.length; i++) {
      await this.setValue(page, this.customizedTextarea(i), customizedTexts[i - 1]);
    }
    if (save) {
      await this.waitForSelectorAndClick(page, this.saveCustomizationButton);
    }
  }

  /**
   * Set product file customizations
   * @param page {Page} Browser tab
   * @param customizedFiles {string[]} Files to set in customizations input
   * @param row {number} Row to start
   * @param save {boolean} True if we need to save
   * @returns {Promise<void>}
   */
  async setProductFileCustomizations(page: Page, customizedFiles: string[], row: number = 1, save: boolean = true):
    Promise<void> {
    let j = row;

    for (let i = 1; i <= customizedFiles.length; i++) {
      await this.uploadFile(page, this.customizedFile(j), customizedFiles[i - 1]);
      j += 1;
    }
    if (save) {
      await this.waitForSelectorAndClick(page, this.saveCustomizationButton);
    }
  }

  /**
   * Get customizations messages
   * @param page {Page} Browser tab
   * @param customizationRow {number} Number of customizations to display
   * @returns {Promise<string>}
   */
  async getCustomizationsMessages(page: Page, customizationRow: number): Promise<string> {
    return this.getTextContent(page, this.customizationsMessage(customizationRow));
  }

  /**
   * Is customization message visible
   * @param page {Page} Browser tab
   * @param customizationRow {number} Number of customizations to display
   * @returns {Promise<string>}
   */
  async isCustomizationMessageVisible(page: Page, customizationRow: number): Promise<boolean> {
    return this.elementVisible(page, this.customizationsMessage(customizationRow));
  }

  /**
   * Get customization image
   * @param page {Page} Browser tab
   * @param customizationRow {number} Number of customizations to display
   * @returns {Promise<string>}
   */
  async getCustomizationImage(page: Page, customizationRow: number): Promise<string> {
    return this.getAttributeContent(page, this.customizationImg(customizationRow), 'href');
  }

  /**
   * Is customization image visible
   * @param page {Page} Browser tab
   * @param customizationRow {number} Number of customizations to display
   * @returns {Promise<string>}
   */
  async isCustomizationImageVisible(page: Page, customizationRow: number): Promise<boolean> {
    return this.elementVisible(page, this.customizationImg(customizationRow));
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   * @param page {Page} Browser tab
   * @param quantity {number|string} Quantity of the product that customer wants
   * @param combination {ProductAttribute[]}  Product's combination data to add to cart
   * @param proceedToCheckout {boolean|null} True to click on proceed to checkout button on modal
   * @param customizedText {string} Value of customization
   * @returns {Promise<void>}
   */
  async addProductToTheCart(
    page: Page,
    quantity: number | string = 1,
    combination: ProductAttribute[] = [],
    proceedToCheckout: boolean | null = true,
    customizedText: string = 'text',
  ): Promise<void> {
    await this.selectDefaultAttributes(page, combination);
    if (quantity !== 1) {
      await this.setValue(page, this.productQuantity, quantity);
    }

    if (await this.elementVisible(page, this.customizedTextarea(1), 2000)) {
      await this.setValue(page, this.customizedTextarea(1), customizedText);
      await this.waitForSelectorAndClick(page, this.saveCustomizationButton);
    }

    await this.waitForSelectorAndClick(page, this.addToCartButton);
    await this.waitForVisibleSelector(page, `${this.blockCartModal}[style*='display: block;']`);

    if (proceedToCheckout === true) {
      await this.waitForVisibleSelector(page, this.proceedToCheckoutButton);
      await this.clickAndWaitForURL(page, this.proceedToCheckoutButton);
      await this.waitForPageTitleToLoad(page);
    }
    if (proceedToCheckout === false) {
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
   * Click social sharing link
   * @param page {Page} Browser tab
   * @param socialSharing {string} Social network's name to get link from
   * @returns {Promise<Page>}
   */
  async clickOnSocialSharingLink(page: Page, socialSharing: string): Promise<Page> {
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

    return this.openLinkWithTargetBlank(page, selector, 'body', 'networkidle', false);
  }

  /**
   * Set quantity
   * @param page {Page} Browser tab
   * @param quantity {number|string} Quantity to set
   * @returns {Promise<void>}
   */
  async setQuantity(page: Page, quantity: number | string): Promise<void> {
    await this.setValue(page, this.productQuantity, quantity);
    await page.waitForResponse((response) => response.url().includes('product&token='));
  }

  /**
   * Click on add to cart button
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnAddToCartButton(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addToCartButton);
  }

  /**
   * Get product quantity
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getProductQuantity(page: Page): Promise<number> {
    return parseInt(await page.locator(this.productQuantity).evaluate((node: HTMLSelectElement) => node.value), 10);
  }

  /**
   * Update quantity value arrow up down in quick view modal
   * @param page {Page} Browser tab
   * @param quantityWanted {number} Value to add/subtract from quantity
   * @param direction {string} Direction to click on
   * @returns {Promise<string>}
   */
  async setQuantityByArrowUpDown(page: Page, quantityWanted: number, direction: string): Promise<void> {
    const inputValue = await this.getProductQuantity(page);
    const nbClick: number = Math.abs(inputValue - quantityWanted);

    for (let i = 0; i < nbClick; i++) {
      await page.locator(this.productRowQuantityUpDownButton(direction)).click();
    }
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
    return this.elementVisible(page, this.customizationBlock, 1000);
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
    return (await page.locator(this.productSizeOption(size)).count()) !== 0;
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
    return this.elementNotVisible(page, `${this.addToCartButton}:disabled`, 3000);
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
   * Click on the button "Add a review"
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickAddReviewButton(page: Page): Promise<void> {
    if (await this.getNumberOfComments(page) !== 0) {
      await page.locator(this.notEmptyReviewAddReviewButton).click();
    } else {
      await page.locator(this.emptyReviewAddReviewButton).click();
    }
  }

  /**
   * Add a product review
   * @param page {Page} Browser tab
   * @param productReviewData {FakerProductReview} The content of the product review (title, content, rating)
   * @returns {Promise<boolean>}
   */
  async addProductReview(page: Page, productReviewData: FakerProductReview): Promise<boolean> {
    await this.clickAddReviewButton(page);
    await this.waitForVisibleSelector(page, this.productReviewModal);
    await this.setValue(page, this.reviewTitle, productReviewData.reviewTitle);
    await this.setValue(page, this.reviewTextContent, productReviewData.reviewContent);
    await page.locator(this.reviewRating(productReviewData.reviewRating)).click();
    await page.locator(this.reviewSubmitButton).click();
    await page.locator(this.closeReviewSentConfirmationModalButton).click();
    return this.elementNotVisible(page, this.reviewSentConfirmationModal, 3000);
  }

  /**
   * Close the product review modal
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeProductReviewModal(page: Page): Promise<boolean> {
    await page.locator(this.reviewCancelButton).click();
    return !(await this.elementNotVisible(page, this.productReviewModal, 3000));
  }

  /**
   * Return if the GDPR field is present
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async hasProductReviewGDPRLabel(page: Page): Promise<boolean> {
    return await page.locator(this.productReviewModalGDPRLabel).count() !== 0;
  }

  /**
   * Return the label for the GDPR field
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getProductReviewGDPRLabel(page: Page): Promise<string> {
    return this.getTextContent(page, this.productReviewModalGDPRLabel);
  }

  /**
   * Get the number of approved review for a product
   * @param page {Page} The browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfComments(page: Page): Promise<number> {
    return page.locator(this.productReviewRows).count();
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
    return page.locator(this.productRatingStar(row)).count();
  }

  /**
   * Get the warning message
   * @param page {Page} browser tab
   * @returns {Promise<string>}
   */
  async getWarningMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.warningMessage);
  }

  /**
   * Has products block
   * @param blockName {'categoryproducts'} The block name in the page
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasProductsBlock(page: Page, blockName: 'categoryproducts'): Promise<boolean> {
    return (await page.locator(this.productsBlock(blockName)).count()) > 0;
  }
}

const productPage = new Product();
export {productPage, Product};
