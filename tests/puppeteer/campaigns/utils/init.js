require('module-alias/register');

module.exports = async function init(pagesObjects, page) {
  let returnItems = {};

  Object.keys(pagesObjects).forEach((item) => {
    const returnItem = {
      [item]: new pagesObjects[item](page),
    };
    returnItems = {
      ...returnItems,
      ...returnItem,
    };
  });
  return returnItems;
};
