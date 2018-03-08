<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Data\Layer\AbstractDataLayer;
use PrestaShop\PrestaShop\Core\Data\Layer\DataLayerException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyDataLayerInterface as CldrCurrencyDataLayerInterface;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShopBundle\Entity\Currency as CurrencyEntity;

/**
 * CLDR Currency Database (Doctrine) data layer
 *
 * Provides and persists currency data from/into database
 */
class CurrencyDatabase extends AbstractDataLayer implements CldrCurrencyDataLayerInterface
{
    /**
     * Doctrine entity manager, used to read and write into the database layer
     *
     * @var EntityManagerInterface
     */
    protected $em;

    protected $localeCode;

    public function __construct(EntityManagerInterface $em, $localeCode)
    {
        $this->em         = $em;
        $this->localeCode = $localeCode;
    }

    /**
     * Set the lower layer.
     * When reading data, if nothing is found then it will try to read in the lower data layer
     * When writing data, the data will also be written in the lower data layer
     *
     * @param CldrCurrencyDataLayerInterface $lowerLayer
     *  The lower data layer.
     *
     * @return self
     */
    public function setLowerLayer(CldrCurrencyDataLayerInterface $lowerLayer)
    {
        $this->lowerDataLayer = $lowerLayer;

        return $this;
    }

    /**
     * Actually read a data object into the current layer
     *
     * Data is read into database
     *
     * @param string $currencyCode
     *  The CurrencyData object identifier
     *
     * @return CurrencyData|null
     *  The wanted CurrencyData object (null if not found)
     */
    protected function doRead($currencyCode)
    {
        /** @var CurrencyEntity $currencyEntity */
        $currencyEntity = $this->em->getRepository(CurrencyEntity::class)->findOneBy(['iso_code' => $currencyCode]);
        $currencyData   = new CurrencyData();

        $currencyData->isoCode                    = $currencyEntity->getIsoCode();
        $currencyData->numericIsoCode             = $currencyEntity->getNumericIsoCode(); // TODO
        $currencyData->symbols[$this->localeCode] = $currencyEntity->getSymbol(); // TODO
        $currencyData->precision                  = $currencyEntity->getPrecision(); // TODO
        $currencyData->names[$this->localeCode]   = $currencyEntity->getName();

        return $currencyData;
    }

    /**
     * Actually write a data object into the current layer
     *
     * Might be a file edit, cache update, DB insert/update...
     *
     * @param mixed $currencyCode
     *  The data object identifier
     *
     * @param mixed $currencyData
     *  The data object to be written
     *
     * @return void
     *
     * @throws DataLayerException
     *  When write fails
     */
    protected function doWrite($currencyCode, $currencyData)
    {
        // TODO: Implement doWrite() method.
    }
}
