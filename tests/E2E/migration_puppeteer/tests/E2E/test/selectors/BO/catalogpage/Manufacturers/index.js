module.exports = Object.assign(
    {
        Manufacturers: {
            submenu: '#subtab-AdminParentManufacturers a'
        }
    },
    require('./brands'),
    require('./brands_address')
);
