<?php

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use Configuration as ConfigurationLegacy;
use Language as LanguageLegacy;
use PrestaShop\PrestaShop\Core\Configuration\MultiLangConfigurationInterface;

/**
 * Class MultiLangConfiguration is responsible for getting multi-language configuration
 */
class MultiLangConfiguration implements MultiLangConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function get($key, $idShopGroup = null, $idShop = null)
    {
        return ConfigurationLegacy::getInt($key, $idShopGroup, $idShop);
    }

    /**
     * @inheritDoc
     */
    public function getIncludingInactiveLocales($key, $idShopGroup = null, $idShop = null)
    {
        $languageIds = LanguageLegacy::getIDs(false);
        $result = [];
        foreach ($languageIds as $idLang) {
            $result[$idLang] = ConfigurationLegacy::get($key, $idLang, $idShopGroup, $idShop);
        }
        return $result;
    }
}
