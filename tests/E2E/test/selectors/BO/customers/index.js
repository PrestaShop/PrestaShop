module.exports = Object.assign(
    {
        BO: {
            success_panel: '//*[@id="content"]/div[@class="bootstrap"]/div[contains(@class, "success")]',
        }
    },
    require('./addresses'),
    require('./customer')
);