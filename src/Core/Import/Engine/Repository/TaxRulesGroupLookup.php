<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;

/**
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class TaxRulesGroupLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly TaxComputer $taxComputer,
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function taxRulesGroupExists(int $taxRulesGroupId): bool
    {
        return false !== $this->connection->fetchOne(
            'SELECT 1 FROM ' . $this->dbPrefix . 'tax_rules_group WHERE id_tax_rules_group = :taxRulesGroupId AND deleted = 0',
            ['taxRulesGroupId' => $taxRulesGroupId]
        );
    }

    /**
     * Tax rate (percentage) of the group for the shop's address country —
     * the rate legacy used to de-tax price_tin values.
     */
    public function getTaxRate(int $taxRulesGroupId): DecimalNumber
    {
        return $this->taxComputer->getTaxRate(
            new TaxRulesGroupId($taxRulesGroupId),
            new CountryId($this->getShopCountryId())
        );
    }

    /**
     * Legacy Shop::getAddress() country resolution.
     */
    private function getShopCountryId(): int
    {
        $shopCountryId = (int) $this->configuration->get('PS_SHOP_COUNTRY_ID');

        return $shopCountryId > 0 ? $shopCountryId : (int) $this->configuration->get('PS_COUNTRY_DEFAULT');
    }
}
