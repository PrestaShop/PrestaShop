module.exports = {
  SiteMapPageFO: {
    brands_link:'#manufacturer-page',
    brands_image:'#main img[alt="%BRAND"]',
    last_brand_description_text:'#main .brand-infos a[href*="%NAME"]:parent:parent p:nth-child(2)', //parent do not work
    name_brand_link:'#main .brand-infos a[href*="%NAME"]',
    manufacturer_short_description_text:'#manufacturer-short_description',
    manufacturer_description_text:'#manufacturer-description',
    nav_brands_link:'(#wrapper a[itemprop="item"]:nth-child(2)',
    //brand_product_link:'//*[@id="main"]//a[text()="%NAME"]/ancestor::li/div[@class="brand-products"]/a[1]',
    list_product_link:'#js-product-list div.product-description a[href*="%TEXT"]',
  }
};
