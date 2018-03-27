module.exports = Object.assign(
    {
        Manufacturers: {
            submenu: '//*[@id="subtab-AdminParentManufacturers"]/a'
        }
    },
    require('./brands'),
    require('./brands_address')
);
