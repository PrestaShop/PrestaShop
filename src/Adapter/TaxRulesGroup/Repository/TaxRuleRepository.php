<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository;

use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotAddTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotDeleteTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\CannotUpdateTaxRuleException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\Exception\TaxRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\TaxRule\ValueObject\TaxRuleId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use TaxRule;

/**
 * Provides access to TaxRule data source
 */
class TaxRuleRepository extends AbstractObjectModelRepository
{
    /**
     * @param TaxRuleId $taxRuleId
     *
     * @return TaxRule
     *
     * @throws CoreException
     * @throws TaxRuleNotFoundException
     */
    public function get(TaxRuleId $taxRuleId): TaxRule
    {
        /** @var TaxRule $taxRule */
        $taxRule = $this->getObjectModel(
            $taxRuleId->getValue(),
            TaxRule::class,
            TaxRuleNotFoundException::class
        );

        return $taxRule;
    }

    /**
     * @param TaxRuleId $taxRuleId
     *
     * @throws CoreException
     * @throws TaxRuleNotFoundException
     */
    public function assertTaxRuleExists(TaxRuleId $taxRuleId): void
    {
        $this->assertObjectModelExists(
            $taxRuleId->getValue(),
            'tax_rule',
            TaxRuleNotFoundException::class
        );
    }

    /**
     * @param TaxRule $taxRule
     *
     * @return TaxRuleId
     *
     * @throws CannotAddTaxRuleException
     */
    public function add(TaxRule $taxRule): TaxRuleId
    {
        $id = $this->addObjectModel($taxRule, CannotAddTaxRuleException::class);

        return new TaxRuleId($id);
    }

    /**
     * @param TaxRule $taxRule
     *
     * @throws CannotUpdateTaxRuleException
     */
    public function update(TaxRule $taxRule): void
    {
        $this->updateObjectModel($taxRule, CannotUpdateTaxRuleException::class);
    }

    /**
     * @param TaxRule $taxRule
     *
     * @throws CannotDeleteTaxRuleException
     */
    public function delete(TaxRule $taxRule): void
    {
        $this->deleteObjectModel($taxRule, CannotDeleteTaxRuleException::class);
    }
}
