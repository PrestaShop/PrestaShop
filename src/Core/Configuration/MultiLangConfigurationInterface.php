<?php

namespace PrestaShop\PrestaShop\Core\Configuration;

interface MultiLangConfigurationInterface
{
    /**
     * Gets configuration from database.
     *
     * @param string $key - a key which represents `name` columns in `configuration` table
     * @param null|int $idShopGroup
     * @param null|int $idShop
     *
     * @return array - returns a value from database. It gets only from active languages. The array key contains
     * language id
     */
    public function get($key, $idShopGroup = null, $idShop = null);

    /**
     * Gets configuration from database.
     *
     * @param string $key - a key which represents `name` columns in `configuration` table
     * @param null|int $idShopGroup
     * @param null|int $idShop
     *
     * @return array - returns a value from database. It gets from installed languages. The array key contains
     * language id
     */
    public function getIncludingInactiveLocales($key, $idShopGroup = null, $idShop = null);
}
