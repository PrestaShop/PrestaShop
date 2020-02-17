require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Product extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors for product page
    this.productName = '#main h1[itemprop="name"]';
    this.productPrice = '#main span[itemprop="price"]';
    this.productQuantity = '#quantity_wanted';
    this.productDescription = '#description';
    this.colorInput = '#group_2 li input[title=%COLOR]';
    this.addToCartButton = '#add-to-cart-or-refresh button[data-button-action="add-to-cart"]';
    this.proceedToCheckoutButton = '#blockcart-modal div.cart-content-btn a';
    this.continueShoppingButton = '#blockcart-modal div.cart-content-btn button';
  }

  /**
   * To check the product information (Product name, price, quantity, description)
   * @param productData, product data to check
   */
  async checkProduct(productData) {
    return {
      name: await this.checkTextValue(this.productName, productData.name),
      price: await this.checkAttributeValue(this.productPrice, 'content', productData.price),
      quantity_wanted: await this.checkAttributeValue(this.productQuantity, 'value', productData.quantity_wanted),
      description: await this.checkTextValue(this.productDescription, productData.description, 'contain'),
    };
  }

  /**
   * Click on Add to cart button then on Proceed to checkout button in the modal
   * @param attributeToChoose
   * @param proceedToCheckout
   * @returns {Promise<void>}
   */
  async addProductToTheCart(attributeToChoose = '', proceedToCheckout = true) {
    await this.page.waitFor(1000);
    if (attributeToChoose.color) {
      await Promise.all([
        this.page.waitForSelector(this.colorInput.replace('%COLOR', attributeToChoose.color), {visible: true}),
        this.page.click(this.colorInput.replace('%COLOR', attributeToChoose.color)),
      ]);
    }
    if (attributeToChoose.quantity) {
      await this.setValue(this.productQuantity, attributeToChoose.quantity.toString());
    }
    await this.waitForSelectorAndClick(this.addToCartButton);
    if (proceedToCheckout) await this.waitForSelectorAndClick(this.proceedToCheckoutButton);
    else await this.waitForSelectorAndClick(this.continueShoppingButton);
  }
};
