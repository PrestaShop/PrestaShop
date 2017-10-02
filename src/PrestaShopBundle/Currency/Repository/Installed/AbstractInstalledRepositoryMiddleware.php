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

use PrestaShopBundle\Currency\Currency;

abstract class AbstractInstalledRepositoryMiddleware implements InstalledRepositoryInterface
{

    /**
     * @var InstalledRepositoryInterface
     */
    protected $nextRepository;

    public function setNextRepository(InstalledRepositoryInterface $nextRepository = null)
    {
        $this->nextRepository = $nextRepository;
    }

    /**
     * Get currency data by internal database identifier
     *
     * @param int $id
     *
     * @return Currency
     */
    public function getCurrencyById($id)
    {
        if ((int) $id != $id) {
            throw new \PrestaShopBundle\Currency\Exception\InvalidArgumentException('$id must be an integer');
        }

        // get data from current repository
        $currency = $this->getCurrencyByIdOnCurrentRepository($id);
        if ($currency !== null) {
            return $currency;
        }

        // no next repository : we have nothing to return :(
        if ($this->nextRepository === null) {
            return null;
        }

        // get from next repository
        $currency = $this->nextRepository->getCurrencyById($id);

        return $currency;
    }

    public function addInstalledCurrency(Currency $currency)
    {
        // we have next repository : store on it first
        if ($this->nextRepository !== null) {
            // currency can be updated by repository
            $currency = $this->nextRepository->addInstalledCurrency($currency);
        }

        // then we can add on current one !
        // currency can be updated by repository
        $currency = $this->addInstalledCurrencyOnCurrentRepository($currency);

        // return potentially updated currency
        return $currency;
    }

    public function updateInstalledCurrency(Currency $currency)
    {
        // we have next repository : update on it first
        if ($this->nextRepository !== null) {
            // currency can be updated by repository
            $currency = $this->nextRepository->updateInstalledCurrency($currency);
        }

        // then we can update on current one !
        // currency can be updated by repository
        $currency = $this->updateInstalledCurrencyCurrentRepository($currency);

        // return potentially updated currency
        return $currency;
    }

    public function deleteInstalledCurrency(Currency $currency)
    {
        // we have next repository : delete from it first
        if ($this->nextRepository !== null) {
            // currency can be deleted from repository
            $this->nextRepository->updateInstalledCurrency($currency);
        }

        // then we can delete from current one !
        $this->updateInstalledCurrencyCurrentRepository($currency);

        return true;
    }

    /**
     * @param $id
     *
     * @return Currency|null
     */
    abstract protected function getCurrencyByIdOnCurrentRepository($id);

    /**
     * @param Currency $currency
     *
     * @return Currency|null
     */
    abstract protected function addInstalledCurrencyOnCurrentRepository(Currency $currency);

    /**
     * @param Currency $currency
     *
     * @return Currency|null
     */
    abstract protected function updateInstalledCurrencyCurrentRepository(Currency $currency);

    /**
     * @param Currency $currency
     *
     * @return bool
     */
    abstract protected function deleteInstalledCurrencyCurrentRepository(Currency $currency);
}
