module.exports = {
  CategoryPageFO: {
    category_top_menu: '(//*[@id="category-%ID"])[%POS]',
    category_name: '//*[@id="left-column"]/div[contains(@class, "categories")]//a[text()="%NAME"]',
    category_title: '(//*[@id="main"]//h1)[1]',
    category_description: '//*[@id="category-description"]',
    category_picture: '//*[@id="main"]//img',
    breadcrumb_path: '#wrapper > div > nav'
  }
};