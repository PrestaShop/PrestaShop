<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Currency\Repository\Installed;

use Doctrine\DBAL\Driver\Connection;
use PrestaShopBundle\Currency\Currency;
use PrestaShop\Prestashop\Adapter\Currency\Currency as CurrencyAdapterModel;
use PrestaShopBundle\Currency\CurrencyFactory;
use PrestaShopBundle\Currency\Exception\CurrencyNotFoundException;

/**
 * Class InstalledDatabaseRepository
 *
 * Provides and saves Currency objects.
 * Implements InstalledRepositoryInterface.
 * This currency repository interacts with database.
 *
 * @package PrestaShopBundle\Currency\Repository\Installed
 */
class InstalledDatabaseRepository extends AbstractInstalledRepositoryMiddleware
{

    protected $connection;

    /**
     * @var \Prestashop\PrestaShop\Adapter\Currency\CurrencyRepository
     */
    protected $currencyProvider;

    public function __construct(
        InstalledRepositoryInterface $nextRepository = null,
        Connection $connection,
        $currencyProviderAdapter
    ) {
        $this->setNextRepository($nextRepository);
        $this->currencyProvider = $currencyProviderAdapter;
    }

    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return Currency
     */
    public function getCurrencyByIdOnCurrentRepository($id)
    {
        $currencyAdapterModel = $this->currencyProvider->getById($id);
        if ($currencyAdapterModel->getId() > 0) {
            $factory  = new CurrencyFactory();
            $currency = $factory->setId($currencyAdapterModel->getId())
                                ->setIsoCode($currencyAdapterModel->getIsoCode())
                                ->setNumericIsoCode($currencyAdapterModel->getIsoCodeNum())
                                ->setDecimalDigits($currencyAdapterModel->getDecimals())
                                ->setDisplayName($currencyAdapterModel->getName())
                                ->build();

            return $currency;
        }

        return null;
    }

    /**
     * @param Currency $currency
     *
     * @return Currency|null
     */
    protected function addInstalledCurrencyOnCurrentRepository(Currency $currency)
    {
        $currencyAdapterModel = $this->currencyProvider->getNewCurrency();
        $this->hydrateCurrencyModel($currencyAdapterModel, $currency);
        $this->currencyProvider->add($currencyAdapterModel);

        return $currency;
    }

    /**
     * @param Currency $currency
     *
     * @return Currency|null
     */
    protected function updateInstalledCurrencyCurrentRepository(Currency $currency)
    {
        if ($currency->getId() <= 0) {
            throw new CurrencyNotFoundException(
                'Cannot update currency with id ' . $currency->getId() . ' : currency not found'
            );
        }
        $currencyAdapterModel = new $this->currencyProvider->getNewMCurrency();
        $this->hydrateCurrencyModel($currencyAdapterModel, $currency);

        return $currency;
    }

    protected function hydrateCurrencyModel(CurrencyAdapterModel $currencyModel, Currency $currency)
    {
        $currencyModel->setIsoCode($currency->getIsoCode());
        $currencyModel->setIsoCodeNum($currency->getNumericIsoCode());
        $currencyModel->setDecimals($currency->getDecimalDigits());
        $currencyModel->setName($currency->getDisplayNames());
    }

    /**
     * @param Currency $currency
     *
     * @return bool
     */
    protected function deleteInstalledCurrencyCurrentRepository(Currency $currency)
    {
        if (!$currency->getId()) {
            throw new CurrencyNotFoundException(
                'Cannot update currency without id '
            );
        }
        $currencyAdapterModel = $this->currencyProvider->getById($currency->getId());
        if ($currencyAdapterModel->getId() <= 0) {
            throw new CurrencyNotFoundException(
                'Cannot update currency with id ' . $currencyAdapterModel->getId() . ' : currency not found'
            );
        }
        $this->currencyProvider->delete($currencyAdapterModel);

        return true;
    }
}
