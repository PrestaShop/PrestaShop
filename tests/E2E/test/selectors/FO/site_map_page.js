module.exports = {
  SiteMapPageFO: {
    brands_link:'//*[@id="manufacturer-page"]',
    brands_image:'//*[@id="main"]//img[@alt="%BRAND"]',
    last_brand_description_text:'//*[@id="main"]//a[text()="%NAME"]',
    name_brand_link:'//*[@id="main"]//a[text()="%NAME"]',
    manufacturer_short_description_text:'//div[@id="manufacturer-short_description"]',
    manufacturer_description_text:'//*[@id="manufacturer-description"]',
    nav_brands_link:'(//*[@id="wrapper"]//a[@itemprop="item"])[2]',
    brand_product_link:'//*[@id="main"]//a[text()="%NAME"]/ancestor::li/div[@class="brand-products"]/a[1]',
    list_product_link:'//*[@id="js-product-list"]//div[@class="product-description"]//a[contains(@href,"%TEXT")]',
  }
};
