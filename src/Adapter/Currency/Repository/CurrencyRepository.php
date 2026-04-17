<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\Repository;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data source of Currency
 */
class CurrencyRepository extends AbstractObjectModelRepository
{
    /**
     * @param CurrencyId $currencyId
     *
     * @throws CoreException
     * @throws CurrencyNotFoundException
     */
    public function assertCurrencyExists(CurrencyId $currencyId): void
    {
        $this->assertObjectModelExists($currencyId->getValue(), 'currency', CurrencyNotFoundException::class);
    }

    /**
     * @param CurrencyId $currencyId
     *
     * @return Currency
     *
     * @throws CoreException
     * @throws CurrencyNotFoundException
     */
    public function get(CurrencyId $currencyId): Currency
    {
        /** @var Currency $currency */
        $currency = $this->getObjectModel(
            $currencyId->getValue(),
            Currency::class,
            CurrencyNotFoundException::class
        );

        return $currency;
    }

    /**
     * @param CurrencyId $currencyId
     *
     * @return string
     */
    public function getIsoCode(CurrencyId $currencyId): string
    {
        return $this->get($currencyId)->iso_code;
    }
}
