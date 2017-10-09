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

namespace PrestaShopBundle\Currency\Repository;

use PrestaShopBundle\Currency\Currency;
use PrestaShopBundle\Currency\Repository\Installed\InstalledRepositoryInterface;
use PrestaShopBundle\Currency\Repository\Reference\ReferenceRepositoryInterface;

/**
 * Class MixedRepository
 *
 * TODO : description
 *
 * @package PrestaShopBundle\Currency\Repository
 */
class MixedRepository implements MixedRepositoryInterface
{

    /**
     * @var InstalledRepositoryInterface
     */
    protected $installedCurrencyRepository;

    /**
     * @var ReferenceRepositoryInterface
     */
    protected $referenceCurrencyRepository;

    /**
     * @param InstalledRepositoryInterface $installedCurrencyRepository
     * @param ReferenceRepositoryInterface $referenceCurrencyRepository
     */
    public function __construct(
        InstalledRepositoryInterface $installedCurrencyRepository,
        ReferenceRepositoryInterface $referenceCurrencyRepository
    ) {
        $this->installedCurrencyRepository = $installedCurrencyRepository;
        $this->referenceCurrencyRepository = $referenceCurrencyRepository;
    }

    /**
     * Get currency data by ISO 4217 code
     *
     * @param string $isoCode
     *
     * @return array The currency data
     */
    public function getReferenceCurrencyByIsoCode($isoCode)
    {
        return $this->referenceCurrencyRepository->getReferenceCurrencyByIsoCode($isoCode);
    }

    /**
     * Get currency by internal database identifier
     *
     * @param int $id
     *
     * @return Currency
     */
    public function getInstalledCurrencyById($id)
    {
        return $this->installedCurrencyRepository->getCurrencyById($id);
    }

    public function addInstalledCurrency(Currency $currency)
    {
        return $this->installedCurrencyRepository->addInstalledCurrency($currency);
    }

    public function updateInstalledCurrency(Currency $currency)
    {
        return $this->installedCurrencyRepository->updateInstalledCurrency($currency);
    }

    public function deleteInstalledCurrency(Currency $currency)
    {
        return $this->installedCurrencyRepository->deleteInstalledCurrency($currency);
    }
}
